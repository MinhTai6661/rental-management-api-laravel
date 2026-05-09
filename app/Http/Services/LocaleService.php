<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\App;

class LocaleService
{
    protected static array $supportedLocales = ['vi', 'en', 'ja'];

    public static function setLocaleFromBrowser(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $request = request();
        $supportedLocales = config('app.supported_locales', ['vi', 'en']);

        // get from cookie
        $localCookie = $request->cookie('i18n_redirected');
        if ($localCookie && in_array($localCookie, $supportedLocales)) {
            $locale = $localCookie;
            App::setLocale($locale);

            return;
        }

        // get from browser
        $locale = $request->getPreferredLanguage($supportedLocales);

        if ($locale) {
            App::setLocale($locale);
        }
    }
}
