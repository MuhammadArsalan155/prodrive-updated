<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentProgressReportNotification extends Notification
{
    use Queueable;

    protected $progressData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $progressData)
    {
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
            ->subject('Your Course Progress Report')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Here\'s an update on your course progress:')
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
            ->action('View Dashboard', url('/student/dashboard'))
            ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'progress_percentage' => $this->progressData['progress_percentage'],
            'theory_hours' => $this->progressData['theory_hours'],
            'practical_hours' => $this->progressData['practical_hours'],
        ];
    }
}

