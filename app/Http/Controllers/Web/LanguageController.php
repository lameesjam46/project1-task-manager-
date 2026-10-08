<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change application language.
     */
    public function change(string $locale): RedirectResponse
    {
        if (!in_array($locale, ['ar', 'en'], true)) {
            $locale = 'ar';
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        return back();
    }
}
