<?php
namespace Dtech\PdfScanner\Rules;

use Dtech\PdfScanner\DTO\ExtractedField;

class TanRule implements RuleContract
{
    public function key(): string
    {
        return 'TAN';
    }

    public function apply(string $text): ExtractedField
    {
        $clean = strtoupper($text);
        $clean = preg_replace('/[^A-Z0-9]/', '', $clean);
        $clean = preg_replace('/X+/', '', $clean);

        if (preg_match('/[A-Z]{4}[0-9]{5}[A-Z]/', $clean, $match)) {
            return ExtractedField::found($match[0], 0.94);
        }

        return ExtractedField::notFound();
    }
}
