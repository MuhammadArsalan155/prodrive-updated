<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Course;
// use App\Models\CourseSchedule;
// use App\Models\Instructor;
// use Carbon\Carbon;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class CourseScheduleController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index(Request $request)
//     {
//         // Get the selected month or default to current month
//         $selectedMonth = $request->input('month', now()->format('Y-m'));

//         // Parse the year and month
//         $date = Carbon::createFromFormat('Y-m', $selectedMonth);
//         $year = $date->year;
//         $month = $date->month;

//         $schedules = CourseSchedule::with(['course', 'instructor'])
//             ->whereYear('date', $year)
//             ->whereMonth('date', $month)
//             ->orderBy('date')
//             ->orderBy('start_time')
//             ->get();

//         return view('admin.course-schedule.index', compact('schedules', 'selectedMonth'));
//     }

//     public function storeMultiple(Request $request)
//     {
//         $request->validate([
//             'schedules' => 'required|array|min:1',
//             'schedules.*.course_id' => 'required|exists:courses,id',
//             'schedules.*.instructor_id' => 'required|exists:instructors,id',
//             'schedules.*.date' => 'required|date',
//             'schedules.*.start_time' => 'required|date_format:H:i',
//             'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
//             'schedules.*.session_type' => 'required|in:theory,practical,hybrid',
//             'schedules.*.max_students' => 'required|integer|min:1',
//             'schedules.*.is_active' => 'boolean',
//         ]);

//         $schedules = $request->input('schedules');

//         $createdSchedules = [];
//         $errors = [];

//         DB::beginTransaction();

//         try {
//             foreach ($schedules as $index => $scheduleData) {
//                 // Set default value for is_active if not provided
//                 $scheduleData['is_active'] = isset($scheduleData['is_active']) ? 1 : 0;

//                 // Check for schedule conflicts
//                 $hasConflict = CourseSchedule::where('instructor_id', $scheduleData['instructor_id'])
//                     ->where('date', $scheduleData['date'])
//                     ->where(function ($query) use ($scheduleData) {
//                         $query
//                             ->whereBetween('start_time', [$scheduleData['start_time'], $scheduleData['end_time']])
//                             ->orWhereBetween('end_time', [$scheduleData['start_time'], $scheduleData['end_time']])
//                             ->orWhere(function ($q) use ($scheduleData) {
//                                 $q->where('start_time', '<=', $scheduleData['start_time'])->where('end_time', '>=', $scheduleData['end_time']);
//                             });
//                     })
//                     ->exists();

//                 if ($hasConflict) {
//                     $errors[] = 'Schedule #' . ($index + 1) . ': Time conflict with existing schedule for this instructor.';
//                     continue;
//                 }

//                 $schedule = CourseSchedule::create($scheduleData);
//                 $createdSchedules[] = $schedule;
//             }

//             if (!empty($errors)) {
//                 DB::rollBack();
//                 return redirect()
//                     ->back()
//                     ->withInput()
//                     ->withErrors(['conflicts' => $errors]);
//             }

//             DB::commit();

//             $successMessage = count($createdSchedules) . ' schedule(s) created successfully.';
//             return redirect()->route('course-schedules.index')->with('success', $successMessage);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Error creating multiple schedules: ' . $e->getMessage());

//             return redirect()
//                 ->back()
//                 ->withInput()
//                 ->withErrors(['error' => 'An error occurred while creating schedules. Please try again.']);
//         }
//     }

//     /**
//      * Bulk toggle status for multiple schedules.
//      */
//     public function bulkToggleStatus(Request $request)
//     {
//         $request->validate([
//             'ids' => 'required|array|min:1',
//             'ids.*' => 'exists:course_schedules,id',
//             'status' => 'required|boolean',
//         ]);

//         $scheduleIds = $request->input('ids');
//         $status = $request->input('status');

//         try {
//             $updatedCount = CourseSchedule::whereIn('id', $scheduleIds)->update(['is_active' => $status]);

//             $statusText = $status ? 'activated' : 'deactivated';
//             return redirect()
//                 ->back()
//                 ->with('success', $updatedCount . ' schedule(s) ' . $statusText . ' successfully.');
//         } catch (\Exception $e) {
//             Log::error('Error bulk toggling schedule status: ' . $e->getMessage());

//             return redirect()
//                 ->back()
//                 ->withErrors(['error' => 'An error occurred while updating schedule status. Please try again.']);
//         }
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         $courses = Course::where('is_active', true)->get();
//         $instructors = Instructor::where('is_active', true)->get();

