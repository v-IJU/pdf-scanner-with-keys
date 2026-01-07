<?php

namespace Dtech\PdfScanner\Rules;

class GstinRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'gst');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}/i', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.95);
        }

        return $this->notFound();
    }
}
