<?php

namespace App\Support;

class Currency
{
    public static function format(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);
        $symbol = (string) config('demo.site.currency.symbol', '৳');
        $decimals = (int) config('demo.site.currency.decimals', 0);
        $position = (string) config('demo.site.currency.position', 'before');

        $formatted = number_format($value, $decimals, '.', ',');

        return $position === 'after'
            ? $formatted . $symbol
            : $symbol . $formatted;
    }
}

