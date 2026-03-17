<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\InstructorEvaluation;
use App\Models\PracticalSession;
use App\Models\ScheduleRescheduleRequest;
use App\Models\SessionAttendance;
use App\Models\Student;
use App\Models\ClassFeedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $instructor = Auth::guard('instructor')->user();
        //dd($instructor);
        // Dashboard Statistics
        $dashboardStats = $this->getDashboardStatistics($instructor);

        // Student counts by status
        $studentCounts = $this->getStudentCountsByStatus($instructor);

        // Upcoming schedules - both theory and practical
        $upcomingTheoryClasses = $this->getUpcomingClasses($instructor, 'theory');
        $upcomingPracticalClasses = $this->getUpcomingClasses($instructor, 'practical');

        // Recent student activities (completions, status changes)
        $recentStudentActivities = $this->getRecentStudentActivities($instructor);

        return view('dashboards.instructor-dashboard', [
            'instructor' => $instructor,
            'stats' => $dashboardStats,
            'studentCounts' => $studentCounts,
            'theoryClasses' => $upcomingTheoryClasses,
            'practicalClasses' => $upcomingPracticalClasses,
            'recentActivities' => $recentStudentActivities,
        ]);
    }

    /**
     * Get dashboard statistics
     */
    protected function getDashboardStatistics($instructor)
    {
        //dd($instructor);
        $totalCourses = CourseSchedule::where('instructor_id', $instructor->id)->distinct('course_id')->count('course_id');

        $totalStudents = Student::where('instructor_id', $instructor->id)->count();

        $pendingTheoryStudents = Student::where('instructor_id', $instructor->id)->where('theory_status', 'pending')->count();

        $pendingPracticalStudents = Student::where('instructor_id', $instructor->id)->where('theory_status', 'completed')->where('practical_status', 'pending')->count();

        $completedStudents = Student::where('instructor_id', $instructor->id)->where('theory_status', 'completed')->where('practical_status', 'completed')->count();

        return [
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'pendingTheoryStudents' => $pendingTheoryStudents,
            'pendingPracticalStudents' => $pendingPracticalStudents,
            'completedStudents' => $completedStudents,
        ];
    }

    /**
     * Get student counts by status
     */
    protected function getStudentCountsByStatus($instructor)
    {
        return [
            'theory' => [
                'pending' => Student::where('instructor_id', $instructor->id)->where('theory_status', 'pending')->count(),
                'in_progress' => Student::where('instructor_id', $instructor->id)->where('theory_status', 'in_progress')->count(),
                'completed' => Student::where('instructor_id', $instructor->id)->where('theory_status', 'completed')->count(),
            ],
            'practical' => [
                'pending' => Student::where('instructor_id', $instructor->id)->where('theory_status', 'completed')->where('practical_status', 'pending')->count(),
                'assigned' => Student::where('instructor_id', $instructor->id)->where('practical_status', 'assigned')->count(),
                'completed' => Student::where('instructor_id', $instructor->id)->where('practical_status', 'completed')->count(),
                'not_appeared' => Student::where('instructor_id', $instructor->id)->where('practical_status', 'not_appeared')->count(),
                'failed' => Student::where('instructor_id', $instructor->id)->where('practical_status', 'failed')->count(),
            ],
        ];
    }

    /**
     * Get upcoming classes by type (theory or practical)
     */
    protected function getUpcomingClasses($instructor, $type)
    {
        return CourseSchedule::where('instructor_id', $instructor->id)
            ->where('session_type', $type)
            ->where('date', '>=', now())
            ->with(['course', 'students'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();
    }

    /**
     * Get recent student activities
     */
    protected function getRecentStudentActivities($instructor)
    {
        // This would be based on a new table that logs status changes
        // For now, we'll just get recent students
        return Student::where('instructor_id', $instructor->id)->with('course')->orderBy('updated_at', 'desc')->take(10)->get();
    }

    /**
     * Show theory calendar view
     */
    public function theoryCalendar(Request $request)
    {
        $instructor = Auth::guard('instructor')->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $courses = Course::whereHas('schedules', function ($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->get();

        $schedules = CourseSchedule::where('instructor_id', $instructor->id)
            ->where('session_type', 'theory')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['course', 'students'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($schedule) {
                return $schedule->date->format('Y-m-d');
            });

        return view('instructor.theory-calendar', [
            'instructor' => $instructor,
            'month' => $month,
            'year' => $year,
            'courses' => $courses,
            'schedules' => $schedules,
            'calendar' => $this->generateCalendarData($month, $year, $schedules),
        ]);
    }

    /**
     * Show practical calendar view
     */
    public function practicalCalendar(Request $request)
    {
        $instructor = Auth::guard('instructor')->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $pendingStudents = Student::where('instructor_id', $instructor->id)->where('theory_status', 'completed')->where('practical_status', 'pending')->with('course')->get();

        $schedules = CourseSchedule::where('instructor_id', $instructor->id)
            ->where('session_type', 'practical')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['course', 'students'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($schedule) {
                return $schedule->date->format('Y-m-d');
            });

        return view('instructor.practical-calendar', [
            'instructor' => $instructor,
            'month' => $month,
            'year' => $year,
            'pendingStudents' => $pendingStudents,
            'schedules' => $schedules,
            'calendar' => $this->generateCalendarData($month, $year, $schedules),
        ]);
    }

    /**
     * Generate calendar data for a given month
     */
    protected function generateCalendarData($month, $year, $schedules)
    {
        $calendar = [];
        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;

        // Find the first day of the month and adjust for the calendar
        $firstDayOfMonth = $date->copy()->firstOfMonth()->dayOfWeek;
        $firstDayOfMonth = $firstDayOfMonth == 0 ? 7 : $firstDayOfMonth;

        // Previous month filler days
        $prevMonthDays = $firstDayOfMonth - 1;
        if ($prevMonthDays > 0) {
            $prevMonth = $date->copy()->subMonth();
            $prevMonthLastDay = $prevMonth->daysInMonth;

            for ($i = $prevMonthDays; $i > 0; $i--) {
                $calendar[] = [
                    'day' => $prevMonthLastDay - $i + 1,
                    'month' => $prevMonth->month,
                    'year' => $prevMonth->year,
                    'isCurrentMonth' => false,
                    'hasSchedules' => false,
                    'schedules' => [],
                ];
            }
        }

        // Current month days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $daySchedules = $schedules[$currentDate] ?? [];

            $calendar[] = [
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'isCurrentMonth' => true,
                'hasSchedules' => count($daySchedules) > 0,
                'schedules' => $daySchedules,
            ];
        }

        // Next month filler days
        $totalDays = count($calendar);
        $nextMonthDays = 42 - $totalDays; // 42 = 6 weeks * 7 days

        if ($nextMonthDays > 0) {
            $nextMonth = $date->copy()->addMonth();

            for ($i = 1; $i <= $nextMonthDays; $i++) {
                $calendar[] = [
                    'day' => $i,
                    'month' => $nextMonth->month,
                    'year' => $nextMonth->year,
                    'isCurrentMonth' => false,
                    'hasSchedules' => false,
                    'schedules' => [],
                ];
            }
        }

        return $calendar;
    }

    /**
     * Mark theory class as complete
     */
    public function markTheoryComplete(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $studentIds = $request->input('student_ids', []);

        // Update student statuses
        Student::whereIn('id', $studentIds)->update([
            'theory_status' => 'completed',
            'theory_completion_date' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', count($studentIds) . ' students marked as completed theory class');
    }

    /**
     * Assign multiple practical sessions for a student (2 hrs each).
     * Input: student_id, total_hours, sessions[] => [{date, start_time}]
     */
    public function assignPracticalSessions(Request $request)
    {
        $request->validate([
            'student_id'             => 'required|exists:students,id',
            'total_hours'            => 'required|numeric|min:2',
            'sessions'               => 'required|array|min:1',
            'sessions.*.date'        => 'required|date',
            'sessions.*.start_time'  => 'required|date_format:H:i',
        ]);

        $instructor  = Auth::guard('instructor')->user();
        $student     = Student::findOrFail($request->student_id);
        $sessionData = $request->sessions;

        // Delete any previously scheduled (but not yet completed) sessions for clean re-assignment
        PracticalSession::where('student_id', $student->id)
            ->where('status', 'scheduled')
            ->delete();

        foreach ($sessionData as $index => $sess) {
            $startTime = Carbon::parse($sess['start_time']);
            $endTime   = $startTime->copy()->addHours(2);

            PracticalSession::create([
                'student_id'     => $student->id,
                'instructor_id'  => $instructor->id,
                'course_id'      => $student->course_id,
                'session_number' => $index + 1,
                'date'           => $sess['date'],
                'start_time'     => $startTime->format('H:i:s'),
                'end_time'       => $endTime->format('H:i:s'),
                'duration_hours' => 2.0,
                'status'         => 'scheduled',
            ]);
        }

        $student->update(['practical_status' => 'assigned']);

        return redirect()->back()->with('success', count($sessionData) . ' practical session(s) assigned successfully.');
    }

    /**
     * Submit feedback for an individual practical session.
     */
    public function submitSessionFeedback(Request $request, PracticalSession $session)
    {
        $request->validate([
            'status'           => 'required|in:completed,failed,not_appeared,cancelled',
            'instructor_notes' => 'nullable|string|max:2000',
        ]);

        $instructor = Auth::guard('instructor')->user();

        // Verify session belongs to this instructor's student
        if ($session->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $session->update([
            'status'           => $request->status,
            'instructor_notes' => $request->instructor_notes,
            'completed_at'     => in_array($request->status, ['completed', 'failed', 'not_appeared'])
                                    ? now() : null,
        ]);

        // Update hours_practical on student from total completed sessions
        $student = $session->student;
        $completedHours = PracticalSession::where('student_id', $student->id)
            ->where('status', 'completed')
            ->sum('duration_hours');
        $student->hours_practical = $completedHours;

        // Check if all sessions are done (completed/failed/not_appeared/cancelled)
        $allSessions   = PracticalSession::where('student_id', $student->id)->get();
        $doneSessions  = $allSessions->whereIn('status', ['completed', 'failed', 'not_appeared', 'cancelled']);
        $allDone       = $allSessions->count() > 0 && $allSessions->count() === $doneSessions->count();
        $anyCompleted  = $allSessions->where('status', 'completed')->count() > 0;

        if ($allDone) {
            // Determine overall practical outcome
            $anyFailed      = $allSessions->where('status', 'failed')->count() > 0;
            $anyNotAppeared = $allSessions->where('status', 'not_appeared')->count() > 0;

            if ($anyCompleted) {
                $student->practical_status           = 'completed';
                $student->practical_completion_date  = now();
            } elseif ($anyFailed) {
                $student->practical_status = 'failed';
            } else {
                $student->practical_status = 'not_appeared';
            }
        }

        $student->save();

        return redirect()->back()->with('success', 'Session feedback saved.');
    }

    /**
     * Mark a theory (or practical) schedule session as complete for all enrolled students.
     * Creates/updates session_attendance records with computed class_order.
     */
    public function markClassComplete(Request $request, CourseSchedule $schedule)
    {
        $instructor = Auth::guard('instructor')->user();

        // Verify instructor has students in this course (schedules are course-level, not per-instructor)
        $hasStudents = Student::where('course_id', $schedule->course_id)
            ->where('instructor_id', $instructor->id)
            ->exists();
        if (!$hasStudents) {
            abort(403, 'Unauthorized action.');
        }

        // If this schedule is explicitly assigned to specific students (via pivot), mark only those.
        // Otherwise (course-level group session) mark all of this instructor's students in the course.
        $pivotStudentIds = DB::table('course_schedule_student')
            ->where('course_schedule_id', $schedule->id)
            ->pluck('student_id');

        if ($pivotStudentIds->isNotEmpty()) {
            $students = Student::whereIn('id', $pivotStudentIds)
                ->where('instructor_id', $instructor->id)
                ->get();
        } else {
            $students = Student::where('course_id', $schedule->course_id)
                ->where('instructor_id', $instructor->id)
                ->get();
        }

        $autoCompletedTheory    = 0;
        $autoCompletedPractical = 0;

        foreach ($students as $student) {
            // Determine class_order: count completed sessions of this type so far, then +1
            $completedCount = SessionAttendance::where('student_id', $student->id)
                ->where('class_type', $schedule->session_type)
                ->where('status', 'completed')
                ->where('course_schedule_id', '!=', $schedule->id) // exclude current if re-marking
                ->count();

            $classOrder = $completedCount + 1;

            SessionAttendance::updateOrCreate(
                [
                    'student_id'         => $student->id,
                    'course_schedule_id' => $schedule->id,
                ],
                [
                    'is_present'   => true,
                    'status'       => 'completed',
                    'class_type'   => $schedule->session_type,
                    'class_order'  => $classOrder,
                    'completed_at' => now(),
                    'notes'        => $request->input('notes'),
                ]
            );

            // Auto-update student status based on completed class counts
            $studentCourse = $student->course; // may be null
            if ($schedule->session_type === 'theory') {
                $totalDone     = $completedCount + 1; // includes the one just recorded
                $totalRequired = $studentCourse ? ($studentCourse->total_theory_classes ?? 0) : 0;

                if ($totalRequired > 0 && $totalDone >= $totalRequired) {
                    // All required theory classes done — auto-complete
                    if ($student->theory_status !== 'completed') {
                        $student->update(['theory_status' => 'completed', 'theory_completion_date' => now()]);
                        $autoCompletedTheory++;
                    }
                } elseif ($student->theory_status === 'pending') {
                    $student->update(['theory_status' => 'in_progress']);
                }
            } elseif ($schedule->session_type === 'practical') {
                $totalDone     = $completedCount + 1;
                $totalRequired = $studentCourse ? ($studentCourse->total_practical_classes ?? 0) : 0;

                if ($totalRequired > 0 && $totalDone >= $totalRequired) {
                    if ($student->practical_status !== 'completed') {
                        $student->update(['practical_status' => 'completed', 'practical_completion_date' => now()]);
                        $autoCompletedPractical++;
                    }
                }
            }
        }

        $msg = 'Session marked as complete for ' . $students->count() . ' student(s). Feedback is now unlocked for them.';
        if ($autoCompletedTheory > 0) {
            $msg .= ' ' . $autoCompletedTheory . ' student(s) have now completed all required theory classes.';
        }
        if ($autoCompletedPractical > 0) {
            $msg .= ' ' . $autoCompletedPractical . ' student(s) have now completed all required practical classes.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Submit instructor evaluation for a student at end of course.
     */
    public function submitEvaluation(Request $request, Student $student)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'performance_rating'             => 'required|integer|min:1|max:5',
            'behavior_rating'                => 'required|integer|min:1|max:5',
            'attendance_rating'              => 'required|integer|min:1|max:5',
            'overall_rating'                 => 'required|integer|min:1|max:5',
            'performance_notes'              => 'nullable|string|max:2000',
            'behavior_notes'                 => 'nullable|string|max:2000',
            'recommendations'                => 'nullable|string|max:2000',
            'is_recommended_for_certificate' => 'boolean',
        ]);

        InstructorEvaluation::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id'  => $student->course_id,
            ],
            [
                'instructor_id'                  => $instructor->id,
                'performance_rating'             => $request->performance_rating,
                'behavior_rating'                => $request->behavior_rating,
                'attendance_rating'              => $request->attendance_rating,
                'overall_rating'                 => $request->overall_rating,
                'performance_notes'              => $request->performance_notes,
                'behavior_notes'                 => $request->behavior_notes,
                'recommendations'                => $request->recommendations,
                'is_recommended_for_certificate' => $request->boolean('is_recommended_for_certificate', true),
            ]
        );

        return redirect()->back()->with('success', 'Evaluation submitted successfully.');
    }

    /**
     * @deprecated – kept for backward-compat but new logic uses assignPracticalSessions().
     */
    public function assignPracticalSlot(Request $request)
    {
        return redirect()->back()->with('error', 'Please use the new practical session assignment form.');
    }

    /**
     * @deprecated – kept for backward-compat; new flow uses submitSessionFeedback().
     */
    public function submitPracticalFeedback(Request $request)
    {
        return redirect()->back()->with('error', 'Please use the per-session feedback form.');
    }

    /**
     * Show student list by status
     */
    public function studentsByStatus($status)
    {
        $instructor = Auth::guard('instructor')->user();

        // Map status to query parameters
        $statusMap = [
            'theory-pending'      => ['theory_status' => 'pending'],
            'theory-in-progress'  => ['theory_status' => 'in_progress'],
            'theory-completed'    => ['theory_status' => 'completed'],
            'practical-pending'   => ['theory_status' => 'completed', 'practical_status' => 'pending'],
            'practical-assigned'  => ['practical_status' => 'assigned'],
            'practical-completed' => ['practical_status' => 'completed'],
            'not-appeared'        => ['practical_status' => 'not_appeared'],
            'failed'              => ['practical_status' => 'failed'],
        ];

        if (!isset($statusMap[$status])) {
            abort(404);
        }

        $query = Student::where('instructor_id', $instructor->id);

        foreach ($statusMap[$status] as $field => $value) {
            $query->where($field, $value);
        }

        $students = $query->with('course')->paginate(15);

        return view('instructor.students-by-status', [
            'instructor' => $instructor,
            'status' => $status,
            'statusTitle' => str_replace('-', ' ', ucfirst($status)),
            'students' => $students,
        ]);
    }
    /**
     * View student details
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\View\View
     */
    public function viewStudent(Student $student)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $practicalSessions = PracticalSession::where('student_id', $student->id)
            ->orderBy('session_number')
            ->get();

        // Theory schedules explicitly assigned to this student (via pivot) — same
        // approach as practical, so only the student's own sessions are listed.
        $theorySchedules = CourseSchedule::whereIn('id', function ($q) use ($student) {
                $q->select('course_schedule_id')
                  ->from('course_schedule_student')
                  ->where('student_id', $student->id);
            })
            ->where('session_type', 'theory')
            ->orderBy('date')
            ->get();

        // Practical schedules specifically assigned to this student (via pivot)
        $practicalSchedules = CourseSchedule::whereIn('id', function ($q) use ($student) {
                $q->select('course_schedule_id')
                  ->from('course_schedule_student')
                  ->where('student_id', $student->id);
            })
            ->where('session_type', 'practical')
            ->orderBy('date')
            ->get();

        // Session attendance records (to show which classes have been marked complete)
        $sessionAttendances = SessionAttendance::where('student_id', $student->id)
            ->get()
            ->keyBy('course_schedule_id');

        // All pivot-assigned future sessions not yet attended — mirrors the student's
        // "Upcoming Classes" tab exactly (no session_type filter).
        $attendedIds = $sessionAttendances->keys()->toArray();
        $upcomingAssigned = CourseSchedule::whereIn('id', function ($q) use ($student) {
                $q->select('course_schedule_id')
                  ->from('course_schedule_student')
                  ->where('student_id', $student->id);
            })
            ->where('date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->whereNotIn('id', $attendedIds)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Existing evaluation (if any)
        $evaluation = InstructorEvaluation::where('student_id', $student->id)
            ->where('course_id', $student->course_id)
            ->first();

        // Already-assigned schedule IDs for this student (query pivot directly to avoid dot-notation issues)
        $assignedScheduleIds = DB::table('course_schedule_student')
            ->where('student_id', $student->id)
            ->pluck('course_schedule_id');

        // Class count progress
        $course = $student->course; // can be null if course was deleted
        $completedTheoryClasses = SessionAttendance::where('student_id', $student->id)
            ->where('class_type', 'theory')
            ->where('status', 'completed')
            ->count();
        $completedPracticalClasses = SessionAttendance::where('student_id', $student->id)
            ->where('class_type', 'practical')
            ->where('status', 'completed')
            ->count();
        $totalTheoryRequired    = $course ? ($course->total_theory_classes    ?? 0) : 0;
        $totalPracticalRequired = $course ? ($course->total_practical_classes ?? 0) : 0;

        // Count upcoming assigned but not yet attended (so we don't over-assign)
        $attendedScheduleIds      = $sessionAttendances->keys()->toArray();
        $pendingAssignedTheory    = DB::table('course_schedules')
            ->join('course_schedule_student', 'course_schedules.id', '=', 'course_schedule_student.course_schedule_id')
            ->where('course_schedule_student.student_id', $student->id)
            ->where('course_schedules.session_type', 'theory')
            ->whereNotIn('course_schedules.id', $attendedScheduleIds)
            ->count();
        $pendingAssignedPractical = DB::table('course_schedules')
            ->join('course_schedule_student', 'course_schedules.id', '=', 'course_schedule_student.course_schedule_id')
            ->where('course_schedule_student.student_id', $student->id)
            ->where('course_schedules.session_type', 'practical')
            ->whereNotIn('course_schedules.id', $attendedScheduleIds)
            ->count();

        $classProgress = [
            'theory' => [
                'completed'           => $completedTheoryClasses,
                'required'            => $totalTheoryRequired,
                'pending_assigned'    => $pendingAssignedTheory,
                'remaining_to_assign' => max(0, $totalTheoryRequired - $completedTheoryClasses - $pendingAssignedTheory),
            ],
            'practical' => [
                'completed'           => $completedPracticalClasses,
                'required'            => $totalPracticalRequired,
                'pending_assigned'    => $pendingAssignedPractical,
                'remaining_to_assign' => max(0, $totalPracticalRequired - $completedPracticalClasses - $pendingAssignedPractical),
            ],
        ];

        // Future schedules available to assign (not yet assigned to student)
        // Only include session types where the student still needs more classes.
        // No instructor_id filter — schedules are created at course level by admin.
        $availableQuery = CourseSchedule::where('course_id', $student->course_id)
            ->where('date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->whereNotIn('id', $assignedScheduleIds);

        if ($totalTheoryRequired > 0 || $totalPracticalRequired > 0) {
            $typesToShow = [];
            if ($classProgress['theory']['remaining_to_assign'] > 0)    $typesToShow[] = 'theory';
            if ($classProgress['practical']['remaining_to_assign'] > 0) $typesToShow[] = 'practical';
            if (!empty($typesToShow)) {
                $availableQuery->whereIn('session_type', $typesToShow);
            } else {
                $availableQuery->whereRaw('1 = 0'); // all slots filled — nothing to assign
            }
        }

        $availableSchedules = $availableQuery->orderBy('date')->orderBy('start_time')->get();

        // Lesson plans for the student's course, grouped by type
        $theoryLessonPlans    = $course ? $course->theoryLessonPlans()->get()    : collect();
        $practicalLessonPlans = $course ? $course->practicalLessonPlans()->get() : collect();

        // Pending reschedule requests from this student
        $rescheduleRequests = ScheduleRescheduleRequest::where('student_id', $student->id)
            ->with('schedule')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('instructor.student-view', [
            'student'              => $student,
            'instructor'           => $instructor,
            'practicalSessions'    => $practicalSessions,
            'theorySchedules'      => $theorySchedules,
            'practicalSchedules'   => $practicalSchedules,
            'upcomingAssigned'     => $upcomingAssigned,
            'sessionAttendances'   => $sessionAttendances,
            'evaluation'           => $evaluation,
            'availableSchedules'   => $availableSchedules,
            'assignedScheduleIds'  => $assignedScheduleIds,
            'classProgress'           => $classProgress,
            'rescheduleRequests'      => $rescheduleRequests,
            'theoryLessonPlans'       => $theoryLessonPlans,
            'practicalLessonPlans'    => $practicalLessonPlans,
        ]);
    }

    /**
     * Instructor assigns upcoming schedules to a specific student.
     */
    public function assignSchedulesToStudent(Request $request, Student $student)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'schedule_ids'   => 'required|array|min:1',
            'schedule_ids.*' => 'exists:course_schedules,id',
        ]);

        $validIds = CourseSchedule::whereIn('id', $request->schedule_ids)
            ->where('course_id', $student->course_id)
            ->pluck('id'); // No instructor_id filter — schedules are course-level

        foreach ($validIds as $scheduleId) {
            $student->assignedSchedules()->syncWithoutDetaching([
                $scheduleId => ['assigned_by' => $instructor->id],
            ]);
        }

        return redirect()->back()->with('success', count($validIds) . ' schedule(s) assigned to ' . $student->first_name . ' successfully.');
    }

    /**
     * Instructor logs a completed theory/practical session directly for a student.
     * Creates a CourseSchedule record on-the-fly and an attendance record.
     */
    public function logSession(Request $request, Student $student)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'session_type' => 'required|in:theory,practical',
            'session_date' => 'required|date',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i',
            'notes'        => 'nullable|string|max:1000',
        ]);

        // Create a schedule record for this session
        $schedule = CourseSchedule::create([
            'course_id'    => $student->course_id,
            'instructor_id'=> $instructor->id,
            'date'         => $request->session_date,
            'start_time'   => $request->start_time . ':00',
            'end_time'     => $request->end_time   . ':00',
            'session_type' => $request->session_type,
            'max_students' => 1,
            'is_active'    => true,
        ]);

        // Compute class_order: count prior completed sessions of this type
        $completedCount = SessionAttendance::where('student_id', $student->id)
            ->where('class_type', $request->session_type)
            ->where('status', 'completed')
            ->count();

        $classOrder = $completedCount + 1;

        // Create the attendance record
        SessionAttendance::create([
            'student_id'         => $student->id,
            'course_schedule_id' => $schedule->id,
            'is_present'         => true,
            'status'             => 'completed',
            'class_type'         => $request->session_type,
            'class_order'        => $classOrder,
            'completed_at'       => now(),
            'notes'              => $request->notes,
        ]);

        // Also link student to schedule in pivot
        $student->assignedSchedules()->syncWithoutDetaching([
            $schedule->id => ['assigned_by' => $instructor->id],
        ]);

        // Auto-update student status
        $studentCourse = $student->course;
        if ($request->session_type === 'theory') {
            $totalDone     = $completedCount + 1;
            $totalRequired = $studentCourse ? ($studentCourse->total_theory_classes ?? 0) : 0;
            if ($totalRequired > 0 && $totalDone >= $totalRequired) {
                if ($student->theory_status !== 'completed') {
                    $student->update(['theory_status' => 'completed', 'theory_completion_date' => now()]);
                }
            } elseif ($student->theory_status === 'pending') {
                $student->update(['theory_status' => 'in_progress']);
            }
        } elseif ($request->session_type === 'practical') {
            $totalDone     = $completedCount + 1;
            $totalRequired = $studentCourse ? ($studentCourse->total_practical_classes ?? 0) : 0;
            if ($totalRequired > 0 && $totalDone >= $totalRequired) {
                if ($student->practical_status !== 'completed') {
                    $student->update(['practical_status' => 'completed', 'practical_completion_date' => now()]);
                }
            }
        }

        return redirect()->back()->with('success',
            ucfirst($request->session_type) . ' session #' . $classOrder . ' logged for ' . $student->first_name . '.'
        );
    }

    /**
     * Instructor schedules a future session for a specific student.
     * Creates a CourseSchedule record and assigns the student — does NOT mark as complete.
     * The session will appear in the student's Upcoming Classes tab.
     */
    public function createSessionForStudent(Request $request, Student $student)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'session_type' => 'required|in:theory,practical',
            'session_date' => 'required|date|after_or_equal:today',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i',
        ]);

        $schedule = CourseSchedule::create([
            'course_id'     => $student->course_id,
            'instructor_id' => $instructor->id,
            'date'          => $request->session_date,
            'start_time'    => $request->start_time . ':00',
            'end_time'      => $request->end_time   . ':00',
            'session_type'  => $request->session_type,
            'max_students'  => 1,
            'is_active'     => true,
        ]);

        $student->assignedSchedules()->syncWithoutDetaching([
            $schedule->id => ['assigned_by' => $instructor->id],
        ]);

        return redirect()->back()->with('success',
            ucfirst($request->session_type) . ' session scheduled for ' . $student->first_name .
            ' on ' . Carbon::parse($request->session_date)->format('M d, Y') . '.'
        );
    }

    /**
     * Instructor handles (approves or rejects) a reschedule request from a student.
     */
    public function handleRescheduleRequest(Request $request, ScheduleRescheduleRequest $rescheduleRequest)
    {
        $instructor = Auth::guard('instructor')->user();

        if ($rescheduleRequest->student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'action'          => 'required|in:approve,reject',
            'instructor_note' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $schedule = $rescheduleRequest->schedule;

            // If multiple students share this schedule, create a separate one for this student
            $sharedCount = DB::table('course_schedule_student')
                ->where('course_schedule_id', $schedule->id)
                ->count();

            if ($sharedCount > 1) {
                $newSchedule = CourseSchedule::create([
                    'course_id'     => $schedule->course_id,
                    'instructor_id' => $instructor->id,
                    'date'          => $rescheduleRequest->requested_date,
                    'start_time'    => $rescheduleRequest->requested_start_time,
                    'end_time'      => $rescheduleRequest->requested_end_time ?? $schedule->end_time,
                    'session_type'  => $schedule->session_type,
                    'max_students'  => 1,
                    'is_active'     => true,
                ]);

                $student = $rescheduleRequest->student;
                $student->assignedSchedules()->detach($schedule->id);
                $student->assignedSchedules()->syncWithoutDetaching([
                    $newSchedule->id => ['assigned_by' => $instructor->id],
                ]);
            } else {
                $schedule->update([
                    'date'       => $rescheduleRequest->requested_date,
                    'start_time' => $rescheduleRequest->requested_start_time,
                    'end_time'   => $rescheduleRequest->requested_end_time ?? $schedule->end_time,
                ]);
            }

            $rescheduleRequest->update([
                'status'          => 'approved',
                'instructor_note' => $request->instructor_note,
                'handled_at'      => now(),
            ]);

            return redirect()->back()->with('success',
                'Reschedule request approved. Session updated to ' .
                Carbon::parse($rescheduleRequest->requested_date)->format('M d, Y') . '.'
            );
        }

        $rescheduleRequest->update([
            'status'          => 'rejected',
            'instructor_note' => $request->instructor_note,
            'handled_at'      => now(),
        ]);

        return redirect()->back()->with('success', 'Reschedule request has been rejected.');
    }
}
