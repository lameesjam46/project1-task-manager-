@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
            <p class="text-gray-600 mt-2">{{ $project->description }}</p>
        </div>
        <div class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }}">
            <a href="{{ route('projects.edit', $project) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                <i class="fas fa-edit {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.edit') }}
            </a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline" 
                  onsubmit="return confirm('{{ __('app.project_delete_confirmation') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    <i class="fas fa-trash {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.delete') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Project Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.project_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.project_status') }}</label>
                        <div class="mt-1">
                            @switch($project->status)
                                @case('Pending')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.pending') }}</span>
                                    @break
                                @case('Abuilding')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.abuilding') }}</span>
                                    @break
                                @case('Finished')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.finished') }}</span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">{{ $project->status }}</span>
                            @endswitch
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.project_manager') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($project->projectManager)
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full mr-2" src="https://ui-avatars.com/api/?name={{ $project->projectManager->name }}&color=3B82F6&background=EBF4FF" alt="{{ $project->projectManager->name }}">
                                    {{ $project->projectManager->name }}
                                </div>
                            @else
                                <span class="text-gray-500">{{ __('app.not_specified') }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.company') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($project->company)
                                {{ $project->company->name }}
                            @else
                                <span class="text-gray-500">{{ __('app.not_specified') }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.created_at') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $project->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Progress -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.progress') }}</h3>
                <div class="grid grid-cols-3 gap-4 text-center mb-6">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $project->tasks->count() }}</div>
                        <div class="text-sm text-gray-500">{{ __('app.total_tasks') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $project->tasks->where('status', 'Completed')->count() }}</div>
                        <div class="text-sm text-gray-500">{{ __('app.completed') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $project->tasks->whereIn('status', ['In Progress'])->count() }}</div>
                        <div class="text-sm text-gray-500">{{ __('app.active_tasks') }}</div>
                    </div>
                </div>

                @if($project->tasks->count() > 0)
                <div>
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>{{ __('app.progress') }}</span>
                        <span>{{ round(($project->tasks->where('status', 'Completed')->count() / $project->tasks->count()) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-primary h-3 rounded-full transition-all duration-300" 
                             style="width: {{ ($project->tasks->where('status', 'Completed')->count() / $project->tasks->count()) * 100 }}%"></div>
                    </div>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">{{ __('app.no_tasks') }}</p>
                @endif
            </div>

            <!-- Project Tasks -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('app.tasks') }}</h3>
                    <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">
                        <i class="fas fa-plus ml-2"></i>{{ __('app.add_task') }}
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($project->tasks as $task)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-tasks text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="mr-3">
                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @switch($task->status)
                                @case('pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.pending') }}</span>
                                    @break
                                @case('in_progress')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.in_progress') }}</span>
                                    @break
                                @case('completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.completed') }}</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $task->status }}</span>
                            @endswitch
                            <a href="{{ route('tasks.show', $task) }}" class="text-primary hover:text-blue-600">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">{{ __('app.no_tasks') }}</p>
                        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="mt-4 bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 inline-block">
                            {{ __('app.add_task') }}
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Project Team -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.project_team') }}</h3>
                
                {{-- Scrollable container with fixed height --}}
                <div class="max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    <div class="space-y-3 pr-2">
                        {{-- Project Manager --}}
                        @if($project->projectManager)
                        <div class="flex items-center p-3 border border-blue-200 rounded-lg bg-blue-50">
                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $project->projectManager->name }}&color=3B82F6&background=EBF4FF" alt="{{ $project->projectManager->name }}">
                            <div class="mr-3">
                                <div class="text-sm font-medium text-gray-900">{{ $project->projectManager->name }}</div>
                                <div class="text-xs text-blue-600 font-semibold">{{ __('app.project_manager') }}</div>
                            </div>
                            <div class="mr-auto">
                                <i class="fas fa-crown text-blue-500"></i>
                            </div>
                        </div>
                        @endif

                        {{-- Team Members (Assigned to tasks) --}}
                        @if($assignedUsers && $assignedUsers->count() > 0)
                            @foreach($assignedUsers as $user)
                                {{-- Skip if this user is already shown as project manager --}}
                                @if(!$project->projectManager || $user->id !== $project->projectManager->id)
                                <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&color=6B7280&background=F3F4F6" alt="{{ $user->name }}">
                                    <div class="mr-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ __('app.team_member') }}</div>
                                    </div>
                                    <div class="mr-auto">
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                            {{ $project->tasks->where('assigned_to', $user->id)->count() }} 
                                            {{ $project->tasks->where('assigned_to', $user->id)->count() == 1 ? __('app.task') : __('app.tasks') }}
                                        </span>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- No team members message --}}
                        @if((!$project->projectManager) && (!$assignedUsers || $assignedUsers->count() == 0))
                        <p class="text-gray-500 text-center py-4">{{ __('app.no_team_members') }}</p>
                        @elseif($project->projectManager && (!$assignedUsers || $assignedUsers->where('id', '!=', $project->projectManager->id)->count() == 0))
                        <p class="text-gray-500 text-center py-2 text-sm">{{ __('app.no_tasks_assigned_to_others') }}</p>
                        @endif
                    </div>
                </div>
                
                {{-- Team count indicator --}}
                @if($assignedUsers && $assignedUsers->count() > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        {{ __('app.total_team_members') }}: 
                        {{ ($project->projectManager ? 1 : 0) + $assignedUsers->where('id', '!=', $project->projectManager?->id)->count() }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Project Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.additional_info') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div class="mr-3">
                            <div class="text-sm font-medium text-gray-900">{{ __('app.created_at') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $project->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="mr-3">
                            <div class="text-sm font-medium text-gray-900">{{ __('app.last_updated') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $project->updated_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-plus ml-3 text-primary"></i>
                        إضافة مهمة جديدة
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-edit ml-3 text-primary"></i>
                        تعديل المشروع
                    </a>
                    <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-list ml-3 text-primary"></i>
                        عرض جميع المهام
                    </a>
                </div>
            </div> -->
        </div>
    </div>
</div>
@endsection 