@extends('layouts.app')

@section('title', __('app.tasks'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.tasks') }}</h1>
        <a href="{{ route('tasks.create') }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center">
            <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('app.add_new_task') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6" x-data="{ showFilters: {{ request()->hasAny(['status', 'priority', 'project_id', 'search']) ? 'true' : 'false' }} }">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ __('app.filter') }} {{ __('app.tasks') }}</h3>
            <div class="flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                @if(request()->hasAny(['status', 'priority', 'project_id', 'search']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ __('app.filters_active') }}
                    </span>
                @endif
                <button @click="showFilters = !showFilters" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-primary hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors duration-200"
                        :class="{ 'bg-blue-50 text-blue-700': showFilters }">
                    <i class="fas fa-filter {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    <span x-text="showFilters ? '{{ __('app.hide_filters') }}' : '{{ __('app.show_filters') }}'"></span>
                </button>
            </div>
        </div>
        
        <form method="GET" action="{{ route('tasks.index') }}" x-show="showFilters" 
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 transform -translate-y-2"
              x-transition:enter-end="opacity-100 transform translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 transform translate-y-0"
              x-transition:leave-end="opacity-0 transform -translate-y-2">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.task_status') }}</label>
                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <option value="">{{ __('app.all_statuses') }}</option>
                        <option value="Not Started" {{ request('status') == 'Not Started' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>{{ __('app.in_progress') }}</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>{{ __('app.completed') }}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.task_priority') }}</label>
                    <select name="priority" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <option value="">{{ __('app.all_priorities') }}</option>
                        <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>{{ __('app.low') }}</option>
                        <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>{{ __('app.medium') }}</option>
                        <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>{{ __('app.high') }}</option>
                        <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>{{ __('app.critical') }}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.project') }}</label>
                    <select name="project_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <option value="">{{ __('app.all_projects') }}</option>
                        @foreach($projects ?? [] as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.search') }}</label>
                    <input type="text" name="search" placeholder="{{ __('app.search_in_tasks') }}" 
                           value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                </div>
            </div>
            
            <!-- Filter Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                        <i class="fas fa-search {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('app.apply_filters') }}
                    </button>
                    
                    <a href="{{ route('tasks.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-200">
                        <i class="fas fa-times {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('app.clear_filters') }}
                    </a>
                </div>
                
                @if(request()->hasAny(['status', 'priority', 'project_id', 'search']))
                    <div class="text-sm text-gray-500">
                        {{ __('app.showing_filtered_results') }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.task') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.project') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.assignee') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.task_status') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.task_priority') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.due_date') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks ?? [] as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                            <i class="fas fa-tasks text-white"></i>
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $task->project->title ?? __('app.not_specified') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($task->assignedTo)
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full {{ app()->getLocale() == 'ar' ? 'ml-3' : 'mr-3' }}" 
                                                 src="https://ui-avatars.com/api/?name={{ $task->assignedTo->name }}&color=3B82F6&background=EBF4FF" 
                                                 alt="{{ $task->assignedTo->name }}">
                                            <div class="text-sm text-gray-900">{{ $task->assignedTo->name }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">{{ __('app.not_specified') }}</span>
                                    @endif
                                </div>
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
                                @if($task->due_date)
                                    <div class="{{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-600 font-medium' : '' }}">
                                        {{ $task->due_date->format('Y-m-d') }}
                                    </div>
                                    @if($task->due_date->isPast() && $task->status != 'completed')
                                        <div class="text-xs text-red-500">{{ __('app.overdue') }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-500">{{ __('app.not_specified') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-primary hover:text-blue-600" title="{{ __('app.view_task') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('app.edit_task') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" 
                                          onsubmit="return confirm('{{ __('app.confirm_delete_task') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('app.delete_task') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-500">{{ __('app.no_tasks') }}</p>
                                    <p class="text-sm text-gray-400 mt-2">{{ __('app.start_by_creating') }}</p>
                                    <a href="{{ route('tasks.create') }}" class="mt-4 bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                        {{ __('app.add_new_task') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            

        </div>
    </div>
</div>


@endsection 