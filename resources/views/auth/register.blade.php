@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                إنشاء حساب جديد
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أو
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-blue-500">
                    تسجيل الدخول إذا كان لديك حساب
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">الاسم</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="الاسم الكامل"
                           value="{{ old('name') }}">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="البريد الإلكتروني"
                           value="{{ old('email') }}">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="كلمة المرور">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="تأكيد كلمة المرور">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="absolute left-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-user-plus text-primary group-hover:text-blue-500"></i>
                    </span>
                    إنشاء الحساب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 