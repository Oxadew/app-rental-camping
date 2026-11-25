<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatCamping extends Model
{
    use HasFactory;

    protected $table = 'alat_camping';

    protected $fillable = [
        'kategori_alat_id',
        'kode_alat',
        'nama_alat',
        'harga_sewa_per_hari',
        'stok_total',
        'stok_tersedia',
        'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_alat_id');
    }

    public function unitAlat()
    {
        return $this->hasMany(UnitAlat::class, 'alat_camping_id');
    }
}