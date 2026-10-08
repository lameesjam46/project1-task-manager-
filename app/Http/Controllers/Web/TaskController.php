<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TaskController as ApiTaskController;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $apiTaskController;

    public function __construct()
    {
        $this->apiTaskController = new ApiTaskController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // عرض جميع المهام مع إمكانية الفلترة
        $query = Task::with(['project', 'assignedTo']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        // Search in task title 
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "{$searchTerm}%");
                //   ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $tasks = $query->orderBy('created_at', 'desc')->get();
        $projects = Project::all();
        
        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projects = Project::all();

        // Determine selected project (if navigated from a project page)
        $selectedProject = null;
        if ($request->has('project_id')) {
            $selectedProject = Project::find($request->project_id);
        }

        // Users filtered by selected project's company (employees only, active, excluding project manager)
        $users = collect();
        if ($selectedProject) {
            $companyId = $selectedProject->company_id;
            $users = User::query()
                ->where('role', 'employee')
                ->where('active', true)
                ->whereHas('companies', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->where('id', '!=', $selectedProject->project_manager_id)
                ->orderBy('name')
                ->get();
        }

        // Candidates for parent task (only main tasks in the selected project)
        $parentCandidates = collect();
        if ($selectedProject) {
            $parentCandidates = Task::query()
                ->where('project_id', $selectedProject->id)
                ->whereNull('parent_task_id')
                ->orderBy('title')
                ->get(['id', 'title']);
        }

        // Minimal tasks dataset for client-side dependency filtering
        // If project is selected, limit to that project's tasks; otherwise keep empty until user selects a project
        $tasksDataset = collect();
        if ($selectedProject) {
            $tasksDataset = Task::query()
                ->where('project_id', $selectedProject->id)
                ->orderBy('title')
                ->get(['id', 'title', 'project_id', 'parent_task_id']);
        }

        return view('tasks.create', compact('projects', 'users', 'selectedProject', 'parentCandidates', 'tasksDataset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // استخدام دالة createTaskWithDependencies من ApiTaskController
            $response = $this->apiTaskController->createTaskWithDependencies($request);
            
            if ($response->getStatusCode() === 201) {
                // Check if we came from a specific project page
                if ($request->has('return_to_project') && $request->return_to_project) {
                    $projectId = $request->project_id;
                    return redirect()->route('projects.show', $projectId)->with('success', __('app.task_created_successfully'));
                }
                
                return redirect()->route('tasks.index')->with('success', __('app.task_created_successfully'));
            }
            
            // في حالة وجود أخطاء، استخراج الرسالة من الاستجابة
            $responseData = json_decode($response->getContent(), true);
            $errorMessage = $responseData['message'] ?? __('app.error_creating_task');
            
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء المهمة: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::with([
            'project',
            'assignedTo',
            'subtasks',
            'predecessorTasks',
            'successorTasks',
            'notes.user',
        ])->findOrFail($id);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        // Preload predecessor tasks for rendering existing dependencies
        $task->load('predecessorTasks');
        $projects = Project::all();

        // Build a filtered list of available tasks for dependencies
        if ($task->parent_task_id) {
            // Subtask: only subtasks with same parent, exclude self
            $availableTasks = Task::with('project')
                ->where('parent_task_id', $task->parent_task_id)
                ->where('id', '!=', $task->id)
                ->orderBy('title')
                ->get();
        } else {
            // Main task: only main tasks in same project, exclude self
            $availableTasks = Task::with('project')
                ->where('project_id', $task->project_id)
                ->whereNull('parent_task_id')
                ->where('id', '!=', $task->id)
                ->orderBy('title')
                ->get();
        }

        // Filter users to employees of the task's project company (active, exclude project manager)
        $users = collect();
        if ($task->project) {
            $companyId = $task->project->company_id;
            $users = User::query()
                ->where('role', 'employee')
                ->where('active', true)
                ->whereHas('companies', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->where('id', '!=', $task->project->project_manager_id)
                ->orderBy('name')
                ->get();
        }

        return view('tasks.edit', compact('task', 'projects', 'users', 'availableTasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        // استخدام دالة updateTaskWithDependencies لضمان تحديث الاعتماديات مع المهمة
        $response = $this->apiTaskController->updateTaskWithDependencies($request, $id);

        if ($response->getStatusCode() === 200) {
            return redirect()->route('tasks.index')->with('success', __('app.task_updated_successfully'));
        }

        // حاول إظهار رسالة الخطأ القادمة من واجهة الـ API بدلاً من الرسالة العامة
        $responseData = [];
        try {
            $responseData = json_decode($response->getContent(), true) ?? [];
        } catch (\Throwable $e) {
            // تجاهل أي خطأ في التحويل
        }
        $errorMessage = $responseData['message'] ?? ($responseData['error'] ?? __('app.error_updating_task'));

        return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // استخدام دالة destroy من ApiTaskController
        $response = $this->apiTaskController->destroy($id);
        
        if ($response->getStatusCode() === 200) {
            return redirect()->route('tasks.index')->with('success', __('app.task_deleted_successfully'));
        }
        
        return redirect()->back()->withErrors(['error' => __('app.error_deleting_task')]);
    }



   

    /**
     * Assign employees to task
     */
    public function assignEmployees(Request $request, $taskId)
    {
        $response = $this->apiTaskController->assignEmployeesToTask($request, $taskId);
        
        if ($response->getStatusCode() === 200) {
            return redirect()->back()->with('success', __('app.employees_assigned_successfully'));
        }
        
        return redirect()->back()->withErrors(['error' => __('app.error_assigning_employees')]);
    }

    /**
     * Return assignable employees for a given project (company employees only, excluding managers and inactive users)
     */
    public function assignableUsers(Project $project)
    {
        $companyId = $project->company_id;

        $users = User::query()
            ->where('role', 'employee')
            ->where('active', true)
            ->whereHas('companies', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('id', '!=', $project->project_manager_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['users' => $users]);
    }

    /**
     * Return minimal tasks dataset for a project (used by create form JS filtering)
     */
    public function tasksDataset(Project $project)
    {
        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->orderBy('title')
            ->get(['id', 'title', 'project_id', 'parent_task_id']);

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Store subtask
     */
    public function storeSubtask(Request $request)
    {
        $response = $this->apiTaskController->storeSubtask($request);
        
        if ($response->getStatusCode() === 201) {
            return redirect()->back()->with('success', __('app.subtask_created_successfully'));
        }
        
        return redirect()->back()->withErrors(['error' => __('app.error_creating_subtask')])->withInput();
    }

    /**
     * Change task status (wrapper for API controller)
     */
    public function changeStatus(Request $request, $id)
    {
        $response = $this->apiTaskController->changeStatus($request, $id);

        // Success
        if ($response->getStatusCode() === 200) {
            return redirect()->back()->with('success', __('app.task_status_updated_successfully'));
        }

        // Extract error message from JSON if possible
        $errorMessage = __('app.error_updating_task_status');
        try {
            $data = json_decode($response->getContent(), true);
            if (isset($data['message'])) {
                $errorMessage = $data['message'];
            } elseif (isset($data['error'])) {
                $errorMessage = $data['error'];
            }
        } catch (\Throwable $e) {
            // ignore parse errors
        }

        return redirect()->back()->withErrors(['error' => $errorMessage]);
    }
}
