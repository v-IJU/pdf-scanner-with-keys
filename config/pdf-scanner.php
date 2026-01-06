<?php

return [
    /*
    | Path to the pdftotext binary. 
    | On Windows: 'C:\xpdf\pdftotext.exe'
    | On Linux: '/usr/bin/pdftotext'
    */
    'binary_path' => env('PDF_SCANNER_BINARY', 'C:\xpdf\pdftotext.exe'),
    
];