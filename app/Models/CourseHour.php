<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseHour extends Model
{
    use HasFactory;

    protected $table = 'course_hours';

    protected $fillable = [
        'student_id',
        'course_type',
        'course_id',
        'hours',
        'date',
    ];

    // course_type values: 1 = theory, 2 = practical

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeTheory($query)
    {
        return $query->where('course_type', 1);
    }

    public function scopePractical($query)
    {
        return $query->where('course_type', 2);
    }

    public function getTypeNameAttribute()
    {
        return $this->course_type == 1 ? 'Theory' : ($this->course_type == 2 ? 'Practical' : 'Unknown');
    }
}
