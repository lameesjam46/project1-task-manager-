<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the application language
     */
    public function changeLanguage($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }
        
        return redirect()->back();
    }
}
