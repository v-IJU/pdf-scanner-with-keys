<?php

namespace Dtech\PdfScanner\DTO;

class ExtractedField
{
    public function __construct(
        public bool $found,
        public ?string $value,
        public float $confidence
    ) {}

    public static function found(string $value, float $confidence): self
    {
        return new self(true, $value, $confidence);
    }

    public static function notFound(): self
    {
        return new self(false, null, 0);
    }

    public function toArray(): array
    {
        return [
            'found' => $this->found,
            'value' => $this->value,
            'confidence' => $this->confidence,
        ];
    }
}
