<?php

namespace Dtech\PdfScanner\Rules;

class DateRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'date');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all(
            '/\b\d{1,2}[\/\-\.][A-Za-z0-9]{1,3}[\/\-\.]\d{2,4}\b|\b\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4}\b/',
            $text,
            $m
        );

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.9);
        }

        return $this->notFound();
    }
}
