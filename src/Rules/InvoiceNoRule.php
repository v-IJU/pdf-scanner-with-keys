<?php

namespace Dtech\PdfScanner\Rules;

class InvoiceNoRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'invoice no') || str_contains(strtolower($field), 'bill');
    }

    public function extract(string $text, string $field): array
    {
        // Match patterns like INV-1234, INV/2025/001, BILL-001
        preg_match_all('/\b(INV|INVOICE|BILL)[\s\-\/#:]*[A-Z0-9]+/i', $text, $matches);

        if (!empty($matches[0])) {
            return $this->found($matches[0][0], 0.9);
        }

        return $this->notFound();
    }
}
