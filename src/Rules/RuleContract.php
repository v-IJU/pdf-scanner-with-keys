<?php
namespace Dtech\PdfScanner\Rules;

use Dtech\PdfScanner\DTO\ExtractedField;

interface RuleContract
{
    public function key(): string;
    public function apply(string $text): ExtractedField;
}
