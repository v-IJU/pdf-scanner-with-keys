<?php

namespace Dtech\PdfScanner\Rules;

class PhoneRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'phone') || str_contains(strtolower($field), 'mobile');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/\+?\d{1,3}[\s\-]?\d{10}\b|\b\d{10}\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.9);
        }

        return $this->notFound();
    }
}
