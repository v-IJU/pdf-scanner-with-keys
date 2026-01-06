<?php

namespace Dtech\PdfScanner\Rules;

use Dtech\PdfScanner\DTO\ExtractedField;

class GenericKeyRule implements RuleContract
{
    public function __construct(protected string $label) {}

    public function key(): string
    {
        return strtoupper($this->label);
    }

    public function apply(string $text): ExtractedField
    {
        $text = strtoupper($text);

        // Normalize spaces
        $text = preg_replace('/\s+/', ' ', $text);

        /**
         * Build safe regex:
         * LABEL\s*:\s*(VALUE)
         * Stop when another LABEL-like pattern starts
         */
        $escapedLabel = preg_quote($this->key(), '/');

        $pattern = '/'
            . $escapedLabel
            . '\s*:\s*'               // LABEL:
            . '(.+?)'                 // VALUE (lazy)
            . '(?=\s+[A-Z ][A-Z ]{2,}\s*:|$)' // stop at NEXT LABEL:
            . '/';

        if (preg_match($pattern, $text, $match)) {
            return ExtractedField::found(trim($match[1]), 0.85);
        }

        return ExtractedField::notFound();
    }
}
