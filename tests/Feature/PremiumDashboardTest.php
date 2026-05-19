<?php

namespace Tests\Feature;

use App\Livewire\DashboardCharts;
use App\Livewire\DashboardStats;
use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PremiumDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Super Admin']);
    }

    public function test_dashboard_stats_component_renders_with_correct_data()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $company = Company::factory()->create();

        // Create 5 staff members for the company
        $staffMembers = Staff::factory()->count(5)->create(['company_id' => $company->id]);

        // Create 10 regular devices - assign to existing staff members to avoid creating extra staff
        Device::factory()->count(10)->create([
            'company_id' => $company->id,
            'staff_id' => $staffMembers->random(),
        ]);

        // Create 3 devices expiring soon - assign to existing staff members
        Device::factory()->count(3)->create([
            'company_id' => $company->id,
            'staff_id' => $staffMembers->random(),
            'warranty_expiry' => now()->addDays(10),
        ]);

        $this->actingAs($user);

        Livewire::test(DashboardStats::class)
            ->assertSet('deviceCount', 13)
            ->assertSet('staffCount', 5) // 5 staff members total
            ->assertSet('expiringSoon', 3);
    }

    public function test_dashboard_charts_component_renders_data_for_status_and_type()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $company = Company::factory()->create();

        Device::factory()->create(['company_id' => $company->id, 'status' => 'active', 'device_type' => 'Laptop']);
        Device::factory()->create(['company_id' => $company->id, 'status' => 'offline', 'device_type' => 'Desktop']);

        $this->actingAs($user);

        Livewire::test(DashboardCharts::class)
            ->assertSet('statusData.labels', ['Active', 'Offline'])
            ->assertSet('statusData.values', [1, 1])
            ->assertSet('typeData.labels', ['Desktop', 'Laptop'])
            ->assertSet('typeData.values', [1, 1]);
    }
}
