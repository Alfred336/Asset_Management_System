<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class DeviceReportPdf
{
    /**
     * Generate the weekly device report PDF.
     *
     * @param  \Illuminate\Support\Collection  $companyStats
     * @param  array  $grandTotals
     * @param  array  $period
     * @return string
     */
    public function generate($companyStats, array $grandTotals, array $period): string
    {
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
