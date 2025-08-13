<?php

namespace App\Notifications;

use App\Models\PatentTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatentTaskDueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected PatentTask $task)
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
            ->line('Patent Application: ' . $this->task->patent->invention)
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

            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_at' => $this->task->due_at,

        ];
    }
}
