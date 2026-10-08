@extends('layouts.app')

@section('title', __('app.projects'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.projects') }}</h1>
        <a href="{{ route('projects.create') }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center">
            <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('app.add_new_project') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('projects.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Company Filter -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.filter_by_company') }}</label>
                <select name="company_id" id="company_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('app.all_companies') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.filter_by_status') }}</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('app.all_statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            @switch($status)
                                @case('Pending')
                                    {{ __('app.pending') }}
                                    @break
                                @case('Abuilding')
                                    {{ __('app.abuilding') }}
                                    @break
                                @case('Finished')
                                    {{ __('app.finished') }}
                                    @break
                                @default
                                    {{ $status }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center">
                    <i class="fas fa-filter ml-2"></i>
                    {{ __('app.filter') }}
                </button>
                <a href="{{ route('projects.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 flex items-center">
                    <i class="fas fa-times ml-2"></i>
                    {{ __('app.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects ?? [] as $project)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Project Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $project->title }}</h3>
                      
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('app.edit_project') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline" 
                              onsubmit="return confirm('{{ __('app.confirm_delete_project') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('app.delete_project') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-4">
                    @switch($project->status)
                        @case('Pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.pending') }}</span>
                            @break
                        @case('Abuilding')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.abuilding') }}</span>
                            @break
                        @case('Finished')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.finished') }}</span>
                            @break
                        @default
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $project->status }}</span>
                    @endswitch
                </div>

                <!-- Project Info -->
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('app.company') }}:</span>
                        <span class="text-gray-900">{{ $project->company->name ?? __('app.not_specified') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('app.project_manager') }}:</span>
                        <span class="text-gray-900">{{ $project->projectManager->name ?? __('app.not_specified') }}</span>
                    </div>
                    {{-- Created at can be shown if desired using translations --}}
                </div>
            </div>

            <!-- Project Stats -->
            <div class="p-6 bg-gray-50">
                <!-- <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $project->tasks->count() }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.tasks') }}</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-green-600">{{ $project->tasks->where('status', 'Completed')->count() }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.completed') }}</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-yellow-600">{{ $project->tasks->whereIn('status', ['In Progress'])->count() }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.active_tasks') }}</div>
                    </div>
                </div> -->

                <!-- Progress Bar -->
                @if($project->tasks->count() > 0)
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>{{ __('app.progress') }}</span>
                        <span>{{ round(($project->tasks->where('status', 'Completed')->count() / $project->tasks->count()) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full" 
                             style="width: {{ ($project->tasks->where('status', 'Completed')->count() / $project->tasks->count()) * 100 }}%"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <a href="{{ route('projects.show', $project) }}" 
                       class="text-primary hover:text-blue-600 text-sm font-medium">
                        {{ __('app.view_details') }}
                    </a>
                    <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" 
                       class="bg-primary text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        {{ __('app.add_task') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-project-diagram text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-500 mb-2">{{ __('app.no_projects') }}</h3>
                <p class="text-gray-400 mb-4">{{ __('app.start_by_creating_project') }}</p>
                <a href="{{ route('projects.create') }}" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600">
                    {{ __('app.create_new_project') }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($projects) && $projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyFilter = document.getElementById('company_id');
    const statusFilter = document.getElementById('status');
    const filterForm = companyFilter.closest('form');
    
    // Auto-submit form when filter values change
    companyFilter.addEventListener('change', function() {
        filterForm.submit();
    });
    
    statusFilter.addEventListener('change', function() {
        filterForm.submit();
    });
});
</script>
@endsection 