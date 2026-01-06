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
        $keys = collect(explode(',', $request->custom_keys))
            ->map(fn($v) => trim($v))
            ->filter()
            ->values()
            ->toArray();

        $result = PdfScanner::extractJson(
            $request->file('pdf')->getRealPath(),
            $keys
        );

        // dd($result);
        // $result = PdfScanner::extract($request->file('pdf')->getRealPath(), $keys);

        // return view('pdf-scanner::test-ui', [
        //     'data' => $result['data'],
        //     'raw_text' => $result['raw_text']
        // ]);

        //dd($result['data']);


        return view('pdf-scanner::test-ui-json', [
            'data' => $result['data'],      // ✅ structured JSON
            'raw_text' => $result['raw_text'] // ✅ debug text
        ]);
    }
}
