<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticalSession extends Model
{
    use HasFactory;

    protected $table = 'practical_sessions';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'course_id',
        'session_number',
        'date',
        'start_time',
        'end_time',
        'duration_hours',
        'status',
        'instructor_notes',
        'completed_at',
    ];

    protected $casts = [
        'date'         => 'date',
        'completed_at' => 'datetime',
        'duration_hours' => 'decimal:1',
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

    /**
     * Formatted start time (e.g. "09:00 AM")
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return $this->start_time
            ? \Carbon\Carbon::parse($this->start_time)->format('h:i A')
            : 'N/A';
    }

    /**
     * Formatted end time (e.g. "11:00 AM")
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return $this->end_time
            ? \Carbon\Carbon::parse($this->end_time)->format('h:i A')
            : 'N/A';
    }
}
