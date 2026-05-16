<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['email' => 'acme@example.com'],
            [
                'name' => 'Acme Corp',
                'phone' => '1234567890',
                'status' => 'active',
            ]
        );

        $staff = Staff::updateOrCreate(
            ['email' => 'alice@example.com'],
            [
                'company_id' => $company->id,
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'position' => 'IT Manager',
                'status' => 'active',
            ]
        );

        // Device with expiring warranty
        Device::updateOrCreate(
            ['asset_tag' => 'LAP-001'],
            [
                'company_id' => $company->id,
                'staff_id' => $staff->id,
                'model' => 'MacBook Pro 16',
                'device_type' => 'Laptop',
                'status' => 'active',
                'warranty_expiry' => now()->addDays(15),
                'purchase_date' => now()->subYears(2),
            ]
        );

        // Another device
        Device::updateOrCreate(
            ['asset_tag' => 'LAP-002'],
            [
                'company_id' => $company->id,
                'model' => 'Dell XPS 15',
                'device_type' => 'Laptop',
                'status' => 'active',
                'warranty_expiry' => now()->addDays(120),
            ]
        );

        // Expired device
        Device::updateOrCreate(
            ['asset_tag' => 'SRV-001'],
            [
                'company_id' => $company->id,
                'model' => 'PowerEdge T150',
                'device_type' => 'Server',
                'status' => 'active',
                'warranty_expiry' => now()->subDays(5),
            ]
        );
    }
}
