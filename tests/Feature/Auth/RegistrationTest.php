<?php

namespace Tests\Feature\Auth;

use Livewire\Volt\Volt;

test('registration screen is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

