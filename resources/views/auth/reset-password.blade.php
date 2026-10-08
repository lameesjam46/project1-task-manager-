@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary">
                <i class="fas fa-lock text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                إعادة تعيين كلمة المرور
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أدخل كلمة المرور الجديدة
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="space-y-4">
                <div>
                    <label for="password" class="sr-only">كلمة المرور الجديدة</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="كلمة المرور الجديدة">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="sr-only">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="تأكيد كلمة المرور">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="absolute left-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-save text-primary group-hover:text-blue-500"></i>
                    </span>
                    إعادة تعيين كلمة المرور
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