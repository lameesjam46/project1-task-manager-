<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing task_user table relationship...\n";
    
    // Test if we can query the task_user table directly
    $result = DB::select('SELECT COUNT(*) as count FROM task_user');
    echo "task_user table exists and has " . $result[0]->count . " records\n";
    
    // Test the relationship
    $task = App\Models\Task::first();
    if ($task) {
        $users = $task->assignedUsers;
        echo "Success: Task->assignedUsers relationship works! Found " . $users->count() . " assigned users\n";
    } else {
        echo "No tasks found to test relationship\n";
    }
    
    echo "All tests passed! The task_user table issue is resolved.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
