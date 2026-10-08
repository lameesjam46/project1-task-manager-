@extends('layouts.app')

@section('title', __('app.edit_task'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.edit_task') }}</h1>
        <p class="text-gray-600 mt-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.edit_task_details') }}: {{ $task->title }}</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="p-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="sync_dependencies" value="1">

            <!-- Task Info Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.task_info') }}</h3>

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.title') }}</label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title', $task->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.description') }}</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.start_time') }}</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time', optional($task->start_time)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.end_time') }}</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time', optional($task->end_time)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- Select Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_priority') }}</label>
                        <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.select_priority') }}..</option>
                            <option value="Low" {{ old('priority', $task->priority) == 'Low' ? 'selected' : '' }}>{{ __('app.low') }}</option>
                            <option value="Medium" {{ old('priority', $task->priority) == 'Medium' ? 'selected' : '' }}>{{ __('app.medium') }}</option>
                            <option value="High" {{ old('priority', $task->priority) == 'High' ? 'selected' : '' }}>{{ __('app.high') }}</option>
                            <option value="Critical" {{ old('priority', $task->priority) == 'Critical' ? 'selected' : '' }}>{{ __('app.critical') }}</option>
                        </select>
                    </div>

                    <!-- Assigned Employee -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.assigned_employee') ?? 'الموظف المكلف' }}</label>
                        <select name="assigned_to" id="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.choose_employee') ?? 'اختر الموظف (اختياري)' }}</option>
                            @foreach(($users ?? []) as $user)
                                <option value="{{ $user->id }}" {{ (string)old('assigned_to', $task->assigned_to) === (string)$user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    
                </div>
            </div>

            <!-- Task Dependencies Section -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.task_dependencies') }}</h3>

                <div id="dependencies-container">
                    @forelse(($task->predecessorTasks ?? []) as $pred)
                        <div class="dependency-item bg-white rounded-lg border border-gray-200 p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.predecessor_task') }}</label>
                                    <select name="dependencies[{{ $loop->index }}][predecessor_task_id]" class="predecessor-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                        <option value="">{{ __('app.choose_predecessor_task') }}</option>
                                        @foreach($availableTasks ?? [] as $t)
                                            <option value="{{ $t->id }}" {{ $pred->id == $t->id ? 'selected' : '' }}>
                                                {{ $t->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.dependency_type') }}</label>
                                    <select name="dependencies[{{ $loop->index }}][dependency_type]" class="dependency-type-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                        <option value="">{{ __('app.choose_dependency_type') }}</option>
                                        <option value="FS" {{ $pred->pivot->dependency_type === 'FS' ? 'selected' : '' }}>{{ __('app.finish_to_start') }} (FS)</option>
                                        <option value="SS" {{ $pred->pivot->dependency_type === 'SS' ? 'selected' : '' }}>{{ __('app.start_to_start') }} (SS)</option>
                                        <option value="FF" {{ $pred->pivot->dependency_type === 'FF' ? 'selected' : '' }}>{{ __('app.finish_to_finish') }} (FF)</option>
                                        <option value="SF" {{ $pred->pivot->dependency_type === 'SF' ? 'selected' : '' }}>{{ __('app.start_to_finish') }} (SF)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button" class="remove-dependency text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="fas fa-trash {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.remove_dependency') }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <!-- No existing dependencies; will be added dynamically -->
                    @endforelse
                </div>

                <!-- Hidden template for dependency items (outside of form to avoid submission) -->
                <template id="dependency-template">
                    <div class="dependency-item bg-white rounded-lg border border-gray-200 p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.predecessor_task') }}</label>
                                <select class="predecessor-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                    <option value="">{{ __('app.choose_predecessor_task') }}</option>
                                    @foreach($availableTasks ?? [] as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.dependency_type') }}</label>
                                <select class="dependency-type-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                    <option value="">{{ __('app.choose_dependency_type') }}</option>
                                    <option value="FS">{{ __('app.finish_to_start') }} (FS)</option>
                                    <option value="SS">{{ __('app.start_to_start') }} (SS)</option>
                                    <option value="FF">{{ __('app.finish_to_finish') }} (FF)</option>
                                    <option value="SF">{{ __('app.start_to_finish') }} (SF)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" class="remove-dependency text-red-600 hover:text-red-800 text-sm font-medium">
                                <i class="fas fa-trash {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.remove_dependency') }}
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" id="add-dependency" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.add_dependency') }}
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200">
                <a href="{{ route('tasks.show', $task) }}" 
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
    let dependencyIndex = {{ isset($task) && $task->predecessorTasks ? $task->predecessorTasks->count() : 0 }};

    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum datetime to now
        const now = new Date();
        const nowString = now.toISOString().slice(0, 16);
        const startTimeInput = document.getElementById('start_time');
        if (startTimeInput && !startTimeInput.value) {
            startTimeInput.min = nowString;
        }

        // Update end_time minimum when start_time changes
        const endTimeInput = document.getElementById('end_time');
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                endTimeInput.min = this.value;
            });
        }
    });

    // Add dependency functionality
    document.getElementById('add-dependency').addEventListener('click', function() {
        const container = document.getElementById('dependencies-container');
        const template = document.getElementById('dependency-template');
        const newDependency = template.content.cloneNode(true);

        // Set the names for form submission
        const predecessorSelect = newDependency.querySelector('.predecessor-select');
        const dependencyTypeSelect = newDependency.querySelector('.dependency-type-select');

        predecessorSelect.name = `dependencies[${dependencyIndex}][predecessor_task_id]`;
        dependencyTypeSelect.name = `dependencies[${dependencyIndex}][dependency_type]`;

        container.appendChild(newDependency);
        dependencyIndex++;
    });

    // Remove dependency functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-dependency')) {
            const dependencyItem = e.target.closest('.dependency-item');
            if (dependencyItem) {
                dependencyItem.remove();
            }
        }
    });
</script>
@endpush

@endsection