<?php

namespace App\Services;

use App\Models\Device;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DeviceReportPdf
{
    /**
     * @param  Collection<int, Device>  $devices
     * @param  array<string, int>  $statusCounts
     */
    public function generate(Collection $devices, array $statusCounts): string
    {
        $total = $devices->count();
        $html = $this->buildHtmlReport($devices, $statusCounts, $total);

        $options = new Options;
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * @return array<int, string>
     */
    public function statuses(): array
    {
        return ['active', 'online', 'offline', 'formatted', 'dead', 'under_repair', 'retired'];
    }

    private function percentage(int $count, int $total): string
    {
        if ($total === 0) {
            return '0.0';
        }

        return number_format(($count / $total) * 100, 1);
    }

    /**
     * @param  Collection<int, Device>  $devices
     * @param  array<string, int>  $statusCounts
     */
    private function buildHtmlReport(Collection $devices, array $statusCounts, int $total): string
    {
        $statusColors = [
            'active' => '#10b981',
            'online' => '#22c55e',
            'offline' => '#ef4444',
            'formatted' => '#f59e0b',
            'dead' => '#6b7280',
            'under_repair' => '#8b5cf6',
            'retired' => '#9ca3af',
        ];

        // Group devices by company
        $devicesByCompany = $devices->groupBy(function ($device) {
            return $device->company?->name ?? 'No Company';
        });

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20mm; }
        body { font-family: Helvetica, Arial, sans-serif; margin: 0; padding: 0; color: #1f2937; }
        .container { max-width: 100%; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #3b82f6; }
        .header img { max-height: 60px; margin-bottom: 15px; }
        .header h1 { margin: 0; color: #1e40af; font-size: 28px; font-weight: bold; }
        .header .subtitle { color: #6b7280; font-size: 14px; margin-top: 8px; }
        .section { margin-bottom: 25px; }
        .section-title { background: linear-gradient(90deg, #3b82f6, #2563eb); color: white; padding: 10px 15px; font-weight: bold; font-size: 16px; margin-bottom: 15px; }
        .stats-grid { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .stat-card { flex: 1; min-width: 150px; background: #f9fafb; border-left: 4px solid #3b82f6; padding: 15px; }
        .stat-card.offline { border-left-color: #ef4444; }
        .stat-card.formatted { border-left-color: #f59e0b; }
        .stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #1f2937; }
        .stat-pct { font-size: 14px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #1e40af; color: white; padding: 10px 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        tr:hover { background: #eff6ff; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .company-section { margin-bottom: 30px; page-break-inside: avoid; }
        .company-header { background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #1e40af; display: flex; align-items: center; }
        .company-icon { background: #3b82f6; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="'.asset('wemanage-logo.png').'" alt="Wemanage Logo">
            <h1>📋 Asset Management Weekly Device Report</h1>
            <div class="subtitle">Generated: '.now()->format('F j, Y \a\t g:i A').' | Report Period: '.now()->subWeek()->format('M j').' - '.now()->format('M j, Y').'</div>
        </div>';

        $html .= '
        <div class="section">
            <div class="section-title">📊 Summary Overview</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Devices</div>
                    <div class="stat-value">'.$total.'</div>
                    <div class="stat-pct">100%</div>
                </div>
                <div class="stat-card offline">
                    <div class="stat-label">Offline Devices</div>
                    <div class="stat-value">'.($statusCounts['offline'] ?? 0).'</div>
                    <div class="stat-pct">'.$this->percentage($statusCounts['offline'] ?? 0, $total).'%</div>
                </div>
                <div class="stat-card formatted">
                    <div class="stat-label">Formatted Devices</div>
                    <div class="stat-value">'.($statusCounts['formatted'] ?? 0).'</div>
                    <div class="stat-pct">'.$this->percentage($statusCounts['formatted'] ?? 0, $total).'%</div>
                </div>
            </div>
        </div>';

        $html .= '
        <div class="section">
            <div class="section-title">📈 Status Breakdown</div>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($this->statuses() as $status) {
            $count = $statusCounts[$status] ?? 0;
            $pct = $this->percentage($count, $total);
            $label = Str::headline($status);
            $color = $statusColors[$status] ?? '#6b7280';
            $html .= '
                    <tr>
                        <td><span class="status-badge" style="background: '.$color.'20; color: '.$color.';">'.$label.'</span></td>
                        <td>'.$count.'</td>
                        <td>'.$pct.'%</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </div>';

        // Company-wise device details
        $html .= '
        <div class="section">
            <div class="section-title">🏢 Company-wise Device Details</div>';

        foreach ($devicesByCompany as $companyName => $companyDevices) {
            $deviceCount = $companyDevices->count();

            $html .= '
            <div class="company-section">
                <div class="company-header">
                    <div class="company-icon">🏢</div>
                    <div class="company-name">'.$companyName.' ('.$deviceCount.' devices)</div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Asset Tag</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>Assigned Staff</th>
                            <th>Status</th>
                            <th>Warranty</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($companyDevices as $device) {
                $statusColor = $statusColors[$device->status] ?? '#6b7280';
                $html .= '
                        <tr>
                            <td><strong>'.$device->asset_tag.'</strong></td>
                            <td>'.($device->model ?? 'N/A').'</td>
                            <td>'.$device->device_type.'</td>
                            <td>'.($device->staff?->full_name ?? 'Unassigned').'</td>
                            <td><span class="status-badge" style="background: '.$statusColor.'20; color: '.$statusColor.';">'.Str::headline($device->status).'</span></td>
                            <td>'.($device->warranty_expiry?->format('Y-m-d') ?? 'N/A').'</td>
                        </tr>';
            }

            $html .= '
                    </tbody>
                </table>
            </div>';
        }

        $html .= '
        </div>

        <div class="footer">
            Asset Management System | Generated on '.now()->format('Y-m-d H:i:s').' | Total Records: '.$devices->count().'
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
