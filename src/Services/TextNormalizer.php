<?php
namespace Dtech\PdfScanner\Services;

class TextNormalizer
{
    public static function normalize(string $text): string
    {
        $text = strtoupper($text);
        $text = preg_replace('/[^\x20-\x7E]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
