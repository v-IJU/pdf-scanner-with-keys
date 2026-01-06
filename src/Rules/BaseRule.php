<?php

namespace Dtech\PdfScanner\Rules;

abstract class BaseRule
{
    protected function notFound(): array
    {
        return [
            'value' => null,
            'confidence' => 0,
            'found' => false
        ];
    }

    protected function found(string $value, float $confidence): array
    {
        return [
            'value' => trim($value),
            'confidence' => $confidence,
            'found' => true
        ];
    }
}
