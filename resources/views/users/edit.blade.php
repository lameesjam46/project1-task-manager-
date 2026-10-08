@extends('layouts.app')

@section('title', __('app.edit_user'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.edit_user') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.edit_user_details') }}: {{ $user->name }}</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.basic_info') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        
                        <!-- <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">الدور *</label>
                            <select name="role" id="role" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                <option value="">اختر الدور</option>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>مستخدم</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>مشرف</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مدير</option>
                            </select>
                        </div> -->
                    </div>
                </div>

                <!-- Password Change -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.change_password') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.new_password') }}</label>
                            <input type="password" name="password" id="password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.new_password') }}">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                   placeholder="{{ __('app.confirm_new_password') }}">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('app.leave_password_blank') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('users.show', $user) }}" 
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
