@extends('layouts.app')

@section('title', __('app.users'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.users') }}</h1>
        <a href="{{ route('users.create') }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center">
            <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('app.add_new_user') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-4 sm:p-6">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.status') }}</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.role') }}</label>
                    <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="manager" {{ request('role')==='manager' ? 'selected' : '' }}>{{ __('app.manager') }}</option>
                        <option value="employee" {{ request('role')==='employee' ? 'selected' : '' }}>{{ __('app.employee') }}</option>
                    </select>
                </div>
                <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-start' }} gap-2">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        {{ __('app.filter') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        {{ __('app.reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.user') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.email') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.role') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.joined_date') }}</th>
                            <th class="px-6 py-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users ?? [] as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&color=3B82F6&background=EBF4FF" alt="{{ $user->name }}">
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        @if($user->profile && $user->profile->phone)
                                            <div class="text-sm text-gray-500">{{ $user->profile->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($user->role)
                                    @case('admin')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('app.admin') }}</span>
                                        @break
                                    @case('manager')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.manager') }}</span>
                                        @break
                                    @case('user')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.user') }}</span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $user->role }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.active') }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">{{ __('app.inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->created_at ? $user->created_at->format('Y-m-d') : __('app.not_specified') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('users.show', $user) }}" class="text-primary hover:text-blue-600" title="{{ __('app.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('app.edit_user') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-1 rounded {{ $user->active ? 'bg-gray-200 text-gray-800 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700' }} disabled:opacity-50 disabled:cursor-not-allowed" title="{{ $user->active ? __('app.disable') : __('app.enable') }}" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            @if($user->active)
                                                <i class="fas fa-user-slash"></i>
                                            @else
                                                <i class="fas fa-user-check"></i>
                                            @endif
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" 
                                          onsubmit="return confirm('{{ __('app.confirm_delete_user') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('app.delete_user') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-500">{{ __('app.no_users') }}</p>
                                    <p class="text-sm text-gray-400 mt-2">{{ __('app.start_by_creating_user') }}</p>
                                    <a href="{{ route('users.create') }}" class="mt-4 bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                        {{ __('app.add_new_user') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($users) && $users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 