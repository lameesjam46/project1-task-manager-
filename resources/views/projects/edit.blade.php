@extends('layouts.app')

@section('title', __('app.edit_project'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.edit_project') }}</h1>
        <p class="text-gray-600 mt-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.edit_project_details') }}: {{ $project->title }}</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('projects.update', $project) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Name -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.project_name') }} *</label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title', $project->title) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                           placeholder="{{ __('app.enter_project_title') }}">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.project_description') }}</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                              placeholder="{{ __('app.enter_project_description') }}">{{ old('description', $project->description) }}</textarea>
                </div>

               

                <!-- Project Manager -->
                <div>
                    <label for="project_manager_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.project_manager') }}</label>
                    <select name="project_manager_id" id="project_manager_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <option value="">{{ __('app.choose_project_manager') }}</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200">
                <a href="{{ route('projects.show', $project) }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600 flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-2 space-x-reverse' : 'space-x-2' }}">
                    <i class="fas fa-save {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });
</script>
@endpush
@endsection 