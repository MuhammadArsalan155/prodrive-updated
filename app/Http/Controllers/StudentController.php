<?php

namespace App\Http\Controllers;

use App\Mail\ParentCredentials;
use App\Mail\StudentCredentials;
use App\Models\Course;
use App\Models\CourseHour;
use App\Models\CourseSchedule;
use App\Models\Installment;
use App\Models\Instructor;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentParent;
use App\Notifications\InstallmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
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

    public function addstudent()
    {
        $courses = Course::where('is_active', true)->get();
        return view('student.add', compact('courses'));
    }

    public function getCourseInstructors($courseId)
    {
        try {
            $instructors = Instructor::whereHas('schedules', function ($query) use ($courseId) {
                $query
                    ->where('course_id', $courseId)
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->where('is_active', true);
            })
                ->where('is_active', true)
                ->select('id', 'instructor_name')
                ->get();

            return response()->json($instructors);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Failed to fetch instructors',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getCourses()
    {
        try {
            $courses = Course::where('is_active', true)->select('id', 'course_name', 'course_price', 'course_type', 'description', 'theory_hours', 'practical_hours')->get();

            // Add registration fee information to each course
            $coursesWithFee = $courses->map(function ($course) {
                $courseArray = $course->toArray();
                $courseArray['registration_fee'] = 10.0;
                $courseArray['total_price'] = $course->course_price + 10.0;
                return $courseArray;
            });

            return response()->json($coursesWithFee);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Failed to fetch courses',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // public function getAvailableSchedules($courseId, $instructorId)
    // {
    //     $schedules = CourseSchedule::where('course_id', $courseId)->where('instructor_id', $instructorId)->where('date', '>=', Carbon::today('America/New_York'))->where('is_active', true)->orderBy('date')->orderBy('start_time')->get();

    //     $schedules = $schedules->map(function ($schedule) {
    //         $scheduleDate = Carbon::parse($schedule->date)->timezone('America/New_York');
    //         $startTime = Carbon::parse($schedule->start_time)->timezone('America/New_York');
    //         $endTime = Carbon::parse($schedule->end_time)->timezone('America/New_York');

    //         $availableSlots = $schedule->max_students - Student::where('course_id', $schedule->course_id)->count();

    //         return [
    //             'id' => $schedule->id,
    //             'date' => $scheduleDate->format('Y-m-d'),
    //             'start_time' => $startTime->format('h:i A'),
    //             'end_time' => $endTime->format('h:i A'),
    //             'session_type' => $schedule->session_type,
    //             'available_slots' => max($availableSlots, 0),
    //         ];
    //     });

    //     Log::info('Available Schedules for Course ' . $courseId . ' and Instructor ' . $instructorId . ': ', $schedules->toArray());

    //     return response()->json($schedules);
    // }

    public function viewstudent()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(10);
        return view('student.view', compact('students'));
    }

    public function viewSingleStudent(Student $student)
    {
        $student->load(['course.installmentPlan', 'invoices.payments', 'invoices.installments', 'parent']);

        $pendingInstallments = Installment::whereHas('invoice', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->get();

        $cashPaymentMethods = PaymentMethod::where('is_active', true)->where('code', 'like', '%cash%')->get();

        return view('student.viewstudent', compact('student', 'cashPaymentMethods', 'pendingInstallments'));
    }

    public function add_student(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'student_id' => ['required', 'unique:students,student_id', 'regex:/^PDA-\d{4}-\d+$/'],
                'first_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\s\'-]+$/'],
                'email' => ['required', 'email', 'unique:students,email', 'max:255'],
                'student_password' => ['required', 'min:6'],
                'student_contact' => ['required'],
                'student_dob' => ['required', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
                'course_id' => ['required', 'exists:courses,id'],
                'instructor_id' => ['required', 'exists:instructors,id'],
                'course_date' => ['required', 'date', 'after_or_equal:today'],
                'course_slot' => ['required', 'exists:course_schedules,id'],
                'address' => ['required', 'string', 'min:10', 'max:255'],
                'parent_name' => ['nullable', 'string', 'max:100'],
                'parent_email' => ['nullable', 'email', 'max:255', 'unique:parents,email'],
                'parent_password' => ['nullable', 'min:6'],
                'parent_contact' => ['nullable', 'string', 'max:20'],
                'parent_address' => ['nullable', 'string', 'max:255'],
            ],
            [
                'student_id.unique' => 'This Student ID is already in use.',
                'student_id.regex' => 'Student ID must follow the format PDA-YYYY-Number',
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
                'email.unique' => 'This email address is already registered.',
                'email.email' => 'Please enter a valid email address.',
                'student_password.required' => 'A password is required for student login.',
                'student_password.min' => 'Student password must be at least 6 characters.',
                'student_dob.before' => 'Date of birth must be before today.',
                'student_dob.after' => 'Date of birth is not valid.',
                'course_date.after_or_equal' => 'Course date must be today or in the future.',
                'address.min' => 'Address must be at least 10 characters long.',
                'address.max' => 'Address cannot be longer than 255 characters.',
                'parent_email.unique' => 'This parent email address is already registered.',
                'parent_password.min' => 'Parent password must be at least 6 characters.',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Please correct the following errors:');
        }

        // Store plain passwords before hashing for email
        $plainStudentPassword = $request->student_password;
        $plainParentPassword = $request->parent_password;

        $studentData = $request->only(['student_id', 'first_name', 'last_name', 'email', 'student_password', 'student_contact', 'student_dob', 'course_id', 'instructor_id', 'course_date', 'course_slot', 'address']);

        if (!empty($studentData['student_password'])) {
            $studentData['student_password'] = Hash::make($studentData['student_password']);
        }

        $studentData['joining_date'] = now();
        $studentData['payment_status'] = 0;
        $studentData['course_status'] = 0;
        $studentData['practical_schedule_id'] = $request->course_slot;

        $hasParentData = !empty($request->parent_name) && !empty($request->parent_email) && !empty($request->parent_password);
        $parentId = null;
        $parent = null;

        DB::beginTransaction();

        try {
            if ($hasParentData) {
                $parentData = [
                    'name' => $request->parent_name,
                    'email' => $request->parent_email,
                    'password' => Hash::make($request->parent_password),
                    'contact' => $request->parent_contact,
                    'address' => $request->parent_address ?? $request->address,
                ];

                $parent = StudentParent::create($parentData);
                $parentId = $parent->id;

                $parentRole = Role::where('name', 'parent')->first();

                if (!$parentRole) {
                    $parentRole = Role::create([
                        'name' => 'parent',
                        'display_name' => 'Parent',
                        'description' => 'Parent role for student guardians',
                        'is_system_role' => true,
                    ]);
                }

                DB::table('model_has_roles')->insert([
                    'role_id' => $parentRole->id,
                    'model_id' => $parent->id,
                    'model_type' => StudentParent::class,
                ]);

                $adminId = Auth::id();
                if ($adminId) {
                    DB::table('permission_assignment_logs')->insert([
                        'admin_id' => $adminId,
                        'role_id' => $parentRole->id,
                        'model_id' => $parent->id,
                        'model_type' => StudentParent::class,
                        'action' => 'assign',
                        'reason' => 'Parent account creation with student registration',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if ($parentId) {
                $studentData['parent_id'] = $parentId;
            }

            $student = Student::create($studentData);

            // Create a properly-typed first session using the selected slot's date/time.
            $selectedSlot = CourseSchedule::findOrFail($request->course_slot);
            $course        = Course::find($student->course_id);
            $ct            = strtolower(trim($course->course_type ?? ''));
            $sessionType   = ($ct === 'practical') ? 'practical' : 'theory'; // hybrid → theory first

            $firstSchedule = CourseSchedule::create([
                'course_id'    => $student->course_id,
                'instructor_id'=> $student->instructor_id,
                'date'         => Carbon::parse($selectedSlot->date)->format('Y-m-d'),
                'start_time'   => Carbon::parse($selectedSlot->start_time)->format('H:i:s'),
                'end_time'     => Carbon::parse($selectedSlot->end_time)->format('H:i:s'),
                'session_type' => $sessionType,
                'max_students' => 1,
                'is_active'    => true,
            ]);
            $student->assignedSchedules()->attach($firstSchedule->id);

            // Load relationships for email
            $student->load(['course', 'instructor']);

            $studentRole = Role::where('name', 'student')->first();

            if (!$studentRole) {
                $studentRole = Role::create([
                    'name' => 'student',
                    'display_name' => 'Student',
                    'description' => 'Regular student role',
                    'is_system_role' => true,
                ]);
            }

            DB::table('model_has_roles')->insert([
                'role_id' => $studentRole->id,
                'model_id' => $student->id,
                'model_type' => Student::class,
            ]);

            $adminId = Auth::id();
            if ($adminId) {
                DB::table('permission_assignment_logs')->insert([
                    'admin_id' => $adminId,
                    'role_id' => $studentRole->id,
                    'model_id' => $student->id,
                    'model_type' => Student::class,
                    'action' => 'assign',
                    'reason' => 'New student registration',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Send emails
            $emailSuccess = [];
            $emailErrors = [];

            try {
                Mail::to($student->email)->send(new StudentCredentials($student, $plainStudentPassword, false));
                Log::info('Welcome email sent to student: ' . $student->email);
                $emailSuccess[] = 'student';
            } catch (\Exception $emailError) {
                Log::error('Failed to send welcome email to student: ' . $student->email . ' - ' . $emailError->getMessage());
                $emailErrors[] = 'student';
            }

            if ($hasParentData && $parent) {
                try {
                    Mail::to($parent->email)->send(new ParentCredentials($parent, $student, $plainParentPassword, false));
                    Log::info('Welcome email sent to parent: ' . $parent->email);
                    $emailSuccess[] = 'parent';
                } catch (\Exception $emailError) {
                    Log::error('Failed to send welcome email to parent: ' . $parent->email . ' - ' . $emailError->getMessage());
                    $emailErrors[] = 'parent';
                }
            }

            DB::commit();

            $message = 'Student registered successfully';
            if (!empty($emailSuccess)) {
                if (in_array('student', $emailSuccess) && in_array('parent', $emailSuccess)) {
                    $message .= ' and credentials sent to both student and parent via email!';
                } elseif (in_array('student', $emailSuccess)) {
                    $message .= ' and credentials sent to student via email!';
                } elseif (in_array('parent', $emailSuccess)) {
                    $message .= ' and credentials sent to parent via email!';
                }
            }

            if (!empty($emailErrors)) {
                $message .= ' (Note: Some email notifications failed to send)';
            }

            return redirect()->route('viewstudent')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Student creation failed: ' . $e->getMessage());

            toastr()->error('An error occurred while adding the student. Please try again.', 'Error');
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }

    public function sendReminder(Request $request)
    {
        $installmentId = $request->input('installment_id');

        try {
            $installment = Installment::findOrFail($installmentId);

            $student = $installment->invoice->student;

            $student->notify(new InstallmentReminderNotification($installment));

            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to send reminder: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateCourseStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_status' => 'required|in:0,1,2',
        ]);

        try {
            $student = Student::findOrFail($request->student_id);
            $oldStatus = $student->course_status;
            $newStatus = $request->course_status;
            if ($newStatus == '2') {
                $completionDate = now();
            } else {
                $completionDate = null;
            }

            $student->update([
                'course_status' => $newStatus,
                'completion_date' => $completionDate,
            ]);

            // Optional: Log status change
            Log::info("Course status changed for student {$student->id}", [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course status updated successfully',
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update course status: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function delete_student($id)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Find the student
            $student = Student::findOrFail($id);

            // Check if student has a parent and store parent_id
            $parentId = $student->parent_id;

            // Delete associated records
            // 1. Delete Invoices and their related records
            $student->invoices()->each(function ($invoice) {
                // Delete installments associated with the invoice
                $invoice->installments()->delete();

                // Delete payments associated with the invoice
                $invoice->payments()->delete();

                // Delete the invoice itself
                $invoice->delete();
            });

            // 2. Delete any additional associated records
            // Add more deletion logic for other related models as needed

            // Finally, delete the student
            $student->delete();

            // If there's a parent and no other students are associated with this parent, delete the parent
            if ($parentId) {
                $hasOtherStudents = Student::where('parent_id', $parentId)->exists();

                if (!$hasOtherStudents) {
                    // Optionally, add confirmation before deleting the parent
                    StudentParent::where('id', $parentId)->delete();
                }
            }

            // Commit the transaction
            DB::commit();

            // Return success response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Student and all associated records have been deleted successfully!',
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log the error
            Log::error('Student deletion failed: ' . $e->getMessage());

            // Return error response for AJAX
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Deleting Student Failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
    public function edit_student(Student $student)
    {
        // Get student with schedule details using the SQL query
        $studentWithSchedule = DB::select(
            "
        SELECT
            s.id AS student_id,
            CONCAT(s.first_name, ' ', s.last_name) AS student_name,
            c.course_name,
            i.instructor_name,
            cs.date AS schedule_date,
            cs.start_time,
            cs.end_time,
            cs.id AS schedule_id
        FROM students s
        INNER JOIN courses c
            ON s.course_id = c.id
        INNER JOIN instructors i
            ON s.instructor_id = i.id
        LEFT JOIN course_schedules cs
            ON s.practical_schedule_id = cs.id
        WHERE s.id = ?
    ",
            [$student->id],
        );

        // Get the first (and only) result
        $currentSchedule = !empty($studentWithSchedule) ? $studentWithSchedule[0] : null;

        $instructors = Instructor::whereHas('schedules', function ($query) use ($student) {
            $query
                ->where('course_id', $student->course_id)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where('is_active', true);
        })
            ->where('is_active', true)
            ->select('id', 'instructor_name')
            ->get();

        $parent = $student->parent;

        return view('student.edit', compact('student', 'instructors', 'parent', 'currentSchedule'));
    }

    public function getSchedulesForEdit($courseId, $instructorId, $currentScheduleId = null)
    {
        // Get current schedule details if exists
        $currentSchedule = null;
        if ($currentScheduleId) {
            $currentSchedule = CourseSchedule::find($currentScheduleId);
        }

        // Get future schedules (for new selections)
        $futureSchedules = CourseSchedule::where('course_id', $courseId)->where('instructor_id', $instructorId)->where('date', '>=', Carbon::today('America/New_York'))->where('is_active', true)->orderBy('date')->orderBy('start_time')->get();

        // Combine current schedule (if exists and not already in future schedules) with future schedules
        $allSchedules = collect();

        if ($currentSchedule && !$futureSchedules->contains('id', $currentSchedule->id)) {
            $allSchedules->push($currentSchedule);
        }

        $allSchedules = $allSchedules->merge($futureSchedules);

        // Format the schedules
        $formattedSchedules = $allSchedules->map(function ($schedule) {
            $scheduleDate = Carbon::parse($schedule->date)->timezone('America/New_York');
            $startTime = Carbon::parse($schedule->start_time)->timezone('America/New_York');
            $endTime = Carbon::parse($schedule->end_time)->timezone('America/New_York');

            $availableSlots = $schedule->max_students - Student::where('practical_schedule_id', $schedule->id)->count();

            return [
                'id' => $schedule->id,
                'date' => $scheduleDate->format('Y-m-d'),
                'start_time' => $startTime->format('h:i A'),
                'end_time' => $endTime->format('h:i A'),
                'session_type' => $schedule->session_type,
                'available_slots' => max($availableSlots, 0),
                'is_current' => $schedule->id == request()->route('currentScheduleId'),
            ];
        });

        Log::info('Schedules for Edit - Course ' . $courseId . ' and Instructor ' . $instructorId . ': ', $formattedSchedules->toArray());

        return response()->json($formattedSchedules);
    }

    // Replace your existing getAvailableSchedules method with this updated version
    public function getAvailableSchedules($courseId, $instructorId)
    {
        // Check if this is an edit request (has currentScheduleId parameter)
        $currentScheduleId = request()->get('currentScheduleId');
        $isEditMode = !empty($currentScheduleId);

        // Base query
        $query = CourseSchedule::where('course_id', $courseId)->where('instructor_id', $instructorId)->where('is_active', true);

        if ($isEditMode) {
            // For edit mode, include current schedule even if it's in the past
            $currentSchedule = CourseSchedule::find($currentScheduleId);

            // Get future schedules
            $futureSchedules = clone $query;
            $futureSchedules = $futureSchedules->where('date', '>=', Carbon::today('America/New_York'))->orderBy('date')->orderBy('start_time')->get();

            // Combine current schedule with future schedules if current is not already included
            $allSchedules = collect();

            if ($currentSchedule && !$futureSchedules->contains('id', $currentSchedule->id)) {
                $allSchedules->push($currentSchedule);
            }

            $schedules = $allSchedules->merge($futureSchedules);
        } else {
            // For new registrations, only show future dates
            $schedules = $query
                ->where('date', '>=', Carbon::today('America/New_York'))
                ->orderBy('date')->orderBy('start_time')->get();
        }

        $formattedSchedules = $schedules->map(function ($schedule) use ($currentScheduleId) {
            // $scheduleDate = Carbon::parse($schedule->date)->timezone('America/New_York');
            // $startTime = Carbon::parse($schedule->start_time)->timezone('America/New_York');
            // $endTime = Carbon::parse($schedule->end_time)->timezone('America/New_York');

            $availableSlots = $schedule->max_students - Student::where('practical_schedule_id', $schedule->id)->count();

            return [
                'id' => $schedule->id,
                'date' => Carbon::parse($schedule->date)->format('Y-m-d'),
                'start_time' => Carbon::parse($schedule->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($schedule->end_time)->format('h:i A'),
                'session_type' => $schedule->session_type,
                'available_slots' => max($availableSlots, 0),
                'is_current' => $schedule->id == $currentScheduleId,
            ];
        });

        Log::info('Schedules for Course ' . $courseId . ' and Instructor ' . $instructorId . ' (Edit Mode: ' . ($isEditMode ? 'Yes' : 'No') . '): ', $formattedSchedules->toArray());

        return response()->json($formattedSchedules);
    }

    // public function edit_student(Student $student)
    // {
    //     $instructors = Instructor::whereHas('schedules', function ($query) use ($student) {
    //         $query
    //             ->where('course_id', $student->course_id)
    //             ->where('date', '>=', now()->format('Y-m-d'))
    //             ->where('is_active', true);
    //     })
    //         ->where('is_active', true)
    //         ->select('id', 'instructor_name')
    //         ->get();

    //     $parent = $student->parent;

    //     return view('student.edit', compact('student', 'instructors', 'parent'));
    // }

    public function update_student(Request $request)
    {
        // Validation rules
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\s\'-]+$/'],
                'email' => ['required', 'email', 'unique:students,email,' . $request->id, 'max:255'],
                'student_password' => ['nullable', 'min:6'],

                'student_dob' => ['required', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
                'parent_name' => ['nullable', 'string', 'max:100'],
                'parent_email' => ['nullable', 'email', 'max:255'],
                'parent_password' => ['nullable', 'min:6'],
                'parent_contact' => ['nullable', 'string', 'max:20'],
                'parent_address' => ['nullable', 'string', 'max:255'],
                'instructor_id' => ['required', 'exists:instructors,id'],
                'course_slot' => ['required', 'exists:course_schedules,id'],
                'course_date' => ['required', 'date', 'after_or_equal:today'],
                'address' => ['required', 'string', 'min:10', 'max:255'],
            ],
            [
                // Custom error messages
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
                'email.unique' => 'This email address is already registered.',
                'student_password.min' => 'Student password must be at least 6 characters.',
                'student_contact.unique' => 'This contact number is already in use.',
                'student_dob.before' => 'Date of birth must be before today.',
                'student_dob.after' => 'Date of birth is not valid.',
                'parent_password.min' => 'Parent password must be at least 6 characters.',
                'course_date.after_or_equal' => 'Course date must be today or in the future.',
                'address.min' => 'Address must be at least 10 characters long.',
                'address.max' => 'Address cannot be longer than 255 characters.',
            ],
        );

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Please correct the following errors:');
        }

        DB::beginTransaction();

        try {
            // Find the student
            $student = Student::findOrFail($request->id);

            // Get current parent data or create a new parent
            $hasParentData = !empty($request->parent_name) && !empty($request->parent_email);
            $parentId = $student->parent_id;

            // Handle parent information
            if ($hasParentData) {
                $parentData = [
                    'name' => $request->parent_name,
                    'email' => $request->parent_email,
                    'contact' => $request->parent_contact,
                    'address' => $request->parent_address ?? $request->address,
                ];

                // If there's a password, hash it
                if (!empty($request->parent_password)) {
                    $parentData['password'] = Hash::make($request->parent_password);
                }

                if ($parentId) {
                    // Update existing parent
                    $parent = StudentParent::findOrFail($parentId);
                    $parent->update($parentData);
                } else {
                    // Create new parent
                    $parent = StudentParent::create($parentData);
                    $parentId = $parent->id;

                    // Assign parent role
                    $parentRole = Role::where('name', 'parent')->first();
                    if (!$parentRole) {
                        $parentRole = Role::create([
                            'name' => 'parent',
                            'display_name' => 'Parent',
                            'description' => 'Parent role for student guardians',
                            'is_system_role' => true,
                        ]);
                    }

                    // Assign parent role to parent model
                    DB::table('model_has_roles')->insert([
                        'role_id' => $parentRole->id,
                        'model_id' => $parent->id,
                        'model_type' => StudentParent::class,
                    ]);
                }
            }

            // Prepare data for student update
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'student_contact' => $request->student_contact,
                'student_dob' => $request->student_dob,
                'instructor_id' => $request->instructor_id,
                'address' => $request->address,
                'parent_id' => $parentId,
                'practical_schedule_id' => $request->course_slot,
                // Keep the original course_id
                'course_id' => $student->course_id,
            ];

            // Only update password if provided
            if (!empty($request->student_password)) {
                $updateData['student_password'] = Hash::make($request->student_password);
            }

            // Update student record
            $student->update($updateData);

            DB::commit();

            // Success notification
            return redirect()->back()->with('success', 'Student updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Student update failed: ' . $e->getMessage());

            // Error notification
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }

    public function change_payment_status(Request $request)
    {
        $status = Student::where('id', $request->student_id_payment)->update([
            'payment_status' => 1,
        ]);
        if ($status) {
            toastr()->success('Payment Status has been updated successfully!', 'Completed!');
            return redirect()->back();
        } else {
            toastr()->error('Updation to Database Failed, Please try again later', 'Unknown!');
            return redirect()->back();
        }
    }

    public function uploadPhoto(Request $request)
    {
        $file = $request->profile_photo;
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move(public_path('profile/'), $filename);
        $status = Student::where('id', $request->student_id)->update([
            'profile_photo' => $filename,
        ]);
        if ($status) {
            toastr()->success('Profile updated successfully!', 'Completed!');
            return redirect()->back();
        } else {
            toastr()->error('Updation to Database Failed, Please try again later', 'Unknown!');
            return redirect()->back();
        }
    }

    public function addRoadTestInfo(Request $request)
    {
        $status = Student::where('id', $request->student_id)->update([
            'road_test_taken' => $request->road_test_taken,
            'road_test_price' => $request->road_test_price,
        ]);
        if ($status) {
            toastr()->success('Inforamtion Added successfully!', 'Completed!');
            return redirect()->back()->withInput();
        } else {
            toastr()->error('Updation to Database Failed, Please try again later', 'Unknown!');
            return redirect()->back();
        }
    }
}
