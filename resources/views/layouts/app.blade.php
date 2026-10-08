<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.dashboard'))</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#64748B',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        [dir="rtl"] {
            direction: rtl;
        }
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        /* Navbar animated gradient */
        .nav-gradient {
            background: linear-gradient(90deg, #1D4ED8, #3B82F6, #60A5FA);
            background-size: 200% 200%;
            animation: gradient-move 6s ease infinite;
        }
        @keyframes gradient-move {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .brand-badge {
            display: inline-flex; align-items: center; gap: .5rem; padding: .375rem .75rem; border-radius: .75rem;
            background: rgba(255,255,255,.12); color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
        }
        .admin-badge {
            display: inline-flex; align-items: center; gap: .375rem; padding: .25rem .5rem; border-radius: .5rem;
            background: rgba(255,255,255,.12); color: #fff; font-size: .75rem; font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans pt-16">
    <!-- Navigation -->
    <nav class="nav-gradient shadow-lg border-b border-transparent fixed top-0 inset-x-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="brand-badge">
                            <i class="fab fa-hubspot"></i>
                            <span class="text-sm font-semibold">TaskHub</span>
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse text-white">
                    <!-- Language Switcher -->
                    <x-language-switcher />

                    @auth
                        @if(auth()->user()->role === 'Admin')
                            <span class="admin-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Admin</span>
                            </span>
                        @endif
                    @endauth

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 space-x-reverse text-white transition-none">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&color=3B82F6&background=EBF4FF" alt="{{ auth()->user()->name }}">
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('app.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest: keep language switcher only; no Login button -->
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        @auth
            <!-- Sidebar -->
            <aside class="w-64 bg-gradient-to-b from-blue-50 to-blue-100 shadow-lg sidebar-transition sticky top-16 self-start h-[calc(100vh-4rem)] overflow-y-auto" x-data="{ open: true }">
                <div class="p-4">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>{{ __('app.dashboard') }}</span>
                        </a>
                        
                        <a href="{{ route('tasks.index') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('tasks.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>{{ __('app.tasks') }}</span>
                        </a>
                        
                        <a href="{{ route('projects.index') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('projects.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span>{{ __('app.projects') }}</span>
                        </a>
                        
                        <a href="{{ route('companies.index') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('companies.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>{{ __('app.companies') }}</span>
                        </a>

                        <a href="{{ route('join-requests.index') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('join-requests.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-user-plus"></i>
                            <span>{{ __('app.join_requests') }}</span>
                        </a>

                        <a href="{{ route('users.index') }}" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-3 space-x-reverse' : 'space-x-3' }} p-3 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('users.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>{{ __('app.users') }}</span>
                        </a>
                    </nav>
                </div>
            </aside>
        @endauth

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html> 