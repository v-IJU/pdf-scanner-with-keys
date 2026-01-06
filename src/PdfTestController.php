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
            'custom_keys' => 'required|array',
            'custom_keys.*' => 'string'
        ]);

        // Convert "PAN, TAN, Name" into ['PAN' => ['PAN'], 'TAN' => ['TAN']...]
        $keys = $request->custom_keys ? $request->custom_keys : [];

        $keys = PresetResolver::resolve($keys);

       // dd($keys);

        $result = PdfScanner::extractJson(
            $request->file('pdf')->getRealPath(),
            $keys
        );


        return view('pdf-scanner::test-ui-json', [
            'data' => $result['data'],      // ✅ structured JSON
            'raw_text' => $result['raw_text'], // ✅ debug text
            'keys' => $keys
        ]);
    }
}
