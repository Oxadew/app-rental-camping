<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_camping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_alat_id')
                  ->constrained('kategori_alat')
                  ->restrictOnDelete();
            $table->string('kode_alat', 20)->unique();
            $table->string('nama_alat', 150);
            $table->decimal('harga_sewa_per_hari', 12, 2);
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat_camping');
    }
};