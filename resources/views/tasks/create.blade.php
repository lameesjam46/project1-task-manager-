@extends('layouts.app')

@section('title', __('app.add_new_task'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.add_new_task') }}</h1>
        <p class="text-gray-600 mt-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.enter_task_details') }}</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('tasks.store') }}" class="p-6">
            @csrf
            
            @if(isset($selectedProject))
                <!-- Hidden fields when coming from project page -->
                <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                <input type="hidden" name="return_to_project" value="1">
            @endif
            
            <!-- Task Info Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.task_info') }}</h3>
                
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.title') }}</label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.description') }}</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ old('description') }}</textarea>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.start_time') }}</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.end_time') }}</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    </div>

                    <!-- Select Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_priority') }}</label>
                        <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.select_priority') }}..</option>
                            <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>{{ __('app.low') }}</option>
                            <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>{{ __('app.medium') }}</option>
                            <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>{{ __('app.high') }}</option>
                            <!-- <option value="Critical" {{ old('priority') == 'Critical' ? 'selected' : '' }}>{{ __('app.critical') }}</option> -->
                        </select>
                    </div>

                    <!-- Select Project -->
                    @if(!isset($selectedProject))
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_project') }}</label>
                            <select name="project_id" id="project_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                <option value="">{{ __('app.select_project') }}..</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title ?? $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Show selected project info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} space-x-2">
                                <i class="fas fa-project-diagram text-blue-600"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.selected_project') }}</p>
                                    <p class="text-lg font-semibold text-blue-900 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $selectedProject->title }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Select Employee (Assigned To) -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_employee') }}</label>
                        <select name="assigned_to" id="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.select_employee') }}..</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Is Sub Task Toggle -->
                    <div class="flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_subtask_toggle" class="sr-only peer" {{ old('parent_task_id') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700 {{ app()->getLocale() == 'ar' ? 'mr-3 ml-0' : '' }}">{{ __('app.is_sub_task') }}?</span>
                        </label>
                    </div>

                    <!-- Select Parent Task (shown when Is Sub Task is checked) -->
                    <div id="parent_task_section" style="display: {{ old('parent_task_id') ? 'block' : 'none' }};">
                        <label for="parent_task_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_parent_task') }}</label>
                        <select name="parent_task_id" id="parent_task_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.select_task') }}..</option>
                            @foreach(($parentCandidates ?? []) as $task)
                                <option value="{{ $task->id }}" {{ old('parent_task_id') == $task->id ? 'selected' : '' }}>
                                    {{ $task->title }}
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
                    <!-- Dependencies will be added here dynamically -->
                </div>
                
                <!-- Hidden template for dependency items (outside of form to avoid submission) -->
                <template id="dependency-template">
                    <div class="dependency-item bg-white rounded-lg border border-gray-200 p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.predecessor_task') }}</label>
                                <select class="predecessor-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                    <option value="">{{ __('app.choose_predecessor_task') }}</option>
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
                @if(isset($selectedProject))
                    <a href="{{ route('projects.show', $selectedProject->id) }}" 
                       class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        {{ __('app.cancel') }}
                    </a>
                @else
                    <a href="{{ route('tasks.index') }}" 
                       class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        {{ __('app.cancel') }}
                    </a>
                @endif
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600 flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-2 space-x-reverse' : 'space-x-2' }}">
                    <i class="fas fa-save {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.save_task') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let dependencyIndex = 0;
    // Initial tasks dataset from server if a project is pre-selected
    const initialTasksDataset = @json($tasksDataset ?? []);

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum datetime to now
        const now = new Date();
        const nowString = now.toISOString().slice(0, 16);
        const startTimeInput = document.getElementById('start_time');
        if (startTimeInput) {
            startTimeInput.min = nowString;
        }
        
        // Update end_time minimum when start_time changes
        const endTimeInput = document.getElementById('end_time');
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                endTimeInput.min = this.value;
            });
        }
        
        // Toggle subtask section
        const subtaskToggle = document.getElementById('is_subtask_toggle');
        if (subtaskToggle) {
            subtaskToggle.addEventListener('change', function() {
                const parentTaskSection = document.getElementById('parent_task_section');
                const parentTaskSelect = document.getElementById('parent_task_id');
                const parentTaskLabel = parentTaskSection ? parentTaskSection.querySelector('label') : null;
                
                if (this.checked) {
                    if (parentTaskSection) parentTaskSection.style.display = 'block';
                    if (parentTaskSelect) {
                        parentTaskSelect.required = true;
                        parentTaskSelect.setAttribute('required', 'required');
                    }
                    // Add required indicator to label
                    if (parentTaskLabel && !parentTaskLabel.textContent.includes('*')) {
                        parentTaskLabel.innerHTML = parentTaskLabel.innerHTML + ' <span style="color: red;">*</span>';
                    }
                } else {
                    if (parentTaskSection) parentTaskSection.style.display = 'none';
                    if (parentTaskSelect) {
                        parentTaskSelect.value = '';
                        parentTaskSelect.required = false;
                        parentTaskSelect.removeAttribute('required');
                    }
                    // Remove required indicator from label
                    if (parentTaskLabel) {
                        parentTaskLabel.innerHTML = parentTaskLabel.innerHTML.replace(' <span style="color: red;">*</span>', '');
                    }
                    // When switching to main task, update dependency options
                    updateAllDependencyPredecessorOptions();
                }
            });
        }

        // If there is an initial dataset, store it for filtering
        if (Array.isArray(initialTasksDataset) && initialTasksDataset.length > 0) {
            window.__tasksDataset = initialTasksDataset;
        } else {
            window.__tasksDataset = [];
        }

        // Populate dependency options initially
        updateAllDependencyPredecessorOptions();
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

        // Populate options for the newly added dependency block
        updateDependencyPredecessorOptions(container.lastElementChild.querySelector('.predecessor-select'));
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
    
    // Project change handler
    (function() {
        const projectSelect = document.getElementById('project_id');
        if (!projectSelect) return;

        projectSelect.addEventListener('change', async function() {
            const projectId = this.value;
            const assignedToSelect = document.getElementById('assigned_to');
            if (!assignedToSelect) return;

            // Reset options
            assignedToSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '{{ __('app.select_employee') }}..';
            assignedToSelect.appendChild(placeholder);

            if (!projectId) {
                // Clear tasks dataset, parent candidates and dependencies when no project
                window.__tasksDataset = [];
                refreshParentCandidates([]);
                updateAllDependencyPredecessorOptions();
                return; // No project selected
            }

            try {
                const url = `{{ url('projects') }}/${projectId}/assignable-users`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) throw new Error('Failed to load users');
                const data = await resp.json();
                (data.users || []).forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.name;
                    assignedToSelect.appendChild(opt);
                });

                // Fetch tasks dataset for this project
                const tasksUrl = `{{ url('projects') }}/${projectId}/tasks-dataset`;
                const tResp = await fetch(tasksUrl, { headers: { 'Accept': 'application/json' } });
                if (!tResp.ok) throw new Error('Failed to load tasks');
                const tData = await tResp.json();
                window.__tasksDataset = Array.isArray(tData.tasks) ? tData.tasks : [];
                // Refresh parent candidates (main tasks only)
                const mainTasks = window.__tasksDataset.filter(t => !t.parent_task_id);
                refreshParentCandidates(mainTasks);
                // Update dependencies options according to current toggle/parent
                updateAllDependencyPredecessorOptions();
            } catch (e) {
                console.error(e);
            }
        });
    })();

    // Helper: refresh parent candidates select
    function refreshParentCandidates(mainTasks) {
        const parentSelect = document.getElementById('parent_task_id');
        if (!parentSelect) return;
        const current = parentSelect.value;
        parentSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '{{ __('app.select_task') }}..';
        parentSelect.appendChild(placeholder);
        (mainTasks || []).forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.title;
            parentSelect.appendChild(opt);
        });
        // Try to preserve selection
        if ([...parentSelect.options].some(o => o.value === current)) {
            parentSelect.value = current;
        }
    }

    // Helper: compute allowed predecessors based on subtask/main selection
    function getAllowedPredecessorTasks() {
        const dataset = window.__tasksDataset || [];
        const projectSelect = document.getElementById('project_id');
        const selectedProjectId = projectSelect ? projectSelect.value : (dataset[0]?.project_id || null);
        const isSubtask = document.getElementById('is_subtask_toggle')?.checked;
        if (!selectedProjectId) return [];
        if (!Array.isArray(dataset) || dataset.length === 0) return [];
        if (isSubtask) {
            const parentId = document.getElementById('parent_task_id')?.value;
            if (!parentId) return [];
            // Only subtasks with same parent
            return dataset.filter(t => String(t.parent_task_id || '') === String(parentId));
        }
        // Main task: only main tasks in same project
        return dataset.filter(t => String(t.project_id) === String(selectedProjectId) && (t.parent_task_id === null || t.parent_task_id === undefined));
    }

    // Helper: update a single predecessor select
    function updateDependencyPredecessorOptions(selectEl) {
        if (!selectEl) return;
        const allowed = getAllowedPredecessorTasks();
        const current = selectEl.value;
        selectEl.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '{{ __('app.choose_predecessor_task') }}';
        selectEl.appendChild(placeholder);
        allowed.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.title;
            selectEl.appendChild(opt);
        });
        if ([...selectEl.options].some(o => o.value === current)) {
            selectEl.value = current;
        }
    }

    // Helper: update all dependency blocks
    function updateAllDependencyPredecessorOptions() {
        document.querySelectorAll('.predecessor-select').forEach(sel => updateDependencyPredecessorOptions(sel));
    }

    // Events: when parent_task_id changes, update dependencies
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'parent_task_id') {
            updateAllDependencyPredecessorOptions();
        }
    });
</script>
@endpush
@endsection 