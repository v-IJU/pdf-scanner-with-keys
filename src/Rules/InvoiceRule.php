<?php

namespace Dtech\PdfScanner\Rules;

class InvoiceRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'invoice')
            || str_contains(strtolower($field), 'bill')
            || str_contains(strtolower($field), 'reference');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all(
            '/\b(INV|INVOICE|BILL)[\s\-#:]*[A-Z0-9\/\-]+\b/i',
            $text,
            $matches
        );

        if (!empty($matches[0])) {
            return $this->found($matches[0][0], 0.9);
        }

        return $this->notFound();
    }
}
