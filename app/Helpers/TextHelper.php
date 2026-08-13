<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TextHelper
{
    /**
     * Clean HTML text by stripping tags, decoding HTML entities (&nbsp;, &amp;, etc.),
     * normalizing whitespace, and truncating to N words safely.
     *
     * @param string|null $content
     * @param int $words
     * @param string $end
     * @return string
     */
    public static function cleanText(?string $content, int $words = 10, string $end = '...'): string
    {
        if (empty($content)) {
            return '';
        }

        // 1. Strip all HTML tags
        $text = strip_tags($content);

        // 2. Decode HTML entities multiple times (handles double-encoded entities like &amp;nbsp;)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 3. Replace non-breaking spaces (\u{00A0}) and multiple spaces/newlines with a single space
        $text = preg_replace('/\s+/u', ' ', $text);

        // 4. Trim leading and trailing whitespace
        $text = trim($text);

        // 5. Truncate by words
        return Str::words($text, $words, $end);
    }
}
