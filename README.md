# PDF Scanner with Custom Keys

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dhina-technologies/pdf-scanner-with-keys.svg?style=flat-square)](https://packagist.org/packages/dhina-technologies/pdf-scanner-with-keys)
[![Total Downloads](https://img.shields.io/packagist/dt/dhina-technologies/pdf-scanner-with-keys.svg?style=flat-square)](https://packagist.org/packages/dhina-technologies/pdf-scanner-with-keys)

A powerful Laravel package to extract data from complex PDFs such as **ITR Form 16**, **Bank Statements**, and **Invoices** using **custom keywords** and **multi-line detection logic**.

---

## 📋 Prerequisites

This package uses the **`pdftotext`** binary.
You **must install it** [https://github.com/spatie/pdf-to-text](https://github.com/spatie/pdf-to-text) on your system before using this package.

```
composer require spatie/pdf-to-text
```

---

### 🪟 Windows

1. Download **Xpdf command-line tools** from:
   [https://www.xpdfreader.com/download.html](https://www.xpdfreader.com/download.html)
2. Extract the ZIP file.
3. Locate `pdftotext.exe`

Example path:

```
C:\xpdf\bin64\pdftotext.exe
```

---

### 🐧 Linux (Ubuntu / Debian)

```
sudo apt-get update
sudo apt-get install poppler-utils
```

---

### 🍎 macOS

Using Homebrew:

```
brew install poppler
```

---

## 🚀 Installation

Install the package via Composer:

```
composer require dhina-technologies/pdf-scanner-with-keys
```

Publish the configuration file and test assets:

```
php artisan vendor:publish --tag="pdf-scanner-assets"
```

---

## ⚙️ Configuration

Set the path to the `pdftotext` binary in your `.env` file.

### Windows

```
PDF_SCANNER_BINARY="C:\xpdf\bin64\pdftotext.exe"
```

### Linux / macOS

```
PDF_SCANNER_BINARY="/usr/bin/pdftotext"
```

---

## 🛠️ Usage

### 1️⃣ Built-in Test Page (UI)

This package includes a built-in **visual test page** to verify PDF scanning.

Start the Laravel server:

```
php artisan serve
```

Open in browser:

```
http://localhost:8000/test-package
```

Upload a PDF and enter comma-separated keys.

Example:

```
PAN, TAN, Assessment Year
```

---

### 2️⃣ Manual Function Call

You can use the scanner programmatically in controllers or services.

```php
use Dtech\PdfScanner\PdfScanner;

$filePath = storage_path('app/pdfs/itr_form.pdf');

$rules = [
    'pan_number'      => ['PAN of the Deductor', 'Permanent Account Number'],
    'assessment_year' => ['Assessment Year'],
    'employer'        => ['Name and address of the Employer'],
];

$result = PdfScanner::extract($filePath, $rules);

print_r($result['data']);

echo $result['raw_text'];
```

---

## 🔍 How It Works

The scanner uses **Multi-Line Detection Logic**:

- Searches for configured keywords
- If the value is not found on the same line
- Automatically scans subsequent lines
- Extracts correct values even from table-style PDFs

Ideal for:

- Government forms (Form 16, ITR)
- Bank statements
- Grid-based invoices

---

## 📦 Output Format

```php
[
    'data' => [
        'pan_number' => 'ABCDE1234F',
        'assessment_year' => '2023-24',
        'employer' => 'XYZ Private Limited'
    ],
    'raw_text' => 'Full extracted PDF text...'
]
```

---

## 📄 License

This package is open-source software licensed under the **MIT License**.
See the `LICENSE` file for more information.

---

## ⭐ Support

If this package helps you, please consider giving it a ⭐ on Packagist or GitHub.

Happy Coding 🚀
