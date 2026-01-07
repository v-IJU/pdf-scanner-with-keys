<?php

namespace Dtech\PdfScanner\Rules;

class IfscRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'ifsc');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/[A-Z]{4}0[A-Z0-9]{6}/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.95);
        }

        return $this->notFound();
    }
}
