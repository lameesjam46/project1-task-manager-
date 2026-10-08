@extends('layouts.app')

@section('title', __('app.profile'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.profile') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.basic_info') }}</p>
    </div>

    <div class="bg-transparent">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Profile Information Card -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('app.account') }}</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">{{ __('app.basic_info') }}</p>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.name') }} *</label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.enter_full_name') }}">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.email') }} *</label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.enter_email') }}">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                                class="bg-gray-800 text-white px-5 py-2 rounded-md hover:bg-gray-900 text-sm">
                            {{ __('app.save') }}
                        </button>
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('app.change_password') }}</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">{{ __('app.password_min_length') }}</p>
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.password') }}</label>
                            <input type="password" name="current_password" id="current_password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.password') }}">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.new_password') }}</label>
                            <input type="password" name="new_password" id="new_password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.new_password') }}">
                        </div>
                        
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.confirm_new_password') }}</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.confirm_new_password') }}">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('app.leave_password_blank') }}</p>
                    <div class="mt-6">
                        <button type="submit"
                                class="bg-gray-800 text-white px-5 py-2 rounded-md hover:bg-gray-900 text-sm">
                            {{ __('app.save') }}
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection 