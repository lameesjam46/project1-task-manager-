<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Task;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تنبيه: مهمة متأخرة')
            ->line('المهمة "' . $this->task->title . '" تجاوزت وقتها المحدد ولم تكتمل.')
            ->action('عرض المهمة', url('/tasks/' . $this->task->id))
            ->line('يرجى اتخاذ الإجراء المناسب.');
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'status' => $this->task->status,
            'end_time' => $this->task->end_time,
        ];
    }
} 