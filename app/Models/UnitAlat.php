<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAlat extends Model
{
    use HasFactory;

    protected $table = 'unit_alat';

    protected $fillable = [
        'alat_camping_id',
        'nomor_seri',
        'status',
        'catatan_kondisi',
    ];

    public function alatCamping()
    {
        return $this->belongsTo(AlatCamping::class, 'alat_camping_id');
    }

    public function detailSewa()
    {
        return $this->hasMany(DetailSewa::class, 'unit_alat_id');
    }
}