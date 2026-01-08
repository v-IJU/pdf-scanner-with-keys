<?php

namespace Dtech\PdfScanner;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PdfTestController extends Controller
{
    public function index()
    {
        return view('pdf-scanner::test-ui-json');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf',
            'custom_keys' => 'nullable|array',
            'custom_keys.*' => 'string',
            'table_columns' => 'nullable|array',
            'table_columns.*' => 'string',
        ]);

        // Convert "PAN, TAN, Name" into ['PAN' => ['PAN'], 'TAN' => ['TAN']...]
        $keys = $request->custom_keys ? $request->custom_keys : [];

        $keys = PresetResolver::resolve($keys);

        // for tables

        $tables = collect($request->input('tables', []))
            ->mapWithKeys(function ($table) {
                return [
                    $table['name'] => array_map(
                        'trim',
                        explode(',', $table['columns'])
                    )
                ];
            })
            ->toArray();

        // dd($tables);

        $result = PdfScanner::extractJson(
            $request->file('pdf')->getRealPath(),
            $keys,
            $tables
        );


       // dd($result['tables']);

        return view('pdf-scanner::test-ui-json', [
            'data' => $result['data'],      // ✅ structured JSON
            'raw_text' => $result['raw_text'], // ✅ debug text
            'tables' => $result['tables'] ?? [],
            'keys' => $keys ?? [],
            "table_keys" => $request->table_columns ?? []
        ]);
    }
}
