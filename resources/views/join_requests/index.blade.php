@extends('layouts.app')

@section('title', __('app.join_requests_title'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.join_requests_title') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('app.join_requests_subtitle') }}</p>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('join-requests.index') }}" class="bg-white p-4 rounded-md border border-gray-200 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.company_name') }}</label>
                <input type="text" name="company" value="{{ $filters['company'] ?? '' }}" placeholder="{{ __('app.company_name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.status') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('app.all') }}</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                    <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>{{ __('app.approved') }}</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>{{ __('app.rejected') }}</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ __('app.filter') }}</button>
                <a href="{{ route('join-requests.index') }}" class="px-4 py-2 rounded-md border">{{ __('app.reset') }}</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-md border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.id') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.company') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.user') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.created_at') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $req)
                        <tr>
                            <td class="px-4 py-2 text-gray-700">{{ $req->id }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $req->company->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $req->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if($req->status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">{{ __('app.pending') }}</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">{{ __('app.approved') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">{{ __('app.rejected') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-500 text-sm">{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">{{ __('app.no_requests') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
