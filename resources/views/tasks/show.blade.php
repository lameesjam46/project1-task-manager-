@extends('layouts.app')

@section('title', __('app.task_details'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('app.task_details') }}</h1>
        </div>
        <div class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                <i class="fas fa-edit {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('app.edit') }}
            </a>
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" 
                  onsubmit="return confirm('{{ __('app.confirm_delete_task') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    <i class="fas fa-trash {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.delete') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Task Details -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Title and Description -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $task->title }}</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $task->description }}</p>
                    </div>

                    <!-- Status and Priority -->
                    <div class="flex items-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('app.task_status') }}:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($task->status == 'Completed') bg-green-100 text-green-800
                                @elseif($task->status == 'In Progress') bg-blue-100 text-blue-800
                                @elseif($task->status == 'Overdue') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ __('app.' . strtolower(str_replace(' ', '_', $task->status))) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('app.task_priority') }}:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($task->priority == 'Critical') bg-red-100 text-red-800
                                @elseif($task->priority == 'High') bg-orange-100 text-orange-800
                                @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ __('app.' . strtolower($task->priority)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('app.start_date') }}:</span>
                            <p class="text-gray-900">
                                @if($task->start_time && !empty($task->start_time))
                                    {{ \Carbon\Carbon::parse($task->start_time)->format('Y-m-d H:i') }}
                                @else
                                    {{ __('app.not_specified') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('app.end_date') }}:</span>
                            <p class="text-gray-900">
                                @if($task->end_time && !empty($task->end_time))
                                    {{ \Carbon\Carbon::parse($task->end_time)->format('Y-m-d H:i') }}
                                @else
                                    {{ __('app.not_specified') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Dependencies (Predecessor Tasks) -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('app.Dependencies') }}</h3>
                        @if($task->predecessorTasks && $task->predecessorTasks->count())
                            <div class="space-y-3">
                                @foreach($task->predecessorTasks as $depTask)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('tasks.show', $depTask) }}" class="hover:text-primary">{{ $depTask->title }}</a>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ __('app.dependency_type') }}: {{ $depTask->pivot->dependency_type ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($depTask->status == 'Completed') bg-green-100 text-green-800
                                            @elseif($depTask->status == 'In Progress') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ __('app.' . strtolower(str_replace(' ', '_', $depTask->status))) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">{{ __('app.no_tasks') }}</p>
                        @endif
                    </div>

                    
                    <!-- Notes -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('app.Notes') }}</h3>
                        @if($task->notes && $task->notes->count())
                            <div class="space-y-3">
                                @foreach($task->notes as $note)
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <img class="h-8 w-8 rounded-full {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}" src="https://ui-avatars.com/api/?name={{ $note->user->name ?? __('app.user') }}&color=3B82F6&background=EBF4FF" alt="{{ $note->user->name ?? __('app.user') }}">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $note->user->name ?? __('app.not_specified') }}</p>
                                                <p class="text-xs text-gray-500">{{ $note->created_at?->format('Y-m-d H:i') }}</p>
                                            </div>
                                        </div>
                                        <p class="text-gray-800 text-sm">{{ $note->note }}</p>
                                        @if(!empty($note->attachment_url))
                                            <a href="{{ $note->attachment_url }}" target="_blank" class="text-primary text-sm hover:underline mt-2 inline-flex items-center">
                                                <i class="fas fa-paperclip {{ app()->getLocale() == 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                                {{ __('app.file') }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">{{ __('app.No Notes Found') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Project -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('app.project') }}</h3>
                    @if($task->project)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $task->project->title }}</h4>
                            <!-- <p class="text-sm text-gray-600 mt-1">{{ $task->project->description }}</p> -->
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('app.not_specified') }}</p>
                    @endif
                </div>

                <!-- Assigned To -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('app.assigned_to') }}</h3>
                    @if($task->assignedTo)
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}" 
                                 src="https://ui-avatars.com/api/?name={{ $task->assignedTo->name }}&color=3B82F6&background=EBF4FF" 
                                 alt="{{ $task->assignedTo->name }}">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $task->assignedTo->name }}</p>
                                <p class="text-sm text-gray-500">{{ $task->assignedTo->email }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('app.not_specified') }}</p>
                    @endif
                </div>

                <!-- Actions -->
                <!-- <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('app.actions') }}</h3>
                    <div class="space-y-2">
                        @if($task->status != 'Completed')
                        <form method="POST" action="{{ route('tasks.status', $task) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Completed">
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                <i class="fas fa-check {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                                {{ __('app.mark_as_completed') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Subtasks -->
    @if($task->subtasks && $task->subtasks->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.tasks') }}</h3>
        <div class="space-y-3">
            @foreach($task->subtasks as $subtask)
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subtask->status == 'Completed') bg-green-100 text-green-800
                            @elseif($subtask->status == 'In Progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ __('app.' . strtolower(str_replace(' ', '_', $subtask->status))) }}
                        </span>
                    </div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'mr-3' : 'ml-3' }}">
                        <p class="text-sm font-medium text-gray-900">{{ $subtask->title }}</p>
                        <p class="text-sm text-gray-500">{{ $subtask->description }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                    <a href="{{ route('tasks.show', $subtask) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
