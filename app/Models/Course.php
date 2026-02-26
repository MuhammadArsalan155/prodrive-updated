<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_name',
        'course_price',
        'course_type',
        'description',
        'theory_hours',
        'practical_hours',
        'total_theory_classes',
        'total_practical_classes',
        'is_active',

        'has_installment_plan',
        'course_installment_plan_id',
    ];

    // Optional: Cast boolean and numeric fields
    protected $casts = [
        'is_active' => 'boolean',
        'has_installment_plan' => 'boolean',
        'course_price' => 'float',
        'total_theory_classes' => 'integer',
        'total_practical_classes' => 'integer',
    ];

    /**
     * Get the schedules for this course
     */
    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    /**
     * Get the lesson plans for this course
     */
    public function lessonPlans()
    {
        return $this->belongsToMany(LessonPlan::class, 'course_lesson_plan')->withPivot('class_type', 'class_order')->withTimestamps();
    }

    /**
     * Get theory lesson plans for this course
     */
    public function theoryLessonPlans()
    {
        return $this->belongsToMany(LessonPlan::class, 'course_lesson_plan')->withPivot('class_order')->wherePivot('class_type', 'theory')->orderBy('class_order')->withTimestamps();
    }

    /**
     * Get practical lesson plans for this course
     */
    public function practicalLessonPlans()
    {
        return $this->belongsToMany(LessonPlan::class, 'course_lesson_plan')->withPivot('class_order')->wherePivot('class_type', 'practical')->orderBy('class_order')->withTimestamps();
    }

    /**
     * Get all feedback responses for this course
     */
    public function feedbackResponses()
    {
        return $this->hasMany(FeedbackResponse::class);
    }

    // Relationship with Installment Plan
    public function installmentPlan()
    {
        return $this->belongsTo(CourseInstallmentPlan::class, 'course_installment_plan_id');
    }

    // Helper method to check installment plan availability
    public function hasInstallmentPlan()
    {
        return $this->has_installment_plan && $this->installmentPlan;
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
