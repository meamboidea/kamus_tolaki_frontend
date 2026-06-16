<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('koreksi', function (Blueprint $table) {
            $table->id();
            $table->string('bahasa_sumber')->default('indonesia');
            $table->string('teks_sumber');                 // kata/frasa Indonesia yg dikoreksi
            $table->string('teks_sumber_norm')->index();   // dinormalisasi utk pencocokan RAG
            $table->text('konteks_kalimat')->nullable();   // kalimat penuh (bantu reviewer)
            $table->string('tolaki_usulan');               // bentuk Tolaki yang benar
            $table->string('tolaki_sistem')->nullable();   // keluaran sistem saat itu
            $table->text('catatan')->nullable();           // penjelasan penyumbang
            $table->string('status')->default('pending')->index();
            $table->boolean('utama')->default(false);      // varian utama di antara approved
            $table->unsignedInteger('suara')->default(1);  // jumlah usulan serupa (bantu moderasi)
            $table->string('penyumbang_id')->nullable();   // device-id (anonim)
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan_tolak')->nullable();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->timestamps();

            // Pencocokan utama saat injeksi RAG: cari koreksi approved per kata/frasa.
            $table->index(['teks_sumber_norm', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koreksi');
    }
};
