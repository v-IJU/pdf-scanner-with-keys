<?php

namespace Dtech\PdfScanner\Rules;

interface ExtractionRule
{
    /**
     * Check if this rule supports the given field
     */
    public function supports(string $field): bool;

    /**
     * Extract value from text
     */
    public function extract(string $text, string $field): array;
}
