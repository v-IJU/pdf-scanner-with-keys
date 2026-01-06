<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dtech | Intelligent PDF Scanner</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        .hero-section {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            padding: 60px 0 120px;
            color: white;
            margin-bottom: -80px;
        }

        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .form-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
        }

        .data-table thead {
            background-color: #f1f5f9;
        }

        .data-table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
            border: none;
        }

        .raw-text-container {
            background-color: #0f172a;
            border-radius: 12px;
            color: #38bdf8;
            font-family: monospace;
            font-size: 12px;
            line-height: 1.6;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>

    <!-- Hero -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="fw-bold">Dtech PDF Intelligence</h1>
            <p class="opacity-75">Upload documents and extract structured key-value data</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Upload Card -->
                <div class="card main-card mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <form method="POST" action="{{ route('pdf-scanner.scan') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">1. Upload PDF</label>
                                    <input type="file" name="pdf" class="form-control form-control-lg" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">2. Target Fields</label>
                                    <input type="text" name="custom_keys" class="form-control form-control-lg"
                                        placeholder="PAN, TAN, Name, Date"
                                        value="{{ request('custom_keys') ?? 'PAN, TAN, Name, Date' }}">
                                    <small class="text-muted">Comma separated field names</small>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-5">
                                        Analyze Document
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Results --}}
                @if (!empty($data))
                    <div class="row g-4">

                        <!-- Extracted Data -->
                        <div class="col-lg-5">
                            <div class="card main-card h-100">
                                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between">
                                    <h5 class="mb-0 fw-bold">Extracted Data</h5>
                                    <span class="status-badge">Completed</span>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table data-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="ps-4">Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $key => $item)
                                                    <tr>
                                                        <td class="ps-4 py-3">
                                                            <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                                        </td>

                                                        <td class="py-3">
                                                            @if (true)
                                                                <div class="fw-bold text-dark">
                                                                    {{ $item['value'] }}
                                                                </div>
                                                                <small class="text-success">
                                                                    Confidence:
                                                                    {{ number_format($item['confidence'] * 100, 1) }}%
                                                                </small>
                                                            @else
                                                                <span class="text-danger fw-semibold">Not Found</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Raw Text -->
                        <div class="col-lg-7">
                            <div class="card main-card h-100 bg-dark">
                                <div class="card-header bg-transparent border-0 py-3">
                                    <h5 class="mb-0 text-white fw-bold">PDF Raw Text (Debug)</h5>
                                </div>
                                <div class="card-body p-0">
                                    <pre class="m-0 p-4 raw-text-container" style="height: 500px; overflow-y: auto;">{{ $raw_text }}</pre>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif

            </div>
        </div>
    </div>

</body>

</html>
