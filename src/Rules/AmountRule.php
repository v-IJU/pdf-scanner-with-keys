<?php

namespace Dtech\PdfScanner\Rules;

class AmountRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'amount')
            || str_contains(strtolower($field), 'total')
            || str_contains(strtolower($field), 'net');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all(
            '/(?:₹|INR|Rs\.?)\s?[\d,]+(?:\.\d{1,2})?|\b\d{1,3}(?:,\d{3})+(?:\.\d{1,2})?\b/',
            $text,
            $matches
        );

        if (!empty($matches[0])) {
            $value = $this->normalizeAmount($matches[0][0]);

            return [
                'value' => $value,
                'currency' => 'INR',
                'confidence' => 0.9,
                'found' => true
            ];
        }

        return $this->notFound();
    }

    private function normalizeAmount(string $amount): float
    {
        return (float) str_replace([',', '₹', 'INR', 'Rs.', ' '], '', $amount);
    }
}
