<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class LanguageHelper
{
    /**
     * Get the current language direction
     */
    public static function getDirection()
    {
        return App::getLocale() == 'ar' ? 'rtl' : 'ltr';
    }

    /**
     * Get the opposite direction
     */
    public static function getOppositeDirection()
    {
        return App::getLocale() == 'ar' ? 'ltr' : 'rtl';
    }

    /**
     * Get margin/padding class for icons based on language
     */
    public static function getIconMargin($position = 'right')
    {
        if (App::getLocale() == 'ar') {
            return $position == 'right' ? 'ml-2' : 'mr-2';
        } else {
            return $position == 'right' ? 'mr-2' : 'ml-2';
        }
    }

    /**
     * Get text alignment class based on language
     */
    public static function getTextAlign()
    {
        return App::getLocale() == 'ar' ? 'text-right' : 'text-left';
    }

    /**
     * Get flex direction class based on language
     */
    public static function getFlexDirection()
    {
        return App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row';
    }

    /**
     * Get space classes for RTL/LTR
     */
    public static function getSpaceClasses($spacing = 'space-x-3')
    {
        return App::getLocale() == 'ar' ? $spacing . ' space-x-reverse' : $spacing;
    }

    /**
     * Get dropdown position class
     */
    public static function getDropdownPosition()
    {
        return App::getLocale() == 'ar' ? 'left-0' : 'right-0';
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages()
    {
        return [
            'ar' => 'العربية',
            'en' => 'English'
        ];
    }

    /**
     * Get current language name
     */
    public static function getCurrentLanguageName()
    {
        $languages = self::getAvailableLanguages();
        return $languages[App::getLocale()] ?? 'Unknown';
    }
}
