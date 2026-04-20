<?php

namespace Database\Factories;

use App\Models\Penyewaan;
use App\Models\Penyewa;
use App\Models\Cabang;
use App\Models\AdminPusat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenyewaanFactory extends Factory
{
    protected $model = Penyewaan::class;

    public function definition(): array
    {
        return [
            'tanggal_sewa' => now(),
            'tanggal_selesai' => now()->addDays(3),
            'tanggal_kembali' => null,
            'sudah_diingatkan' => 0,
            'total' => 100000,
            'total_produk' => 1,
            'bukti_bayar' => null,
            'status_penyewaan' => 'menunggu_pembayaran',
            'metode_bayar' => 'transfer',
            'batas_pembayaran' => now()->addDay(),

            // 🔥 WAJIB FK VALID
            'penyewa_idpenyewa' => Penyewa::factory(),
            'cabang_idcabang' => Cabang::factory(),
            'admin_pusat_idadmin_pusat' => AdminPusat::factory(),
        ];
    }
}