<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function addToFavorites($taskID){
        Task::query()->findOrFail($taskID);
        Auth::user()->favoriteTasks()->syncWithoutDetaching($taskID);
        return response()->json(['message'=>'Task added To Favorite'],200);

    }
    public function removeFormFavorites($taskID)
    {
        Task::query()->findOrFail($taskID);
        Auth::user()->favoriteTasks()->detach($taskID);
        return response()->json(['message'=>'Task remove From Favorite'],200);

    }
    public function getFavoritesTask()
    {
        $tasksFavorite=Auth::user()->favoriteTasks;
        return response()->json($tasksFavorite,200);

    }



















    public function  getCategoryTasks($categoryId){
        $tasks=Category::query()->findOrFail($categoryId)->tasks;
        return response()->json($tasks,200);
    }



    public function addCategoriesToTask(Request $request,$taskId){
        $task=Task::query()->findOrFail($taskId);
        $task->categories()->attach($request->category_id);
        return response()->json('category attached successfully',200);
    }
    public function getCategoriesToTask($taskId){
        $categories=Task::query()->findOrFail($taskId)->categories;
        return response()->json($categories,200);

    }


    public function getTasksUser($id){
        $user=Task::query()->findOrFail($id)->user;
        return response()->json($user,200);

    }

    //   Admin

    public function getAllTasks(){
        $tasks=Task::all();
        return response()->json($tasks,200);
    }

    public function getTasksByPriority(){
        $tasks=Auth::user()->tasks()->orderByRaw("FIELD(priority,'Low','Medium','High','Critical')")->get();
        return response()->json($tasks,200);
    }




    public function index(){
        $tasks = Auth::user()->tasks()->with(['project', 'assignedTo'])->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $projects = Project::all();
        $users = User::all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // جلب المشروع الذي يديره المستخدم الحالي فقط
            $project = Project::where('id', $validatedData['project_id'])
                ->where('project_manager_id', auth()->id())
                ->first();

            if (!$project) {
                return response()->json([
                    'message' => 'Unauthorized - Only the project manager can create tasks for this project.'
                ], 403);
            }

            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'priority' => $validatedData['priority'],
                'project_id' => $project->id, // ربط المهمة بمشروع يديره المستخدم الحالي
                'status' => $validatedData['status'] ?? 'Not Started',
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'due_date' => $validatedData['due_date'] ?? null
            ]);

            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = Task::with('project')->findOrFail($id);

            // Check if the task has a project and if the project belongs to the user
            if (!$task->project || $task->project->project_manager_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized - You can only update tasks in your projects'
                ], 403);
            }

            $validatedData = $request->validated();

            // Update the task with validated data
            $task->update($validatedData);

            // Refresh the model to get the latest data
            $task->refresh();

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id){
        $task=Task::query()->findOrFail($id);
        return response()->json($task,200);
    }

    public function destroy($id){
        try {
        $task=Task::query()->findOrFail($id);
        $task->delete();
        return response()->json('task deleted successfully',200);
    }catch (ModelNotFoundException $exception){
            return response()->json([
                'error'=>' Task Not Fount',
                'details'=>$exception->getMessage()
            ],404);
        }
    }


    public function getTasksForProjectByPriority($projectId)
    {
        $tasks = Task::where('project_id', $projectId)
            ->orderByRaw("FIELD(priority, 'Low', 'Medium', 'High', 'Critical')")
            ->get();

        return response()->json($tasks, 200);
    }



    public function assignEmployeesToTask(Request $request, $taskId)
    {
        try {
            $task = Task::with('project')->findOrFail($taskId);

            // Validate the request - accept either a single user_id or an array of user_ids
            $request->validate([
                'user_id' => 'sometimes|exists:users,id',
                'user_ids' => 'sometimes|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            // Get the project's team members
            $projectTeamMembers = $task->project->teamMembers->pluck('id')->toArray();

            // Handle both single user and multiple users cases
            $userIds = [];
            if ($request->has('user_id')) {
                $userIds[] = $request->user_id;
            }
            if ($request->has('user_ids')) {
                $userIds = array_merge($userIds, $request->user_ids);
            }

            // Remove duplicates
            $userIds = array_unique($userIds);

            // Filter out any user IDs that are not part of the project team
            $validUserIds = array_intersect($userIds, $projectTeamMembers);

            if (empty($validUserIds)) {
                return response()->json([
                    'message' => 'None of the provided users are part of the project team'
                ], 400);
            }

            // Always use syncWithoutDetaching to add all users without removing existing assignments
            $task->assignedUsers()->syncWithoutDetaching($validUserIds);

            return response()->json([
                'message' => 'Employees assigned to task successfully',
                'assigned_users' => $task->assignedUsers
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign employees to task',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function storeSubtask(StoreSubtaskRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $parentTask = Task::findOrFail($validatedData['parent_task_id']);

            $subtask = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'priority' => $validatedData['priority'],
                'project_id' => $parentTask->project_id,
                'parent_task_id' => $validatedData['parent_task_id'],
                'assigned_to' => $validatedData['assigned_to'],
                'is_subtask' => true,
                'status' => $validatedData['status'] ?? 'Not Started',
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time']
            ]);

            return response()->json([
                'message' => 'Subtask created successfully',
                'data' => $subtask
            ], 201);
        } catch (\Exception $e) {
                return response()->json([
                'message' => 'Failed to create subtask',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // إضافة مهمة مع اعتماديات
    public function createTaskWithDependencies(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'project_id' => 'required|exists:projects,id',
            'status' => 'sometimes|in:Not Started,In Progress,Completed',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'assigned_to' => 'nullable|exists:users,id',
            'dependencies' => 'nullable|array',
            'dependencies.*.predecessor_task_id' => 'required_with:dependencies|exists:tasks,id',
            'dependencies.*.dependency_type' => 'required_with:dependencies|in:FS,SS,FF,SF',
        ]);

        // Validate that assigned_to (if provided) is an active employee of the project's company and not the project manager
        if (!empty($validated['assigned_to'])) {
            $project = Project::find($validated['project_id']);
            if (!$project) {
                return response()->json(['message' => 'Invalid project.'], 422);
            }
            $companyId = $project->company_id;
            $isAssignable = User::query()
                ->where('id', $validated['assigned_to'])
                ->where('role', 'employee')
                ->where('active', true)
                ->whereHas('companies', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->where('id', '!=', $project->project_manager_id)
                ->exists();
            if (!$isAssignable) {
                return response()->json(['message' => 'The selected employee is not eligible for this project.'], 422);
            }
        }

        foreach ($validated['dependencies'] ?? [] as $dep) {
            $predecessor = Task::find($dep['predecessor_task_id']);
            if (!$predecessor) {
                return response()->json(['message' => 'المهمة السابقة غير موجودة.'], 422);
            }
            if ($dep['predecessor_task_id'] == null) {
                return response()->json(['message' => 'لا يمكن للمهمة أن تعتمد على نفسها.'], 422);
            }
            if ($this->hasCircularDependency($dep['predecessor_task_id'], null)) {
                return response()->json(['message' => 'لا يمكن إضافة اعتماد يؤدي إلى حلقة بين المهام.'], 422);
            }
            // تحقق من نوع المهمة
            if (empty($validated['parent_task_id'])) {
                // مهمة رئيسية: تعتمد فقط على مهام رئيسية بنفس المشروع
                if ($predecessor->parent_task_id !== null || $predecessor->project_id != $validated['project_id']) {
                    return response()->json(['message' => 'المهام الرئيسية يمكنها الاعتماد فقط على مهام رئيسية بنفس المشروع.'], 422);
                }
            } else {
                // مهمة فرعية: تعتمد فقط على مهام فرعية بنفس المهمة الرئيسية
                if ($predecessor->parent_task_id != $validated['parent_task_id']) {
                    return response()->json(['message' => 'المهام الفرعية يمكنها الاعتماد فقط على مهام بنفس المهمة الرئيسية.'], 422);
                }
            }
            switch ($dep['dependency_type']) {
                case 'FS':
                    if ($validated['start_time'] < $predecessor->end_time) {
                        return response()->json(['message' => 'نوع الاعتمادية FS: يجب أن يكون وقت بدء المهمة بعد أو يساوي وقت انتهاء المهمة السابقة.'], 422);
                    }
                    break;
                case 'SS':
                    if ($validated['start_time'] < $predecessor->start_time) {
                        return response()->json(['message' => 'نوع الاعتمادية SS: يجب أن يكون وقت بدء المهمة بعد أو يساوي وقت بدء المهمة السابقة.'], 422);
                    }
                    break;
                case 'FF':
                    if ($validated['end_time'] < $predecessor->end_time) {
                        return response()->json(['message' => 'نوع الاعتمادية FF: يجب أن يكون وقت انتهاء المهمة بعد أو يساوي وقت انتهاء المهمة السابقة.'], 422);
                    }
                    break;
                case 'SF':
                    if ($validated['end_time'] < $predecessor->start_time) {
                        return response()->json(['message' => 'نوع الاعتمادية SF: يجب أن يكون وقت انتهاء المهمة بعد أو يساوي وقت بدء المهمة السابقة.'], 422);
                    }
                    break;
            }
        }

        // تحديد إذا كانت المهمة فرعية أو أساسية بدون مهام فرعية
        $isSubtask = !empty($validated['parent_task_id']);
        $assignedTo = null;
        if ($isSubtask) {
            $assignedTo = $validated['assigned_to'] ?? null;
        } else {
            // مهمة أساسية: نسمح بتخصيص موظف فقط إذا لم يكن لها مهام فرعية
            // (عند الإنشاء لن يكون لها مهام فرعية بعد)
            $assignedTo = $validated['assigned_to'] ?? null;
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'project_id' => $validated['project_id'],
            'status' => $validated['status'] ?? 'Not Started',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'parent_task_id' => $validated['parent_task_id'] ?? null,
            'is_subtask' => $isSubtask,
            'assigned_to' => $assignedTo,
        ]);

        foreach ($validated['dependencies'] ?? [] as $dep) {
             TaskDependency::create([
                'predecessor_task_id' => $dep['predecessor_task_id'],
                'successor_task_id' => $task->id,
                'dependency_type' => $dep['dependency_type'],
            ]);
        }

        return response()->json([
            'message' => 'Task created successfully with dependencies',
            // 'task' => $task->load('predecessorDependencies.predecessorTask'),
        ], 201);
    }

    // تحديث مهمة مع اعتماديات
    public function updateTaskWithDependencies(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|in:Low,Medium,High,Critical',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'assigned_to' => 'nullable|exists:users,id',
            'dependencies' => 'nullable|array',
            'dependencies.*.predecessor_task_id' => 'required_with:dependencies|exists:tasks,id',
            'dependencies.*.dependency_type' => 'required_with:dependencies|in:FS,SS,FF,SF',
        ]);

        // If assigned_to is present in the request, validate eligibility: active employee in the same company and not the project manager
        if ($request->has('assigned_to')) {
            $assignedTo = $validated['assigned_to'] ?? null;
            if (!empty($assignedTo)) {
                $project = Project::find($task->project_id);
                if (!$project) {
                    return response()->json(['message' => 'Invalid project for this task.'], 422);
                }
                $companyId = $project->company_id;
                $isAssignable = User::query()
                    ->where('id', $assignedTo)
                    ->where('role', 'employee')
                    ->where('active', true)
                    ->whereHas('companies', function ($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    })
                    ->where('id', '!=', $project->project_manager_id)
                    ->exists();
                if (!$isAssignable) {
                    return response()->json(['message' => 'The selected employee is not eligible for this project.'], 422);
                }
            }
            // Defer setting assigned_to until just before save
        }

        foreach ($validated['dependencies'] ?? [] as $dep) {
            $predecessor = Task::find($dep['predecessor_task_id']);
            if (!$predecessor) {
                return response()->json(['message' => 'المهمة السابقة غير موجودة.'], 422);
            }
            if ($dep['predecessor_task_id'] == $task->id) {
                return response()->json(['message' => 'لا يمكن للمهمة أن تعتمد على نفسها.'], 422);
            }
            if ($this->hasCircularDependency($dep['predecessor_task_id'], $task->id)) {
                return response()->json(['message' => 'لا يمكن إضافة اعتماد يؤدي إلى حلقة بين المهام.'], 422);
            }
            // تحقق من نوع المهمة
            if (empty($validated['parent_task_id'] ?? $task->parent_task_id)) {
                // مهمة رئيسية: تعتمد فقط على مهام رئيسية بنفس المشروع
                $projectId = $validated['project_id'] ?? $task->project_id;
                if ($predecessor->parent_task_id !== null || $predecessor->project_id != $projectId) {
                    return response()->json(['message' => 'المهام الرئيسية يمكنها الاعتماد فقط على مهام رئيسية بنفس المشروع.'], 422);
                }
            } else {
                // مهمة فرعية: تعتمد فقط على مهام فرعية بنفس المهمة الرئيسية
                $parentId = $validated['parent_task_id'] ?? $task->parent_task_id;
                if ($predecessor->parent_task_id != $parentId) {
                    return response()->json(['message' => 'المهام الفرعية يمكنها الاعتماد فقط على مهام بنفس المهمة الرئيسية.'], 422);
                }
            }
            $start_time = $validated['start_time'] ?? $task->start_time;
            $end_time = $validated['end_time'] ?? $task->end_time;
            switch ($dep['dependency_type']) {
                case 'FS':
                    if ($start_time < $predecessor->end_time) {
                        return response()->json(['message' => 'نوع الاعتمادية FS: يجب أن يكون وقت بدء المهمة بعد أو يساوي وقت انتهاء المهمة السابقة.'], 422);
                    }
                    break;
                case 'SS':
                    if ($start_time < $predecessor->start_time) {
                        return response()->json(['message' => 'نوع الاعتمادية SS: يجب أن يكون وقت بدء المهمة بعد أو يساوي وقت بدء المهمة السابقة.'], 422);
                    }
                    break;
                case 'FF':
                    if ($end_time < $predecessor->end_time) {
                        return response()->json(['message' => 'نوع الاعتمادية FF: يجب أن يكون وقت انتهاء المهمة بعد أو يساوي وقت انتهاء المهمة السابقة.'], 422);
                    }
                    break;
                case 'SF':
                    if ($end_time < $predecessor->start_time) {
                        return response()->json(['message' => 'نوع الاعتمادية SF: يجب أن يكون وقت انتهاء المهمة بعد أو يساوي وقت بدء المهمة السابقة.'], 422);
                    }
                    break;
            }
        }

        $task->fill([
            'title' => $validated['title'] ?? $task->title,
            'description' => $validated['description'] ?? $task->description,
            'priority' => $validated['priority'] ?? $task->priority,
            // 'project_id' => $validated['project_id'] ?? $task->project_id,
            // 'status' => $validated['status'] ?? $task->status,
            'start_time' => $validated['start_time'] ?? $task->start_time,
            'end_time' => $validated['end_time'] ?? $task->end_time,
            // 'parent_task_id' => $validated['parent_task_id'] ?? $task->parent_task_id,
            // 'is_subtask' => ($validated['parent_task_id'] ?? $task->parent_task_id) ? true : false,
        ]);
        // Apply assigned_to if explicitly provided (can be null to clear)
        if ($request->has('assigned_to')) {
            $task->assigned_to = $validated['assigned_to'] ?? null;
        }
        $task->save();

        // Sync dependencies behavior:
        // - If sync_dependencies flag is true (from web edit form), always delete all and recreate from provided list (or none)
        // - Else, only modify dependencies when an array is provided
        if ($request->boolean('sync_dependencies')) {
            TaskDependency::where('successor_task_id', $task->id)->delete();
            foreach (($validated['dependencies'] ?? []) as $dep) {
                TaskDependency::create([
                    'predecessor_task_id' => $dep['predecessor_task_id'],
                    'successor_task_id' => $task->id,
                    'dependency_type' => $dep['dependency_type'],
                ]);
            }
        } elseif (isset($validated['dependencies'])) {
            TaskDependency::where('successor_task_id', $task->id)->delete();
            foreach ($validated['dependencies'] as $dep) {
                TaskDependency::create([
                    'predecessor_task_id' => $dep['predecessor_task_id'],
                    'successor_task_id' => $task->id,
                    'dependency_type' => $dep['dependency_type'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Task updated successfully with dependencies'
            // 'task' => $task->load('predecessorDependencies.predecessorTask'),
        ], 200);
    }

    // حذف جميع اعتماديات مهمة
    public function deleteTaskDependencies($id)
    {
        $task = Task::findOrFail($id);
        TaskDependency::where('successor_task_id', $task->id)->delete();
        return response()->json(['message' => 'All dependencies deleted for this task.']);
    }

    // دالة منع الحلقات
    private function hasCircularDependency($predecessorId, $successorId)
    {
        if ($predecessorId == $successorId && $successorId !== null) return true;
        $visited = $successorId ? [$successorId] : [];
        $stack = [$predecessorId];
        while ($stack) {
            $current = array_pop($stack);
            if ($successorId !== null && $current == $successorId) return true;
            $next = TaskDependency::where('predecessor_task_id', $current)->pluck('successor_task_id');
            foreach ($next as $n) {
                if (!in_array($n, $visited)) {
                    $visited[] = $n;
                    $stack[] = $n;
                }
            }
        }
        return false;
    }

    public function getAvailableDependencies($taskId)
    {
        $task = Task::findOrFail($taskId);


        if ($task->parent_task_id) {
            // Subtask: فقط المهام الفرعية بنفس المهمة الرئيسية
            $candidates = Task::where('parent_task_id', $task->parent_task_id)
                ->where('id', '!=', $task->id)
                ->get();
        } else {
            // Main task: فقط المهام الرئيسية بنفس المشروع
            $candidates = Task::where('project_id', $task->project_id)
                ->whereNull('parent_task_id')
                ->where('id', '!=', $task->id)
                ->get();
        }

        $result = [];
        foreach ($candidates as $candidate) {
            $types = [];
            // FS: Finish-to-Start
            if ($task->start_time >= $candidate->end_time) {
                $types[] = 'FS';
            }
            // SS: Start-to-Start
            if ($task->start_time >= $candidate->start_time) {
                $types[] = 'SS';
            }
            // FF: Finish-to-Finish
            if ($task->end_time >= $candidate->end_time) {
                $types[] = 'FF';
            }
            // SF: Start-to-Finish
            if ($task->end_time >= $candidate->start_time) {
                $types[] = 'SF';
            }
            if (!empty($types)) {
                $result[] = [
                    'task_id' => $candidate->id,
                    'title' => $candidate->title,
                    'available_dependency_types' => $types,
                    'start_time' => $candidate->start_time,
                    'end_time' => $candidate->end_time,
                ];
            }
        }

        return response()->json([
            'available_dependencies' => $result
        ]);
    }

    /**
     * جلب مهمة مع اعتمادياتها
     */
    public function getTaskWithDependencies($id)
    {
        try {
            $task = Task::with('predecessorDependencies.predecessorTask')->findOrFail($id);

            return response()->json([
                'task' => $task
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve task with dependencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغيير حالة المهمة مع مراعاة الاعتماديات والأولويات
     * ويسمح فقط للعضو المكلف بتغيير الحالة
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Not Started,In Progress,Completed',
        ]);

        $task = Task::with(['subtasks', 'predecessorDependencies.predecessorTask', 'successorDependencies.successorTask', 'project.projectManager'])->findOrFail($id);
        $user = Auth::user();
        $newStatus = $request->status;
        $now = Carbon::now();

        // منع تغيير حالة المهمة الرئيسية يدويًا إذا كان لديها مهام فرعية
        if ($task->subtasks && $task->subtasks->count() > 0) {
            return response()->json([
                'message' => 'لا يمكن تغيير حالة المهمة الرئيسية يدويًا إذا كان لديها مهام فرعية. الحالة تتغير تلقائيًا بناءً على حالة المهام الفرعية.'
            ], 403);
        }

        // تحقق أن المستخدم هو العضو المكلف فقط
        if ($task->assigned_to !== $user->id) {
            return response()->json([
                'message' => 'غير مصرح لك بتغيير حالة هذه المهمة. فقط العضو المكلف يمكنه ذلك.'
            ], 403);
        }

        // تحقق من الوقت الحالي بالنسبة للبداية
        if ($newStatus == 'In Progress' && $now->lt(Carbon::parse($task->start_time))) {
            return response()->json(['message' => 'لا يمكن بدء المهمة قبل وقت بدايتها المحدد.'], 422);
        }

        // تحقق من الاعتماديات
        foreach ($task->predecessorDependencies as $dependency) {
            $predecessor = $dependency->predecessorTask;
            if (!$predecessor) continue;
            switch ($dependency->dependency_type) {
                case 'FS':
                    if ($newStatus != 'Not Started' && $predecessor->status != 'Completed') {
                        return response()->json(['message' => 'لا يمكن بدء أو إنهاء المهمة قبل اكتمال المهمة السابقة (FS).'], 422);
                    }
                    break;
                case 'SS':
                    if ($newStatus == 'In Progress' && $predecessor->status == 'Not Started') {
                        return response()->json(['message' => 'لا يمكن بدء المهمة قبل بدء المهمة السابقة (SS).'], 422);
                    }
                    break;
                case 'FF':
                    if ($newStatus == 'Completed' && $predecessor->status != 'Completed') {
                        return response()->json(['message' => 'لا يمكن إنهاء المهمة قبل إنهاء المهمة السابقة (FF).'], 422);
                    }
                    break;
                case 'SF':
                    if ($newStatus == 'Completed' && $predecessor->status == 'Not Started') {
                        return response()->json(['message' => 'لا يمكن إنهاء المهمة قبل بدء المهمة السابقة (SF).'], 422);
                    }
                    break;
            }
        }

        // تحقق من الوقت الحالي بالنسبة للنهاية
        $isOverdue = false;
        if ($now->gt(Carbon::parse($task->end_time)) && $newStatus != 'Completed') {
            $isOverdue = true;
            // إرسال إشعار للعضو المكلف ومدير المشروع
            Notification::send([$task->assignedTo, $task->project->projectManager], new TaskOverdueNotification($task));
        }

        // (اختياري) تحقق من الأولويات
        if ($newStatus == 'Completed') {
            $higherPriorityTasks = Task::where('project_id', $task->project_id)
                ->where('priority', '>', $task->priority)
                ->where('status', '!=', 'Completed')
                ->count();
            if ($higherPriorityTasks > 0) {
                return response()->json(['message' => 'هناك مهام ذات أولوية أعلى لم تكتمل بعد.'], 422);
            }
        }

        // تحديث الحالة
        $task->status = $isOverdue ? 'Overdue' : $newStatus;
        $task->save();

        // تحديث حالة المهام التابعة تلقائياً إذا لزم الأمر (Blocked)
        if ($isOverdue) {
            foreach ($task->successorDependencies as $dependency) {
                $successor = $dependency->successorTask;
                if ($successor && $successor->status == 'Not Started') {
                    $successor->status = 'Blocked';
                    $successor->save();
                }
            }
        }

        // (مبسط) تحقق من المسار الحرج: إذا كانت المهمة متأخرة وفي المسار الحرج، أرسل إشعار خاص
        // هنا سنعتبر أن كل مهمة ليس لها predecessor هي بداية مسار، ويمكنك تطوير منطق المسار الحرج لاحقاً
        $isCritical = $task->predecessorDependencies->count() == 0;
        if ($isOverdue && $isCritical) {
            // إشعار خاص لمدير المشروع
            Notification::send($task->project->projectManager, new TaskOverdueNotification($task));
        }

        return response()->json([
            'message' => $isOverdue ? 'تم وضع المهمة كمتأخرة وتم إشعار المعنيين.' : 'تم تحديث حالة المهمة بنجاح',
           // 'task' => $task
        ]);
    }

    /**
     * إعادة فتح مهمة متأخرة أو منحها وقت إضافي
     * فقط العضو المكلف أو مدير المشروع يمكنه ذلك
     */
    public function reopenOrExtend(Request $request, $id)
    {
        $request->validate([
            'extra_minutes' => 'nullable|integer|min:1',
        ]);

        $task = Task::with(['successorDependencies.successorTask', 'project.projectManager'])->findOrFail($id);
        $user = Auth::user();

        // السماح فقط للعضو المكلف أو مدير المشروع
        $isManager = $task->project && $task->project->projectManager && $user->id == $task->project->projectManager->id;
        if ($task->assigned_to !== $user->id && !$isManager) {
            return response()->json([
                'message' => 'غير مصرح لك بإعادة فتح أو تمديد هذه المهمة.'
            ], 403);
        }

        if ($task->status !== 'Overdue') {
            return response()->json([
                'message' => 'لا يمكن إعادة فتح أو تمديد مهمة ليست متأخرة.'
            ], 422);
        }

        // إعادة فتح المهمة
        $task->status = 'In Progress';
        $task->is_overdue = false;

        // منح وقت إضافي إذا تم طلب ذلك
        if ($request->filled('extra_minutes')) {
            $extraMinutes = (int) $request->extra_minutes;
            $task->end_time = \Carbon\Carbon::parse($task->end_time)->addMinutes($extraMinutes);
        }
        $task->save();

        // تحديث المهام التابعة (إزالة Blocked إذا لزم الأمر)
        foreach ($task->successorDependencies as $dependency) {
            $successor = $dependency->successorTask;
            if ($successor && $successor->status == 'Blocked') {
                $successor->status = 'Not Started';
                $successor->save();
            }
        }

        return response()->json([
            'message' => 'تم إعادة فتح المهمة' . ($request->filled('extra_minutes') ? ' ومنحها وقت إضافي.' : '.'),
            'task' => $task
        ]);
    }

    /**
     * إعادة جدولة المهام التابعة تلقائياً عند تمديد مهمة متأخرة (للاعتماديات FS)
     * فقط العضو المكلف أو مدير المشروع يمكنه ذلك
     */
    public function rescheduleSuccessors(Request $request, $id)
    {
        $request->validate([
            'extra_minutes' => 'required|integer|min:1',
        ]);

        $task = Task::with(['successorDependencies.successorTask', 'project.projectManager'])->findOrFail($id);
        $user = Auth::user();
        $isManager = $task->project && $task->project->projectManager && $user->id == $task->project->projectManager->id;
        if ($task->assigned_to !== $user->id && !$isManager) {
            return response()->json([
                'message' => 'غير مصرح لك بإعادة جدولة المهام التابعة لهذه المهمة.'
            ], 403);
        }

        if ($task->status !== 'Overdue') {
            return response()->json([
                'message' => 'لا يمكن إعادة جدولة المهام التابعة إلا لمهمة متأخرة.'
            ], 422);
        }

        // منح وقت إضافي للمهمة الحالية
        $extraMinutes = (int) $request->extra_minutes;
        $oldEnd = \Carbon\Carbon::parse($task->end_time);
        $task->end_time = $oldEnd->copy()->addMinutes($extraMinutes);
        $task->status = 'In Progress';
        $task->is_overdue = false;
        $task->save();

        $shift = $extraMinutes;
        $updated = [];
        // إعادة جدولة المهام التابعة (FS فقط)
        foreach ($task->successorDependencies as $dependency) {
            if ($dependency->dependency_type === 'FS') {
                $successor = $dependency->successorTask;
                if ($successor) {
                    $succStart = \Carbon\Carbon::parse($successor->start_time)->addMinutes($shift);
                    $succEnd = \Carbon\Carbon::parse($successor->end_time)->addMinutes($shift);
                    $successor->start_time = $succStart;
                    $successor->end_time = $succEnd;
                    if ($successor->status == 'Blocked') {
                        $successor->status = 'Not Started';
                    }
                    $successor->save();
                    $updated[] = $successor->id;
                }
            }
        }

        return response()->json([
            'message' => 'تم إعادة جدولة المهام التابعة بنجاح.',
            'updated_successors' => $updated,
            'task' => $task
        ]);
    }

    /**
     * إعادة جدولة متسلسلة لجميع المهام المتأثرة مع إشعار الموظف المعني عند كل تحديث
     */
    public function rescheduleCascade(Request $request, $id)
    {
        $request->validate([
            'extra_minutes' => 'required|integer|min:1',
        ]);

        $extraMinutes = (int) $request->extra_minutes;

        $task = Task::with(['successorDependencies.successorTask', 'project.projectManager'])->findOrFail($id);
        $user = Auth::user();
        $isManager = $task->project && $task->project->projectManager && $user->id == $task->project->projectManager->id;
        if ($task->assigned_to !== $user->id && !$isManager) {
            return response()->json([
                'message' => 'غير مصرح لك بإعادة جدولة المهام التابعة لهذه المهمة.'
            ], 403);
        }

        if ($task->status !== 'Overdue') {
            return response()->json([
                'message' => 'لا يمكن إعادة جدولة المهام التابعة إلا لمهمة متأخرة.'
            ], 422);
        }

        // منح وقت إضافي للمهمة الحالية
        $oldEnd = Carbon::parse($task->end_time);
        $task->end_time = $oldEnd->copy()->addMinutes($extraMinutes);
        $task->status = 'In Progress';
        $task->is_overdue = false;
        $task->save();

        // إشعار الموظف المعني بالمهمة الحالية
        if ($task->assignedTo) {
             Notification::send($task->assignedTo, new TaskOverdueNotification($task));
        }

        $visited = [];
        $queue = [[$task, $extraMinutes]];
        $updated = [];

        while (!empty($queue)) {
            [$currentTask, $shift] = array_shift($queue);
            $shift = (int) $shift;
            foreach ($currentTask->successorDependencies as $dependency) {
                $successor = $dependency->successorTask;
                if (!$successor || in_array($successor->id, $visited)) continue;

                $visited[] = $successor->id;
                $changed = false;

                switch ($dependency->dependency_type) {
                    case 'FS':
                        $succStart = \Carbon\Carbon::parse($successor->start_time);
                        $succEnd = \Carbon\Carbon::parse($successor->end_time);
                        $minStart = \Carbon\Carbon::parse($currentTask->end_time);
                        if ($succStart->lt($minStart)) {
                            $diff = $minStart->diffInMinutes($succStart, false) * -1;
                            $successor->start_time = $minStart;
                            $successor->end_time = $succEnd->addMinutes($diff);
                            $changed = true;
                        }
                        break;
                    case 'SS':
                        $succStart = \Carbon\Carbon::parse($successor->start_time);
                        $minStart = \Carbon\Carbon::parse($currentTask->start_time);
                        if ($succStart->lt($minStart)) {
                            $diff = $minStart->diffInMinutes($succStart, false) * -1;
                            $successor->start_time = $minStart;
                            $successor->end_time = \Carbon\Carbon::parse($successor->end_time)->addMinutes($diff);
                            $changed = true;
                        }
                        break;
                    case 'FF':
                        $succEnd = Carbon::parse($successor->end_time);
                        $minEnd = Carbon::parse($currentTask->end_time);
                        if ($succEnd->lt($minEnd)) {
                            $diff = $minEnd->diffInMinutes($succEnd, false) * -1;
                            $successor->end_time = $minEnd;
                            $successor->start_time = Carbon::parse($successor->start_time)->addMinutes($diff);
                            $changed = true;
                        }
                        break;
                    case 'SF':
                        $succEnd = Carbon::parse($successor->end_time);
                        $minStart = Carbon::parse($currentTask->start_time);
                        if ($succEnd->lt($minStart)) {
                            $diff = $minStart->diffInMinutes($succEnd, false) * -1;
                            $successor->end_time = $minStart;
                            $successor->start_time = Carbon::parse($successor->start_time)->addMinutes($diff);
                            $changed = true;
                        }
                        break;
                }

                if ($changed) {
                    if ($successor->status == 'Blocked') {
                        $successor->status = 'Not Started';
                    }
                    $successor->save();
                    $updated[] = $successor->id;
                    // إشعار الموظف المعني بالمهمة التابعة
                    if ($successor->assignedTo) {
                        Notification::send($successor->assignedTo, new TaskOverdueNotification($successor));
                    }
                    // أضف المهمة التابعة إلى الطابور لإعادة جدولة المهام التابعة لها أيضاً
                    $queue[] = [$successor, $shift];
                }
            }
        }

        return response()->json([
            'message' => 'تمت إعادة جدولة جميع المهام المتأثرة بنجاح مع إشعار الموظفين المعنيين.',
            'updated_tasks' => $updated,
            'task' => $task
        ]);
    }

    /***
     * اعادة جدولة للمشروع كامل مع الحفاظ على الفترات الزمنية النسبية بين المهام
     */
   // ... existing code ...

   public function rescheduleProject(Request $request, $projectId)
   {
       $request->validate([
           'new_start_time' => 'required|date',
       ]);

       $project = Project::findOrFail($projectId);
       $tasks = Task::where('project_id', $projectId)
           ->with(['predecessorDependencies.predecessorTask', 'successorDependencies.successorTask', 'assignedTo'])
           ->get();

       if ($tasks->isEmpty()) {
           return response()->json([
               'message' => 'لا توجد مهام في هذا المشروع لإعادة جدولتها.'
           ], 404);
       }

       // حساب وقت بداية المشروع الأصلي (أول مهمة تبدأ)
       $originalProjectStart = $tasks->min('start_time');
       $originalProjectStart = Carbon::parse($originalProjectStart);

       // الوقت الجديد لبداية المشروع
       $newProjectStart = Carbon::parse($request->new_start_time);

       // حساب الفرق الزمني بين الوقت الجديد والأصلي
       $timeShift = $newProjectStart->diffInMinutes($originalProjectStart, false);

       // بناء خريطة الاعتماديات
       $taskMap = $tasks->keyBy('id');
       $dependencies = [];
       foreach ($tasks as $task) {
           $dependencies[$task->id] = $task->predecessorDependencies->pluck('predecessor_task_id')->toArray();
       }

       // ترتيب المهام حسب الاعتماديات (Topological Sort)
       $sorted = [];
       $visited = [];
       $visit = function($id) use (&$visit, &$visited, &$sorted, &$dependencies) {
           if (isset($visited[$id])) return;
           $visited[$id] = true;
           // إذا لم تكن هناك إدخالات اعتماد لهذه المهمة، أضفها مباشرةً
           if (!isset($dependencies[$id])) {
               $sorted[] = $id;
               return;
           }
           foreach ($dependencies[$id] as $depId) {
               // قد تشير الاعتماديات إلى مهام خارج مجموعة هذا المشروع؛ تجاهلها بأمان
               if (isset($dependencies[$depId])) {
                   $visit($depId);
               }
           }
           $sorted[] = $id;
       };
       foreach ($taskMap as $id => $task) {
           $visit($id);
       }

       // إعادة جدولة المهام مع الحفاظ على الفترات النسبية
       $startTimes = [];
       $updatedTasks = [];

       foreach ($sorted as $taskId) {
           $task = $taskMap[$taskId];

           // حساب الوقت الأصلي النسبي للمهمة من بداية المشروع
           $originalTaskStart = Carbon::parse($task->start_time);
           // الفرق من بداية المشروع الأصلي إلى بداية المهمة (قيمة موجبة للمهمات بعد البداية)
           $relativeStartMinutes = $originalProjectStart->diffInMinutes($originalTaskStart, false);

           // حساب الوقت الجديد للمهمة مع الحفاظ على الفترة النسبية
           $newTaskStart = $newProjectStart->copy()->addMinutes($relativeStartMinutes);

           // حساب مدة المهمة
           $taskDuration = Carbon::parse($task->end_time)->diffInMinutes(Carbon::parse($task->start_time));

           // التحقق من الاعتماديات
           $maxStart = $newTaskStart->copy();
           foreach ($task->predecessorDependencies as $dep) {
               $predecessor = $dep->predecessorTask;
               if (!$predecessor) continue;

               // التحقق من وجود المهمة السابقة في startTimes
               if (!isset($startTimes[$predecessor->id])) {
                   // إذا لم يتم تحديث المهمة السابقة بعد، استخدم الوقت الأصلي مع الإزاحة
                   $originalPredEnd = Carbon::parse($predecessor->end_time);
                   // الفرق من بداية المشروع إلى نهاية المهمة السابقة
                   $relativePredEndMinutes = $originalProjectStart->diffInMinutes($originalPredEnd, false);
                   $predecessorEnd = $newProjectStart->copy()->addMinutes($relativePredEndMinutes);

                   $originalPredStart = Carbon::parse($predecessor->start_time);
                   // الفرق من بداية المشروع إلى بداية المهمة السابقة
                   $relativePredStartMinutes = $originalProjectStart->diffInMinutes($originalPredStart, false);
                   $predecessorStart = $newProjectStart->copy()->addMinutes($relativePredStartMinutes);
               } else {
                   $predecessorEnd = $startTimes[$predecessor->id]['end'];
                   $predecessorStart = $startTimes[$predecessor->id]['start'];
               }

               switch ($dep->dependency_type) {
                   case 'FS': // Finish to Start
                       if ($predecessorEnd->gt($maxStart)) {
                           $maxStart = $predecessorEnd->copy();
                       }
                       break;
                   case 'SS': // Start to Start
                       if ($predecessorStart->gt($maxStart)) {
                           $maxStart = $predecessorStart->copy();
                       }
                       break;
                   case 'FF': // Finish to Finish
                       $possibleStart = $predecessorEnd->copy()->subMinutes($taskDuration);
                       if ($possibleStart->gt($maxStart)) {
                           $maxStart = $possibleStart;
                       }
                       break;
                   case 'SF': // Start to Finish
                       $possibleStart = $predecessorStart->copy()->subMinutes($taskDuration);
                       if ($possibleStart->gt($maxStart)) {
                           $maxStart = $possibleStart;
                       }
                       break;
               }
           }

           // لا تسمح بجدولة أي مهمة قبل بداية المشروع الجديدة
           if ($maxStart->lt($newProjectStart)) {
               $maxStart = $newProjectStart->copy();
           }

           // تحديث أوقات المهمة
           $task->start_time = $maxStart;
           $task->end_time = $maxStart->copy()->addMinutes($taskDuration);
           $task->save();

           $startTimes[$task->id] = [
               'start' => $task->start_time instanceof Carbon ? $task->start_time : Carbon::parse($task->start_time),
               'end' => $task->end_time instanceof Carbon ? $task->end_time : Carbon::parse($task->end_time),
           ];

           $updatedTasks[] = $task->id;

           // إشعار الموظف المعني
           if ($task->assignedTo) {
               Notification::send($task->assignedTo, new TaskOverdueNotification($task));
           }
       }

       return response()->json([
           'message' => 'تمت إعادة جدولة جميع مهام المشروع بنجاح مع الحفاظ على الفترات الزمنية النسبية.',
           'updated_tasks' => $updatedTasks,
           'original_project_start' => $originalProjectStart->toDateTimeString(),
           'new_project_start' => $newProjectStart->toDateTimeString(),
           'time_shift_minutes' => $timeShift
       ]);
   }

// ... existing code ...

    /**
     * جلب جميع الموظفين العاملين على مهمة رئيسية (من المهام الفرعية)
     */
    public function getMainTaskEmployees($mainTaskId)
    {
        $mainTask = Task::with('subtasks.assignedTo')->findOrFail($mainTaskId);
        $employees = $mainTask->subtasks->pluck('assignedTo')->filter()->unique('id')->values();
        return response()->json([
            'employees' => $employees
        ]);
    }

    /**
     * تحديث حالة المشروع يدوياً بناءً على حالة المهام
     */
    public function updateProjectStatus($projectId)
    {
        $project = \App\Models\Project::findOrFail($projectId);

        // التحقق من الصلاحيات
        $user = \Illuminate\Support\Facades\Auth::user();
        $isManager = $project->projectManager && $user->id == $project->projectManager->id;

        if (!$isManager) {
            return response()->json([
                'message' => 'غير مصرح لك بتحديث حالة المشروع.'
            ], 403);
        }

        $oldStatus = $project->status;
        $project->updateStatusBasedOnTasks();
        $newStatus = $project->status;

        $message = 'تم تحديث حالة المشروع بنجاح.';
        if ($oldStatus !== $newStatus) {
            $statusMessages = [
                'Pending' => 'معلق',
                'Abuilding' => 'قيد العمل',
                'Finished' => 'مكتمل'
            ];
            $message = 'تم تغيير حالة المشروع من ' . ($statusMessages[$oldStatus] ?? $oldStatus) . ' إلى ' . ($statusMessages[$newStatus] ?? $newStatus);
        }

        return response()->json([
            'message' => $message,
            'project' => $project,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'tasks_count' => $project->tasks->count(),
            'completed_tasks' => $project->tasks->where('status', 'Completed')->count(),
            'in_progress_tasks' => $project->tasks->where('status', 'In Progress')->count()
        ]);
    }


    // ... existing code ...

    /**
     * جلب جميع المشاريع التي يعمل عليها الموظف
     */
    public function getUserProjects()
    {
        try {
            $user = Auth::user();

            // جلب جميع التاسكات المخصصة للموظف مع المشاريع
            $assignedTasks = $user->tasks()
                ->with('project')
                ->get();

            // استخراج المشاريع الفريدة
            $projects = $assignedTasks
                ->pluck('project')
                ->unique('id')
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'description' => $project->description,
                        'status' => $project->status,
                        'company' => $project->company->name,
                        'project_manager' =>  $project->projectManager->name,
                        'created_at' => $project->created_at,
                        'updated_at' => $project->updated_at,
                    ];
                })
                ->values();

            return response()->json([
                'message' => 'تم جلب المشاريع بنجاح',
                'projects_count' => $projects->count(),
                'projects' =>$projects,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في جلب المشاريع',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
