<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Device Status Report</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b; /* slate-800 */
            line-height: 1.6;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        
        /* Premium Header Accent */
        .top-accent {
            height: 6px;
            background: linear-gradient(90deg, #1e293b 0%, #4f46e5 100%);
            width: 100%;
        }
        
        .header-section {
            display: table;
            width: 100%;
            padding: 40px 1.5cm 30px 1.5cm;
            border-bottom: 1px solid #e2e8f0;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        
        .report-title {
            font-size: 20pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .report-subtitle {
            font-size: 9pt;
            color: #6366f1; /* indigo-500 */
            margin-top: 4px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .logo {
            max-height: 45px;
        }
        
        .doc-info {
            margin-top: 12px;
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .content-body {
            padding: 1cm 1.5cm 2cm 1.5cm;
        }
        
        h2 {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 8px;
        }
        
        .section-num {
            color: #6366f1;
            margin-right: 8px;
        }
        
        .metadata-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .metadata-item {
            display: table-cell;
            width: 33.33%;
        }
        .metadata-label {
            font-size: 7pt;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .metadata-value {
            font-size: 9.5pt;
            font-weight: 600;
            color: #1e293b;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            text-align: left;
            padding: 12px 15px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5pt;
            color: #334155;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .data-table tr.total-row td {
            background-color: #f8fafc;
            font-weight: 800;
            color: #0f172a;
            border-top: 2px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            width: 60%;
            margin-bottom: 10px;
        }
        
        .text-right { text-align: right; }
        .indigo { color: #4f46e5; }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 7.5pt;
            color: #94a3b8;
            padding: 20px 1.5cm;
            text-align: center;
            background-color: #ffffff;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="top-accent"></div>
    <div class="header-section">
        <div class="header-left">
            <h1 class="report-title">Infrastructure Status</h1>
            <div class="report-subtitle">Standard Operating Procedure &bull; IT Asset Management</div>
            <div class="doc-info">
                Protocol: SOP-IT-001 &bull; 
                Ref: WSR-{{ date('Ymd') }} &bull; 
                Classification: Confidential
            </div>
        </div>
        <div class="header-right">
            @php
                $logoPath = public_path('wemanage_logo.png');
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            @endphp
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" class="logo">
            @else
                <div style="font-size: 14pt; font-weight: 900; color: #0f172a; letter-spacing: -1px;">WE<span class="indigo">MANAGE</span></div>
            @endif
        </div>
    </div>

    <div class="content-body">
        <h2><span class="section-num">1.0</span> Document Control</h2>
        <div class="metadata-grid">
            <div class="metadata-item">
                <div class="metadata-label">Department</div>
                <div class="metadata-value">Information Technology</div>
            </div>
            <div class="metadata-item">
                <div class="metadata-label">Reporting Period</div>
                <div class="metadata-value">{{ $periodStart }} &mdash; {{ $periodEnd }}</div>
            </div>
            <div class="metadata-item">
                <div class="metadata-label">Status</div>
                <div class="metadata-value">System Validated</div>
            </div>
        </div>

        <h2><span class="section-num">2.0</span> Key Performance Metrics</h2>
        <div class="summary-box">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase; padding: 5px 0;">Total Management Portfolios</td>
                    <td class="text-right" style="font-weight: 800; font-size: 11pt;">{{ $companyStats->count() }}</td>
                </tr>
                <tr>
                    <td style="font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase; padding: 5px 0;">Total Physical Assets</td>
                    <td class="text-right" style="font-weight: 800; font-size: 11pt;">{{ $grandTotal }}</td>
                </tr>
                <tr>
                    <td style="font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase; padding: 5px 0;">Global Availability Index</td>
                    <td class="text-right" style="font-weight: 800; font-size: 11pt; color: #4f46e5;">
                        @php
                            $globalHealth = $grandTotal > 0 ? ($totalActive / $grandTotal) * 100 : 0;
                        @endphp
                        {{ number_format($globalHealth, 1) }}%
                    </td>
                </tr>
            </table>
        </div>

        <h2><span class="section-num">3.0</span> Asset Distribution by Entity</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Entity / Company</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Active</th>
                    <th class="text-right">Offline</th>
                    <th class="text-right">Retired</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companyStats as $stat)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">{{ $stat->company_name }}</td>
                        <td class="text-right font-bold">{{ $stat->total }}</td>
                        <td class="text-right">{{ $stat->active_count }}</td>
                        <td class="text-right" style="color: {{ $stat->offline_count > 0 ? '#ef4444' : 'inherit' }}">{{ $stat->offline_count }}</td>
                        <td class="text-right" style="color: #94a3b8;">{{ $stat->retired_count }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>AGGREGATE TOTAL</td>
                    <td class="text-right">{{ $grandTotal }}</td>
                    <td class="text-right">{{ $totalActive }}</td>
                    <td class="text-right">{{ $totalOffline }}</td>
                    <td class="text-right">{{ $totalRetired }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        Standard Operating Procedure [SOP-IT-001] &bull; Internal Compliance Document &bull; Confidential &bull; Page {PAGE_NUM} of {PAGE_COUNT}
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->get_font("helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.5, 0.5, 0.5));
        }
    </script>
</body>
</html>
