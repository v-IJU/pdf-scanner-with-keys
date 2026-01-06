<?php

namespace Dtech\PdfScanner\Rules;

class PanRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'pan');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/\b[A-Z]{5}[0-9]{4}[A-Z]\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.95);
        }

        return $this->notFound();
    }
}
