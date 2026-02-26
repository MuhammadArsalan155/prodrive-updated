<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'certificate_number',
        'issue_date',
        'certificate_path',
        'verification_url',
        'is_active'
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the student this certificate belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course this certificate is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate formatted certificate number
     */
    public function getFormattedCertificateNumberAttribute()
    {
        return strtoupper($this->certificate_number);
    }

    /**
     * Check if certificate is valid
     */
    public function isValid()
    {
        return $this->is_active;
    }
}