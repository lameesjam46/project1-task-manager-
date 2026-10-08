@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                <i class="fas fa-edit ml-2"></i>{{ __('app.edit') }}
            </a>
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" 
                  onsubmit="return confirm('{{ __('app.confirm_delete_user') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    <i class="fas fa-trash ml-2"></i>{{ __('app.delete') }}
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- User Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Info (left of first row) -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6 h-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.basic_info') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.name') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.email') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user->email }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.role') }}</label>
                        <div class="mt-1">
                            @switch($user->role)
                                @case('admin')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('app.admin') }}</span>
                                    @break
                                @case('manager')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.manager') }}</span>
                                    @break
                                @case('user')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.user') }}</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $user->role }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.registration_date') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $user->created_at ? $user->created_at->format('Y-m-d') : __('app.not_specified') }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.company') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($managedCompany)
                                <a href="{{ route('companies.show', $managedCompany->id) }}" class="text-primary hover:underline">
                                    <i class="fas fa-building ml-2"></i>{{ $managedCompany->name }}
                                </a>
                            @elseif($employeeCompany)
                                <a href="{{ route('companies.show', $employeeCompany->id) }}" class="text-primary hover:underline">
                                    <i class="fas fa-building ml-2"></i>{{ $employeeCompany->name }}
                                </a>
                            @else
                                <span class="text-gray-500">{{ __('app.company_membership_none') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.role_in_company') }}</label>
                        <div class="mt-1 space-x-2 space-x-reverse">
                            @if($managedCompany)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">{{ __('app.company_owner') }}</span>
                            @elseif($employeeCompany && $isProjectManager)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.project_manager') }}</span>
                            @elseif($employeeCompany)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.employee') }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ __('app.not_specified') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (right of first row) -->
        <div class="space-y-6">
            <!-- Profile Picture -->
            <div class="bg-white shadow rounded-lg p-6 text-center h-full">
                <div class="mb-4">
                    <img class="h-24 w-24 rounded-full mx-auto" 
                         src="https://ui-avatars.com/api/?name={{ $user->name }}&color=3B82F6&background=EBF4FF&size=96" 
                         alt="{{ $user->name }}">
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <div class="mt-2">
                    @switch($user->role)
                        @case('admin')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('app.admin') }}</span>
                            @break
                        @case('manager')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.manager') }}</span>
                            @break
                        @case('user')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.user') }}</span>
                            @break
                        @default
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $user->role }}</span>
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Full-width below the first row -->
        <!-- Profile Details -->
        @if($user->profile)
        <div class="lg:col-span-3">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.additional_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($user->profile->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.phone') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user->profile->phone }}</div>
                    </div>
                    @endif
                    @if($user->profile->address)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.address') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user->profile->address }}</div>
                    </div>
                    @endif
                    @if($user->profile->bio)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.bio') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user->profile->bio }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Full-width stats below grid (match width of User Tasks) -->
    @if(!$isRoleManager && !$isProjectManager && (($taskStats['total'] ?? 0) > 0))
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.task_statistics') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $taskStats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.total_tasks') }}</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $taskStats['completed'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.completed_tasks') }}</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $taskStats['in_progress'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.in_progress') }}</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $taskStats['overdue'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.overdue_tasks') }}</div>
            </div>
        </div>
    </div>
    @if(!$isRoleManager && $isProjectManager)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.managed_projects_stats') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $projectStats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.total_projects') }}</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $projectStats['finished'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.finished') }}</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $projectStats['abuilding'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.in_progress') }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-700">{{ $projectStats['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">{{ __('app.pending') }}</div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- User Tasks -->
    @if(!$isRoleManager && !$isProjectManager)
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ __('app.user_tasks') }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.task') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.project') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.task_priority') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.due_date') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($user->tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $task->project->title ?? __('app.not_specified') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($task->status)
                                @case('pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.pending') }}</span>
                                    @break
                                @case('in_progress')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.in_progress') }}</span>
                                    @break
                                @case('completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.completed') }}</span>
                                    @break
                                @default
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $task->status }}</span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($task->priority)
                                @case('low')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.low') }}</span>
                                    @break
                                @case('medium')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.medium') }}</span>
                                    @break
                                @case('high')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('app.high') }}</span>
                                    @break
                                @default
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $task->priority }}</span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $task->due_date ? $task->due_date->format('Y-m-d') : __('app.not_specified') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('tasks.show', $task) }}" class="text-primary hover:text-blue-600">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            {{ __('app.no_tasks_for_user') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection