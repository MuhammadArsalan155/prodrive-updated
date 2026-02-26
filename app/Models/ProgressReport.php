<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instructor_id',
        'course_id',
        'performance_notes',
        'areas_of_improvement',
        'rating'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    /**
     * Relationship with Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship with Instructor
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Relationship with Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope to get reports for a specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get reports for a specific course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Check if the report has a rating
     */
    public function hasRating()
    {
        return !is_null($this->rating);
    }

    /**
     * Get formatted rating
     */
    public function getFormattedRatingAttribute()
    {
        return $this->rating ? round($this->rating, 1) . '/5' : 'N/A';
    }
}