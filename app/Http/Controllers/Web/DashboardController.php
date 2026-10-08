<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Top-level counts (camelCase)
        $usersCount = \App\Models\User::count();
        $companiesCount = \App\Models\Company::count();
        $projectsCount = Project::count();
        $tasksCount = Task::count();

        // Task status breakdown (camelCase) - match enum values from migration
        $notStarted = Task::where('status', 'Not Started')->count();
        $inProgress = Task::where('status', 'In Progress')->count();
        $completed = Task::where('status', 'Completed')->count();

        // Duplicate in snake_case for the current Blade
        $users_count = $usersCount;
        $companies_count = $companiesCount;
        $projects_count = $projectsCount;
        $tasks_count = $tasksCount;
        $not_started_tasks_cont = $notStarted;
        $in_progress_tasks_cont = $inProgress;
        $completec_tasks_cont = $completed; // note: matches Blade variable name

        return view('dashboard.index', compact(
            'usersCount', 'companiesCount', 'projectsCount', 'tasksCount',
            'notStarted', 'inProgress', 'completed',
            'users_count', 'companies_count', 'projects_count', 'tasks_count',
            'not_started_tasks_cont', 'in_progress_tasks_cont', 'completec_tasks_cont'
        ));
    }
} 