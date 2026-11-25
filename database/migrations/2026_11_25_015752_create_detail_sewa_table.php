<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_sewa_id')
                  ->constrained('transaksi_sewa')
                  ->cascadeOnDelete();
            $table->foreignId('unit_alat_id')
                  ->constrained('unit_alat')
                  ->restrictOnDelete();
            $table->decimal('harga_satuan_per_hari', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->enum('kondisi_kembali', ['baik', 'rusak'])->nullable();
            $table->decimal('biaya_kerusakan_unit', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_sewa');
    }
};