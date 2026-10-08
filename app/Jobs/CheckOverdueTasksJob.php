<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Notifications\ProjectDelayedNotification;

class CheckOverdueTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();
        $tasks = Task::where('status', '!=', 'Completed')
            ->where('end_time', '<', $now)
            ->where('is_overdue', false)
            ->get();

        foreach ($tasks as $task) {
            $task->status = 'Overdue';
            $task->is_overdue = true;
            $task->save();

            // إشعار العضو المكلف ومدير المشروع
            if ($task->assignedTo && $task->project && $task->project->projectManager) {
                Notification::send([$task->assignedTo, $task->project->projectManager], new TaskOverdueNotification($task));
            }

            // تحديث المهام التابعة (Blocked)
            foreach ($task->successorDependencies as $dependency) {
                $successor = $dependency->successorTask;
                if ($successor && $successor->status == 'Not Started') {
                    $successor->status = 'Blocked';
                    $successor->save();
                }
            }

            // إذا كانت المهمة حرجة (is_critical) وأثرت على المشروع
            if ($task->is_critical && $task->project && $task->project->company && $task->project->company->owner) {
                Notification::send($task->project->company->owner, new ProjectDelayedNotification($task->project, $task->title));
                // هنا يمكن اقتراح إعادة جدولة المشروع أو تنفيذ منطق إضافي
            }
        }
    }
} 