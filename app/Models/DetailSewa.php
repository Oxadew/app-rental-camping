<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSewa extends Model
{
    use HasFactory;

    protected $table = 'detail_sewa';

    protected $fillable = [
        'transaksi_sewa_id',
        'unit_alat_id',
        'harga_satuan_per_hari',
        'subtotal',
        'kondisi_kembali',
        'biaya_kerusakan_unit',
    ];

    public function transaksiSewa()
    {
        return $this->belongsTo(TransaksiSewa::class, 'transaksi_sewa_id');
    }

    public function unitAlat()
    {
        return $this->belongsTo(UnitAlat::class, 'unit_alat_id');
    }
}