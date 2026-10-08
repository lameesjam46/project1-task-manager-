<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ملخص سريع للمهام
    public function summary()
    {
        $user = Auth::user();
        $projects = Project::where('project_manager_id', $user->id)->pluck('id');
        $tasks = Task::whereIn('project_id', $projects)->get();

        $summary = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'Completed')->count(),
            'overdue' => $tasks->where('status', 'Overdue')->count(),
            'critical' => $tasks->where('is_critical', true)->count(),
            'by_priority' => [
                'Low' => $tasks->where('priority', 'Low')->count(),
                'Medium' => $tasks->where('priority', 'Medium')->count(),
                'High' => $tasks->where('priority', 'High')->count(),
                'Critical' => $tasks->where('priority', 'Critical')->count(),
            ],
        ];
        return response()->json($summary);
    }

    // رسم بياني للمسار الحرج (Critical Path) وتقدم المشروع
    public function criticalPath($projectId)
    {
        $project = Project::findOrFail($projectId);
        $tasks = Task::where('project_id', $projectId)->get();
        // المهام الحرجة: is_critical = true
        $criticalTasks = $tasks->where('is_critical', true)->values();
        // نسبة التقدم في المسار الحرج
        $completed = $criticalTasks->where('status', 'Completed')->count();
        $progress = $criticalTasks->count() > 0 ? round(($completed / $criticalTasks->count()) * 100, 2) : 0;
        // ترتيب المهام الحرجة حسب البداية
        $criticalTasks = $criticalTasks->sortBy('start_time')->values();
        return response()->json([
            'critical_path' => $criticalTasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'start_time' => $task->start_time,
                    'end_time' => $task->end_time,
                ];
            }),
            'progress' => $progress,
        ]);
    }
} 