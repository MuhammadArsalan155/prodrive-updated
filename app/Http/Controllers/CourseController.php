<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseInstallmentPlan;
use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');

    // }

    private function checkCourseManagePermission()
    {
        $user = $this->getCurrentUser();

        if (!$this->userHasRole($user, ['admin', 'manager', 'instructor'])) {
            Log::warning('Unauthorized course management attempt', [
                'user_id' => $user ? $user->id : 'Unknown',
                'user_email' => $user ? $user->email : 'Unknown',
                'roles' => $user ? $this->getUserRoles($user) : [],
            ]);

            abort(403, 'You do not have permission to manage courses.');
        }
    }

    private function getCurrentUser()
    {
        $guards = ['web', 'student', 'instructor'];
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }

    private function userHasRole($user, $roles)
    {
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($roles);
        }

        if (method_exists($user, 'roles')) {
            return $user
                ->roles()
                ->whereIn('name', is_array($roles) ? $roles : [$roles])
                ->exists();
        }

        Log::error('No role checking method found for user', [
            'user_type' => get_class($user),
            'user_id' => $user->id ?? 'Unknown',
        ]);

        return false;
    }

    private function getUserRoles($user)
    {
        if (method_exists($user, 'roles')) {
            return $user->roles->pluck('name')->toArray();
        }

        return [];
    }

    private function prepareCourseFormData()
    {
        $installmentPlans = CourseInstallmentPlan::where('is_active', true)->get();

        $lessonPlans = LessonPlan::where('is_active', true)->orderBy('title')->get();

        return compact('installmentPlans', 'lessonPlans');
    }

    public function addcourse()
    {
        $this->checkCourseManagePermission();

        $data = $this->prepareCourseFormData();

        return view('courses.addcourse', $data);
    }

    public function add_course(Request $request)
    {
        $this->checkCourseManagePermission();

        $validatedData = $request->validate([
            'course_name' => 'required|string|max:255|unique:courses,course_name',
            'course_price' => 'required|numeric|min:0|max:100000',
            'course_type' => 'required|in:theory,practical,hybrid',
            'theory_hours' => [
                'required',
                'integer',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('course_type') === 'practical' && $value > 0) {
                        $fail('Practical courses cannot have theory hours.');
                    }
                },
            ],
            'practical_hours' => [
                'required',
                'integer',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('course_type') === 'theory' && $value > 0) {
                        $fail('Theory courses cannot have practical hours.');
                    }
                },
            ],
            'total_theory_classes' => [
                'nullable',
                'integer',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('course_type') === 'practical' && $value > 0) {
                        $fail('Practical courses cannot have theory classes.');
                    }
                },
            ],
            'total_practical_classes' => [
                'nullable',
                'integer',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('course_type') === 'theory' && $value > 0) {
                        $fail('Theory courses cannot have practical classes.');
                    }
                },
            ],
            'theory_lesson_plans' => 'nullable|array',
            'theory_lesson_plans.*' => 'nullable|exists:lesson_plans,id',
            'practical_lesson_plans' => 'nullable|array',
            'practical_lesson_plans.*' => 'nullable|exists:lesson_plans,id',
            'description' => 'nullable|string|max:1000',
            'has_installment_plan' => 'sometimes|boolean',
            'course_installment_plan_id' => 'nullable|exists:course_installment_plans,id',
            'is_active' => 'boolean',
        ]);

        return DB::transaction(function () use ($validatedData, $request) {
            // Log incoming data for debugging
            Log::info('Adding course with lesson plan data', [
                'theory_lesson_plans' => $request->theory_lesson_plans,
                'practical_lesson_plans' => $request->practical_lesson_plans,
                'total_theory_classes' => $validatedData['total_theory_classes'] ?? 0,
                'total_practical_classes' => $validatedData['total_practical_classes'] ?? 0,
                'course_type' => $validatedData['course_type'],
            ]);

            // Prepare course data
            $courseData = $validatedData;
            $courseData['has_installment_plan'] = $request->input('has_installment_plan', false);

            // Create the course
            $course = Course::create($courseData);

            // Handle installment plan association
            if ($courseData['has_installment_plan'] && !empty($courseData['course_installment_plan_id'])) {
                $installmentPlan = CourseInstallmentPlan::findOrFail($courseData['course_installment_plan_id']);
                $course->update([
                    'course_installment_plan_id' => $installmentPlan->id,
                ]);
            }

            // Associate theory lesson plans
            if ($request->has('theory_lesson_plans') && is_array($request->theory_lesson_plans)) {
                $theoryPlansCount = 0;
                foreach ($request->theory_lesson_plans as $classOrder => $lessonPlanId) {
                    if (!empty($lessonPlanId)) {
                        $course->lessonPlans()->attach($lessonPlanId, [
                            'class_type' => 'theory',
                            'class_order' => $classOrder + 1,
                        ]);
                        $theoryPlansCount++;
                    }
                }

                // Update total_theory_classes if needed
                if ($theoryPlansCount > 0 && empty($course->total_theory_classes)) {
                    $course->update(['total_theory_classes' => $theoryPlansCount]);
                }

                Log::info('Theory lesson plans attached', [
                    'course_id' => $course->id,
                    'count' => $theoryPlansCount,
                ]);
            }

            // Associate practical lesson plans
            if ($request->has('practical_lesson_plans') && is_array($request->practical_lesson_plans)) {
                $practicalPlansCount = 0;
                foreach ($request->practical_lesson_plans as $classOrder => $lessonPlanId) {
                    if (!empty($lessonPlanId)) {
                        $course->lessonPlans()->attach($lessonPlanId, [
                            'class_type' => 'practical',
                            'class_order' => $classOrder + 1,
                        ]);
                        $practicalPlansCount++;
                    }
                }

                // Update total_practical_classes if needed
                if ($practicalPlansCount > 0 && empty($course->total_practical_classes)) {
                    $course->update(['total_practical_classes' => $practicalPlansCount]);
                }

                Log::info('Practical lesson plans attached', [
                    'course_id' => $course->id,
                    'count' => $practicalPlansCount,
                ]);
            }

            // Log summary of the course creation
            Log::info('Course created successfully', [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'theory_plans_count' => $course->theoryLessonPlans()->count(),
                'practical_plans_count' => $course->practicalLessonPlans()->count(),
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('viewcourse')->with('success', 'Course created successfully!');
        });
    }
    public function viewcourse()
    {
        // Get the current user
        $user = $this->getCurrentUser();
        // Determine course query based on user role
        if ($this->userHasRole($user, ['admin', 'manager'])) {
            // Admin/Manager sees all courses
            $courses = Course::with('installmentPlan')->orderBy('created_at', 'desc')->get();
        } elseif ($this->userHasRole($user, 'instructor')) {
            // Instructor sees courses they are associated with
            dd($user);
            $instructor = $user->instructor ?? null; // Assuming you have an instructor relationship
            dd($instructor);
            $courses = $instructor ? Course::where('instructor_id', $instructor->id)->with('installmentPlan')->orderBy('created_at', 'desc')->get() : collect();
        } elseif ($this->userHasRole($user, 'student')) {
            // Student sees their enrolled courses
            $student = $user->student ?? null; // Assuming you have a student relationship
            $courses = $student ? Course::where('id', $student->course_id)->with('installmentPlan')->get() : collect();
        } else {
            // Other roles see no courses
            $courses = collect();
        }

        return view('courses.viewcourse', compact('courses'));
    }

    /**
     * Store a new course
     */
    // public function add_course(Request $request)
    // {
    //     // Check course management permission
    //     $this->checkCourseManagePermission();

    //     // Validate course data
    //     $validatedData = $request->validate([
    //         'course_name' => 'required|string|max:255|unique:courses,course_name',
    //         'course_price' => 'required|numeric|min:0|max:100000',
    //         'course_type' => 'required|in:theory,practical,hybrid',
    //         'theory_hours' => [
    //             'required',
    //             'integer',
    //             'min:0',
    //             'max:1000',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($request->input('course_type') === 'practical' && $value > 0) {
    //                     $fail('Practical courses cannot have theory hours.');
    //                 }
    //             },
    //         ],
    //         'practical_hours' => [
    //             'required',
    //             'integer',
    //             'min:0',
    //             'max:1000',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($request->input('course_type') === 'theory' && $value > 0) {
    //                     $fail('Theory courses cannot have practical hours.');
    //                 }
    //             },
    //         ],
    //         'description' => 'nullable|string|max:1000',
    //         'has_installment_plan' => 'sometimes|boolean',
    //         'course_installment_plan_id' => 'nullable|exists:course_installment_plans,id',
    //         'is_active' => 'boolean',
    //     ]);

    //     return DB::transaction(function () use ($validatedData, $request) {
    //         // Prepare course data
    //         $courseData = $validatedData;
    //         $courseData['has_installment_plan'] = $request->input('has_installment_plan', false);

    //         // Create the course
    //         $course = Course::create($courseData);

    //         // If installment plan is selected, associate it
    //         if ($courseData['has_installment_plan'] && !empty($courseData['course_installment_plan_id'])) {
    //             $installmentPlan = CourseInstallmentPlan::findOrFail($courseData['course_installment_plan_id']);
    //             $course->update([
    //                 'course_installment_plan_id' => $installmentPlan->id
    //             ]);
    //         }

    //         // Log the course creation
    //         Log::info('New course created', [
    //             'course_id' => $course->id,
    //             'course_name' => $course->course_name,
    //             'created_by' => Auth::id()
    //         ]);

    //         return redirect()->route('viewcourse')->with('success', 'Course created successfully!');
    //     });
    // }

    /**
     * Show the form to edit a course
     */
    public function edit_course(Course $course)
    {
        // Check course management permission
        $this->checkCourseManagePermission();

        // Get form data
        $data = $this->prepareCourseFormData();

        // Load course with its lesson plans
        $course->load('theoryLessonPlans', 'practicalLessonPlans');

        // Add course to the data
        $data['course'] = $course;

        return view('courses.editcourse', $data);
    }

    public function update_course(Request $request)
    {
        $this->checkCourseManagePermission();

        $validatedData = $request->validate([
            'id' => 'required|exists:courses,id',
            'course_name' => 'required|string|max:255',
            'course_price' => 'required|numeric|min:0',
            'course_type' => 'required|string|in:theory,practical,hybrid',
            'description' => 'nullable|string',
            'theory_hours' => 'required|integer|min:0',
            'practical_hours' => 'required|integer|min:0',
            'total_theory_classes' => 'nullable|integer|min:0',
            'total_practical_classes' => 'nullable|integer|min:0',
            'theory_lesson_plans' => 'array',
            'theory_lesson_plans.*' => 'exists:lesson_plans,id',
            'practical_lesson_plans' => 'array',
            'practical_lesson_plans.*' => 'exists:lesson_plans,id',
            'is_active' => 'boolean',
            'has_installment_plan' => 'sometimes|boolean',
            'course_installment_plan_id' => 'nullable|exists:course_installment_plans,id',
        ]);

        return DB::transaction(function () use ($request, $validatedData) {
            $course = Course::findOrFail($request->id);

            $courseData = $validatedData;
            $courseData['has_installment_plan'] = $request->input('has_installment_plan', false);

            if (!$courseData['has_installment_plan']) {
                $courseData['course_installment_plan_id'] = null;
            }

            $course->update($courseData);


            $course->lessonPlans()->detach();

            if ($request->has('theory_lesson_plans') && is_array($request->theory_lesson_plans)) {
                foreach ($request->theory_lesson_plans as $classOrder => $lessonPlanId) {
                    if (!empty($lessonPlanId)) {
                        $course->lessonPlans()->attach($lessonPlanId, [
                            'class_type' => 'theory',
                            'class_order' => $classOrder + 1,
                        ]);
                    }
                }
            }

            if ($request->has('practical_lesson_plans') && is_array($request->practical_lesson_plans)) {
                foreach ($request->practical_lesson_plans as $classOrder => $lessonPlanId) {
                    if (!empty($lessonPlanId)) {
                        $course->lessonPlans()->attach($lessonPlanId, [
                            'class_type' => 'practical',
                            'class_order' => $classOrder + 1,
                        ]);
                    }
                }
            }

            Log::info('Course updated', [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('viewcourse')->with('success', 'Course Updated Successfully');
        });
    }

    public function delete_course($id)
    {
        $this->checkCourseManagePermission();

        $course = Course::findOrFail($id);

        Log::info('Course deletion attempt', [
            'course_id' => $course->id,
            'course_name' => $course->course_name,
            'deleted_by' => Auth::id(),
        ]);

        $status = $course->delete();

        if ($status) {
            toastr()->success('Course Deleted Successfully', 'Completed!');
            return redirect()->back();
        } else {
            toastr()->error('Course Deletion Failed', 'Error');
            return redirect()->back();
        }
    }

    public function toggleCourseStatus($id)
    {
        $this->checkCourseManagePermission();

        try {
            $course = Course::findOrFail($id);

            $course->is_active = !$course->is_active;
            $course->save();

            Log::info('Course status toggled', [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'new_status' => $course->is_active,
                'changed_by' => Auth::id(),
            ]);

            $message = $course->is_active ? 'Course has been activated successfully.' : 'Course has been deactivated successfully.';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Course status toggle failed', [
                'course_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Failed to update course status. Please try again.');
        }
    }
}
