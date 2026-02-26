<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_plan_id',
        'question_text',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the lesson plan this question belongs to
     */
    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }

    /**
     * Get responses to this question
     */
    public function responses()
    {
        return $this->hasMany(FeedbackResponse::class);
    }
}