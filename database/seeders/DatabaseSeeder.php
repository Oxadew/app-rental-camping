<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KategoriAlat;
use App\Models\AlatCamping;
use App\Models\UnitAlat;
use App\Models\Penyewa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin / Petugas Rental
        User::updateOrCreate(
            ['email' => 'admin@outdoorgear.test'],
            [
                'name'     => 'Admin OutdoorGear',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Kategori Alat
        $katTenda = KategoriAlat::create([
            'nama_kategori' => 'Tenda Dome',
            'deskripsi'     => 'Tenda kapasitas 2 hingga 6 orang tahan angin dan hujan.',
        ]);

        $katCarrier = KategoriAlat::create([
            'nama_kategori' => 'Tas Carrier & Daypack',
            'deskripsi'     => 'Tas gunung berkapasitas besar dengan backsystem ergonomis.',
        ]);

        $katMasak = KategoriAlat::create([
            'nama_kategori' => 'Alat Masak & Penerangan',
            'deskripsi'     => 'Kompor portable, nesting, headlamp, dan lampu tenda.',
        ]);

        // 3. Katalog Alat Camping
        $tenda4P = AlatCamping::create([
            'kategori_alat_id'    => $katTenda->id,
            'kode_alat'           => 'TND-001',
            'nama_alat'           => 'Tenda Eiger Guardian 4P',
            'harga_sewa_per_hari' => 60000,
            'stok_total'          => 3,
            'stok_tersedia'       => 3,
            'deskripsi'           => 'Double layer waterproof 3000mm, frame alloy.',
        ]);

        $tenda2P = AlatCamping::create([
            'kategori_alat_id'    => $katTenda->id,
            'kode_alat'           => 'TND-002',
            'nama_alat'           => 'Tenda Consina Magnum 2P',
            'harga_sewa_per_hari' => 40000,
            'stok_total'          => 3,
            'stok_tersedia'       => 3,
            'deskripsi'           => 'Ringan untuk pendakian solo/berdua, berat 2.2 kg.',
        ]);

        $carrier60L = AlatCamping::create([
            'kategori_alat_id'    => $katCarrier->id,
            'kode_alat'           => 'CRR-001',
            'nama_alat'           => 'Carrier Osprey Atmos AG 65L',
            'harga_sewa_per_hari' => 50000,
            'stok_total'          => 2,
            'stok_tersedia'       => 2,
            'deskripsi'           => 'Anti-Gravity suspension, termasuk raincover.',
        ]);

        $komporSet = AlatCamping::create([
            'kategori_alat_id'    => $katMasak->id,
            'kode_alat'           => 'MSK-001',
            'nama_alat'           => 'Paket Kompor Windproof + Nesting',
            'harga_sewa_per_hari' => 25000,
            'stok_total'          => 2,
            'stok_tersedia'       => 2,
            'deskripsi'           => 'Kompor bunga mawar + set panci nesting 3 susun.',
        ]);

        // 4. Unit Fisik Bernomor Seri
        $units = [
            ['alat_camping_id' => $tenda4P->id, 'nomor_seri' => 'TND001-U01', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap pasak 14 pcs + 2 frame'],
            ['alat_camping_id' => $tenda4P->id, 'nomor_seri' => 'TND001-U02', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap, kondisi 95%'],
            ['alat_camping_id' => $tenda4P->id, 'nomor_seri' => 'TND001-U03', 'status' => 'tersedia', 'catatan_kondisi' => 'Baru'],
            ['alat_camping_id' => $tenda2P->id, 'nomor_seri' => 'TND002-U01', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap pasak 10 pcs'],
            ['alat_camping_id' => $tenda2P->id, 'nomor_seri' => 'TND002-U02', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap'],
            ['alat_camping_id' => $tenda2P->id, 'nomor_seri' => 'TND002-U03', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap'],
            ['alat_camping_id' => $carrier60L->id, 'nomor_seri' => 'CRR001-U01', 'status' => 'tersedia', 'catatan_kondisi' => 'Zipper lancar, raincover ada'],
            ['alat_camping_id' => $carrier60L->id, 'nomor_seri' => 'CRR001-U02', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap'],
            ['alat_camping_id' => $komporSet->id, 'nomor_seri' => 'MSK001-U01', 'status' => 'tersedia', 'catatan_kondisi' => 'Pemantik berfungsi normal'],
            ['alat_camping_id' => $komporSet->id, 'nomor_seri' => 'MSK001-U02', 'status' => 'tersedia', 'catatan_kondisi' => 'Lengkap dengan tas pouch'],
        ];

        foreach ($units as $unit) {
            UnitAlat::create($unit);
        }

        // 5. Data Penyewa
        Penyewa::create([
            'nik'          => '3578011204050001',
            'nama_lengkap' => 'Rizky Pratama',
            'no_hp'        => '081234567890',
            'alamat'       => 'Jl. Keputih Tegal Timur No. 14, Sukolilo, Surabaya',
        ]);

        Penyewa::create([
            'nik'          => '3578042508040002',
            'nama_lengkap' => 'Nabila Putri Maharani',
            'no_hp'        => '085711223344',
            'alamat'       => 'Jl. Gebang Lor No. 42, Sukolilo, Surabaya',
        ]);

        Penyewa::create([
            'nik'          => '3515081011030003',
            'nama_lengkap' => 'Dimas Aditya Saputra',
            'no_hp'        => '089655443322',
            'alamat'       => 'Perum Pondok Tjandra Indah Blok C-12, Waru, Sidoarjo',
        ]);
    }
}