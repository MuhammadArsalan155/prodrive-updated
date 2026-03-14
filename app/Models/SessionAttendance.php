<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    protected $table = 'session_attendance';

    protected $fillable = [
        'student_id',
        'course_schedule_id',
        'is_present',
        'status',
        'notes',
        'class_order',
        'class_type',
        'completed_at',
    ];

    protected $casts = [
        'is_present'   => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }
}
