<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('role-management')
        ->assertStatus(200);
});
