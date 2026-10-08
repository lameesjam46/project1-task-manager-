<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function updated(Task $task)
    {
        // تحديث حالة المهمة الأب إذا كانت المهمة فرعية
        if ($task->parent_task_id && $task->isDirty('status')) {
            $parent = $task->parentTask;
            if ($parent) {
                $subtasks = $parent->subtasks;
                $allCompleted = $subtasks->every(fn($t) => $t->status === 'Completed');
                $anyInProgress = $subtasks->contains(fn($t) => $t->status === 'In Progress');

                if ($allCompleted && $parent->status !== 'Completed') {
                    $parent->status = 'Completed';
                    $parent->save();
                } elseif ($anyInProgress && $parent->status !== 'In Progress') {
                    $parent->status = 'In Progress';
                    $parent->save();
                } elseif (!$anyInProgress && !$allCompleted && $parent->status !== 'Not Started') {
                    $parent->status = 'Not Started';
                    $parent->save();
                }
            }
        }

        // تحديث حالة المشروع عند تغيير حالة المهمة
        if ($task->isDirty('status') && $task->project_id) {
            $project = $task->project;
            if ($project) {
                $project->updateStatusBasedOnTasks();
            }
        }
    }

    public function created(Task $task)
    {
        // تحديث حالة المشروع عند إضافة مهمة جديدة
        if ($task->project_id) {
            $project = $task->project;
            if ($project) {
                $project->updateStatusBasedOnTasks();
            }
        }
    }

    public function deleted(Task $task)
    {
        // تحديث حالة المشروع عند حذف مهمة
        if ($task->project_id) {
            $project = $task->project;
            if ($project) {
                $project->updateStatusBasedOnTasks();
            }
        }
    }
} 