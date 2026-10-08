@extends('layouts.app')

@section('title', __('app.add_new_company'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.add_new_company') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.enter_company_details') }}</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('companies.store') }}" class="p-6">
            @csrf
            
            <!-- Company Info Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.company_info') }}</h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.company_name') }} *</label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                                placeholder="{{ __('app.enter_company_name') }}">
                        </div>

                        <!-- Company Manager -->
                        <div class="md:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.company_owner') }} *</label>
                            <select name="user_id" id="user_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                <option value="">{{ __('app.choose_company_owner') }}..</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} @if($user->email) - {{ $user->email }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200">
                <a href="{{ route('companies.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-2 space-x-reverse' : 'space-x-2' }}">
                    <i class="fas fa-save {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection