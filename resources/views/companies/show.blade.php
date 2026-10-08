@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $company->description }}</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('companies.edit', $company) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                <i class="fas fa-edit {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.edit_company') }}
            </a>
            <form method="POST" action="{{ route('companies.destroy', $company) }}" class="inline" 
                  onsubmit="return confirm('{{ __('app.confirm_delete_company') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    <i class="fas fa-trash {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.delete_company') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Company Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.company_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($company->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.email') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $company->email }}</div>
                    </div>
                    @endif
                    
                    @if($company->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.phone') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $company->phone }}</div>
                    </div>
                    @endif
                    
                    @if($company->address)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.address') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $company->address }}</div>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.creation_date') }}</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $company->created_at ? $company->created_at->format('Y-m-d') : __('app.not_specified') }}
                        </div>
                    </div>

                    @if($company->owner)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">{{ __('app.company_owner') }}</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $company->owner->name }}</div>
                        @if($company->owner->email)
                        <div class="text-xs text-gray-500">{{ $company->owner->email }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Company Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.company_statistics') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $company->employees->count() }}</div>
                        <div class="text-sm text-gray-600">{{ __('app.employees') }}</div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $company->projects->count() }}</div>
                        <div class="text-sm text-gray-600">{{ __('app.projects') }}</div>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $company->projects->where('status', 'Abuilding')->count() }}</div>
                        <div class="text-sm text-gray-600">{{ __('app.active_projects') }}</div>
                    </div>
                    
                </div>
            </div>

            <!-- Company Projects -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('app.company_projects') }}</h3>
                    <a href="{{ route('projects.create', ['company_id' => $company->id, 'from_company' => 1]) }}" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">
                        <i class="fas fa-plus {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.add_project') }}
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($company->projects as $project)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-project-diagram text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="mr-3">
                                <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($project->description, 50) }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @switch($project->status)
                                @case('active')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('app.active') }}</span>
                                    @break
                                @case('completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('app.completed') }}</span>
                                    @break
                                @case('on_hold')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('app.on_hold') }}</span>
                                    @break
                                @case('cancelled')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('app.cancelled') }}</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $project->status }}</span>
                            @endswitch
                            <a href="{{ route('projects.show', $project) }}" class="text-primary hover:text-blue-600">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-project-diagram text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">{{ __('app.no_company_projects') }}</p>
                        <a href="{{ route('projects.create', ['company_id' => $company->id, 'from_company' => 1]) }}" class="mt-4 bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 inline-block">
                            {{ __('app.add_new_project') }}
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

      
            <!-- Company Employees -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.company_employees') }}</h3>
                <div class="space-y-3">
                    @forelse($company->employees as $employee)
                    <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $employee->name }}&color=3B82F6&background=EBF4FF" alt="{{ $employee->name }}">
                        <div class="mr-3">
                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                {{ $employee->name }}
                                @if($company->owner && $employee->id === $company->owner->id)
                                    <span class="inline-flex items-center text-[10px] font-semibold text-yellow-700 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}" title="{{ __('app.company_owner') }}">
                                        <i class="fas fa-crown"></i>
                                        <span class="{{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }}">{{ __('app.company_owner') }}</span>
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">{{ __('app.no_employees') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <!-- <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('projects.create', ['company_id' => $company->id, 'from_company' => 1]) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-plus ml-3 text-primary"></i>
                        إضافة مشروع جديد
                    </a>
                    <a href="{{ route('companies.edit', $company) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-edit ml-3 text-primary"></i>
                        تعديل الشركة
                    </a>
                    <a href="{{ route('projects.index', ['company_id' => $company->id]) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-list ml-3 text-primary"></i>
                        عرض جميع المشاريع
                    </a>
                </div>
            </div> -->
        </div>
    </div>
</div>
@endsection 