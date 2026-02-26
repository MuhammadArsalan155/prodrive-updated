<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_question_id',
        'course_id',
        'user_id',
        'user_type',
        'response',
        'comments',
        'class_order',
    ];

    protected $casts = [
        'response' => 'boolean',
    ];

    /**
     * Get the question this response is for
     */
    public function question()
    {
        return $this->belongsTo(FeedbackQuestion::class, 'feedback_question_id');
    }

    /**
     * Get the course this feedback is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who provided the feedback
     * This could be a student or instructor
     */
    public function user()
    {
        if ($this->user_type === 'student') {
            return $this->belongsTo(Student::class, 'user_id');
        } else {
            return $this->belongsTo(Instructor::class, 'user_id');
        }
    }
}