<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_camping_id')
                  ->constrained('alat_camping')
                  ->restrictOnDelete();
            $table->string('nomor_seri', 50)->unique();
            $table->enum('status', ['tersedia', 'disewa', 'rusak'])->default('tersedia');
            $table->text('catatan_kondisi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_alat');
    }
};