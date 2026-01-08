<?php

namespace Dtech\PdfScanner\Table;

class TableExtractor
{
    public static function extract(string $text, array $headers): array
    {
        $lines = preg_split("/\r?\n/", $text);

        $startIndex = null;

        foreach ($lines as $i => $line) {
            if (self::isHeaderLine($line, $headers)) {
                $startIndex = $i + 1;
                break;
            }
        }

        if ($startIndex === null) {
            return [];
        }

        return self::parseRows(array_slice($lines, $startIndex));
    }

    public static function extractAll(string $text, array $headers): array
    {
        $lines = preg_split("/\r?\n/", $text);
        $tables = [];

        //dd($lines);

        for ($i = 0; $i < count($lines); $i++) {

            if (self::isHeaderLine($lines[$i], $headers)) {
                $rows = self::parseRows(array_slice($lines, $i + 1));

                if (!empty($rows)) {
                    $tables[] = $rows;
                }
            }
        }

        return $tables;
    }

    protected static function isHeaderLine(string $line, array $headers): bool
    {
        foreach ($headers as $header) {
            if (!str_contains(strtolower($line), strtolower($header))) {
                return false;
            }
        }
        return true;
    }

    protected static function parseRows(array $lines): array
    {
        $rows = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                break;
            }

            // ✅ First try TAB-based split
            if (str_contains($line, "\t")) {
                $columns = array_map('trim', explode("\t", $line));
            }
            // ✅ Fallback to space-aligned split
            else {
                $columns = preg_split('/\s{2,}/', $line);
            }

            if (count($columns) < 2) {
                continue;
            }

            $rows[] = $columns;
        }

        return $rows;
    }
}
