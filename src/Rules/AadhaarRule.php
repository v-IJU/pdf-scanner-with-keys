<?php

namespace Dtech\PdfScanner\Rules;

class AadhaarRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'aadhaar') || str_contains(strtolower($field), 'cin');
    }

    public function extract(string $text, string $field): array
    {
        // Aadhaar: 12 digits
        preg_match_all('/\b\d{4}\s?\d{4}\s?\d{4}\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found(str_replace(' ', '', $m[0][0]), 0.9);
        }

        // CIN: L12345MH2020PLC123456
        preg_match_all('/[L|U][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}/', $text, $cin);
        if (!empty($cin[0])) {
            return $this->found($cin[0][0], 0.9);
        }

        return $this->notFound();
    }
}
