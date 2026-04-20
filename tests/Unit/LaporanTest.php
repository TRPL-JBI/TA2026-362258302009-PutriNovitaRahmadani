<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Cabang;
use App\Models\AdminCabang;
use App\Models\Penyewaan;
use App\Models\Penyewa;
use App\Models\AdminPusat;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdminCabang()
    {
        $user = User::factory()->create([
            'status' => 'admin_cabang'
        ]);

        $cabang = Cabang::create([
            'nama_cabang' => 'Cabang Test',
            'status_cabang' => 'aktif',
            'lokasi' => 'Banyuwangi'
        ]);

        AdminCabang::create([
            'users_idusers' => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
        ]);

        return [$user, $cabang];
    }

    private function createPenyewaan($cabang, $bulan = null)
    {
        $userPenyewa = User::factory()->create();

        $penyewa = Penyewa::create([
            'users_idusers' => $userPenyewa->idusers,
            'gambar_identitas' => null,
            'status_penyewa' => 'aktif',
        ]);

        $adminPusat = AdminPusat::factory()->create();

        return Penyewaan::create([
            'tanggal_sewa' => $bulan ?? now(),
            'tanggal_selesai' => now()->addDays(3),
            'tanggal_kembali' => now(),
            'sudah_diingatkan' => 0,
            'total' => 100000,
            'total_produk' => 1,
            'bukti_bayar' => null,
            'status_penyewaan' => 'selesai',
            'metode_bayar' => 'transfer',
            'batas_pembayaran' => now()->addDay(),

            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang' => $cabang->idcabang,
            'admin_pusat_idadmin_pusat' => $adminPusat->idadmin_pusat,
        ]);
    }

    /** @test */
    public function tc_lp_01_admin_dapat_melihat_halaman_laporan()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->actingAs($user)
            ->get(route('laporan'))
            ->assertStatus(200);
    }

    /** @test */
    public function tc_lp_02_filter_laporan_berdasarkan_bulan()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->createPenyewaan($cabang, now());

        $this->actingAs($user)
            ->get(route('laporan', [
                'bulan' => '2025-01'
            ]))
            ->assertStatus(200)
            ->assertViewHas('penyewaan');
    }

    /** @test */
    public function tc_lp_03_filter_bulan_tanpa_data()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->actingAs($user)
            ->get(route('laporan', [
                'bulan' => '2024-02'
            ]))
            ->assertStatus(200)
            ->assertViewHas('penyewaan', function ($data) {
                return count($data) === 0;
            });
    }

    /** @test */
    public function tc_lp_04_cetak_laporan_filter_tanggal()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->createPenyewaan($cabang, now());

        $this->actingAs($user)
            ->get(route('laporan', [
                'bulan' => '2025-01'
            ]))
            ->assertStatus(200)
            ->assertViewHas('totalPendapatan');
    }
}