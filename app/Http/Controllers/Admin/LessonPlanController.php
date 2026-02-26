<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\FeedbackQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonPlanController extends Controller
{
    private function checkLessonPlanPermission()
    {
        $user = $this->getCurrentUser();
        
        if (!$this->userHasRole($user, ['admin', 'manager'])) {
            Log::warning('Unauthorized lesson plan management attempt', [
                'user_id' => $user ? $user->id : 'Unknown',
                'user_email' => $user ? $user->email : 'Unknown',
                'roles' => $user ? $this->getUserRoles($user) : []
            ]);
            
            abort(403, 'You do not have permission to manage lesson plans.');
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
            return $user->roles()
                ->whereIn('name', is_array($roles) ? $roles : [$roles])
                ->exists();
        }
    
        Log::error('No role checking method found for user', [
            'user_type' => get_class($user),
            'user_id' => $user->id ?? 'Unknown'
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
    
  
    public function index()
    {
        $this->checkLessonPlanPermission();
        
        $lessonPlans = LessonPlan::with('feedbackQuestions')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.lesson-plans.index', compact('lessonPlans'));
    }
    

    public function create()
    {
        $this->checkLessonPlanPermission();
        
        return view('admin.lesson-plans.create');
    }
    

    public function store(Request $request)
    {
        $this->checkLessonPlanPermission();
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'sometimes|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.display_order' => 'required|integer|min:1'
        ]);
        
        return DB::transaction(function () use ($validatedData, $request) {
            try {
                $lessonPlan = LessonPlan::create([
                    'title' => $validatedData['title'],
                    'content' => $validatedData['content'],
                    'is_active' => $request->has('is_active'),
                    'created_by' => Auth::id()
                ]);
                
                $questionsData = collect($validatedData['questions'])->map(function ($question) {
                    return [
                        'question_text' => $question['question_text'],
                        'is_active' => true,
                        'display_order' => $question['display_order']
                    ];
                })->toArray();
                
                $lessonPlan->feedbackQuestions()->createMany($questionsData);
                
                Log::info('New lesson plan created', [
                    'lesson_plan_id' => $lessonPlan->id,
                    'lesson_plan_title' => $lessonPlan->title,
                    'created_by' => Auth::id()
                ]);
                
                return redirect()->route('admin.lesson-plans.index')
                    ->with('success', 'Lesson plan created successfully!');
            } catch (\Exception $e) {
                Log::error('Failed to create lesson plan', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create lesson plan. Please try again.');
            }
        });
    }
    

    public function show(LessonPlan $lessonPlan)
    {
        $this->checkLessonPlanPermission();
        
        $lessonPlan->load('feedbackQuestions', 'creator');
        
        return view('admin.lesson-plans.show', compact('lessonPlan'));
    }
    

    public function edit(LessonPlan $lessonPlan)
    {
        $this->checkLessonPlanPermission();
        
        $lessonPlan->load('feedbackQuestions');
        
        return view('admin.lesson-plans.edit', compact('lessonPlan'));
    }
    

    public function update(Request $request, LessonPlan $lessonPlan)
    {
        $this->checkLessonPlanPermission();
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'sometimes|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'sometimes|exists:feedback_questions,id',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.display_order' => 'required|integer|min:1',
            'questions.*.is_active' => 'sometimes|boolean'
        ]);
        
        return DB::transaction(function () use ($validatedData, $request, $lessonPlan) {
            try {
                $lessonPlan->update([
                    'title' => $validatedData['title'],
                    'content' => $validatedData['content'],
                    'is_active' => $request->has('is_active')
                ]);
                
                $existingQuestionIds = [];
                
                foreach ($validatedData['questions'] as $questionData) {
                    if (isset($questionData['id'])) {
                        $question = FeedbackQuestion::find($questionData['id']);
                        if ($question && $question->lesson_plan_id == $lessonPlan->id) {
                            $question->update([
                                'question_text' => $questionData['question_text'],
                                'display_order' => $questionData['display_order'],
                                'is_active' => isset($questionData['is_active']) ? true : false
                            ]);
                            $existingQuestionIds[] = $question->id;
                        }
                    } else {
                        $question = $lessonPlan->feedbackQuestions()->create([
                            'question_text' => $questionData['question_text'],
                            'display_order' => $questionData['display_order'],
                            'is_active' => true
                        ]);
                        $existingQuestionIds[] = $question->id;
                    }
                }
                
                $lessonPlan->feedbackQuestions()
                    ->whereNotIn('id', $existingQuestionIds)
                    ->delete();
                
                Log::info('Lesson plan updated', [
                    'lesson_plan_id' => $lessonPlan->id,
                    'lesson_plan_title' => $lessonPlan->title,
                    'updated_by' => Auth::id()
                ]);
                
                return redirect()->route('admin.lesson-plans.index')
                    ->with('success', 'Lesson plan updated successfully!');
            } catch (\Exception $e) {
                Log::error('Failed to update lesson plan', [
                    'error' => $e->getMessage(),
                    'lesson_plan_id' => $lessonPlan->id,
                    'user_id' => Auth::id()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to update lesson plan. Please try again.');
            }
        });
    }
    

    public function destroy(LessonPlan $lessonPlan)
    {
        $this->checkLessonPlanPermission();
        
        try {
            $inUse = $lessonPlan->courses()->exists();
            
            if ($inUse) {
                return redirect()->back()
                    ->with('error', 'This lesson plan cannot be deleted because it is being used by one or more courses.');
            }
            
            Log::info('Lesson plan deletion', [
                'lesson_plan_id' => $lessonPlan->id,
                'lesson_plan_title' => $lessonPlan->title,
                'deleted_by' => Auth::id()
            ]);
            
            $lessonPlan->delete();
            
            return redirect()->route('admin.lesson-plans.index')
                ->with('success', 'Lesson plan deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete lesson plan', [
                'error' => $e->getMessage(),
                'lesson_plan_id' => $lessonPlan->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete lesson plan. Please try again.');
        }
    }
    

    public function toggleStatus(LessonPlan $lessonPlan)
    {
        $this->checkLessonPlanPermission();
        
        try {
            $lessonPlan->update(['is_active' => !$lessonPlan->is_active]);
            
            $status = $lessonPlan->is_active ? 'activated' : 'deactivated';
            
            Log::info("Lesson plan {$status}", [
                'lesson_plan_id' => $lessonPlan->id,
                'new_status' => $lessonPlan->is_active,
                'toggled_by' => Auth::id()
            ]);
            
            return redirect()->back()->with('success', "Lesson plan {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to toggle lesson plan status', [
                'error' => $e->getMessage(),
                'lesson_plan_id' => $lessonPlan->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update lesson plan status. Please try again.');
        }
    }
}