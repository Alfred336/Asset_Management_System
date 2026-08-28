<?php

namespace Tests\Feature;

use App\Exports\DevicesExport;
use App\Imports\DevicesImport;
use App\Livewire\CompanyManagement;
use App\Livewire\DashboardCharts;
use App\Livewire\DashboardStats;
use App\Livewire\DeviceManagement;
use App\Livewire\DeviceStatusManager;
use App\Livewire\StaffManagement;
use App\Livewire\UserManagement;
use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use App\Models\User;
use App\Services\DeviceReportPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $restrictedUser;
    protected Company $companyA;
    protected Company $companyB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);

        $permissions = [
            'view-staff', 'view-users', 'view-companies', 'view-devices', 'view-roles',
            'create-user', 'edit-user', 'delete-user', 'restore-user',
            'create-companies', 'edit-companies', 'delete-companies', 'restore-companies',
            'create-devices', 'edit-devices', 'delete-devices', 'restore-devices',
            'update-device-status', 'view-device-status-history',
            'create-staff', 'edit-staff', 'delete-staff', 'restore-staff',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $adminRole->syncPermissions($permissions);

        // Create Companies
        $this->companyA = Company::create(['name' => 'Company Alpha', 'email' => 'alpha@test.com', 'status' => 'active']);
        $this->companyB = Company::create(['name' => 'Company Beta', 'email' => 'beta@test.com', 'status' => 'active']);

        // Create Super Admin
        $this->superAdmin = User::factory()->create(['email' => 'superadmin@test.com']);
        $this->superAdmin->assignRole('Super Admin');

        // Create Restricted User (assigned only to Company Alpha)
        $this->restrictedUser = User::factory()->create(['email' => 'restricted@test.com']);
        $this->restrictedUser->assignRole('Admin');
        $this->restrictedUser->companies()->attach($this->companyA->id);
    }

    public function test_user_management_can_assign_companies_on_user_creation_and_update()
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(UserManagement::class)
            ->set('name', 'John User')
            ->set('email', 'john@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('selectedCompanies', [(string) $this->companyA->id, (string) $this->companyB->id])
            ->call('create')
            ->assertHasNoErrors();

        $newUser = User::where('email', 'john@test.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->companies->contains($this->companyA->id));
        $this->assertTrue($newUser->companies->contains($this->companyB->id));

        // Update company assignments
        Livewire::test(UserManagement::class)
            ->call('edit', $newUser->id)
            ->set('name', 'John User Updated')
            ->set('email', 'john@test.com')
            ->set('status', 'active')
            ->set('selectedCompanies', [(string) $this->companyA->id])
            ->call('update')
            ->assertHasNoErrors();

        $this->assertTrue($newUser->fresh()->companies->contains($this->companyA->id));
        $this->assertFalse($newUser->fresh()->companies->contains($this->companyB->id));
    }

    public function test_restricted_user_can_only_see_devices_from_assigned_companies()
    {
        $deviceA = Device::create([
            'company_id' => $this->companyA->id,
            'asset_tag' => 'DEV-A-001',
            'model' => 'Model A',
            'device_type' => 'Laptop',
            'status' => 'active',
        ]);

        $deviceB = Device::create([
            'company_id' => $this->companyB->id,
            'asset_tag' => 'DEV-B-001',
            'model' => 'Model B',
            'device_type' => 'Desktop',
            'status' => 'active',
        ]);

        $this->actingAs($this->restrictedUser);

        Livewire::test(DeviceManagement::class)
            ->assertSee('DEV-A-001')
            ->assertDontSee('DEV-B-001');
    }

    public function test_restricted_user_cannot_edit_or_delete_device_from_unassigned_company()
    {
        $deviceB = Device::create([
            'company_id' => $this->companyB->id,
            'asset_tag' => 'DEV-B-002',
            'model' => 'Model B',
            'device_type' => 'Desktop',
            'status' => 'active',
        ]);

        $this->actingAs($this->restrictedUser);

        // Attempt edit
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Livewire::test(DeviceManagement::class)
            ->call('edit', $deviceB->id);
    }

    public function test_restricted_user_cannot_create_device_in_unassigned_company()
    {
        $this->actingAs($this->restrictedUser);

        Livewire::test(DeviceManagement::class)
            ->set('company_id', $this->companyB->id)
            ->set('asset_tag', 'HACK-001')
            ->set('model', 'Malicious Model')
            ->set('device_type', 'Laptop')
            ->set('status', 'active')
            ->call('create')
            ->assertSee('You do not have access to the selected company');

        $this->assertDatabaseMissing('devices', ['asset_tag' => 'HACK-001']);
    }

    public function test_restricted_user_can_only_see_staff_from_assigned_companies()
    {
        $staffA = Staff::create([
            'company_id' => $this->companyA->id,
            'first_name' => 'Alice',
            'last_name' => 'Alpha',
            'email' => 'alice@alpha.com',
            'employment_type' => 'full_time',
            'status' => 'active',
        ]);

        $staffB = Staff::create([
            'company_id' => $this->companyB->id,
            'first_name' => 'Bob',
            'last_name' => 'Beta',
            'email' => 'bob@beta.com',
            'employment_type' => 'full_time',
            'status' => 'active',
        ]);

        $this->actingAs($this->restrictedUser);

        Livewire::test(StaffManagement::class)
            ->assertSee('Alice')
            ->assertDontSee('Bob');
    }

    public function test_dashboard_stats_and_charts_are_scoped_to_assigned_companies()
    {
        Staff::create(['company_id' => $this->companyA->id, 'first_name' => 'A1', 'last_name' => 'L1', 'email' => 'a1@test.com', 'status' => 'active']);
        Staff::create(['company_id' => $this->companyB->id, 'first_name' => 'B1', 'last_name' => 'L2', 'email' => 'b1@test.com', 'status' => 'active']);

        Device::create(['company_id' => $this->companyA->id, 'asset_tag' => 'A-01', 'model' => 'M1', 'device_type' => 'Laptop', 'status' => 'active']);
        Device::create(['company_id' => $this->companyB->id, 'asset_tag' => 'B-01', 'model' => 'M2', 'device_type' => 'Desktop', 'status' => 'active']);

        $this->actingAs($this->restrictedUser);

        Livewire::test(DashboardStats::class)
            ->assertSet('deviceCount', 1)
            ->assertSet('staffCount', 1)
            ->assertSet('companyCount', 1);

        Livewire::test(DashboardCharts::class)
            ->assertSet('typeData.labels', ['Laptop'])
            ->assertSet('typeData.values', [1]);
    }

    public function test_super_admin_can_access_all_companies_and_data()
    {
        Device::create(['company_id' => $this->companyA->id, 'asset_tag' => 'A-10', 'model' => 'M1', 'device_type' => 'Laptop', 'status' => 'active']);
        Device::create(['company_id' => $this->companyB->id, 'asset_tag' => 'B-10', 'model' => 'M2', 'device_type' => 'Desktop', 'status' => 'active']);

        $this->actingAs($this->superAdmin);

        Livewire::test(DashboardStats::class)
            ->assertSet('deviceCount', 2)
            ->assertSet('companyCount', 2);
    }

    public function test_report_pdf_and_export_are_scoped_to_assigned_companies()
    {
        Device::create(['company_id' => $this->companyA->id, 'asset_tag' => 'REP-A', 'model' => 'M1', 'device_type' => 'Laptop', 'status' => 'active']);
        Device::create(['company_id' => $this->companyB->id, 'asset_tag' => 'REP-B', 'model' => 'M2', 'device_type' => 'Desktop', 'status' => 'active']);

        $this->actingAs($this->restrictedUser);

        $reportPdf = new DeviceReportPdf();
        $data = $reportPdf->getReportData();

        $companyNames = $data['companyStats']->pluck('company_name')->toArray();
        $this->assertContains('Company Alpha', $companyNames);
        $this->assertNotContains('Company Beta', $companyNames);

        $export = new DevicesExport();
        $devices = $export->collection();
        $this->assertEquals(1, $devices->count());
        $this->assertEquals('REP-A', $devices->first()->asset_tag);
    }
}
