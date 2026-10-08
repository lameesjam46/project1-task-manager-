<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center {{ app()->getLocale() == 'ar' ? 'space-x-2 space-x-reverse' : 'space-x-2' }} text-white transition-none">
        <i class="fas fa-globe"></i>
        <span class="text-sm font-medium">{{ app()->getLocale() == 'ar' ? __('app.arabic') : __('app.english') }}</span>
        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false" 
         class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 py-1 z-50">
        
        <a href="{{ route('language.change', 'ar') }}" 
           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150 {{ app()->getLocale() == 'ar' ? 'bg-blue-50 text-blue-700' : '' }}">
            <span class="ml-2">🇸🇦</span>
            <span>العربية</span>
            @if(app()->getLocale() == 'ar')
                <i class="fas fa-check text-blue-600 mr-auto"></i>
            @endif
        </a>
        
        <a href="{{ route('language.change', 'en') }}" 
           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150 {{ app()->getLocale() == 'en' ? 'bg-blue-50 text-blue-700' : '' }}">
            <span class="ml-2">🇺🇸</span>
            <span>English</span>
            @if(app()->getLocale() == 'en')
                <i class="fas fa-check text-blue-600 mr-auto"></i>
            @endif
        </a>
    </div>
</div>
