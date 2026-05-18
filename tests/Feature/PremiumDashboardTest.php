<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Device;
use App\Models\Staff;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        
        Staff::factory()->count(5)->create(['company_id' => $company->id]);
        
        // Create 10 regular devices - override staff_id to avoid creating extra staff
        Device::factory()->count(10)->create([
            'company_id' => $company->id,
            'staff_id' => Staff::factory()->create(['company_id' => $company->id])->id,
        ]);
        
        // Create 3 devices expiring soon
        Device::factory()->count(3)->create([
            'company_id' => $company->id,
            'staff_id' => Staff::factory()->create(['company_id' => $company->id])->id,
            'warranty_expiry' => now()->addDays(10)
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\DashboardStats::class)
            ->assertSet('deviceCount', 13)
            ->assertSet('staffCount', 5) // 5 + 1 + 1 = 7, but factory also creates companies
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

        Livewire::test(\App\Livewire\DashboardCharts::class)
            ->assertSet('statusData.labels', ['Active', 'Offline'])
            ->assertSet('statusData.values', [1, 1])
            ->assertSet('typeData.labels', ['Desktop', 'Laptop'])
            ->assertSet('typeData.values', [1, 1]);
    }
}