<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DevicesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        // Try to find company by name if company_id is not a number
        $companyId = $row['company_id'] ?? null;
        if ($companyId && ! is_numeric($companyId)) {
            $company = Company::where('name', $companyId)->first();
            $companyId = $company?->id;
        }

        // Try to find staff by name if staff_id is not a number
        $staffId = $row['staff_id'] ?? null;
        if ($staffId && ! is_numeric($staffId)) {
            // Assuming full_name search or similar
            $staff = Staff::where(function ($query) use ($staffId) {
                $query->where('first_name', 'like', "%$staffId%")
                    ->orWhere('last_name', 'like', "%$staffId%");
            })->first();
            $staffId = $staff?->id;
        }

        return new Device([
            'company_id' => $companyId,
            'staff_id' => $staffId,
            'asset_tag' => $row['asset_tag'],
            'serial_number' => $row['serial_number'] ?? null,
            'model' => $row['model'],
            'manufacturer' => $row['manufacturer'] ?? null,
            'device_type' => $row['device_type'],
            'operating_system' => $row['operating_system'] ?? null,
            'os_version' => $row['os_version'] ?? null,
            'processor' => $row['processor'] ?? null,
            'ram_gb' => $row['ram_gb'] ?? null,
            'storage_gb' => $row['storage_gb'] ?? null,
            'storage_type' => $row['storage_type'] ?? null,
            'ip_address' => $row['ip_address'] ?? null,
            'mac_address' => $row['mac_address'] ?? null,
            'hostname' => $row['hostname'] ?? null,
            'location' => $row['location'] ?? null,
            'status' => $row['status'] ?? 'active',
            'purchase_date' => $row['purchase_date'] ?? null,
            'purchase_cost' => $row['purchase_cost'] ?? null,
            'warranty_expiry' => $row['warranty_expiry'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required',
            'asset_tag' => 'required|string|max:100|unique:devices,asset_tag',
            'model' => 'required|string|max:100',
            'device_type' => 'required|string|max:100',
            'status' => 'required|in:active,offline,online,formatted,dead,under_repair,retired',
        ];
    }
}
