<?php

namespace Dtech\PdfScanner\Rules;

class TanRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'tan');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/\b[A-Z]{4}[0-9]{5}[A-Z]\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.94);
        }

        return $this->notFound();
    }
}
