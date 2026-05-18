<?php

namespace App\Exports;

use App\Models\Device;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DevicesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Device::with(['company', 'staff'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company',
            'Staff',
            'Asset Tag',
            'Serial Number',
            'Model',
            'Manufacturer',
            'Device Type',
            'Operating System',
            'OS Version',
            'Processor',
            'RAM (GB)',
            'Storage (GB)',
            'Storage Type',
            'IP Address',
            'MAC Address',
            'Hostname',
            'Location',
            'Status',
            'Purchase Date',
            'Purchase Cost',
            'Warranty Expiry',
            'Notes',
            'Created At',
        ];
    }

    /**
     * @var Device
     */
    public function map($device): array
    {
        return [
            $device->id,
            $device->company?->name,
            $device->staff?->full_name,
            $device->asset_tag,
            $device->serial_number,
            $device->model,
            $device->manufacturer,
            $device->device_type,
            $device->operating_system,
            $device->os_version,
            $device->processor,
            $device->ram_gb,
            $device->storage_gb,
            $device->storage_type,
            $device->ip_address,
            $device->mac_address,
            $device->hostname,
            $device->location,
            $device->status,
            $device->purchase_date?->format('Y-m-d'),
            $device->purchase_cost,
            $device->warranty_expiry?->format('Y-m-d'),
            $device->notes,
            $device->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
