<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
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
     * Assign student to practical slot
     */
    public function assignPracticalSlot(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $studentId = $request->input('student_id');

        // Check if the slot is already filled
        $schedule = CourseSchedule::findOrFail($scheduleId);

        if ($schedule->students()->count() > 0) {
            return redirect()->back()->with('error', 'This slot already has a student assigned');
        }

        // Assign student to slot
        $schedule->students()->sync([$studentId]);

        // Update student status
        Student::findOrFail($studentId)->update([
            'practical_status' => 'assigned',
            'practical_schedule_id' => $scheduleId,
        ]);

        return redirect()->back()->with('success', 'Student assigned to practical slot');
    }

    /**
     * Submit practical class feedback
     */
    public function submitPracticalFeedback(Request $request)
    {
        $studentId = $request->input('student_id');
        $scheduleId = $request->input('schedule_id');
        $status = $request->input('status'); // completed, failed, not_appeared
        $feedback = $request->input('feedback');

        // Update student status
        Student::findOrFail($studentId)->update([
            'practical_status' => $status,
            'practical_completion_date' => $status == 'completed' ? now() : null,
        ]);

        // Save feedback
        // ClassFeedback::create([
        //     'student_id' => $studentId,
        //     'schedule_id' => $scheduleId,
        //     'instructor_id' => Auth::guard('instructor')->id(),
        //     'feedback' => $feedback,
        //     'status' => $status,
        // ]);

        return redirect()->back()->with('success', 'Practical class feedback submitted');
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
        // Check if the student belongs to the logged-in instructor
        $instructor = Auth::guard('instructor')->user();

        if ($student->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get feedback history
        //$feedbacks = ClassFeedback::where('student_id', $student->id)->orderBy('created_at', 'desc')->get();

        return view('instructor.student-view', [
            'student' => $student,
            'instructor' => $instructor,
            //'feedbacks' => $feedbacks,
        ]);
    }
}
