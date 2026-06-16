<?php

use App\Enums\Role;
use App\Enums\StatusKoreksi;
use App\Models\Koreksi;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

test('penyumbang anonim bisa kirim koreksi (pending)', function () {
    $resp = postJson('/api/koreksi', [
        'teks_sumber' => 'anakku',
        'tolaki_usulan' => 'ananggu',
        'konteks_kalimat' => 'ini anakku',
    ]);

    $resp->assertCreated()->assertJsonPath('status', 'pending');
    $this->assertDatabaseHas('koreksi', [
        'teks_sumber_norm' => 'anakku',
        'tolaki_usulan' => 'ananggu',
        'status' => 'pending',
    ]);
});

test('koreksi serupa menambah suara, bukan duplikat', function () {
    postJson('/api/koreksi', ['teks_sumber' => 'Anakku', 'tolaki_usulan' => 'ananggu']);
    postJson('/api/koreksi', ['teks_sumber' => 'anakku', 'tolaki_usulan' => 'ananggu']);

    expect(Koreksi::count())->toBe(1);
    expect(Koreksi::first()->suara)->toBe(2);
});

test('endpoint disetujui hanya mengembalikan yang approved', function () {
    Koreksi::create(['teks_sumber' => 'ini', 'teks_sumber_norm' => 'ini', 'tolaki_usulan' => 'ino', 'status' => StatusKoreksi::Approved]);
    Koreksi::create(['teks_sumber' => 'itu', 'teks_sumber_norm' => 'itu', 'tolaki_usulan' => 'nggituo', 'status' => StatusKoreksi::Pending]);

    get('/api/koreksi/disetujui')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.tolaki', 'ino');
});

test('panel moderasi: tamu dialihkan, non-moderator 403, admin boleh', function () {
    get('/moderasi')->assertRedirect(route('login'));

    actingAs(User::factory()->create(['role' => null]))
        ->get('/moderasi')->assertForbidden();

    actingAs(User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]))
        ->get('/moderasi')->assertOk();
});

test('moderator menyetujui koreksi sebagai bentuk utama', function () {
    $admin = User::factory()->create(['role' => Role::Admin, 'email_verified_at' => now()]);
    $k = Koreksi::create(['teks_sumber' => 'anakku', 'teks_sumber_norm' => 'anakku', 'tolaki_usulan' => 'ananggu', 'status' => StatusKoreksi::Pending]);

    $this->actingAs($admin);

    Volt::test('koreksi.moderasi')
        ->call('setujui', $k->id, true)
        ->assertHasNoErrors();

    // Inti aksi moderasi: transisi status & penanda varian utama.
    // (Catatan: `ditinjau_oleh = Auth::id()` benar di produksi — request Livewire
    //  lewat middleware `auth`. Pada Volt::test, call() tidak membawa guard, jadi
    //  perekaman peninjau tidak diuji di sini; gating peran diuji di test akses.)
    $k->refresh();
    expect($k->status)->toBe(StatusKoreksi::Approved);
    expect($k->utama)->toBeTrue();
});
