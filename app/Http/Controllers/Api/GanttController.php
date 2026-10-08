<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    /**
     * Return Gantt data for a specific project as JSON.
     */
    public function project(Request $request, int $projectId): JsonResponse
    {
        $project = Project::with('tasks')->findOrFail($projectId);

        // Eager-load needed relations for tasks
        $tasks = Task::with([
                'assignedTo:id,name',
                'predecessorDependencies',
                'successorDependencies',
            ])
            ->where('project_id', $projectId)
            ->get();

        $dependencyTypeMap = [
            'FS' => 0, // Finish-to-Start
            'SS' => 1, // Start-to-Start
            'FF' => 2, // Finish-to-Finish
            'SF' => 3, // Start-to-Finish
        ];

        $taskItems = $tasks->map(function (Task $task) {
            // start_time/end_time are cast to Carbon|null by the model
            $start = $task->start_time; // Carbon|null
            $end = $task->end_time;     // Carbon|null
            $durationMinutes = null;
            $isMilestone = false;

            if ($start && $end) {
                $durationMinutes = $end->diffInMinutes($start, false);
                $isMilestone = ($durationMinutes === 0);
            }

            $progress = match ($task->status) {
                'Completed' => 1.0,
                'In Progress' => 0.5,
                default => 0.0,
            };

            return [
                'id' => $task->id,
                'title' => $task->title ?? $task->name ?? ('Task #' . $task->id),
                'start' => $start ? $start->toIso8601String() : null,
                'end' => $end ? $end->toIso8601String() : null,
                'duration_minutes' => $durationMinutes,
                'status' => $task->status,
                'priority' => $task->priority ?? null,
                'parent' => $task->parent_task_id,
                'assigned_to' => $task->assigned_to,
                'assigned_to_name' => optional($task->assignedTo)->name,
                'is_milestone' => $isMilestone,
                'progress' => $progress,
            ];
        })->values();

        // Build dependencies (unique list)
        $links = [];
        foreach ($tasks as $task) {
            foreach ($task->predecessorDependencies as $dep) {
                $type = $dep->dependency_type;
                $links[] = [
                    'id' => $dep->id,
                    'source' => $dep->predecessor_task_id,
                    'target' => $dep->successor_task_id,
                    'type' => $type,
                    'type_code' => $dependencyTypeMap[$type] ?? null,
                ];
            }
        }

        // Project start/end derived from min/max of tasks when available
        $projectStart = $tasks->min(fn ($t) => $t->start_time) ?: null;
        $projectEnd = $tasks->max(fn ($t) => $t->end_time) ?: null;

        // If nested requested, build hierarchical tree by parent_task_id
        $isNested = $request->boolean('nested');
        $tasksPayload = $taskItems;

        if ($isNested) {
            // Build map of id => node with children
            $nodes = [];
            foreach ($taskItems as $item) {
                $item['children'] = [];
                $nodes[$item['id']] = $item;
            }

            $roots = [];
            foreach ($nodes as $id => $node) {
                $parentId = $node['parent'] ?? null;
                if ($parentId && isset($nodes[$parentId])) {
                    $nodes[$parentId]['children'][] = &$nodes[$id];
                } else {
                    $roots[] = &$nodes[$id];
                }
            }

            $tasksPayload = array_values($roots);
        }

        return response()->json([
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status,
                'start' => $projectStart ? $projectStart->toIso8601String() : null,
                'end' => $projectEnd ? $projectEnd->toIso8601String() : null,
            ],
            'tasks' => $tasksPayload,
            'dependencies' => array_values($links),
            'meta' => [
                'dependency_type_map' => $dependencyTypeMap,
                'generated_at' => now()->toIso8601String(),
                'hierarchical' => $isNested,
            ],
        ]);
    }
}
