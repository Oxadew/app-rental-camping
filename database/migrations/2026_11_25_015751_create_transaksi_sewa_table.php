<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_sewa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique();
            $table->foreignId('penyewa_id')
                  ->constrained('penyewa')
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->date('tanggal_sewa');
            $table->date('tanggal_rencana_kembali');
            $table->date('tanggal_aktual_kembali')->nullable();
            $table->integer('lama_sewa_hari');
            $table->decimal('total_biaya_sewa', 12, 2)->default(0);
            $table->decimal('denda_keterlambatan', 12, 2)->default(0);
            $table->decimal('denda_kerusakan', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('status', ['berjalan', 'selesai', 'terlambat'])->default('berjalan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_sewa');
    }
};