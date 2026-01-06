<?php

namespace Dtech\PdfScanner\Services;

use Dtech\PdfScanner\Rules\GenericKeyRule;
use Dtech\PdfScanner\Rules\RuleContract;

class RuleEngine
{
    protected array $rules = [];

    public function __construct(array $requestedFields = [])
    {
        $defaultRules = config('pdf-scanner.default_rules', []);

        foreach ($requestedFields as $field) {

            // 🔒 SAFETY: Force everything to string
            if (is_array($field)) {
                $field = array_key_first($field);
            }

            $field = trim((string) $field);

            if ($field === '') {
                continue;
            }

            // ✅ PAN / TAN / registered rule
            if (isset($defaultRules[$field]) && class_exists($defaultRules[$field])) {
                $this->rules[] = new $defaultRules[$field];
            }
            // ✅ GENERIC FIELD
            else {
                $this->rules[] = new GenericKeyRule($field);
            }
        }
    }

    public function process(string $text): array
    {
        $results = [];

        foreach ($this->rules as $rule) {
            $results[$rule->key()] = $rule->apply($text)->toArray();
        }

        return $results;
    }
}
