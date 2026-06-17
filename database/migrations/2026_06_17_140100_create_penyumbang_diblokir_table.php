<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyumbang_diblokir', function (Blueprint $table) {
            $table->id();
            // Salah satu (atau keduanya) terisi: identitas cookie dan/atau hash IP.
            $table->string('penyumbang_id')->nullable()->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->text('alasan')->nullable();
            $table->foreignId('diblokir_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyumbang_diblokir');
    }
};