//         return view('admin.course-schedule.create', compact('courses', 'instructors'));
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'course_id' => 'required|exists:courses,id',
//             'instructor_id' => 'required|exists:instructors,id',
//             'date' => 'required|date',
//             'start_time' => 'required|date_format:H:i',
//             'end_time' => 'required|date_format:H:i|after:start_time',
//             'session_type' => 'required|in:theory,practical,hybrid',
//             'max_students' => 'required|integer|min:1',
//             'is_active' => 'boolean',
//         ]);

//         // Check for schedule conflicts
//         $hasConflict = CourseSchedule::where('instructor_id', $validated['instructor_id'])
//             ->where('date', $validated['date'])
//             ->where(function ($query) use ($validated) {
//                 $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
//             })
//             ->exists();

//         if ($hasConflict) {
//             return redirect()
//                 ->back()
//                 ->withInput()
//                 ->withErrors(['time_conflict' => 'The instructor already has a schedule during this time.']);
//         }

//         $schedule = CourseSchedule::create($validated);

//         return redirect()->route('course-schedules.index')->with('success', 'Course schedule created successfully.');
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(CourseSchedule $courseSchedule)
//     {
//         return view('course_schedules.show', compact('courseSchedule'));
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(CourseSchedule $courseSchedule)
//     {
//         $courses = Course::where('is_active', true)->get();
//         $instructors = Instructor::where('is_active', true)->get();

//         return view('admin.course-schedule.edit', compact('courseSchedule', 'courses', 'instructors'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, CourseSchedule $courseSchedule)
//     {
//         $validated = $request->validate([
//             'course_id' => 'required|exists:courses,id',
//             'instructor_id' => 'required|exists:instructors,id',
//             'date' => 'required|date',
//             'start_time' => 'required|date_format:H:i',
//             'end_time' => 'required|date_format:H:i|after:start_time',
//             'session_type' => 'required|in:theory,practical,hybrid',
//             'max_students' => 'required|integer|min:1',
//             'is_active' => 'boolean',
//         ]);

//         // Check for schedule conflicts, excluding current schedule
//         $hasConflict = CourseSchedule::where('instructor_id', $validated['instructor_id'])
//             ->where('date', $validated['date'])
//             ->where('id', '!=', $courseSchedule->id)
//             ->where(function ($query) use ($validated) {
//                 $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
//             })
//             ->exists();

//         if ($hasConflict) {
//             return redirect()
//                 ->back()
//                 ->withInput()
//                 ->withErrors(['time_conflict' => 'The instructor already has a schedule during this time.']);
//         }

//         $courseSchedule->update($validated);

//         return redirect()->route('course-schedules.index')->with('success', 'Course schedule updated successfully.');
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(CourseSchedule $courseSchedule)
//     {
//         $courseSchedule->delete();

//         return redirect()->route('course-schedules.index')->with('success', 'Course schedule deleted successfully.');
//     }

//     /**
//      * Copy schedule to next month.
//      */
//     public function copyMonth(Request $request)
//     {
//         $validated = $request->validate([
//             'month' => 'required|date_format:Y-m',
//         ]);

//         [$year, $month] = explode('-', $validated['month']);

//         try {
//             CourseSchedule::copyScheduleToNextMonth($year, $month);
//             return redirect()->back()->with('success', 'Schedule copied to next month successfully.');
//         } catch (\Exception $e) {
//             return redirect()
//                 ->back()
//                 ->with('error', 'Failed to copy schedule: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Toggle the status of the schedule.
//      */
//     public function toggleStatus(CourseSchedule $courseSchedule)
//     {
//         $courseSchedule->update([
//             'is_active' => !$courseSchedule->is_active,
//         ]);

//         return redirect()->back()->with('success', 'Schedule status updated successfully.');
//     }
// }




namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseScheduleController extends Controller
{
    /**
     * Get Oklahoma timezone
     */
    private function getOklahomaTimezone()
    {
        return 'America/Chicago'; // Oklahoma follows Central Time
    }

    /**
     * Convert time to Oklahoma timezone
     */
    private function toOklahomaTime($dateTime = null)
    {
        if ($dateTime === null) {
            return Carbon::now($this->getOklahomaTimezone());
        }

        return Carbon::parse($dateTime)->setTimezone($this->getOklahomaTimezone());
    }

    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    // Get the selected month or default to current month in Oklahoma timezone
    $oklahomaTime = $this->toOklahomaTime();
    $selectedMonth = $request->input('month', $oklahomaTime->format('Y-m'));

    // Parse year and month correctly
    [$year, $month] = explode('-', $selectedMonth);
    $year = (int) $year;
    $month = (int) $month;

    // Simple and reliable query using whereYear and whereMonth
    $schedules = CourseSchedule::with(['course', 'instructor'])
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();

    // Debug information (remove this in production)
    if ($request->has('debug')) {
        Log::info('Schedule Filter Debug', [
            'selected_month' => $selectedMonth,
            'year' => $year,
            'month' => $month,
            'total_schedules_found' => $schedules->count(),
            'schedule_dates' => $schedules->pluck('date')->toArray(),
            'raw_query' => CourseSchedule::whereYear('date', $year)->whereMonth('date', $month)->toSql(),
            'query_bindings' => [$year, $month]
        ]);

        // Additional check: get all September 2025 records directly
        $directQuery = DB::select("SELECT * FROM course_schedules WHERE YEAR(date) = ? AND MONTH(date) = ?", [$year, $month]);
        Log::info('Direct SQL Query Result', [
            'count' => count($directQuery),
            'sample_dates' => array_slice(array_column($directQuery, 'date'), 0, 5)
        ]);
    }

    return view('admin.course-schedule.index', compact('schedules', 'selectedMonth'));
}

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array|min:1',
            'schedules.*.course_id' => 'required|exists:courses,id',
            'schedules.*.instructor_id' => 'required|exists:instructors,id',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.session_type' => 'required|in:theory,practical,hybrid',
            'schedules.*.max_students' => 'required|integer|min:1',
            'schedules.*.is_active' => 'boolean',
        ]);

        $schedules = $request->input('schedules');

        $createdSchedules = [];
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($schedules as $index => $scheduleData) {
                // Set default value for is_active if not provided
                $scheduleData['is_active'] = isset($scheduleData['is_active']) ? 1 : 0;

                // Convert date to Oklahoma timezone for consistency
                $scheduleDate = Carbon::parse($scheduleData['date'])->setTimezone($this->getOklahomaTimezone());
                $scheduleData['date'] = $scheduleDate->format('Y-m-d');

                // Validate that the date is not in the past (Oklahoma time)
                $oklahomaToday = $this->toOklahomaTime()->startOfDay();
                if ($scheduleDate->startOfDay()->lt($oklahomaToday)) {
                    $errors[] = "Schedule #" . ($index + 1) . ": Date cannot be in the past (Oklahoma time).";
                    continue;
                }

                // Check for schedule conflicts
                $hasConflict = CourseSchedule::where('instructor_id', $scheduleData['instructor_id'])
                    ->where('date', $scheduleData['date'])
                    ->where(function ($query) use ($scheduleData) {
                        $query->whereBetween('start_time', [$scheduleData['start_time'], $scheduleData['end_time']])
                              ->orWhereBetween('end_time', [$scheduleData['start_time'], $scheduleData['end_time']])
                              ->orWhere(function ($q) use ($scheduleData) {
                                  $q->where('start_time', '<=', $scheduleData['start_time'])
                                    ->where('end_time', '>=', $scheduleData['end_time']);
                              });
                    })
                    ->exists();

                if ($hasConflict) {
                    $errors[] = "Schedule #" . ($index + 1) . ": Time conflict with existing schedule for this instructor.";
                    continue;
                }

                // Add Oklahoma timezone metadata
                $scheduleData['created_at'] = $this->toOklahomaTime();
                $scheduleData['updated_at'] = $this->toOklahomaTime();

                $schedule = CourseSchedule::create($scheduleData);
                $createdSchedules[] = $schedule;
            }

            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['conflicts' => $errors]);
            }

            DB::commit();

            $successMessage = count($createdSchedules) . ' schedule(s) created successfully (Oklahoma Time).';
            return redirect()->route('course-schedules.index')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating multiple schedules: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating schedules. Please try again.']);
        }
    }

    /**
     * Bulk toggle status for multiple schedules.
     */
    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:course_schedules,id',
            'status' => 'required|boolean'
        ]);

        $scheduleIds = $request->input('ids');
        $status = $request->input('status');

        try {
            $updatedCount = CourseSchedule::whereIn('id', $scheduleIds)
                ->update([
                    'is_active' => $status,
                    'updated_at' => $this->toOklahomaTime()
                ]);

            $statusText = $status ? 'activated' : 'deactivated';
            return redirect()->back()
                ->with('success', $updatedCount . ' schedule(s) ' . $statusText . ' successfully.');

        } catch (\Exception $e) {
            Log::error('Error bulk toggling schedule status: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating schedule status. Please try again.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        $instructors = Instructor::where('is_active', true)->get();

        // Get current Oklahoma date for default date selection
        $oklahomaDate = $this->toOklahomaTime()->format('Y-m-d');

        return view('admin.course-schedule.create', compact('courses', 'instructors', 'oklahomaDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_type' => 'required|in:theory,practical,hybrid',
            'max_students' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Convert date to Oklahoma timezone for consistency
        $scheduleDate = Carbon::parse($validated['date'])->setTimezone($this->getOklahomaTimezone());
        $validated['date'] = $scheduleDate->format('Y-m-d');

        // Validate that the date is not in the past (Oklahoma time)
        $oklahomaToday = $this->toOklahomaTime()->startOfDay();
        if ($scheduleDate->startOfDay()->lt($oklahomaToday)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['date' => 'Date cannot be in the past (Oklahoma time).']);
        }

        // Check for schedule conflicts
        $hasConflict = CourseSchedule::where('instructor_id', $validated['instructor_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($hasConflict) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['time_conflict' => 'The instructor already has a schedule during this time.']);
        }

        // Add Oklahoma timezone metadata
        $validated['created_at'] = $this->toOklahomaTime();
        $validated['updated_at'] = $this->toOklahomaTime();

        $schedule = CourseSchedule::create($validated);

        return redirect()->route('course-schedules.index')->with('success', 'Course schedule created successfully (Oklahoma Time).');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSchedule $courseSchedule)
    {
        return view('course_schedules.show', compact('courseSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseSchedule $courseSchedule)
    {
        $courses = Course::where('is_active', true)->get();
        $instructors = Instructor::where('is_active', true)->get();

        return view('admin.course-schedule.edit', compact('courseSchedule', 'courses', 'instructors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseSchedule $courseSchedule)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_type' => 'required|in:theory,practical,hybrid',
            'max_students' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Convert date to Oklahoma timezone for consistency
        $scheduleDate = Carbon::parse($validated['date'])->setTimezone($this->getOklahomaTimezone());
        $validated['date'] = $scheduleDate->format('Y-m-d');

        // Validate that the date is not in the past (Oklahoma time) unless it's the same date as existing
        $oklahomaToday = $this->toOklahomaTime()->startOfDay();
        $existingDate = Carbon::parse($courseSchedule->date)->startOfDay();

        if ($scheduleDate->startOfDay()->lt($oklahomaToday) && !$scheduleDate->startOfDay()->eq($existingDate)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['date' => 'Date cannot be changed to a past date (Oklahoma time).']);
        }

        // Check for schedule conflicts, excluding current schedule
        $hasConflict = CourseSchedule::where('instructor_id', $validated['instructor_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $courseSchedule->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($hasConflict) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['time_conflict' => 'The instructor already has a schedule during this time.']);
        }

        // Add Oklahoma timezone metadata
        $validated['updated_at'] = $this->toOklahomaTime();

        $courseSchedule->update($validated);

        return redirect()->route('course-schedules.index')->with('success', 'Course schedule updated successfully (Oklahoma Time).');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSchedule $courseSchedule)
    {
        $courseSchedule->delete();

        return redirect()->route('course-schedules.index')->with('success', 'Course schedule deleted successfully.');
    }

    /**
     * Copy schedule to next month.
     */
    public function copyMonth(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        [$year, $month] = explode('-', $validated['month']);

        try {
            // Get schedules for the specified month using the same logic as index
            $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

            $schedules = CourseSchedule::whereBetween('date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->get();

            if ($schedules->isEmpty()) {
                return redirect()->back()->with('error', 'No schedules found for the selected month.');
            }

            $nextMonth = Carbon::create($year, $month, 1)->addMonth();
            $copiedCount = 0;
            $skippedCount = 0;

            DB::beginTransaction();

            foreach ($schedules as $schedule) {
                // Calculate the corresponding date in next month
                $originalDate = Carbon::parse($schedule->date);
                $newDate = Carbon::create(
                    $nextMonth->year,
                    $nextMonth->month,
                    $originalDate->day
                );

                // Skip if the day doesn't exist in the next month (e.g., Jan 31 -> Feb 31)
                if ($newDate->month != $nextMonth->month) {
                    $skippedCount++;
                    continue;
                }

                // Ensure we're working in Oklahoma timezone
                $newDate = $newDate->setTimezone($this->getOklahomaTimezone());

                // Check if schedule already exists for this date/time/instructor
                $exists = CourseSchedule::where('instructor_id', $schedule->instructor_id)
                    ->where('date', $newDate->format('Y-m-d'))
                    ->where('start_time', $schedule->start_time)
                    ->where('end_time', $schedule->end_time)
                    ->exists();

                if (!$exists) {
                    CourseSchedule::create([
                        'course_id' => $schedule->course_id,
                        'instructor_id' => $schedule->instructor_id,
                        'date' => $newDate->format('Y-m-d'),
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'session_type' => $schedule->session_type,
                        'max_students' => $schedule->max_students,
                        'is_active' => $schedule->is_active,
                        'created_at' => $this->toOklahomaTime(),
                        'updated_at' => $this->toOklahomaTime(),
                    ]);
                    $copiedCount++;
                } else {
                    $skippedCount++;
                }
            }

            DB::commit();

            $message = "{$copiedCount} schedule(s) copied to next month successfully (Oklahoma Time).";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} schedule(s) were skipped (already exist or invalid dates).";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error copying schedule to next month: ' . $e->getMessage(), [
                'source_month' => $validated['month'],
                'error' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Failed to copy schedule: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the schedule.
     */
    public function toggleStatus(CourseSchedule $courseSchedule)
    {
        $courseSchedule->update([
            'is_active' => !$courseSchedule->is_active,
            'updated_at' => $this->toOklahomaTime(),
        ]);

        $status = $courseSchedule->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Schedule {$status} successfully (Oklahoma Time).");
    }

    /**
     * Get current Oklahoma time for frontend
     */
    public function getCurrentOklahomaTime()
    {
        return response()->json([
            'current_time' => $this->toOklahomaTime()->toISOString(),
            'current_date' => $this->toOklahomaTime()->format('Y-m-d'),
            'timezone' => $this->getOklahomaTimezone(),
            'timezone_name' => 'Central Time (Oklahoma)'
        ]);
    }

    /**
     * Debug method to check schedules for a specific month
     */
    public function debugSchedules(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $month = $request->input('month', now()->setTimezone('America/Chicago')->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        // Get all schedules for debugging
        $allSchedules = CourseSchedule::with(['course', 'instructor'])
            ->selectRaw('*, DATE_FORMAT(date, "%Y-%m") as year_month')
            ->get();

        // Filter by different methods
        $methodResults = [
            'whereBetween' => CourseSchedule::with(['course', 'instructor'])
                ->whereBetween('date', [
                    Carbon::create($year, $monthNum, 1)->format('Y-m-d'),
                    Carbon::create($year, $monthNum, 1)->endOfMonth()->format('Y-m-d')
                ])
                ->get(),

            'whereYear_whereMonth' => CourseSchedule::with(['course', 'instructor'])
                ->whereYear('date', $year)
                ->whereMonth('date', $monthNum)
                ->get(),

            'like_pattern' => CourseSchedule::with(['course', 'instructor'])
                ->where('date', 'like', $month . '%')
                ->get()
        ];

        return response()->json([
            'debug_info' => [
                'requested_month' => $month,
                'year' => $year,
                'month_number' => $monthNum,
                'current_oklahoma_time' => $this->toOklahomaTime()->format('Y-m-d H:i:s T'),
            ],
            'total_schedules_in_db' => $allSchedules->count(),
            'all_schedule_dates' => $allSchedules->pluck('date')->unique()->sort()->values(),
            'method_results' => [
                'whereBetween_count' => $methodResults['whereBetween']->count(),
                'whereYear_whereMonth_count' => $methodResults['whereYear_whereMonth']->count(),
                'like_pattern_count' => $methodResults['like_pattern']->count(),
            ],
            'schedules_by_month' => $allSchedules->groupBy('year_month')->map->count(),
            'sample_schedules' => $allSchedules->take(5)->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'date' => $schedule->date,
                    'course' => $schedule->course->course_name ?? 'N/A',
                    'instructor' => $schedule->instructor->instructor_name ?? 'N/A',
                ];
            })
        ]);
    }
}
