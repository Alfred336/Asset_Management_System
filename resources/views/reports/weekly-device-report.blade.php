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
            color: #334155;
            line-height: 1.6;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-container {
            background-color: #db2777; /* pink-600 */
            color: #ffffff;
            padding: 40px 1.5cm;
            margin-bottom: 30px;
        }
        .header-content {
            display: table;
            width: 100%;
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
        .logo {
            max-height: 70px;
            margin-bottom: 0;
            background-color: white;
            padding: 10px;
            border-radius: 8px;
        }
        h1 {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 22pt;
            font-weight: 800;
            color: #ffffff;
        }
        .meta {
            font-size: 9pt;
            color: #fce7f3; /* pink-100 */
            margin-top: 5px;
            font-weight: 300;
        }
        .content-body {
            padding: 0 1.5cm 2cm 1.5cm;
        }
        .section-title {
            color: #9d174d; /* pink-800 */
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 5px;
            border-bottom: 2px solid #fbcfe8; /* pink-200 */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th {
            background-color: #fdf2f8; /* pink-50 */
            color: #9d174d; /* pink-800 */
            text-align: left;
            padding: 15px 12px;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #db2777;
        }
        td {
            padding: 12px 12px;
            border-bottom: 1px solid #fce7f3;
            font-size: 10pt;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .summary-wrapper {
            display: table;
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .summary-box {
            background-color: #9d174d;
            color: white;
            padding: 30px;
            border-radius: 12px;
        }
        .summary-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
        }
        .summary-stats {
            width: 100%;
        }
        .summary-item-label {
            font-size: 10pt;
            opacity: 0.9;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .summary-item-value {
            font-size: 14pt;
            font-weight: bold;
            text-align: right;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .grand-total-row td {
            background-color: #fdf2f8;
            color: #9d174d;
            font-weight: 800;
            font-size: 11pt;
            border-top: 2px solid #db2777;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            background-color: #f8fafc;
            line-height: 50px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-content">
            <div class="header-left">
                <h1>Weekly Device Status Report</h1>
                <div class="meta">
                    Reporting Period: <strong>{{ $periodStart }} &mdash; {{ $periodEnd }}</strong><br>
                    Generated on: {{ $generatedDate }}
                </div>
            </div>
            <div class="header-right">
                @php
                    $logoPath = public_path('wemanage-logo.png');
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" class="logo">
                @else
                    <div style="font-size: 24pt; font-weight: 900; opacity: 0.5;">WEMANAGE</div>
                @endif
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="section-title">Company Statistics Summary</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%">Company Name</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Active</th>
                    <th class="text-right">Offline</th>
                    <th class="text-right">Dead</th>
                    <th class="text-right">Retired</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companyStats as $stat)
                    <tr>
                        <td class="font-bold" style="color: #9d174d;">{{ $stat->company_name }}</td>
                        <td class="text-right font-bold">{{ $stat->total }}</td>
                        <td class="text-right">{{ $stat->active_count }}</td>
                        <td class="text-right">{{ $stat->offline_count }}</td>
                        <td class="text-right">{{ $stat->dead_count }}</td>
                        <td class="text-right">{{ $stat->retired_count }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="grand-total-row">
                    <td>GRAND TOTAL</td>
                    <td class="text-right">{{ $grandTotal }}</td>
                    <td class="text-right">{{ $totalActive }}</td>
                    <td class="text-right">{{ $totalOffline }}</td>
                    <td class="text-right">{{ $totalDead }}</td>
                    <td class="text-right">{{ $totalRetired }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-wrapper">
            <div class="summary-box">
                <div class="summary-title">Executive Performance Overview</div>
                <table class="summary-stats" style="margin-bottom: 0;">
                    <tr>
                        <td class="summary-item-label" style="border-top:none;">Total Active Companies</td>
                        <td class="summary-item-value" style="border-top:none;">{{ $companyStats->count() }}</td>
                    </tr>
                    <tr>
                        <td class="summary-item-label">Total Assets Under Management</td>
                        <td class="summary-item-value">{{ $grandTotal }}</td>
                    </tr>
                    <tr>
                        <td class="summary-item-label" style="border-bottom:none;">Overall Operational Health Index</td>
                        <td class="summary-item-value" style="border-bottom:none;">
                            @if($grandTotal > 0)
                                {{ number_format(($totalActive / $grandTotal) * 100, 1) }}%
                            @else
                                0.0%
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        Confidential Asset Report &bull; Generated by Wemanage System &bull; Page {PAGE_NUM} of {PAGE_COUNT}
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
