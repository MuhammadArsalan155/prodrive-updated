<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\PracticalSession;
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
            'theory-pending' => ['theory_status' => 'pending'],
            'theory-completed' => ['theory_status' => 'completed'],
            'practical-pending' => ['theory_status' => 'completed', 'practical_status' => 'pending'],
            'practical-assigned' => ['practical_status' => 'assigned'],
            'practical-completed' => ['practical_status' => 'completed'],
            'not-appeared' => ['practical_status' => 'not_appeared'],
            'failed' => ['practical_status' => 'failed'],
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

        return view('instructor.student-view', [
            'student'           => $student,
            'instructor'        => $instructor,
            'practicalSessions' => $practicalSessions,
        ]);
    }
}
