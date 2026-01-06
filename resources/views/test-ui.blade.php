<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dtech PDF Scanner Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4 text-center">Dtech PDF Scanner Test UI</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('pdf-scanner.scan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">1. Upload PDF</label>
                            <input type="file" name="pdf" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">2. Keys to Extract (comma separated)</label>
                            <input type="text" name="custom_keys" class="form-control"
                                placeholder="e.g. PAN, TAN, Assessment Year, Total Tax"
                                value="{{ request('custom_keys') ?? 'PAN, TAN, Name' }}">
                            <div class="form-text">Type exactly what appears in the PDF.</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Analyze PDF</button>
                </form>
            </div>
        </div>

        @if (isset($data))
            <div class="row">
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">Extracted Keys</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $value)
                                        <tr>
                                            <td class="text-muted small text-uppercase">
                                                {{ str_replace('_', ' ', $key) }}</td>
                                            <td class="fw-bold text-primary">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">Source Raw Text</div>
                        <div class="card-body p-0">
                            <pre class="m-0 p-3 bg-dark text-success" style="height: 400px; overflow-y: scroll; font-size: 11px;">{{ $raw_text }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
