

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF to Text Binary Path (Optional)
    |--------------------------------------------------------------------------
    */
    'binary_path' => env('PDF_SCANNER_BINARY', 'C:\xpdf\pdftotext.exe'),

    /*
    |--------------------------------------------------------------------------
    | Minimum confidence required to accept value
    |--------------------------------------------------------------------------
    */
    'confidence_threshold' => 0.6,

    /*
    |--------------------------------------------------------------------------
    | Enable generic fallback rule
    |--------------------------------------------------------------------------
    */
    'enable_fallback' => true,

    /*
    |--------------------------------------------------------------------------
    | Registered extraction rules (ORDER MATTERS)
    |--------------------------------------------------------------------------
    */
    // 'rules' => [
    //     \Dtech\PdfScanner\Rules\PanRule::class,
    //     \Dtech\PdfScanner\Rules\TanRule::class,
    //     \Dtech\PdfScanner\Rules\DateRule::class,
    //     \Dtech\PdfScanner\Rules\InvoiceRule::class,
    //     \Dtech\PdfScanner\Rules\NameRule::class,
    // ],


    'presets' => [

        'invoice' => [
            'Invoice Number',
            'Invoice Date',
            'Total',
            'GST',
            'PAN',
        ],

        'form16' => [
            'PAN',
            'TAN',
            'Name of Employee',
            'Assessment Year',
            'Date',
            "Email",
        ],

        'salary-slip' => [
            'Employee Name',
            'Net Pay',
            'Gross Salary',
            'PAN',
            'Date',
        ],

    ],
    'pdftotext_options' => '-layout',

];
