@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.dashboard') }}</h1>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-600 text-white rounded-lg shadow relative overflow-hidden">
            <div class="p-5">
                <div class="text-3xl font-bold">{{ $usersCount }}</div>
                <div class="text-sm opacity-90">{{ __('app.users') }}</div>
                <div class="mt-3 text-xs opacity-90"><a class="underline" href="{{ route('users.index') }}">{{ __('app.more_info') }}</a></div>
            </div>
            <i class="fa-solid fa-users absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-4 text-white/30 text-6xl"></i>
        </div>
        <div class="bg-green-700 text-white rounded-lg shadow relative overflow-hidden">
            <div class="p-5">
                <div class="text-3xl font-bold">{{ $companiesCount }}</div>
                <div class="text-sm opacity-90">{{ __('app.companies') }}</div>
                <div class="mt-3 text-xs opacity-90"><a class="underline" href="{{ route('companies.index') }}">{{ __('app.more_info') }}</a></div>
            </div>
            <i class="fa-solid fa-building absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-4 text-white/30 text-6xl"></i>
        </div>
        <div class="bg-yellow-500 text-white rounded-lg shadow relative overflow-hidden">
            <div class="p-5">
                <div class="text-3xl font-bold">{{ $projectsCount }}</div>
                <div class="text-sm opacity-90">{{ __('app.projects') }}</div>
                <div class="mt-3 text-xs opacity-90"><a class="underline" href="{{ route('projects.index') }}">{{ __('app.more_info') }}</a></div>
            </div>
            <i class="fa-solid fa-folder absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-4 text-white/30 text-6xl"></i>
        </div>
        <div class="bg-red-600 text-white rounded-lg shadow relative overflow-hidden">
            <div class="p-5">
                <div class="text-3xl font-bold">{{ $tasksCount }}</div>
                <div class="text-sm opacity-90">{{ __('app.tasks') }}</div>
                <div class="mt-3 text-xs opacity-90"><a class="underline" href="{{ route('tasks.index') }}">{{ __('app.more_info') }}</a></div>
            </div>
            <i class="fa-solid fa-tasks absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-4 text-white/30 text-6xl"></i>
        </div>
    </div>

    <!-- Goal Completion -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-center font-medium text-gray-800 mb-4">{{ __('app.goal_completion') }}</h3>
        @php
            // Use overall tasks count as denominator to include other statuses (e.g., Overdue, Blocked)
            $total = max(1, $tasksCount ?? 0);
            $ns = $total ? round(($notStarted / $total) * 100) : 0;
            $ip = $total ? round(($inProgress / $total) * 100) : 0;
            $cp = $total ? round(($completed / $total) * 100) : 0;
        @endphp
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>{{ __('app.not_started') }}</span>
                    <span>{{ $notStarted }}/{{ $total }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded">
                    <div class="h-2 bg-gray-700 rounded" style="width: {{ $ns }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>{{ __('app.in_progress') }}</span>
                    <span>{{ $inProgress }}/{{ $total }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded">
                    <div class="h-2 bg-yellow-500 rounded" style="width: {{ $ip }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>{{ __('app.completed') }}</span>
                    <span>{{ $completed }}/{{ $total }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded">
                    <div class="h-2 bg-green-600 rounded" style="width: {{ $cp }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 