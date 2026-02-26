<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentReminderNotification extends Notification
{
    use Queueable;
    protected $installment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($installment)
    {
        $this->installment = $installment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Upcoming Installment Payment Reminder')
            ->greeting('Hello ' . $notifiable->first_name)
            ->line('This is a friendly reminder about your upcoming course installment.')
            ->line('Invoice: ' . $this->installment->invoice->invoice_number)
            ->line('Amount Due: $' . number_format($this->installment->amount, 2))
            ->line('Due Date: ' . $this->installment->due_date->format('F j, Y'))
            ->action('Pay Now', url('/student/pay-installment/' . $this->installment->id))
            ->line('Thank you for your prompt attention!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'installment_id' => $this->installment->id,
            'invoice_number' => $this->installment->invoice->invoice_number,
            'amount' => $this->installment->amount,
            'due_date' => $this->installment->due_date->format('F j, Y'),
            'student_name' => $notifiable->first_name,
        ];
    }
}