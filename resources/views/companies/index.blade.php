@extends('layouts.app')

@section('title', __('app.companies'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.companies') }}</h1>
        <a href="{{ route('companies.create') }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center">
            <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('app.add_new_company') }}
        </a>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('companies.index') }}" class="bg-white shadow rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('app.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('app.search') }} {{ __('app.companies') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
            </div>
            <div class="flex space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600">
                    <i class="fas fa-search {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.apply_filters') }}
                </button>
                <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    <i class="fas fa-times {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.clear_filters') }}
                </a>
            </div>
        </div>
    </form>

    <!-- Companies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($companies ?? [] as $company)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Company Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $company->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($company->description, 100) }}</p>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <a href="{{ route('companies.edit', $company) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('app.edit_company') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('companies.destroy', $company) }}" class="inline" 
                              onsubmit="return confirm('{{ __('app.confirm_delete_company') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('app.delete_company') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="space-y-2 text-sm">
                    @if($company->email)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('app.email') }}:</span>
                        <span class="text-gray-900">{{ $company->email }}</span>
                    </div>
                    @endif
                    @if($company->phone)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('app.phone') }}:</span>
                        <span class="text-gray-900">{{ $company->phone }}</span>
                    </div>
                    @endif
                    @if($company->address)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('app.address') }}:</span>
                        <span class="text-gray-900">{{ Str::limit($company->address, 30) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Company Stats -->
            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $company->employees->count() }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.employees') }}</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $company->projects->count() }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.projects') }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <a href="{{ route('companies.show', $company) }}" 
                       class="text-primary hover:text-blue-600 text-sm font-medium">
                        {{ __('app.view_details') }}
                    </a>
                    <a href="{{ route('projects.create', ['company_id' => $company->id]) }}" 
                       class="bg-primary text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        {{ __('app.add_project') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-building text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-500 mb-2">{{ __('app.no_companies') }}</h3>
                <p class="text-gray-400 mb-4">{{ __('app.start_by_creating_company') }}</p>
                <a href="{{ route('companies.create') }}" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-600">
                    {{ __('app.create_new_company') }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($companies) && $companies->hasPages())
        <div class="mt-6">
            {{ $companies->appends(request()->only('search'))->links() }}
        </div>
    @endif
</div>
@endsection