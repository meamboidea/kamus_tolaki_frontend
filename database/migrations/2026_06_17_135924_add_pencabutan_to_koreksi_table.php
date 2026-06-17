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
        Schema::table('koreksi', function (Blueprint $table) {
            // Jejak pencabutan oleh admin (terpisah dari ditinjau_oleh = approver awal).
            $table->foreignId('dicabut_oleh')->nullable()->after('ditinjau_pada')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('dicabut_pada')->nullable()->after('dicabut_oleh');
            $table->text('alasan_cabut')->nullable()->after('dicabut_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koreksi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dicabut_oleh');
            $table->dropColumn(['dicabut_pada', 'alasan_cabut']);
        });
    }
};
