<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InstructorProgressReportNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $progressData;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, array $progressData)
    {
        $this->student = $student;
        $this->progressData = $progressData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Student Progress Report')
            ->greeting('Hello ' . $notifiable->instructor_name . '!')
            ->line('Progress report for student: ' . $this->student->first_name . ' ' . $this->student->last_name)
            ->line('Course: ' . $this->student->course->course_name)
            ->line('Total Progress: ' . $this->progressData['progress_percentage'] . '%')
            ->line('Theory Hours: ' . 
                $this->progressData['theory_hours']['completed'] . 
                '/' . 
                $this->progressData['theory_hours']['total']
            )
            ->line('Practical Hours: ' . 
                $this->progressData['practical_hours']['completed'] . 
                '/' . 
                $this->progressData['practical_hours']['total']
            )
            ->action('View Student Details', url('/admin/students/' . $this->student->id))
            ->line('Please review and provide necessary guidance.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->first_name . ' ' . $this->student->last_name,
            'progress_percentage' => $this->progressData['progress_percentage'],
            'theory_hours' => $this->progressData['theory_hours'],
            'practical_hours' => $this->progressData['practical_hours'],
        ];
    }
}