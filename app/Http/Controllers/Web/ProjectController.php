<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['tasks', 'projectManager', 'company']);
        
        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $projects = $query->latest()->paginate(15)->appends($request->query());
        
        // Get companies for filter dropdown
        $companies = \App\Models\Company::orderBy('name')->get();
        
        // Get available statuses
        $statuses = ['Pending', 'Abuilding', 'Finished'];
        
        return view('projects.index', compact('projects', 'companies', 'statuses'));
    }

    public function create(Request $request)
    {
        $companies = \App\Models\Company::all();
        // Preselected company flags are kept for navigation, but manager candidates follow the new rule:
        $preselectedCompanyId = $request->query('company_id');
        $fromCompany = $request->boolean('from_company');

        // Only active managers not linked to any company
        $users = User::query()
            ->where('role', 'manager')
            ->where('active', true)
            ->whereDoesntHave('companies')
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('users', 'companies', 'preselectedCompanyId', 'fromCompany'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,Abuilding,Finished',
            'project_manager_id' => 'nullable|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        // If manager selected, ensure they are active managers and not linked to any company
        if ($request->filled('project_manager_id')) {
            $eligible = User::query()
                ->where('id', $request->project_manager_id)
                ->where('role', 'manager')
                ->where('active', true)
                ->whereDoesntHave('companies')
                ->exists();
            if (!$eligible) {
                return back()->withErrors(['project_manager_id' => 'يجب اختيار مدير نشط وغير مرتبط بأي شركة.'])->withInput();
            }
        }

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'project_manager_id' => $request->project_manager_id,
            'company_id' => $request->company_id,
        ]);

        // If coming from company page, go back to that company's details
        if ($request->has('from_company')) {
            return redirect()->route('companies.show', $project->company_id)
                ->with('success', __('app.project_created_successfully'));
        }

        return redirect()->route('projects.index')
            ->with('success', __('app.project_created_successfully'));
    }

    public function show(Project $project)
    {
        $project->load(['tasks', 'projectManager', 'company']);
        
        // Get all unique users assigned to tasks in this project using direct database query
        $assignedUserIds = \DB::table('tasks')
            ->where('project_id', $project->id)
            ->whereNotNull('assigned_to')
            ->where('assigned_to', '!=', '')
            ->pluck('assigned_to')
            ->unique()
            ->filter();
        
        $assignedUsers = \App\Models\User::whereIn('id', $assignedUserIds)->get();
        
        return view('projects.show', compact('project', 'assignedUsers'));
    }

    public function edit(Project $project)
    {
        // Only active managers not linked to any company
        $users = User::query()
            ->where('role', 'manager')
            ->where('active', true)
            ->whereDoesntHave('companies')
            ->orderBy('name')
            ->get();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_manager_id' => 'nullable|exists:users,id',
        ]);

        // Validate manager eligibility on update as well (active manager with no company)
        if ($request->filled('project_manager_id')) {
            $eligible = User::query()
                ->where('id', $request->project_manager_id)
                ->where('role', 'manager')
                ->where('active', true)
                ->whereDoesntHave('companies')
                ->exists();
            if (!$eligible) {
                return back()->withErrors(['project_manager_id' => 'يجب اختيار مدير نشط وغير مرتبط بأي شركة.'])->withInput();
            }
        }

        // Build update payload without overriding manager with null
        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Only update manager if a non-empty value is provided
        if ($request->filled('project_manager_id')) {
            $data['project_manager_id'] = $request->project_manager_id;
        }

        $project->update($data);

        return redirect()->route('projects.index')->with('success', __('app.project_updated_successfully'));
    }

    /**
     * Return eligible project manager candidates for a company (employees + project managers, active)
     */
    public function eligibleManagers(\App\Models\Company $company)
    {
        $users = User::query()
            ->whereIn('role', ['employee', 'project_manager'])
            ->where('active', true)
            ->whereHas('companies', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['users' => $users]);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح!');
    }
} 