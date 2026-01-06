<?php

namespace Dtech\PdfScanner\Rules;

class NameRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'name');
    }

    public function extract(string $text, string $field): array
    {
        $lines = preg_split('/\R/', $text);

        foreach ($lines as $i => $line) {

            // Label present in same line
            if (stripos($line, $field) !== false) {

                $value = trim(str_ireplace($field, '', $line), " :-\t");

                if ($this->looksLikeName($value)) {
                    return $this->found($value, 0.85);
                }

                // Check next few lines
                for ($j = 1; $j <= 3; $j++) {
                    if (!empty($lines[$i + $j])) {
                        $candidate = trim($lines[$i + $j]);

                        if ($this->looksLikeName($candidate)) {
                            return $this->found($candidate, 0.85);
                        }
                    }
                }
            }
        }

        return $this->notFound();
    }

    /**
     * Validate human names (avoid PAN, numbers, junk)
     */
    private function looksLikeName(string $value): bool
    {
        return strlen($value) >= 3
            && !preg_match('/\d/', $value)
            && preg_match('/^[A-Za-z][A-Za-z .]+$/', $value);
    }
}
