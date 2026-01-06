<?php

return [
    /*
    | Path to the pdftotext binary. 
    | On Windows: 'C:\xpdf\pdftotext.exe'
    | On Linux: '/usr/bin/pdftotext'
    */
    'binary_path' => env('PDF_SCANNER_BINARY', 'C:\xpdf\pdftotext.exe'),

    // this is for if we ysed raw insted of json 

    'default_rules' => [
        'PAN' => Dtech\PdfScanner\Rules\PanRule::class,
        'TAN' => Dtech\PdfScanner\Rules\TanRule::class,
    ],
    
];