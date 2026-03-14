<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorEvaluation extends Model
{
    protected $fillable = [
        'student_id',
        'instructor_id',
        'course_id',
        'performance_rating',
        'behavior_rating',
        'attendance_rating',
        'overall_rating',
        'performance_notes',
        'behavior_notes',
        'recommendations',
        'is_recommended_for_certificate',
    ];

    protected $casts = [
        'is_recommended_for_certificate' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round(
            ($this->performance_rating + $this->behavior_rating + $this->attendance_rating + $this->overall_rating) / 4,
            1
        );
    }
}
