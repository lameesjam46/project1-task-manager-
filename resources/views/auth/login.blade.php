@extends('layouts.app')

@section('title', 'TaskHub')

@push('styles')
<style>
    @keyframes blue-flash {
        0% { box-shadow: 0 0 0 rgba(59,130,246,0); transform: translateY(10px); opacity: 0; }
        40% { box-shadow: 0 0 30px rgba(59,130,246,0.35); }
        100% { box-shadow: 0 10px 25px rgba(0,0,0,0.08); transform: translateY(0); opacity: 1; }
    }
    .login-card { animation: blue-flash 650ms ease-out; }
    .bg-gradient-soft {
        background: radial-gradient(1200px 600px at 50% -10%, rgba(59,130,246,0.10), rgba(59,130,246,0) 60%),
                    radial-gradient(800px 400px at 80% 0%, rgba(99,102,241,0.08), rgba(99,102,241,0) 60%);
    }
    .input-focus:focus { outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.25); border-color: rgba(59,130,246,0.6); }
    .brand-gradient { background: linear-gradient(135deg, #3B82F6, #6366F1); }
</style>
@endpush

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 bg-gradient-soft py-12 px-4 sm:px-6 lg:px-8">
    <!-- Brand above the login card -->
    <div class="mb-6 text-center">
        <div class="mx-auto h-14 w-14 flex items-center justify-center rounded-xl brand-gradient shadow-sm">
            <i class="fab fa-hubspot text-white text-xl"></i>
        </div>
        <h1 class="mt-3 text-3xl font-extrabold text-gray-900">TaskHub</h1>
    </div>

    <div class="max-w-md w-full space-y-8 login-card bg-white/90 backdrop-blur rounded-2xl p-8 border border-gray-100">
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">{{ __('app.email') }}</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="{{ __('app.email') }}"
                           value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="sr-only">{{ __('app.password') }}</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="{{ __('app.password') }}">
                </div>
            </div>


            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="absolute left-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-sign-in-alt text-white opacity-80"></i>
                    </span>
                    {{ __('app.sign_in') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection