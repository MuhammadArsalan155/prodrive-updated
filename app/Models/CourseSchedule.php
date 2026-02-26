<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['course_id', 'instructor_id', 'date', 'start_time', 'end_time', 'session_type', 'max_students', 'is_active'];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'practical_schedule_id');
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    // Custom Methods
    public static function copyScheduleToNextMonth($year, $month)
    {
        $currentMonth = Carbon::create($year, $month, 1);
        $nextMonth = $currentMonth->copy()->addMonth();

        $schedules = self::forMonth($year, $month)->get();

        foreach ($schedules as $schedule) {
            $newDate = Carbon::parse($schedule->date)->addMonth();

            self::create([
                'course_id' => $schedule->course_id,
                'instructor_id' => $schedule->instructor_id,
                'date' => $newDate,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'session_type' => $schedule->session_type,
                'max_students' => $schedule->max_students,
                'is_active' => true,
            ]);
        }
    }
}
