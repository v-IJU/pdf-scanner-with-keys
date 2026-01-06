<?php

namespace Dtech\PdfScanner\Rules;

use Dtech\PdfScanner\DTO\ExtractedField;

class PanRule implements RuleContract
{
    public function key(): string
    {
        return 'PAN';
    }

    public function apply(string $text): ExtractedField
    {
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", strtoupper($text)))
        ));

        // 1️⃣ First: Look for PAN near label (MOST RELIABLE)
        foreach ($lines as $line) {
            if (str_contains($line, 'PAN')) {

                if (preg_match('/PAN\s*[:\-]?\s*([A-Z]{5}[0-9]{4}[A-Z])/', $line, $m)) {
                    return ExtractedField::found($m[1], 0.99);
                }
            }
        }

        // 2️⃣ Fallback: Global PAN search (STRICT FILTERING)
        foreach ($lines as $line) {

            // ❌ Skip invoice / order lines
            if (preg_match('/INVOICE|INV|ORDER|NO\b/', $line)) {
                continue;
            }

            if (preg_match('/\b([A-Z]{5}[0-9]{4}[A-Z])\b/', $line, $m)) {
                return ExtractedField::found($m[1], 0.90);
            }
        }

        return ExtractedField::notFound();
    }
}
