@extends('layouts.app')

@section('title', __('app.edit_company'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.edit_company') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.edit_company_details') }}: {{ $company->name }}</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('companies.update', $company) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.company_name') }} *</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $company->name) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                           placeholder="{{ __('app.enter_company_name') }}">
                </div>

                <!-- Company Manager -->
                <div class="md:col-span-2">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.company_owner') }} *</label>
                    <select name="user_id" id="user_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                        <option value="">{{ __('app.choose_company_owner') }}..</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $company->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} @if($user->email) - {{ $user->email }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('companies.show', $company) }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600 flex items-center">
                    <i class="fas fa-save ml-2"></i>
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 