<?php

use App\Models\User;
use App\Models\Company;
use App\Models\Device;

test('authenticated user can download the weekly report', function () {
    $user = User::factory()->create();
    
    // Create some data to ensure report generation works
    $company = Company::create([
        'name' => 'Test Company',
        'email' => 'test@example.com',
        'status' => 'active',
    ]);

    Device::create([
        'company_id' => $company->id,
        'asset_tag' => 'TEST-001',
        'model' => 'Test Model',
        'device_type' => 'Laptop',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)
        ->get(route('reports.weekly-download'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename=weekly-device-report-' . now()->format('Y-m-d') . '.pdf');
});

test('guest cannot download the weekly report', function () {
    $response = $this->get(route('reports.weekly-download'));

    $response->assertRedirect(route('login'));
});
