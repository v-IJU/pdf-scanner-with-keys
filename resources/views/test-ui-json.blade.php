<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelligent PDF Scanner</title>

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
            <h1 class="fw-bold">PDF Intelligence</h1>
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

                                {{-- <div class="col-md-6">
                                    <label class="form-label fw-bold">2. Target Fields</label>

                                    <div class="form-control form-control-lg d-flex flex-wrap gap-2" id="tags-input">
                                        <!-- Tags will appear here -->
                                        <input type="text" id="tag-input" class="border-0 flex-grow-1"
                                            placeholder="Type field and press Enter" style="outline: none;">
                                    </div>

                                    <!-- Hidden inputs (array submission) -->
                                    <div id="tags-hidden"></div>

                                    <small class="text-muted">Press Enter or comma to add field</small>
                                </div> --}}

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">2. Target Fields / Presets</label>

                                    <!-- Presets -->
                                    <div id="preset-buttons" class="mb-2">
                                        @foreach (config('pdf-scanner.presets') as $preset => $fields)
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm me-1 mb-1 preset-btn"
                                                data-preset="{{ $preset }}">
                                                {{ ucfirst($preset) }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <!-- Tags Input -->
                                    <div class="form-control form-control-lg d-flex flex-wrap gap-2" id="tags-input">
                                        <input type="text" id="tag-input" class="border-0 flex-grow-1"
                                            placeholder="Type field and press Enter" style="outline:none;">
                                    </div>

                                    <!-- Hidden inputs -->
                                    <div id="tags-hidden"></div>

                                    <small class="text-muted">Click preset or type fields manually. Press Enter or comma
                                        to add field.</small>
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
                                                            @if ($item['found'])
                                                                <div class="fw-bold text-dark">
                                                                    {{ $item['value'] }}
                                                                </div>
                                                                <small class="text-success">
                                                                    Confidence:
                                                                    {{ number_format(@$item['confidence'] * 100, 1) }}%
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
    {{-- <script>
        const tagInput = document.getElementById('tag-input');
        const tagsContainer = document.getElementById('tags-input');
        const hiddenContainer = document.getElementById('tags-hidden');

        const initialTags = @json($keys ?? []);

        let tags = initialTags.length ?
            initialTags : ['PAN', 'TAN', 'Name', 'Date'];

        //let tags = ['PAN', 'TAN', 'Name', 'Date'];

        function renderTags() {
            tagsContainer.querySelectorAll('.badge').forEach(e => e.remove());
            hiddenContainer.innerHTML = '';

            tags.forEach((tag, index) => {
                // Badge
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary px-3 py-2';
                badge.innerHTML = `
                ${tag}
                <span style="cursor:pointer;margin-left:8px;" data-index="${index}">&times;</span>
            `;
                tagsContainer.insertBefore(badge, tagInput);

                // Hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'custom_keys[]';
                input.value = tag;
                hiddenContainer.appendChild(input);
            });
        }

        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const value = tagInput.value.trim();

                if (value && !tags.includes(value)) {
                    tags.push(value);
                    renderTags();
                }
                tagInput.value = '';
            }
        });

        tagsContainer.addEventListener('click', function(e) {
            if (e.target.dataset.index !== undefined) {
                tags.splice(e.target.dataset.index, 1);
                renderTags();
            }
        });

        renderTags();
    </script> --}}
    <script>
        const tagInput = document.getElementById('tag-input');
        const tagsContainer = document.getElementById('tags-input');
        const hiddenContainer = document.getElementById('tags-hidden');

        let tags = []; // { type: 'custom'|'preset', value: 'PAN' }

        function renderTags() {
            // Clear badges
            tagsContainer.querySelectorAll('.badge').forEach(e => e.remove());
            hiddenContainer.innerHTML = '';

            tags.forEach((tag, index) => {
                const badge = document.createElement('span');
                badge.className = tag.type === 'preset' ? 'badge bg-success px-3 py-2' :
                    'badge bg-primary px-3 py-2';
                badge.innerHTML = `
            ${tag.value}
            <span style="cursor:pointer;margin-left:8px;" data-index="${index}">&times;</span>
        `;
                tagsContainer.insertBefore(badge, tagInput);

                // Hidden input for submission
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'custom_keys[]';
                input.value = tag.value;
                hiddenContainer.appendChild(input);
            });
        }

        // Handle manual input
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const value = tagInput.value.trim();
                if (value && !tags.find(t => t.value === value)) {
                    tags.push({
                        type: 'custom',
                        value
                    });
                    renderTags();
                }
                tagInput.value = '';
            }
        });

        // Remove tag
        tagsContainer.addEventListener('click', function(e) {
            if (e.target.dataset.index !== undefined) {
                tags.splice(e.target.dataset.index, 1);
                renderTags();
            }
        });

        // Handle preset button clicks
        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const preset = btn.dataset.preset;
                if (!tags.find(t => t.value === preset)) {
                    tags.push({
                        type: 'preset',
                        value: preset
                    });
                    renderTags();
                }
            });
        });

        // Initialize if you want previously selected keys
        @isset($keys)
            tags = @json(array_map(fn($v) => ['type' => 'custom', 'value' => $v], $keys));
            renderTags();
        @endisset
    </script>
</body>

</html>
