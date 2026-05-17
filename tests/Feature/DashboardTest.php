<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'Super Admin']);
    $user->assignRole('Super Admin');

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
