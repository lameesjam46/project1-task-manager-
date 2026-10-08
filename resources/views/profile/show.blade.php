@extends('layouts.app')

@section('title', __('app.profile'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.profile') }}</h1>
        <a href="{{ route('profile.edit') }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600">
            <i class="fas fa-edit ml-2"></i>{{ __('app.edit') }}
        </a>
    </div>

    <!-- Basic Info Only -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.basic_info') }}</h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('app.name') }}</label>
                <div class="mt-1 text-sm text-gray-900">{{ $user->name }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('app.email') }}</label>
                <div class="mt-1 text-sm text-gray-900">{{ $user->email }}</div>
            </div>
        </div>
    </div>
</div>
@endsection