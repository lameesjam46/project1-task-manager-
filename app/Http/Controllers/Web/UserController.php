<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile'])->latest();

        // Filter by status
        $status = $request->get('status');
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('active', $status === 'active');
        }

        // Filter by role
        $role = $request->get('role');
        if (in_array($role, ['manager', 'employee'], true)) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Employee,Manager',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح!');
    }

    public function show(User $user)
    {
        // Eager load basic relations
        $user->load(['profile']);

        // Normalize role check (handle 'manager' or 'Manager')
        $isRoleManager = strtolower((string) $user->role) === 'manager';

        // Company that the user manages (owner in companies table)
        $managedCompany = Company::where('user_id', $user->id)->first();

        // Company that the user belongs to via employee_company pivot (take first if multiple)
        $employeeCompany = $user->companies()->first();

        // Is the user marked as a project manager in the pivot table?
        $isProjectManager = DB::table('employee_company')
            ->where('user_id', $user->id)
            ->where('is_project_manager', true)
            ->exists();

        // Task stats for the user (assigned_to) — only relevant for employees (not company managers)
        $taskStats = [
            'total' => 0,
            'completed' => 0,
            'overdue' => 0,
            'in_progress' => 0,
        ];
        if (!$isRoleManager) {
            $tasksQuery = $user->tasks();
            $taskStats = [
                'total' => (clone $tasksQuery)->count(),
                'completed' => (clone $tasksQuery)->where('status', 'Completed')->count(),
                'overdue' => (clone $tasksQuery)->where('status', 'Overdue')->count(),
                'in_progress' => (clone $tasksQuery)->where('status', 'In Progress')->count(),
            ];
        }

        // Project stats for projects managed by the user — show only if employee is also project manager
        $projectStats = [
            'total' => 0,
            'pending' => 0,
            'abuilding' => 0,
            'finished' => 0,
        ];
        if (!$isRoleManager && $isProjectManager) {
            $projectsQuery = $user->projects();
            $projectStats = [
                'total' => (clone $projectsQuery)->count(),
                'pending' => (clone $projectsQuery)->where('status', 'Pending')->count(),
                'abuilding' => (clone $projectsQuery)->where('status', 'Abuilding')->count(),
                'finished' => (clone $projectsQuery)->where('status', 'Finished')->count(),
            ];
        }

        return view('users.show', compact(
            'user',
            'managedCompany',
            'employeeCompany',
            'isRoleManager',
            'isProjectManager',
            'taskStats',
            'projectStats'
        ));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // 'role' => 'required|in:admin,user,manager',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            // 'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح!');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الشخصي.');
        }

        $user->active = !$user->active;
        $user->save();

        return back()->with('success', $user->active ? 'تم تفعيل المستخدم.' : 'تم تعطيل المستخدم.');
    }
}