<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('akses halaman pengguna: tamu dialihkan, moderator 403, admin boleh', function () {
    get('/pengguna')->assertRedirect(route('login'));

    actingAs(User::factory()->create(['role' => Role::Moderator, 'email_verified_at' => now()]))
        ->get('/pengguna')->assertForbidden();

    actingAs(User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]))
        ->get('/pengguna')->assertOk();
});

test('admin bisa menunjuk pengguna jadi moderator dan mencabutnya', function () {
    $admin = User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]);
    $orang = User::factory()->create(['role' => null]);

    $this->actingAs($admin);

    Volt::test('pengguna.kelola')->call('setPeran', $orang->id, 'moderator')->assertHasNoErrors();
    expect($orang->refresh()->role)->toBe(Role::Moderator);

    Volt::test('pengguna.kelola')->call('setPeran', $orang->id, null)->assertHasNoErrors();
    expect($orang->refresh()->role)->toBeNull();
});

test('admin tidak bisa mengubah peran akunnya sendiri (cegah kunci-diri)', function () {
    $admin = User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]);

    $this->actingAs($admin);
    Volt::test('pengguna.kelola')->call('setPeran', $admin->id, null);

    expect($admin->refresh()->role)->toBe(Role::Admin);
});

test('admin bisa menambahkan pengguna baru', function () {
    $admin = User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]);

    $this->actingAs($admin);

    Volt::test('pengguna.kelola')
        ->set('name', 'Staf Baru')
        ->set('email', 'stafbaru@tolaki.test')
        ->set('password', 'password123')
        ->set('role', 'moderator')
        ->call('createUser')
        ->assertHasNoErrors()
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('password', '')
        ->assertSet('role', 'moderator')
        ->assertSet('showCreateForm', false);

    $this->assertDatabaseHas('users', [
        'name' => 'Staf Baru',
        'email' => 'stafbaru@tolaki.test',
        'role' => 'moderator',
    ]);

    $user = User::where('email', 'stafbaru@tolaki.test')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

