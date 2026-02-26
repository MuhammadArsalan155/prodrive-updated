<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ProgressReport;

class ProgressReportCreatedNotification extends Notification
{
    use Queueable;

    protected $progressReport;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProgressReport $progressReport)
    {
        $this->progressReport = $progressReport;
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
            ->subject('New Progress Report Available')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('A new progress report has been created for your course.')
            ->line('Course: ' . $this->progressReport->course->course_name)
            ->line('Instructor: ' . $this->progressReport->instructor->instructor_name)
            ->when($this->progressReport->rating, function ($message) {
                return $message->line('Rating: ' . $this->progressReport->rating . '/5');
            })
            ->action('View Progress Report', url('/student/progress-reports/' . $this->progressReport->id))
            ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->progressReport->id,
            'course_name' => $this->progressReport->course->course_name,
            'instructor_name' => $this->progressReport->instructor->instructor_name,
            'rating' => $this->progressReport->rating,
            'created_at' => $this->progressReport->created_at,
        ];
    }
}