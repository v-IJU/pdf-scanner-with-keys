<?php

namespace Dtech\PdfScanner\Rules;

class FallbackRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return true; // Always last
    }

    public function extract(string $text, string $field): array
    {
        $pattern = '/' . preg_quote($field, '/') . '\s*[:\-]?\s*(.{2,50})/i';

        if (preg_match($pattern, $text, $m)) {
            return $this->found($m[1], 0.6);
        }

        return $this->notFound();
    }
}
