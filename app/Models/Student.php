<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    protected $table = 'students';
    protected $appends = ['instructor', 'course'];

    public function getInstructorAttribute()
    {
        return Instructor::where('id', $this->instructor_id)->first();
    }

    public function getCourseAttribute()
    {
        return Course::where('id', $this->course_id)->first();
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'student_password',
        'student_contact',
        'student_dob',
        'profile_photo',
        'instructor_id',
        'parent_id',
        'course_id',
        'course_status',
        'payment_status',
        'address',
        'joining_date',
        'completion_date',
        'hours_theory',
        'hours_practical',
        'student_password',
        'theory_status',
        'theory_completion_date',
        'practical_status',
        'practical_completion_date',
        'practical_schedule_id',
    ];

    protected $casts = [
        'theory_completion_date' => 'datetime',
        'practical_completion_date' => 'datetime',
    ];

    protected $hidden = ['student_password', 'remember_token'];

    public function practicalSchedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'practical_schedule_id');
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->student_password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get the parent that owns the student.
     */
    public function parent()
    {
        return $this->belongsTo(StudentParent::class, 'parent_id');
    }

    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id')->where('model_type', static::class);
    }

    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }

        return $this->roles()->where('name', $roleName)->exists();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function hasCertificateForCurrentCourse()
    {
        return $this->certificates()->where('course_id', $this->course_id)->exists();
    }
}
