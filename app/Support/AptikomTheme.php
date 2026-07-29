<?php

namespace App\Support;

use App\Models\User;

class AptikomTheme
{
    public const APTIKOM_COLOR = '#007bff';

    public const NON_APTIKOM_COLOR = '#ffeb3b';

    public static function resolve(?User $user, ?string $fallback = null): string
    {
        if ($user && $user->prodi) {
            return filter_var($user->prodi->is_aptikom, FILTER_VALIDATE_BOOLEAN)
                ? self::APTIKOM_COLOR
                : self::NON_APTIKOM_COLOR;
        }

        return $fallback ?: self::APTIKOM_COLOR;
    }

    public static function isColorDark(string $hexColor): bool
    {
        $hex = ltrim($hexColor, '#');

        if (strlen($hex) !== 6) {
            return true;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $yiq < 128;
    }
}
