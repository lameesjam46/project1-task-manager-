<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\TaskNote;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'name');

        $mobileApp = Project::query()->where('title', 'Mobile App Launch')->firstOrFail();
        $apiRefactor = Project::query()->where('title', 'API Refactor')->firstOrFail();
        $brandRefresh = Project::query()->where('title', 'Brand Refresh')->firstOrFail();
        $websiteRelaunch = Project::query()->where('title', 'Website Relaunch')->firstOrFail();

        $omar = User::query()->where('email', 'omar.employee@taskmanager.test')->firstOrFail();
        $layla = User::query()->where('email', 'layla.employee@taskmanager.test')->firstOrFail();
        $khaled = User::query()->where('email', 'khaled.employee@taskmanager.test')->firstOrFail();
        $yousef = User::query()->where('email', 'yousef.employee@taskmanager.test')->firstOrFail();
        $noura = User::query()->where('email', 'noura.employee@taskmanager.test')->firstOrFail();

        // ===== Mobile App tasks =====
        $designAuth = $this->createTask([
            'title' => 'Design auth screens',
            'description' => 'Create login and register UI screens.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $layla->id,
            'priority' => 'High',
            'status' => 'Completed',
            'is_critical' => true,
            'start_time' => now()->subDays(10)->setTime(9, 0),
            'end_time' => now()->subDays(7)->setTime(17, 0),
            'due_date' => now()->subDays(7)->setTime(17, 0),
        ], ['Working', 'Projects']);

        $apiAuth = $this->createTask([
            'title' => 'Implement auth API',
            'description' => 'Build authentication endpoints for the mobile app.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $omar->id,
            'priority' => 'Critical',
            'status' => 'In Progress',
            'is_critical' => true,
            'start_time' => now()->subDays(6)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(17, 0),
            'due_date' => now()->addDays(2)->setTime(17, 0),
        ], ['Working', 'Projects']);

        $ciPipeline = $this->createTask([
            'title' => 'Setup CI pipeline',
            'description' => 'Configure GitHub Actions for build and tests.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $khaled->id,
            'priority' => 'Medium',
            'status' => 'In Progress',
            'start_time' => now()->subDays(4)->setTime(10, 0),
            'end_time' => now()->addDays(3)->setTime(16, 0),
            'due_date' => now()->addDays(3)->setTime(16, 0),
        ], ['Working']);

        $pushNotifications = $this->createTask([
            'title' => 'Push notifications',
            'description' => 'Integrate FCM push notifications.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $omar->id,
            'priority' => 'High',
            'status' => 'Not Started',
            'start_time' => now()->addDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(8)->setTime(17, 0),
            'due_date' => now()->addDays(8)->setTime(17, 0),
        ], ['Projects']);

        // Subtasks under auth API (times within parent range)
        $loginEndpoint = $this->createTask([
            'title' => 'Login endpoint',
            'description' => 'POST /login with validation and tokens.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $omar->id,
            'priority' => 'High',
            'status' => 'Completed',
            'is_subtask' => true,
            'parent_task_id' => $apiAuth->id,
            'start_time' => now()->subDays(6)->setTime(9, 0),
            'end_time' => now()->subDays(4)->setTime(17, 0),
            'due_date' => now()->subDays(4)->setTime(17, 0),
        ], ['Working']);

        $registerEndpoint = $this->createTask([
            'title' => 'Register endpoint',
            'description' => 'POST /register with email verification flow.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $omar->id,
            'priority' => 'Medium',
            'status' => 'In Progress',
            'is_subtask' => true,
            'parent_task_id' => $apiAuth->id,
            'start_time' => now()->subDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(1)->setTime(17, 0),
            'due_date' => now()->addDays(1)->setTime(17, 0),
        ], ['Working']);

        // Dependencies: design -> auth API (FS), CI -> push (FS)
        $this->createDependency($designAuth, $apiAuth, 'FS');
        $this->createDependency($ciPipeline, $pushNotifications, 'FS');
        $this->createDependency($loginEndpoint, $registerEndpoint, 'FS');

        // ===== API Refactor tasks =====
        $auditEndpoints = $this->createTask([
            'title' => 'Audit existing endpoints',
            'description' => 'List and document current API endpoints.',
            'project_id' => $apiRefactor->id,
            'assigned_to' => $omar->id,
            'priority' => 'Medium',
            'status' => 'Not Started',
            'start_time' => now()->addDays(1)->setTime(9, 0),
            'end_time' => now()->addDays(5)->setTime(17, 0),
            'due_date' => now()->addDays(5)->setTime(17, 0),
        ], ['Learning', 'Working']);

        $this->createTask([
            'title' => 'Add response caching',
            'description' => 'Introduce Redis caching for heavy endpoints.',
            'project_id' => $apiRefactor->id,
            'assigned_to' => $khaled->id,
            'priority' => 'High',
            'status' => 'Blocked',
            'start_time' => now()->addDays(6)->setTime(9, 0),
            'end_time' => now()->addDays(12)->setTime(17, 0),
            'due_date' => now()->addDays(12)->setTime(17, 0),
        ], ['Working']);

        $this->createDependency($auditEndpoints, Task::query()->where('title', 'Add response caching')->first(), 'FS');

        // ===== Brand Refresh tasks =====
        $moodboard = $this->createTask([
            'title' => 'Create moodboard',
            'description' => 'Collect visual references for the new brand.',
            'project_id' => $brandRefresh->id,
            'assigned_to' => $noura->id,
            'priority' => 'High',
            'status' => 'Completed',
            'start_time' => now()->subDays(14)->setTime(9, 0),
            'end_time' => now()->subDays(10)->setTime(17, 0),
            'due_date' => now()->subDays(10)->setTime(17, 0),
        ], ['Personal', 'Projects']);

        $colorPalette = $this->createTask([
            'title' => 'Define color palette',
            'description' => 'Finalize primary and secondary brand colors.',
            'project_id' => $brandRefresh->id,
            'assigned_to' => $noura->id,
            'priority' => 'Critical',
            'status' => 'In Progress',
            'is_critical' => true,
            'start_time' => now()->subDays(9)->setTime(9, 0),
            'end_time' => now()->addDays(1)->setTime(17, 0),
            'due_date' => now()->addDays(1)->setTime(17, 0),
        ], ['Projects']);

        $this->createDependency($moodboard, $colorPalette, 'FS');

        // ===== Website Relaunch (finished project) =====
        $homepage = $this->createTask([
            'title' => 'Build homepage',
            'description' => 'Implement the new homepage layout.',
            'project_id' => $websiteRelaunch->id,
            'assigned_to' => $noura->id,
            'priority' => 'High',
            'status' => 'Completed',
            'start_time' => now()->subDays(30)->setTime(9, 0),
            'end_time' => now()->subDays(20)->setTime(17, 0),
            'due_date' => now()->subDays(20)->setTime(17, 0),
        ], ['Projects', 'Education']);

        $this->createTask([
            'title' => 'QA website relaunch',
            'description' => 'Full QA pass before go-live.',
            'project_id' => $websiteRelaunch->id,
            'assigned_to' => $yousef->id,
            'priority' => 'Medium',
            'status' => 'Completed',
            'start_time' => now()->subDays(19)->setTime(9, 0),
            'end_time' => now()->subDays(15)->setTime(17, 0),
            'due_date' => now()->subDays(15)->setTime(17, 0),
        ], ['Working']);

        $this->createDependency($homepage, Task::query()->where('title', 'QA website relaunch')->first(), 'FS');

        // Overdue example
        $this->createTask([
            'title' => 'Write release notes',
            'description' => 'Prepare release notes for mobile beta.',
            'project_id' => $mobileApp->id,
            'assigned_to' => $layla->id,
            'priority' => 'Low',
            'status' => 'Overdue',
            'is_overdue' => true,
            'start_time' => now()->subDays(8)->setTime(9, 0),
            'end_time' => now()->subDays(5)->setTime(17, 0),
            'due_date' => now()->subDays(5)->setTime(17, 0),
        ], ['Personal']);

        // Notes (unique per user+task)
        TaskNote::query()->updateOrCreate(
            ['user_id' => $omar->id, 'task_id' => $apiAuth->id],
            ['note' => 'Token refresh flow still needs review.', 'attachment' => null]
        );

        TaskNote::query()->updateOrCreate(
            ['user_id' => $layla->id, 'task_id' => $designAuth->id],
            ['note' => 'Approved by manager. Handed off to backend.', 'attachment' => null]
        );

        TaskNote::query()->updateOrCreate(
            ['user_id' => $noura->id, 'task_id' => $colorPalette->id],
            ['note' => 'Waiting for final brand book approval.', 'attachment' => null]
        );

        // Favorites
        $omar->favoriteTasks()->syncWithoutDetaching([$apiAuth->id, $ciPipeline->id]);
        $layla->favoriteTasks()->syncWithoutDetaching([$designAuth->id]);
        $noura->favoriteTasks()->syncWithoutDetaching([$colorPalette->id, $moodboard->id]);
    }

    private function createTask(array $attributes, array $categoryNames = []): Task
    {
        $task = Task::query()->updateOrCreate(
            [
                'title' => $attributes['title'],
                'project_id' => $attributes['project_id'],
            ],
            array_merge([
                'is_subtask' => false,
                'is_critical' => false,
                'is_overdue' => false,
                'parent_task_id' => null,
                'assigned_to' => null,
                'due_date' => null,
            ], $attributes)
        );

        if (!empty($categoryNames)) {
            $ids = Category::query()->whereIn('name', $categoryNames)->pluck('id')->all();
            $task->categories()->syncWithoutDetaching($ids);
        }

        return $task;
    }

    private function createDependency(Task $predecessor, ?Task $successor, string $type): void
    {
        if (!$successor) {
            return;
        }

        TaskDependency::query()->firstOrCreate(
            [
                'predecessor_task_id' => $predecessor->id,
                'successor_task_id' => $successor->id,
            ],
            [
                'dependency_type' => $type,
            ]
        );
    }
}
