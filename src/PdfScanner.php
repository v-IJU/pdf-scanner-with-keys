<?php

namespace Dtech\PdfScanner;

use Dtech\PdfScanner\Services\RuleEngine;
use Dtech\PdfScanner\Services\TextExtractor;
use Dtech\PdfScanner\Services\TextNormalizer;
use Spatie\PdfToText\Pdf;

use Exception;
use Smalot\PdfParser\Parser;

class PdfScanner
{
    public static function hello()
    {
        return "Success! The path is now correct.";
    }

    /**
     * The core method to extract data.
     */
    public static function extract(string $pdfPath, array $fields = []): array
    {
        $text = TextExtractor::extract($pdfPath);

        $normalized = TextNormalizer::normalize($text);

        $engine = new RuleEngine($fields);
        $data = $engine->process($normalized);

        return [
            'data' => $data,
            'raw_text' => $text,
        ];
    }

    public static function extractJson(string $filePath, array $fields): array
    {
        $binary = config('pdf-scanner.binary_path');

        // Extract raw text from PDF

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
        //$text = (new \Smalot\PdfParser\Parser())->parseFile($filePath)->getText();

        // Normalize text
        $text = self::normalize($text);

        $data = [];

        foreach ($fields as $field) {
            $data[$field] = self::extractField($text, $field);
        }

        return [
            'data' => $data,
            'raw_text' => $text
        ];
    }

    private static function normalize(string $text): string
    {
        $text = preg_replace("/[ \t]+/", " ", $text); // remove extra spaces
        $text = preg_replace("/\n{2,}/", "\n", $text); // collapse multiple newlines
        return trim($text);
    }

    private static function extractField(string $text, string $field): array
    {
        $key = strtolower(trim($field));

        if (str_contains($key, 'pan')) {
            return self::extractPAN($text);
        }

        if (str_contains($key, 'tan')) {
            return self::extractTAN($text);
        }

        if (str_contains($key, 'date')) {
            return self::extractDate($text);
        }

        if (str_contains($key, 'invoice')) {
            return self::extractInvoice($text);
        }

        if (str_contains($key, 'name')) {
            return self::extractName($text, $field);
        }

        // Generic fallback
        return self::fallbackLabelValue($text, $field);
    }

    private static function extractDate(string $text): array
    {
        preg_match_all(
            '/\b\d{1,2}[\/\-\.][A-Za-z0-9]{1,3}[\/\-\.]\d{2,4}\b|\b\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4}\b/',
            $text,
            $matches
        );

        return self::best($matches[0]);
    }

    private static function extractInvoice(string $text): array
    {
        preg_match_all(
            '/\b(INV|INVOICE)[\s\-#:]*[A-Z0-9\/\-]+\b/i',
            $text,
            $matches
        );

        return self::best($matches[0]);
    }

    private static function fallbackLabelValue(string $text, string $label): array
    {
        $pattern = '/' . preg_quote($label, '/') . '\s*[:\-]?\s*(.{2,50})/i';

        if (preg_match($pattern, $text, $m)) {
            return [
                'value' => trim($m[1]),
                'confidence' => 0.6,
                'found' => true
            ];
        }

        return [
            'value' => null,
            'confidence' => 0,
            'found' => false
        ];
    }


    private static function best(array $values): array
    {
        $values = array_values(array_unique(array_filter($values)));

        if (empty($values)) {
            return ['value' => null, 'confidence' => 0];
        }

        return [
            'value' => $values[0],
            'confidence' => count($values) > 1 ? 90 : 95
        ];
    }

    private static function extractName(string $text, string $label): array
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, $label) !== false) {
                // Same line
                $value = trim(str_ireplace($label, '', $line), " :-\t");
                if (self::looksLikeName($value)) {
                    return ['value' => $value, 'confidence' => 85];
                }

                // Next lines
                for ($j = 1; $j <= 3; $j++) {
                    if (!empty($lines[$i + $j])) {
                        $candidate = trim($lines[$i + $j]);
                        if (self::looksLikeName($candidate)) {
                            return ['value' => $candidate, 'confidence' => 85];
                        }
                    }
                }
            }
        }

        return ['value' => null, 'confidence' => 0];
    }

    private static function looksLikeName(string $value): bool
    {
        return strlen($value) >= 3 &&
            preg_match('/^[A-Z][A-Z .]+$/i', $value) &&
            !preg_match('/\d/', $value);
    }

    private static function extractPAN(string $text): array
    {
        preg_match_all('/\b[A-Z]{5}[0-9]{4}[A-Z]\b/', $text, $matches);

        if (!empty($matches[0])) {
            return ['value' => $matches[0][0], 'confidence' => 0.95];
        }

        return ['value' => null, 'confidence' => 0];
    }

    private static function extractTAN(string $text): array
    {
        preg_match_all('/\b[A-Z]{4}[0-9]{5}[A-Z]\b/', $text, $matches);

        if (!empty($matches[0])) {
            return ['value' => $matches[0][0], 'confidence' => 0.94];
        }

        return ['value' => null, 'confidence' => 0];
    }
   
    // public static function extract(string $filePath, array $mapping)
    // {
    //     // 1. Get the binary path from config (we will set this up next)
    //     $binaryPath = config('pdf-scanner.binary_path', 'C:\xpdf\pdftotext.exe');

    //     if (!file_exists($filePath)) {
    //         throw new Exception("PDF file not found at: " . $filePath);
    //     }

    //     // 2. Extract raw text from PDF
    //     $text = (new Pdf($binaryPath))
    //         ->setPdf($filePath)
    //         ->setOptions(['layout']) // Important for keeping tables aligned
    //         ->text();

    //     // 3. Loop through the "Keywords" the user wants
    //     $results = [];
    //     foreach ($mapping as $field => $keywords) {
    //         $results[$field] = self::findValue($text, (array)$keywords);
    //     }

    //     return [
    //         'data' => $results,
    //         'raw_text' => $text
    //     ];
    // }

    /**
     * Logic to find a value near a keyword
     */
    private static function findValue(string $text, array $keywords)
    {
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", $text))
        ));

        foreach ($lines as $index => $line) {
            foreach ($keywords as $keyword) {

                if (stripos($line, $keyword) !== false) {

                    // Scan next 6 lines for the actual value
                    for ($i = 1; $i <= 6; $i++) {
                        if (!isset($lines[$index + $i])) {
                            continue;
                        }

                        $candidate = preg_replace('/[^A-Z0-9]/', '', strtoupper($lines[$index + $i]));

                        // PAN pattern
                        if (preg_match('/[A-Z]{5}[0-9]{4}[A-Z]/', $candidate, $match)) {
                            return $match[0];
                        }

                        // TAN pattern
                        if (preg_match('/[A-Z]{4}[0-9]{5}[A-Z]/', $candidate, $match)) {
                            return $match[0];
                        }

                        // Generic fallback (for names, refs)
                        if (strlen($candidate) >= 5) {
                            return $lines[$index + $i];
                        }
                    }
                }
            }
        }

        return 'Not Found';
    }
}
