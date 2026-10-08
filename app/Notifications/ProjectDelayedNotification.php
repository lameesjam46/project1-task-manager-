<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Project;

class ProjectDelayedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;
    public $delayedTaskTitle;

    public function __construct(Project $project, $delayedTaskTitle)
    {
        $this->project = $project;
        $this->delayedTaskTitle = $delayedTaskTitle;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تنبيه: تأخر في المشروع')
            ->line('المشروع "' . $this->project->title . '" تأخر بسبب المهمة الحرجة: "' . $this->delayedTaskTitle . '".')
            ->action('عرض المشروع', url('/projects/' . $this->project->id))
            ->line('يرجى مراجعة الجدول الزمني للمشروع واتخاذ الإجراء المناسب.');
    }

    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'delayed_task' => $this->delayedTaskTitle,
        ];
    }
} 