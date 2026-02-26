<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Instructor;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagerDashboardController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get basic statistics
        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalInstructors = Instructor::count();
        $completedStudents = Student::where('course_status', 1)->count();

        // Calculate student enrollment trends
        $monthlyEnrollments = $this->getMonthlyEnrollments();

        // Course distribution
        $courseDistribution = $this->getCourseTypeDistribution();

        // Upcoming schedules
        $upcomingSchedules = CourseSchedule::with(['course', 'instructor'])
            ->where('date', '>=', now())
            ->orderBy('date')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = $this->getRecentActivities();

        // Financial metrics
        $financialMetrics = $this->getFinancialMetrics();

        return view('dashboards.manager-dashboard', [
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'totalInstructors' => $totalInstructors,
            'completedStudents' => $completedStudents,
            'monthlyEnrollments' => $monthlyEnrollments,
            'courseDistribution' => $courseDistribution,
            'upcomingSchedules' => $upcomingSchedules,
            'recentActivities' => $recentActivities,
            'financialMetrics' => $financialMetrics,
        ]);
    }

    /**
     * Get monthly student enrollments for the last 6 months
     *
     * @return array
     */
    private function getMonthlyEnrollments()
    {
        return Student::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total_enrollments'))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    Carbon::create(null, $item->month)->format('M') => $item->total_enrollments,
                ];
            })
            ->toArray();
    }

    /**
     * Get course type distribution
     *
     * @return array
     */
    private function getCourseTypeDistribution()
    {
        return Course::select('course_type', DB::raw('COUNT(*) as total'))
            ->groupBy('course_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->course_type => $item->total];
            })
            ->toArray();
    }

    /**
     * Get recent system activities
     *
     * @return array
     */
    private function getRecentActivities()
    {
        // Combine activities from different sources
        $studentActivities = Student::latest()
            ->limit(2)
            ->get()
            ->map(function ($student) {
                return [
                    'type' => 'student',
                    'text' => "New student {$student->first_name} {$student->last_name} enrolled",
                    'time' => $student->created_at->diffForHumans(),
                ];
            });

        $courseActivities = Course::latest()
            ->limit(2)
            ->get()
            ->map(function ($course) {
                return [
                    'type' => 'course',
                    'text' => "New course {$course->course_name} added",
                    'time' => $course->created_at->diffForHumans(),
                ];
            });

        return $studentActivities->merge($courseActivities)->sortByDesc('time')->values()->all();
    }

    /**
     * Get financial metrics
     *
     * @return array
     */
    private function getFinancialMetrics()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return [
            'total_revenue' => Payment::sum('amount'),
            'monthly_revenue' => Payment::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount'),
            'yearly_revenue' => Payment::whereYear('created_at', $currentYear)->sum('amount'),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
        ];
    }

    public function filterDashboardData(Request $request)
    {
        try {
            $filter = $request->input('filter', 'today');

            switch ($filter) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
            }

            $filteredData = [
                'total_students' => Student::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_courses' => Course::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
                'total_enrollments' => Invoice::whereBetween('created_at', [$startDate, $endDate])->count(),
                'enrollments' => $this->getMonthlyEnrollments(),
            ];

            return response()->json($filteredData);
        } catch (\Exception $e) {
            // Log the full error for debugging
            Log::error('Dashboard Filter Error: ' . $e->getMessage());

            return response()->json(
                [
                    'error' => 'An error occurred while processing the request',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
