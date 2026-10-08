<?php

use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\CompanyJoinRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskNoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Api\GanttController;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

//Route::get('tasks',[taskController::class,'index']);
//Route::post('tasks',[taskController::class,'store']);
//Route::put('tasks/{id}',[taskController::class,'update']);
//Route::delete('tasks/{id}',[taskController::class,'destroy']);
//Route::get('tasks/{id}',[taskController::class,'show']);


Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');



// Password reset routes
Route::post('password/send-code', [ForgotPasswordController::class, 'sendCode']);
Route::post('password/verify-code', [ForgotPasswordController::class, 'verifyCode']);
Route::post('password/reset', [ForgotPasswordController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group(function (){


Route::apiResource('tasks',TaskController::class);
Route::prefix('tasks')->group(function (){
//Route::get('/{id}/user',[TaskController::class,'getTasksUser']);
Route::post('/{taskId}/categories',[TaskController::class,'addCategoriesToTask']);
Route::get('/{taskId}/categories',[TaskController::class,'getCategoriesToTask']);
Route::get('/{categoryId}/category',[TaskController::class,'getCategoryTasks']);
Route::post('/{taskId}/assign-employees',[TaskController::class,'assignEmployeesToTask']);

Route::post('/{id}/change-status', [TaskController::class, 'changeStatus']);
Route::post('/{id}/reopen-or-extend', [TaskController::class, 'reopenOrExtend']);
Route::post('/{id}/reschedule-successors', [TaskController::class, 'rescheduleSuccessors']);
Route::post('/{id}/reschedule-cascade', [TaskController::class, 'rescheduleCascade']);
Route::post('projects/{id}/reschedule', [TaskController::class, 'rescheduleProject']);

Route::post('/{taskId}/note', [TaskNoteController::class, 'addNote']);
Route::delete('task-notes/{noteId}', [TaskNoteController::class, 'deleteNote']);
Route::get('task-notes/{noteId}', [TaskNoteController::class, 'getNote']);
Route::get('/{taskId}/notes', [TaskNoteController::class, 'getNotesForTask']);
Route::post('/note/{noteId}/update', [TaskNoteController::class, 'updateNote']);
 });
Route::get('task/ordered',[TaskController::class,'getTasksByPriority']);
//  Admin
Route::get('task/all',[TaskController::class,'getAllTasks'])->middleware('CheckUser');
//favorite
Route::post('task/{id}/favorite',[TaskController::class,'addToFavorites']);
Route::delete('task/{id}/favorite',[TaskController::class,'removeFormFavorites']);
Route::get('task/favorite',[TaskController::class,'getFavoritesTask']);


// Task dependencies management
Route::post('tasks/with-dependencies', [TaskController::class, 'createTaskWithDependencies']);
Route::put('tasks/with-dependencies/{id}', [TaskController::class, 'updateTaskWithDependencies']);
Route::delete('tasks/{id}/dependencies', [TaskController::class, 'deleteTaskDependencies']);
Route::get('tasks/{task}/available-dependencies', [TaskController::class, 'getAvailableDependencies']);
Route::get('tasks/{id}/with-dependencies', [TaskController::class, 'getTaskWithDependencies']);


Route::post('profile',[ProfileController::class,'store']);
Route::get('profile',[ProfileController::class,'show']);
Route::put('profile',[ProfileController::class,'update']);


Route::get('user/tasks',[UserController::class,'getUserTasks']);
Route::get('user/profile',[UserController::class,'getProfile']);
Route::get('user/',[UserController::class,'getUser']);
Route::get('user/all',[UserController::class,'getAllUser']);

// جلب جميع الإشعارات للمستخدم الحالي
Route::get('user/notifications', function () {
    return response()->json([
        'notifications' => auth()->user()->notifications
    ]);
})->middleware('auth:sanctum');

// جلب الإشعارات غير المقروءة فقط للمستخدم الحالي
Route::get('user/notifications/unread', function () {
    return response()->json([
        'notifications' => auth()->user()->unreadNotifications
    ]);
})->middleware('auth:sanctum');

// تعليم إشعار معين كمقروء
Route::post('user/notifications/{id}/mark-as-read', function ($id) {
    $notification = auth()->user()->notifications()->where('id', $id)->first();
    if ($notification) {
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read.']);
    }
    return response()->json(['message' => 'Notification not found.'], 404);
})->middleware('auth:sanctum');

// Project routes
Route::post('companies', [ProjectController::class, 'storeCompany']);
Route::post('projects', [ProjectController::class, 'store']);
Route::put('projects/{id}', [ProjectController::class, 'update']);
Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
Route::get('company/projects/{id}', [ProjectController::class, 'getCompanyProjects']);
Route::get('projects/{project}/tasks/ordered', [TaskController::class, 'getTasksForProjectByPriority']);
Route::post('projects/add-team-member', [ProjectController::class, 'addEmployeeToProject']);
Route::put('projects/{project}/manager', [App\Http\Controllers\ProjectController::class, 'updateProjectManager']);
Route::post('projects/{projectId}/update-status', [ProjectController::class, 'updateProjectStatus']);
Route::get('projects/{projectId}/stats', [ProjectController::class, 'getProjectStats']);
// Project team management routes
// Route::prefix('project-team')->group(function () {
//     Route::post('add-members', [ProjectController::class, 'addEmployeeToProject']);
// });

//employee routes
Route::post('employee/connect-company/{companyId}', [EmployeeController::class, 'connectCompanyToUser']);
Route::get('companies/{companyId}/employees', [EmployeeController::class, 'getEmployeesByCompany']);
Route::post('employee/connect-to-company', [EmployeeController::class, 'connectToCompany']);
Route::post('companies/{companyId}/assign-employees', [EmployeeController::class, 'assignEmployeesToCompany']);

// طلب انضمام لشركة
Route::post('companies/{company}/join-request', [CompanyJoinRequestController::class, 'store'])->middleware('auth:sanctum');
// عرض الطلبات للمدير
Route::get('companies/{company}/join-requests', [CompanyJoinRequestController::class, 'index'])->middleware('auth:sanctum');
// موافقة أو رفض الطلب
Route::post('join-requests/{request}/approve', [CompanyJoinRequestController::class, 'approve'])->middleware('auth:sanctum');
Route::post('join-requests/{request}/reject', [CompanyJoinRequestController::class, 'reject'])->middleware('auth:sanctum');

// Chat messages routes
Route::post('chat/send', [ChatMessageController::class, 'sendMessage']);
Route::get('chat/project/{projectId}', [ChatMessageController::class, 'getProjectMessages']);
Route::get('chat/user/{userId}/project/{projectId}', [ChatMessageController::class, 'getUserMessages']);
Route::put('chat/message/{id}', [ChatMessageController::class, 'updateMessage']);
Route::delete('chat/message/{id}', [ChatMessageController::class, 'deleteMessage']);

Route::get('dashboard/summary', [DashboardController::class, 'summary']);
Route::get('dashboard/critical-path/{projectId}', [DashboardController::class, 'criticalPath']);

Route::get('tasks/{id}/employees', [TaskController::class, 'getMainTaskEmployees']);

// تحديث حالة المشروع بناءً على حالة المهام
Route::post('projects/{projectId}/update-status', [TaskController::class, 'updateProjectStatus']);
Route::get('/user/projects-with-tasks', [TaskController::class, 'getUserProjects']);

 // Gantt data endpoint for a project
 Route::get('projects/{projectId}/gantt', [GanttController::class, 'project']);

});
