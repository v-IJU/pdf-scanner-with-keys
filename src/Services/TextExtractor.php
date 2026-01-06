<?php

namespace Dtech\PdfScanner\Services;

use Spatie\PdfToText\Pdf;

class TextExtractor
{
    public static function extract(string $file): string
    {
        $binary = config('pdf-scanner.binary_path');

        return (new Pdf($binary))
            ->setPdf($file)
            ->setOptions(['layout'])
            ->text();
    }
}
