<?php

namespace Dtech\PdfScanner;

use Spatie\PdfToText\Pdf;
use Exception;

class PdfScanner
{
    public static function hello()
    {
        return "Success! The path is now correct.";
    }

    /**
     * The core method to extract data.
     */
    public static function extract(string $filePath, array $mapping)
    {
        // 1. Get the binary path from config (we will set this up next)
        $binaryPath = config('pdf-scanner.binary_path', 'C:\xpdf\pdftotext.exe');

        if (!file_exists($filePath)) {
            throw new Exception("PDF file not found at: " . $filePath);
        }

        // 2. Extract raw text from PDF
        $text = (new Pdf($binaryPath))
            ->setPdf($filePath)
            ->setOptions(['layout']) // Important for keeping tables aligned
            ->text();

        // 3. Loop through the "Keywords" the user wants
        $results = [];
        foreach ($mapping as $field => $keywords) {
            $results[$field] = self::findValue($text, (array)$keywords);
        }

        return [
            'data' => $results,
            'raw_text' => $text
        ];
    }

    /**
     * Logic to find a value near a keyword
     */
    private static function findValue($text, $keywords)
    {
        $lines = explode("\n", $text);

        foreach ($lines as $index => $line) {
            foreach ($keywords as $key) {
                if (stripos($line, $key) !== false) {
                    // 1. Check same line first
                    $value = trim(str_ireplace($key, '', $line), " \t\n\r\0\x0B:-");

                   // dd($value);

                    // 2. If same line is empty, look at the next 1-3 lines (Grid Layout)
                    if (empty($value)) {
                        for ($i = 1; $i <= 3; $i++) {
                            if (isset($lines[$index + $i])) {
                                $nextLine = trim($lines[$index + $i]);
                                // Ensure the next line isn't just another label
                                if (!empty($nextLine) && !preg_match('/[a-zA-Z]{5,}/', $nextLine)) {
                                    return $nextLine;
                                }
                                // If we find something that looks like a PAN/ID, return it
                                if (preg_match('/[A-Z0-9]{5,}/', $nextLine)) {
                                    return $nextLine;
                                }
                            }
                        }
                    } else {
                        return $value;
                    }
                }
            }
        }

        return "Not Found";
    }
}
