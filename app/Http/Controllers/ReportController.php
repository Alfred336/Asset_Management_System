<?php

namespace App\Http\Controllers;

use App\Services\DeviceReportPdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Download the weekly device status report.
     *
     * @param  \App\Services\DeviceReportPdf  $reportPdf
     * @return \Illuminate\Http\Response
     */
    public function download(DeviceReportPdf $reportPdf)
    {
        $pdfContent = $reportPdf->generate();
        $filename = 'weekly-device-report-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
