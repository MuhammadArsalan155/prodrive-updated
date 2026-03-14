<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Instructor;
use App\Models\PaymentMethod;
use App\Models\Student;
use App\Mail\StudentCredentials;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterStudentController extends Controller
{
    // Define the date when payment functionality was implemented
    // Change this to your actual implementation date
    const PAYMENT_FUNCTIONALITY_START_DATE = '2024-08-08'; // Adjust this date!

    public function index()
    {
        return view('registration');
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

            return response()->json($courses);
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

    public function getAvailableSchedules($courseId, $instructorId)
    {
        $schedules = CourseSchedule::with(['students' => function($query) {
                // Only count active students if you have status fields
            }])
            ->where('course_id', $courseId)
            ->where('instructor_id', $instructorId)
            ->where('date', '>=', Carbon::today('America/New_York'))
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $schedules = $schedules->map(function ($schedule) {
            $registeredStudents = $schedule->students->count();
            $availableSlots = $schedule->max_students - $registeredStudents;

            return [
                'id' => $schedule->id,
                'date' => Carbon::parse($schedule->date)->format('Y-m-d'),
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
                'session_type' => $schedule->session_type,
                'max_students' => $schedule->max_students,
                'registered_students' => $registeredStudents,
                'available_slots' => max($availableSlots, 0),
            ];
        });

        return response()->json($schedules);
    }

    public function getPaymentMethods()
    {
       $paymentMethods = PaymentMethod::where('is_active', true)
    ->whereRaw('LOWER(name) != ?', ['cash'])
    ->select('id', 'name', 'code', 'logo')
    ->get();
        return response()->json($paymentMethods);
    }

    private function generateStudentPassword()
    {
        return Str::random(8);
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) {
                        $existingStudent = Student::where('email', $value)
                            ->where(function($query) {
                                $query->where('payment_status', 3)
                                      ->orWhere('created_at', '<', self::PAYMENT_FUNCTIONALITY_START_DATE); // Old records
                            })
                            ->first();

                        if ($existingStudent) {
                            if ($existingStudent->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
                                $fail('This email is already registered in our system (existing student).');
                            } else {
                                $fail('This email is already registered with a completed payment.');
                            }
                        }
                    }
                ],
                'student_contact' => 'required|string|max:20',
                'student_dob' => 'required|date|before:today',
                'address' => 'required|string',
                'course_id' => 'required|exists:courses,id',
                'instructor_id' => 'required|exists:instructors,id',
                'schedule_id' => 'required|exists:course_schedules,id',
            ], [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'student_contact.required' => 'Contact number is required.',
                'student_dob.required' => 'Date of birth is required.',
                'student_dob.before' => 'Date of birth must be before today.',
                'address.required' => 'Address is required.',
                'course_id.required' => 'Please select a course.',
                'instructor_id.required' => 'Please select an instructor.',
                'schedule_id.required' => 'Please select a schedule.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // SAFE CLEANUP: Only cleanup NEW incomplete registrations (created after payment functionality)
            $this->safeCleanupIncompleteRegistrations($validated['email']);

            // Generate password for the student
            $plainPassword = $this->generateStudentPassword();
            $hashedPassword = Hash::make($plainPassword);

            // Create the student with pending status AND payment tracking flag
            $student = Student::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'student_password' => $hashedPassword,
                'student_contact' => $validated['student_contact'],
                'student_dob' => $validated['student_dob'],
                'address' => $validated['address'],
                'course_id' => $validated['course_id'],
                'instructor_id' => $validated['instructor_id'],
                'practical_schedule_id' => $validated['schedule_id'],
                'course_status' => 0,  // Pending
                'payment_status' => 0, // Pending
                'joining_date' => now(),
                'has_payment_process' => true,
            ]);

            // Assign the selected schedule to the student via pivot
            $student->assignedSchedules()->attach($validated['schedule_id']);

            // Store the password in session for later use
            session([
                'student_registration_password' => $plainPassword,
                'student_registration_id' => $student->id
            ]);

            Log::info('NEW Student Registration (With Payment Process):', [
                'student_id' => $student->id,
                'email' => $student->email,
                'has_payment_process' => true
            ]);

            return response()->json([
                'success' => true,
                'student_id' => $student->id,
                'message' => 'Student registered successfully. Please complete payment to activate your account.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Student Registration Error:', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error_details' => config('app.debug') ? $e->getMessage() : 'An error occurred during registration.'
            ], 500);
        }
    }

    /**
     * SAFE cleanup - Only cleans up NEW registrations created after payment functionality
     */
    private function safeCleanupIncompleteRegistrations($email)
    {
        try {
            // SAFE QUERY: Only find incomplete registrations that:
            // 1. Have the same email
            // 2. Were created AFTER payment functionality was implemented
            // 3. Have payment_status 0, 1, or 2 (incomplete)
            // 4. Have the has_payment_process flag OR were created after the start date
            $incompleteStudents = Student::where('email', $email)
                ->whereIn('payment_status', [0, 1, 2]) // Only incomplete payments
                ->where(function($query) {
                    $query->where('has_payment_process', true) // New registrations with flag
                          ->orWhere('created_at', '>=', self::PAYMENT_FUNCTIONALITY_START_DATE); // Or created after payment functionality
                })
                ->get();

            if ($incompleteStudents->count() > 0) {
                Log::info('Found NEW incomplete registrations to cleanup (SAFE):', [
                    'email' => $email,
                    'count' => $incompleteStudents->count(),
                    'student_ids' => $incompleteStudents->pluck('id')->toArray(),
                    'created_dates' => $incompleteStudents->pluck('created_at')->toArray()
                ]);

                foreach ($incompleteStudents as $student) {
                    // Double-check safety before cleanup
                    if ($this->isSafeToDelete($student)) {
                        $this->safeImmediateCleanup($student->id, 'Safe cleanup before new registration attempt');
                    } else {
                        Log::warning('Skipped cleanup for safety:', [
                            'student_id' => $student->id,
                            'email' => $student->email,
                            'created_at' => $student->created_at
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error in safe cleanup:', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if a student record is safe to delete
     */
    private function isSafeToDelete($student)
    {
        // Don't delete if:
        // 1. Created before payment functionality start date
        // 2. Has successful payment status
        // 3. Doesn't have payment process flag and is old

        if ($student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
            return false; // OLD RECORD - DON'T DELETE
        }

        if ($student->payment_status == 3) {
            return false; // SUCCESSFUL PAYMENT - DON'T DELETE
        }

        if (!isset($student->has_payment_process) && $student->created_at < self::PAYMENT_FUNCTIONALITY_START_DATE) {
            return false; // OLD RECORD WITHOUT FLAG - DON'T DELETE
        }

        return true; // SAFE TO DELETE
    }

    /**
     * SAFE IMMEDIATE CLEANUP - with extra safety checks
     */
    private function safeImmediateCleanup($studentId, $reason = 'Safe cleanup incomplete registration')
    {
        DB::beginTransaction();

        try {
            $student = Student::find($studentId);

            if (!$student) {
                DB::rollBack();
                return;
            }

            // FINAL SAFETY CHECK
            if (!$this->isSafeToDelete($student)) {
                Log::warning('SAFETY CHECK FAILED - Aborting cleanup:', [
                    'student_id' => $studentId,
                    'email' => $student->email,
                    'created_at' => $student->created_at,
                    'payment_status' => $student->payment_status,
                    'has_payment_process' => $student->has_payment_process ?? 'not_set'
                ]);
                DB::rollBack();
                return;
            }

            Log::info('Starting SAFE immediate cleanup:', [
                'student_id' => $studentId,
                'email' => $student->email,
                'created_at' => $student->created_at,
                'reason' => $reason
            ]);

            // Only cleanup related records that exist (for new payment system)
            if (class_exists('\App\Models\Invoice')) {
                $invoices = \App\Models\Invoice::where('student_id', $studentId)->get();

                foreach ($invoices as $invoice) {
                    if (class_exists('\App\Models\Payment')) {
                        \App\Models\Payment::where('invoice_id', $invoice->id)->delete();
                    }

                    if (class_exists('\App\Models\Installment')) {
                        \App\Models\Installment::where('invoice_id', $invoice->id)->delete();
                    }

                    $invoice->delete();
                }
            }

            // Delete the student record
            $student->delete();

            DB::commit();

            Log::info('SAFE immediate cleanup completed successfully:', [
                'student_id' => $studentId,
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('SAFE immediate cleanup failed:', [
                'student_id' => $studentId,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
        }
    }
}
