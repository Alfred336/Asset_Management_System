<?php

namespace App\Services;

use App\Models\Device;
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
        $lines = [
            'Asset Management Weekly Device Report',
            'Generated: '.now()->format('Y-m-d H:i'),
            '',
            'Summary',
            'Total devices: '.$total,
            'Offline devices: '.($statusCounts['offline'] ?? 0).' ('.$this->percentage($statusCounts['offline'] ?? 0, $total).'%)',
            'Formatted devices: '.($statusCounts['formatted'] ?? 0).' ('.$this->percentage($statusCounts['formatted'] ?? 0, $total).'%)',
            '',
            'Status breakdown',
        ];

        foreach ($this->statuses() as $status) {
            $count = $statusCounts[$status] ?? 0;
            $lines[] = Str::headline($status).': '.$count.' ('.$this->percentage($count, $total).'%)';
        }

        $lines[] = '';
        $lines[] = 'Full device report';
        $lines[] = 'Asset Tag | Model | Type | Company | Assigned Staff | Status | Warranty';

        foreach ($devices as $device) {
            $lines[] = implode(' | ', [
                $device->asset_tag,
                $device->model,
                $device->device_type,
                $device->company?->name ?? 'N/A',
                $device->staff?->full_name ?? 'Unassigned',
                Str::headline($device->status),
                $device->warranty_expiry?->format('Y-m-d') ?? 'N/A',
            ]);
        }

        return $this->buildPdf($lines);
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
     * Build a simple text PDF without external dependencies.
     *
     * @param  array<int, string>  $lines
     */
    private function buildPdf(array $lines): string
    {
        $pages = array_chunk($lines, 42);
        $objects = [];

        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids ['.$this->pageReferences(count($pages)).'] /Count '.count($pages).' >>';

        $contentObjectStart = 3 + count($pages);

        foreach ($pages as $index => $pageLines) {
            $contentObject = $contentObjectStart + $index;
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 '.$this->fontObjectNumber(count($pages)).' 0 R >> >> /Contents '.$contentObject.' 0 R >>';
        }

        foreach ($pages as $pageLines) {
            $stream = $this->pageStream($pageLines);
            $objects[] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
        }

        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        return $this->assemblePdf($objects);
    }

    private function pageReferences(int $pageCount): string
    {
        return collect(range(3, 2 + $pageCount))
            ->map(fn (int $objectNumber): string => $objectNumber.' 0 R')
            ->join(' ');
    }

    private function fontObjectNumber(int $pageCount): int
    {
        return 3 + ($pageCount * 2);
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function pageStream(array $lines): string
    {
        $stream = "BT\n/F1 10 Tf\n50 750 Td\n14 TL\n";

        foreach ($lines as $line) {
            $stream .= '('.$this->escapePdfText(Str::limit($line, 115, '')).") Tj\nT*\n";
        }

        return $stream.'ET';
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    /**
     * @param  array<int, string>  $objects
     */
    private function assemblePdf(array $objects): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n".$xrefOffset."\n%%EOF";

        return $pdf;
    }
}
