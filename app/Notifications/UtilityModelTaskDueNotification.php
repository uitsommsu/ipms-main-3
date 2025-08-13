<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\UtilityModelTask;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UtilityModelTaskDueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected UtilityModelTask $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Due Soon: ' . $this->task->title)
            ->line('Utility Model Application: ' . $this->task->utilityModel->title)
            ->line('The following task is due in 2 days:')
            ->line('Task: ' . $this->task->title)
            ->line('Due Date: ' . $this->task->due_at->format('F j, Y'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
