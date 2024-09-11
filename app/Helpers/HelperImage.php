<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class HelperImage
{
    const PATH_LOGO_DEFAULT = 'system_settings/system_logo/logoDefault.png';
    const PATH_LOGO = 'system_settings/system_logo/logoDefault.png';
    const PUBLIC_DISK = 'public';

    public static function getLogo(): string
    {
        if (!Storage::disk(self::PUBLIC_DISK)->exists(self::PATH_LOGO)) {
            return Storage::disk(self::PUBLIC_DISK)->url(self::PATH_LOGO_DEFAULT);
        }

        return Storage::disk(self::PUBLIC_DISK)->url(self::PATH_LOGO);
    }
}
