<?php

use App\Support\Currency;

if (!function_exists('format_price')) {
    function format_price(float|int|string|null $amount): string
    {
        return Currency::format($amount);
    }
}

