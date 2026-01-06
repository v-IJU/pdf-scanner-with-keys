<?php

namespace Dtech\PdfScanner\Rules;

use Dtech\PdfScanner\Contracts\RuleInterface;

class EmailRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'email');
    }

    public function extract(string $text, string $field): array
    {
        preg_match(
            '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i',
            $text,
            $m
        );

        if (!empty($m[0])) {
            return $this->found($m[0], 0.95);
        }

        return $this->notFound();
    }

    public function priority(): int
    {
        return 80;
    }
}
