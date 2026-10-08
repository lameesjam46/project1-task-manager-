@extends('layouts.app')

@section('title', __('app.add_new_user'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.add_new_user') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.enter_user_details') }}</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.basic_info') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.name') }} *</label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.enter_full_name') }}">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.email') }} *</label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.enter_email') }}">
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.role') }} *</label>
                            <select name="role" id="role" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                <option value="">{{ __('app.choose_role') }}</option>
                                <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>{{ __('app.employee') }}</option>
                                <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>{{ __('app.manager') }}</option>
                                <!-- <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مدير</option> -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.password') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.password') }} *</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.enter_password') }}">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.password_confirmation') }} *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.confirm_password') }}">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('app.password_min_length') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600 flex items-center">
                    <i class="fas fa-save ml-2"></i>
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 
