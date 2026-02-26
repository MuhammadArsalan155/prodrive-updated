<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use App\Models\Student;
use App\Notifications\ProgressReportCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProgressReportController extends Controller
{
    public function index()
    {
        try {
            $student = Auth::guard('student')->user();

            // Fetch progress reports with related data
            $progressReports = ProgressReport::with(['instructor', 'course'])
                ->where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('progress-reports.index', compact('progressReports'));
        } catch (\Exception $e) {
            Log::error('Error fetching progress reports: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch progress reports');
        }
    }

    /**
     * Show details of a specific progress report
     */
    public function show($reportId)
    {
        try {
            $student = Auth::guard('student')->user();

            $report = ProgressReport::with(['instructor', 'course'])
                ->where('student_id', $student->id)
                ->findOrFail($reportId);

            return view('progress-reports.show', compact('report'));
        } catch (\Exception $e) {
            Log::error('Error fetching progress report details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Progress report not found');
        }
    }

    /**
     * Create a new progress report (typically by instructor)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'performance_notes' => 'required|string',
            'areas_of_improvement' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        try {
            // Ensure the instructor is creating the report
            $validatedData['instructor_id'] = Auth::guard('instructor')->user()->id;

            $progressReport = ProgressReport::create($validatedData);

            // Optionally send notification to student
            $student = Student::findOrFail($validatedData['student_id']);
            $student->notify(new ProgressReportCreatedNotification($progressReport));

            return response()->json([
                'success' => true,
                'message' => 'Progress report created successfully',
                'report' => $progressReport
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating progress report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create progress report'
            ], 500);
        }
    }

    /**
     * Update an existing progress report
     */
    public function update(Request $request, $reportId)
    {
        $validatedData = $request->validate([
            'performance_notes' => 'sometimes|required|string',
            'areas_of_improvement' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        try {
            $report = ProgressReport::findOrFail($reportId);

            // Ensure only the original instructor can update
            $this->authorize('update', $report);

            $report->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Progress report updated successfully',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating progress report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update progress report'
            ], 500);
        }
    }

    /**
     * Calculate overall student progress
     */
    public function calculateOverallProgress(Student $student)
    {
        $reports = ProgressReport::where('student_id', $student->id)->get();

        $progressMetrics = [
            'total_reports' => $reports->count(),
            'average_rating' => $reports->avg('rating'),
            'improvement_areas' => $reports->pluck('areas_of_improvement')->filter()->unique()->values()->toArray()
        ];

        return $progressMetrics;
    }
}