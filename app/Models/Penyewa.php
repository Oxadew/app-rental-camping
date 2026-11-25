<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'no_hp',
        'alamat',
    ];

    public function transaksiSewa()
    {
        return $this->hasMany(TransaksiSewa::class, 'penyewa_id');
    }
}