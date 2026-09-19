<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ResolvesMediaUrl
{
    /** Path lokal (storage/app/public) -> URL publik. URL penuh dibiarkan apa adanya. */
    protected function mediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $url = Storage::disk('public')->url($path);

        if (!Str::startsWith($url, ['http://', 'https://'])) {
            if (Str::startsWith($url, '//')) {
                return 'https:' . $url;
            }
            return 'https://' . ltrim($url, '/');
        }

        return $url;
    }
}
