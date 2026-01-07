<?php

namespace Dtech\PdfScanner\Rules;

class AccountRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'account') || str_contains(strtolower($field), 'utr');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/\b[0-9]{9,18}\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.9);
        }

        return $this->notFound();
    }
}
