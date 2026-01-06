<?php

namespace Dtech\PdfScanner;

use Dtech\PdfScanner\Rules\ExtractionRule;

class RuleRegistry
{
    protected static array $rules = [];

    public static function register(ExtractionRule $rule): void
    {
        self::$rules[] = $rule;
    }

    public static function all(): array
    {
        return self::$rules;
    }

    public static function clear(): void
    {
        self::$rules = [];
    }
}
