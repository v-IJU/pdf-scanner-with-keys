<?php

namespace Dtech\PdfScanner\Rules;

class InvoiceDateRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'invoice date');
    }

    public function extract(string $text, string $field): array
    {
        // Match DD/MM/YYYY, DD-MM-YYYY, YYYY/MM/DD etc.
        preg_match_all(
            '/\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\b|\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b/',
            $text,
            $matches
        );

        if (!empty($matches[0])) {
            return $this->found($matches[0][0], 0.85);
        }

        return $this->notFound();
    }
}
