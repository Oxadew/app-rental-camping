<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiSewa extends Model
{
    use HasFactory;

    protected $table = 'transaksi_sewa';

    protected $fillable = [
        'kode_transaksi',
        'penyewa_id',
        'user_id',
        'tanggal_sewa',
        'tanggal_rencana_kembali',
        'tanggal_aktual_kembali',
        'lama_sewa_hari',
        'total_biaya_sewa',
        'denda_keterlambatan',
        'denda_kerusakan',
        'grand_total',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sewa'            => 'date',
        'tanggal_rencana_kembali' => 'date',
        'tanggal_aktual_kembali'  => 'date',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailSewa()
    {
        return $this->hasMany(DetailSewa::class, 'transaksi_sewa_id');
    }
}