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

Supports:

File upload

Tag‑based field selection

Preset selection

JSON output view

Upload a PDF and enter comma-separated keys.

Example:

```
PAN, TAN, Assessment Year or Select Preset
```

---

### 2️⃣ Manual Function Call

You can use the scanner programmatically in controllers or services.

```php
use Dtech\PdfScanner\PdfScanner;
use Dtech\PdfScanner\PresetResolver;

$filePath = storage_path('app/pdfs/itr_form.pdf');

//what are the keys you need to extract from UI here preset also come and if you need some Random Key output you can mention here
$keys = $request->custom_keys ? $request->custom_keys : [];

$keys = PresetResolver::resolve($keys);

$result = PdfScanner::extractJson($filePath, $keys);

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

## 🧩 Rule System (Core Feature)

Each field uses a Rule Class.

Rule Interface

```
<?php

namespace Dtech\PdfScanner\Rules;

interface ExtractionRule
{
    /**
     * Check if this rule supports the given field
     */
    public function supports(string $field): bool;

    /**
     * Extract value from text
     */
    public function extract(string $text, string $field): array;
}


```

## Example: PAN Rule

```

<?php

namespace Dtech\PdfScanner\Rules;

class PanRule extends BaseRule implements ExtractionRule
{
    public function supports(string $field): bool
    {
        return str_contains(strtolower($field), 'pan');
    }

    public function extract(string $text, string $field): array
    {
        preg_match_all('/\b[A-Z]{5}[0-9]{4}[A-Z]\b/', $text, $m);

        if (!empty($m[0])) {
            return $this->found($m[0][0], 0.95);
        }

        return $this->notFound();
    }
}


```

## 🤝 Contributor Guide

Create new Rule in src/Rules

Implement RuleInterface

Register rule in RuleRegistry

```
PdfScannerServiceProvider.php

Update Rule Registory

```

Add preset mapping if needed

No core modification required.

---

## 📄 License

This package is open-source software licensed under the **MIT License**.
See the `LICENSE` file for more information.

---

## 🔄 Updating to a New Version

When you update this package to a newer version, follow the steps below to ensure everything works correctly.

---

### 1️⃣ Update the Package Code

Update the package using Composer:

```bash
composer update dhina-technologies/pdf-scanner-with-keys
```

**Effect:**
This replaces the old **Controller** and **Service** logic inside the `vendor` folder with the latest version from the package.

---

### 2️⃣ Update Assets (Views & Config)

Laravel publishes the test UI and configuration files only once during the first install. These files **do not update automatically**.

Force overwrite them using:

```bash
php artisan vendor:publish --tag="pdf-scanner-assets" --force
```

**Effect:**

- Replaces `resources/views/vendor/pdf-scanner/test-ui.blade.php`
- Replaces `config/pdf-scanner.php`

⚠️ **Warning:**
This will overwrite any manual changes made to these specific files.

---

### 3️⃣ Clear System Cache

Clear Laravel caches to ensure new routes and configuration are loaded:

```bash
php artisan optimize:clear
```

**Effect:**
Clears route, config, and view caches so the application immediately uses the updated version.

---

## ⭐ Support

If this package helps you, please consider giving it a ⭐ on Packagist or GitHub.

Happy Coding 🚀
