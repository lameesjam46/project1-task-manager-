<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Store a newly created company in storage.
     */
    public function storeCompany(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:companies'
            ]);

            // Get the authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user already owns a company
            $existingCompany = Company::where('user_id', $user->id)->first();
            if ($existingCompany) {
                return response()->json([
                    'message' => 'You already own a company',
                    'company' => $existingCompany
                ], 400);
            }

            // Create the company with the logged-in user's ID
            $company = new Company();
            $company->name = $request->name;
            $company->user_id = $user->id;
            $company->save();

            return response()->json([
                'message' => 'Company created successfully',
                'data' => $company
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Company name is already taken',
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            // Get the logged-in user
            $user = Auth::user();

            // Check if user belongs to a company by looking in the company table
            $company = Company::where('user_id', $user->id)->first();

            if (!$company) {
                return response()->json([
                    'message' => 'User does not belong to any company'
                ], 400);
            }

            // تحقق من أن project_manager_id موظف في الشركة
            $projectManagerId = $request->project_manager_id;
            $isEmployee = DB::table('employee_company')
                ->where('user_id', $projectManagerId)
                ->where('company_id', $company->id)
                ->exists();

            if (!$isEmployee) {
                return response()->json([
                    'message' => 'The selected project manager is not an employee of this company.'
                ], 400);
            }

            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status ?? 'Pending',
                'company_id' => $company->id,
                'project_manager_id' => $projectManagerId
            ]);

            // تحديث is_project_manager في جدول employee_company
            DB::table('employee_company')
                ->where('user_id', $projectManagerId)
                ->where('company_id', $company->id)
                ->update(['is_project_manager' => true]);

            return response()->json([
                'message' => 'Project created successfully',
                'data' => $project
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $user = Auth::user();

            // Get the company that owns this project
            $company = Company::where('id', $project->company_id)->first();

            // Check if the authenticated user is the manager of the company
            if (!$company || $company->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized - Only the company manager can update projects'
                ], 403);
            }

            $data = $request->validated();
            $project->fill($data);
            $project->save();

            // Load the project with its relationships if needed
           // $project->load('user');

            return response()->json([
                'message' => 'Project updated successfully',
                'data' => $project
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $user = Auth::user();

            // Get the company that owns this project
            $company = Company::where('id', $project->company_id)->first();

            // Check if the authenticated user is the manager of the company
            if (!$company || $company->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized - Only the company manager can delete projects'
                ], 403);
            }

            $project->delete();

            return response()->json([
                'message' => 'Project deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add employees to the project team.
     * Only employees who belong to the company that owns the project can be added.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addEmployeeToProject(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $project = Project::with('company')->findOrFail($request->project_id);

            // Get the company that owns the project
            $company = $project->company;

            // Get all employees that belong to the company
            $companyEmployees = $company->employees()
                ->select('users.id')
                ->where('users.role', 'Employee')
                ->pluck('users.id')
                ->toArray();

            // Filter out any user IDs that are not employees of the company
            $validUserIds = array_intersect($request->user_ids, $companyEmployees);

            if (empty($validUserIds)) {
                return response()->json([
                    'message' => 'None of the provided users are employees of the company that owns this project'
                ], 400);
            }

            // Add the employees to the project team
            $project->teamMembers()->syncWithoutDetaching($validUserIds);

            return response()->json([
                'message' => 'Employees added to project team successfully',
                'project' => $project,
                'added_employees' => $project->teamMembers()
                    ->whereIn('users.id', $validUserIds)
                    ->get()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add employees to project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all projects for a specific company.
     */
    public function getCompanyProjects($companyId)
    {
        try {
            // Verify if the company exists
            $company = Company::findOrFail($companyId);

            // Get all projects for the company
            $projects = Project::where('company_id', $companyId)
               // ->with(['teamMembers', 'company'])
                ->get();

            return response()->json([
                'message' => 'Company projects retrieved successfully',
                'data' => [
                    //'company' => $company,
                    'projects' => $projects
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve company projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateProjectManager(Request $request, $projectId)
    {
        $request->validate([
            'project_manager_id' => 'required|exists:users,id',
        ]);

        try {
            $project = Project::findOrFail($projectId);
            $companyId = $project->company_id;
            $newManagerId = $request->project_manager_id;
            $oldManagerId = $project->project_manager_id;

            // تحقق أن المدير الجديد موظف في الشركة
            $isEmployee = DB::table('employee_company')
                ->where('user_id', $newManagerId)
                ->where('company_id', $companyId)
                ->exists();
            if (!$isEmployee) {
                return response()->json([
                    'message' => 'The selected project manager is not an employee of this company.'
                ], 400);
            }

            // تحديث is_project_manager للمدير القديم إلى false
            if ($oldManagerId) {
                DB::table('employee_company')
                    ->where('user_id', $oldManagerId)
                    ->where('company_id', $companyId)
                    ->update(['is_project_manager' => false]);
            }

            // تحديث project_manager_id في جدول projects
            $project->project_manager_id = $newManagerId;
            $project->save();

            // تحديث is_project_manager للمدير الجديد إلى true
            DB::table('employee_company')
                ->where('user_id', $newManagerId)
                ->where('company_id', $companyId)
                ->update(['is_project_manager' => true]);

            return response()->json([
                'message' => 'Project manager updated successfully',
                'project' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update project manager',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة المشروع بناءً على حالة المهام
     */
    public function updateProjectStatus($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            
            // التحقق من الصلاحيات - فقط مدير المشروع يمكنه تحديث الحالة
            $user = Auth::user();
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
                'in_progress_tasks' => $project->tasks->where('status', 'In Progress')->count(),
                'pending_tasks' => $project->tasks->where('status', 'Not Started')->count()
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'المشروع غير موجود',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في تحديث حالة المشروع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب إحصائيات المشروع مع حالة المهام
     */
    public function getProjectStats($projectId)
    {
        try {
            $project = Project::with('tasks')->findOrFail($projectId);
            
            $tasks = $project->tasks;
            $totalTasks = $tasks->count();
            $completedTasks = $tasks->where('status', 'Completed')->count();
            $inProgressTasks = $tasks->where('status', 'In Progress')->count();
            $pendingTasks = $tasks->where('status', 'Not Started')->count();
            $overdueTasks = $tasks->where('is_overdue', true)->count();

            $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            return response()->json([
                'message' => 'تم جلب إحصائيات المشروع بنجاح',
                'project' => $project,
                'stats' => [
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'in_progress_tasks' => $inProgressTasks,
                    'pending_tasks' => $pendingTasks,
                    'overdue_tasks' => $overdueTasks,
                    'completion_percentage' => $completionPercentage,
                    'project_status' => $project->status
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'المشروع غير موجود',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في جلب إحصائيات المشروع',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
