<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('permission-management')
        ->assertStatus(200);
});
