<?php

namespace Dtech\PdfScanner;

class PresetResolver
{
    public static function resolve(array $keys): array
    {
        $resolved = [];

        foreach ($keys as $key) {

            $preset = config("pdf-scanner.presets.$key");

            if ($preset) {
                $resolved = array_merge($resolved, $preset);
            } else {
                $resolved[] = $key;
            }
        }

        return array_values(array_unique($resolved));
    }
}
