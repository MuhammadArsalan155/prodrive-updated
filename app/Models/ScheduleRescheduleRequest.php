<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRescheduleRequest extends Model
{
    protected $fillable = [
        'student_id',
        'course_schedule_id',
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'reason',
        'status',
        'instructor_note',
        'handled_at',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'handled_at'     => 'datetime',
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
