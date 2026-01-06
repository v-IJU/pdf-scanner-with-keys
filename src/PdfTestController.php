<?php

namespace Dtech\PdfScanner;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PdfTestController extends Controller
{
    public function index()
    {
        return view('pdf-scanner::test-ui');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf',
            'custom_keys' => 'required|string'
        ]);

        // Convert "PAN, TAN, Name" into ['PAN' => ['PAN'], 'TAN' => ['TAN']...]
        $inputKeys = explode(',', $request->input('custom_keys'));
        $mapping = [];

        foreach ($inputKeys as $key) {
            $trimmedKey = trim($key);
            if (!empty($trimmedKey)) {
                // We use the same name as the key and the search term
                $mapping[$trimmedKey] = [$trimmedKey];
            }
        }

        $result = PdfScanner::extract($request->file('pdf')->getRealPath(), $mapping);

        return view('pdf-scanner::test-ui', [
            'data' => $result['data'],
            'raw_text' => $result['raw_text']
        ]);
    }
}
