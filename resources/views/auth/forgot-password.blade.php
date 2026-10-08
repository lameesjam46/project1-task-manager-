@extends('layouts.app')

@section('title', 'نسيت كلمة المرور')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary">
                <i class="fas fa-key text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                نسيت كلمة المرور
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور
            </p>
        </div>
        
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div>
                <label for="email" class="sr-only">البريد الإلكتروني</label>
                <input id="email" name="email" type="email" required 
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                       placeholder="البريد الإلكتروني"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="absolute left-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-paper-plane text-primary group-hover:text-blue-500"></i>
                    </span>
                    إرسال رابط إعادة تعيين كلمة المرور
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-blue-500">
                    العودة لتسجيل الدخول
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 