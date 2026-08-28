<?php

namespace App\Services;

use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class DeviceReportPdf
{
    /**
     * Get the aggregated data for the report.
     *
     * @return array
     */
    public function getReportData(?User $user = null): array
    {
        $user = $user ?: auth()->user();

        $companyStats = \App\Models\Device::forUser($user)
            ->join('companies', 'devices.company_id', '=', 'companies.id')
            ->select('companies.name as company_name')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when devices.status in ('active', 'online') then 1 end) as active_count")
            ->selectRaw("count(case when devices.status = 'offline' then 1 end) as offline_count")
            ->selectRaw("count(case when devices.status = 'dead' then 1 end) as dead_count")
            ->selectRaw("count(case when devices.status = 'retired' then 1 end) as retired_count")
            ->whereNull('devices.deleted_at')
            ->groupBy('companies.id', 'companies.name')
            ->orderBy('companies.name')
            ->get();

        $grandTotals = [
            'total' => (int) $companyStats->sum('total'),
            'active' => (int) $companyStats->sum('active_count'),
            'offline' => (int) $companyStats->sum('offline_count'),
            'dead' => (int) $companyStats->sum('dead_count'),
            'retired' => (int) $companyStats->sum('retired_count'),
        ];

        $period = [
            'start' => now()->subWeek()->startOfDay()->format('M j, Y'),
            'end' => now()->format('M j, Y'),
        ];

        return [
            'companyStats' => $companyStats,
            'grandTotals' => $grandTotals,
            'period' => $period,
        ];
    }

    /**
     * Generate the weekly device report PDF.
     *
     * @param  \Illuminate\Support\Collection|null  $companyStats
     * @param  array|null  $grandTotals
     * @param  array|null  $period
     * @return string
     */
    public function generate($companyStats = null, ?array $grandTotals = null, ?array $period = null, ?User $user = null): string
    {
        if (!$companyStats || !$grandTotals || !$period) {
            $data = $this->getReportData($user);
            $companyStats = $data['companyStats'];
            $grandTotals = $data['grandTotals'];
            $period = $data['period'];
        }

        $html = View::make('reports.weekly-device-report', [
            'companyStats' => $companyStats,
            'grandTotal' => $grandTotals['total'],
            'totalActive' => $grandTotals['active'],
            'totalOffline' => $grandTotals['offline'],
            'totalDead' => $grandTotals['dead'],
            'totalRetired' => $grandTotals['retired'],
            'periodStart' => $period['start'],
            'periodEnd' => $period['end'],
            'generatedDate' => now()->format('F j, Y g:i A'),
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
