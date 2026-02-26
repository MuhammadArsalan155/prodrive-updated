<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the feedback questions for this lesson plan
     */
    public function feedbackQuestions()
    {
        return $this->hasMany(FeedbackQuestion::class)->orderBy('display_order');
    }

    /**
     * Get the courses that use this lesson plan
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_lesson_plan')
                    ->withPivot('class_type', 'class_order')
                    ->withTimestamps();
    }

    /**
     * Get the user who created this lesson plan
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}