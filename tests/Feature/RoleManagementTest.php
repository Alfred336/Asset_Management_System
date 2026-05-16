<?php

use App\Models\User;

test('authenticated user can visit roles page', function () {
    // Seed permissions first
    $this->seed(\Database\Seeders\PermissionsSeeder::class);
    
    $user = User::factory()->create();
    $user->givePermissionTo('view-roles');
    
    $response = $this->actingAs($user)->get('/roles');
    
    $response->assertStatus(200);
});
