<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                if (!theme) {
                    var legacy = localStorage.getItem('darkMode') || localStorage.getItem('internconnect_darkmode');
                    if (legacy === 'true' || legacy === 'enabled' || legacy === '1') theme = 'dark';
                }
                if (theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark-mode');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        .export-page { page-break-after: always; }
        .export-page:last-child { page-break-after: auto; }
        thead { display: table-header-group; }
        tr, .footer, .header { page-break-inside: avoid; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #dc2626;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .eyebrow {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #dc2626;
            font-weight: 700;
            margin-bottom: 4px;
        }
        h1 {
            margin: 0;
            font-size: 22px;
            color: #111827;
        }
        .subtitle {
            color: #6b7280;
            margin-top: 4px;
        }
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary-grid td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            vertical-align: top;
            width: 25%;
        }
        .summary-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin: 18px 0 8px;
            color: #111827;
        }
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 9px;
            text-align: left;
        }
        .report-table th {
            background: #f9fafb;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }
        .report-table tr:nth-child(even) td {
            background: #fcfcfd;
        }
        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @php
        $reportPages = collect($monthlyRows)->chunk(8);
        if ($reportPages->isEmpty()) {
            $reportPages = collect([collect()]);
        }
        $generatedAt = now()->format('M d, Y h:i A');
    @endphp
    @foreach($reportPages as $reportPage)
    <div class="export-page">
    <div class="header">
        <div class="eyebrow">InternConnect OJTIMS</div>
        <h1>{{ $title }}</h1>
        <div class="subtitle">{{ $subtitle }}</div>
    </div>

    <table class="summary-grid">
        <tr>
            @foreach($summaryRows as $row)
                <td>
                    <span class="summary-label">{{ $row['label'] }}</span>
                    <div class="summary-value">{{ $row['value'] }}</div>
                </td>
                @if(($loop->iteration % 4) === 0 && !$loop->last)
                    </tr><tr>
                @endif
            @endforeach
        </tr>
    </table>

    <div class="section-title">Monthly Activity</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>Month</th>
                <th>Files</th>
                <th>Students</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportPage as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td>{{ $row['files'] ?? $row['sent'] ?? 0 }}</td>
                    <td>{{ $row['students'] ?? $row['submitted'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No monthly data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ $generatedAt }} &nbsp;|&nbsp; Page {{ $loop->iteration }} of {{ $reportPages->count() }}
        <p>This report was generated by InternConnect OJTIMS. No physical or handwritten signature is required.</p>
    </div>
    </div>
    @endforeach
</body>
</html>
