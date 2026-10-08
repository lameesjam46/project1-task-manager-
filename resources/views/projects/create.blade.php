@extends('layouts.app')

@section('title', __('app.add_new_project'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.add_new_project') }}</h1>
        <p class="text-gray-600 mt-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.enter_project_details') }}</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('projects.store') }}" class="p-6">
            @csrf
            
            <!-- Project Info Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.project_info') }}</h3>
                
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

                    <!-- Select Company -->
                    @if(!empty($preselectedCompanyId))
                        <!-- Coming from company page: lock company and hide selector -->
                        <input type="hidden" name="company_id" value="{{ $preselectedCompanyId }}">
                        <input type="hidden" name="from_company" value="1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.company') }}</label>
                            <input type="text" readonly
                                   value="{{ optional(($companies ?? collect())->firstWhere('id', $preselectedCompanyId))->name }}"
                                   class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        </div>
                    @else
                        <div>
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_company') }}</label>
                            <select name="company_id" id="company_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                <option value="">{{ __('app.select_company') }}..</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}" {{ (old('company_id', $preselectedCompanyId ?? '') == $company->id) ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Select Project Manager -->
                    <div>
                        <label for="project_manager_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.select_project_manager') }}</label>
                        <select name="project_manager_id" id="project_manager_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <option value="">{{ __('app.select_project_manager') }}..</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hidden Status Field (default to Pending) -->
                    <input type="hidden" name="status" value="Pending">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200">
                <a href="{{ (!empty($preselectedCompanyId)) ? route('companies.show', $preselectedCompanyId) : route('projects.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-2 space-x-reverse' : 'space-x-2' }}">
                    <i class="fas fa-save {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.save_project') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const companySelect = document.getElementById('company_id');
        const managerSelect = document.getElementById('project_manager_id');
        if (!companySelect || !managerSelect) return;

        async function loadManagers(companyId){
            // reset
            managerSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '{{ __('app.select_project_manager') }}..';
            managerSelect.appendChild(placeholder);
            if (!companyId) return;

            try {
                const url = `{{ url('companies') }}/${companyId}/eligible-managers`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) throw new Error('Failed');
                const data = await resp.json();
                (data.users || []).forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.name;
                    managerSelect.appendChild(opt);
                });
            } catch(e){
                console.error(e);
            }
        }

        companySelect.addEventListener('change', function(){
            loadManagers(this.value);
        });
    })();
</script>
@endpush
@endsection