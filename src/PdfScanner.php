<?php

namespace Dtech\PdfScanner;

use Dtech\PdfScanner\Rules\FallbackRule;
use Dtech\PdfScanner\Services\RuleEngine;
use Dtech\PdfScanner\Services\TextExtractor;
use Dtech\PdfScanner\Services\TextNormalizer;
use Spatie\PdfToText\Pdf;
use Dtech\PdfScanner\RuleRegistry;
use Exception;
use Smalot\PdfParser\Parser;

class PdfScanner
{
    public static function hello()
    {
        return "Success! The path is now correct.";
    }


    public static function extractJson(string $filePath, array $fields): array
    {
        $parser = new Parser();
        $text = trim($parser->parseFile($filePath)->getText());

        $data = [];

        // dd($fields);

        foreach ($fields as $field) {
            $data[$field] = self::applyRules($text, $field);
        }

        return [
            'data' => $data,
            'raw_text' => $text
        ];
    }

    private static function applyRules(string $text, string $field): array
    {
        foreach (RuleRegistry::all() as $rule) {

             //dd($rule);

            if ($rule->supports($field)) {
                $result = $rule->extract($text, $field);

                if (
                    $result['found']
                    && $result['confidence'] >= config('pdf-scanner.confidence_threshold')
                ) {
                    return $result;
                }
            }
        }


        return config('pdf-scanner.enable_fallback')
            ? (new FallbackRule())->extract($text, $field)
            : ['value' => null, 'confidence' => 0, 'found' => false];
    }

    public static function preset(string $name): array
    {
        return config("pdf-scanner.presets.$name", []);
    }

    private static function applyRulesOld(string $text, string $field): array
    {
        foreach (config('pdf-scanner.rules', []) as $ruleClass) {
            $rule = new $ruleClass;

            if ($rule->supports($field)) {
                $result = $rule->extract($text, $field);

                if (
                    $result['found'] &&
                    $result['confidence'] >= config('pdf-scanner.confidence_threshold')
                ) {
                    return $result;
                }
            }
        }

        if (config('pdf-scanner.enable_fallback')) {
            return (new FallbackRule())->extract($text, $field);
        }

        return [
            'value' => null,
            'confidence' => 0,
            'found' => false
        ];
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
