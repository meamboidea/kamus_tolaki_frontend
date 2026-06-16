<?php

use Illuminate\Support\Facades\Http;
use Livewire\Volt\Volt;

use function Pest\Laravel\get;

test('halaman terjemah & kamus & donasi dapat diakses publik', function () {
    get('/')->assertOk()->assertSee('Uji Terjemahan');
    get('/kamus')->assertOk()->assertSee('Telusur Kamus');
    get('/donasi')->assertOk()->assertSee('Dukung Pengembangan');
});

test('terjemah Indonesia→Tolaki menampilkan hasil', function () {
    Http::fake(['*/terjemah' => Http::response([
        'arah' => 'id-tolaki',
        'terjemahan' => 'ino ananggu',
        'jembatan' => 'ini anakku',
        'alternatif' => [],
        'kata' => [['indonesia' => 'anakku', 'tolaki' => 'ananggu', 'sumber' => 'kamus']],
        'kata_tanpa_padanan' => [],
        'disclaimer' => 'perkiraan',
    ])]);

    Volt::test('publik.terjemah')
        ->set('kalimat', 'ini anakku')
        ->call('terjemah')
        ->assertSee('ino ananggu');
});

test('balik arah ke Tolaki→Indonesia lalu terjemah', function () {
    Http::fake(['*/terjemah' => Http::response([
        'arah' => 'tolaki-id',
        'terjemahan' => 'rumah ini',
        'jembatan' => 'laika ino',
        'alternatif' => [],
        'kata' => [['tolaki' => 'laika', 'indonesia' => 'rumah', 'sumber' => 'kamus']],
        'kata_tanpa_padanan' => [],
        'disclaimer' => 'perkiraan',
    ])]);

    Volt::test('publik.terjemah')
        ->call('balik')
        ->assertSet('arah', 'tolaki-id')
        ->set('kalimat', 'laika ino')
        ->call('terjemah')
        ->assertSee('rumah ini')
        ->assertSee('Tolaki'); // label arah terbalik
});

test('kirim koreksi dari web membuat koreksi pending', function () {
    Volt::test('publik.terjemah')
        ->set('kalimat', 'ini anakku')
        ->call('mulaiPerbaiki', 'anakku', 'anaku')
        ->set('tolakiBenar', 'ananggu')
        ->call('kirimKoreksi');

    $this->assertDatabaseHas('koreksi', [
        'teks_sumber' => 'anakku',
        'tolaki_usulan' => 'ananggu',
        'status' => 'pending',
    ]);
});

test('telusur kamus dengan bidang tolaki', function () {
    Http::fake(['*/cari*' => Http::response(['q' => 'laika', 'bidang' => 'tolaki', 'hasil' => [
        ['kata_tolaki' => 'laika', 'arti_indonesia' => 'rumah', 'induk_kata' => null, 'contoh' => []],
    ]])]);

    Volt::test('publik.kamus')
        ->call('pilihBidang', 'tolaki')
        ->set('q', 'laika')
        ->call('cari')
        ->assertSee('laika');
});
