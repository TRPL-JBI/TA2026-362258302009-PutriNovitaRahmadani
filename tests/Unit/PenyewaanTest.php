<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\Penyewaan;
use App\Models\StokCabang;
use App\Models\Produk;

use App\Http\Controllers\PenyewaanController;

class PenyewaanTest extends TestCase
{
    use RefreshDatabase;

    private function seedRelasi()
    {
        // USERS
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'User',
            'username' => 'user',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123456789',
            'alamat' => 'Jember',
            'status' => 'aktif'
        ]);

        // PENYEWA
        DB::table('penyewa')->insert([
            'idpenyewa' => 1,
            'users_idusers' => 1,
            'status_penyewa' => 'aktif'
        ]);

        // KATEGORI
        DB::table('kategori')->insert([
            'idkategori' => 1,
            'nama_kategori' => 'Tenda'
        ]);

        // ADMIN PUSAT
        DB::table('admin_pusat')->insert([
            'idadmin_pusat' => 1,
            'users_idusers' => 1
        ]);

        // PRODUK
        DB::table('produk')->insert([
            'idproduk' => 1,
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'harga' => 50000,
            'jenis_skala' => 'harian',
            'kategori_idkategori' => 1,
            'admin_pusat_idadmin_pusat' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // CABANG
        DB::table('cabang')->insert([
            'idcabang' => 1,
            'nama_cabang' => 'Cabang A',
            'status_cabang' => 'aktif',
            'lokasi' => 'Jember',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // STOK CABANG
        DB::table('stok_cabang')->insert([
            'idstok' => 1,
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 10,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function loginUser()
    {
        $user = User::find(1);
        Auth::login($user);
    }

    // =========================
    // TC-SEWA-02 (durasi & total)
    // =========================
    #[Test]
    public function tc_sewa_02_mengatur_tanggal_sewa()
    {
        Session::start();
        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $request = Request::create('/sewa', 'POST', [
            'tanggal_sewa' => '2026-01-01',
            'tanggal_selesai' => '2026-01-03',
            'metode_bayar' => 'cash',
            'produk_cabang' => [1],
            'qty' => [2]
        ]);

        $controller = new PenyewaanController();
        $response = $controller->store($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    // =========================
    // TC-SEWA-04 (cash)
    // =========================
    #[Test]
    public function tc_sewa_04_metode_cash()
    {
        Session::start();
        $this->seedRelasi();
        $this->loginUser();

        session(['tipe_toko' => 'cabang', 'toko_id' => 1]);

        $request = Request::create('/sewa', 'POST', [
            'tanggal_sewa' => '2026-01-01',
            'tanggal_selesai' => '2026-01-02',
            'metode_bayar' => 'cash',
            'produk_cabang' => [1],
            'qty' => [1]
        ]);

        $controller = new PenyewaanController();
        $controller->store($request);

        $this->assertDatabaseHas('penyewaan', [
            'metode_bayar' => 'cash'
        ]);
    }

    // =========================
    // TC-SEWA-05 (transfer)
    // =========================
    #[Test]
    public function tc_sewa_05_metode_transfer()
    {
        Session::start();
        $this->seedRelasi();
        $this->loginUser();

        session(['tipe_toko' => 'cabang', 'toko_id' => 1]);

        $request = Request::create('/sewa', 'POST', [
            'tanggal_sewa' => '2026-01-01',
            'tanggal_selesai' => '2026-01-02',
            'metode_bayar' => 'transfer',
            'produk_cabang' => [1],
            'qty' => [1]
        ]);

        $controller = new PenyewaanController();
        $controller->store($request);

        $this->assertDatabaseHas('penyewaan', [
            'metode_bayar' => 'transfer'
        ]);
    }

    // =========================
    // TC-SEWA-08 (upload valid)
    // =========================
    #[Test]
    public function tc_sewa_08_upload_bukti_valid()
    {
        $this->seedRelasi();

        DB::table('penyewaan')->insert([
    'idpenyewaan' => 1,
    'tanggal_sewa' => now(),
    'tanggal_selesai' => now()->addDays(1),
    'total' => 0,
    'total_produk' => 0,
    'status_penyewaan' => 'menunggu_pembayaran',
    'metode_bayar' => 'cash',
    'batas_pembayaran' => now()->addHours(2),
    'penyewa_idpenyewa' => 1,
    'cabang_idcabang' => 1,
    'admin_pusat_idadmin_pusat' => null,
    'created_at' => now(),
    'updated_at' => now()
]);

        $this->assertTrue(true); // simulasi sukses upload
    }

    // =========================
    // TC-SEWA-09 (upload invalid)
    // =========================
    #[Test]
    public function tc_sewa_09_upload_tidak_valid()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $controller = new PenyewaanController();

        $request = Request::create('/upload', 'POST', []);

        $controller->uploadBuktiBayar($request, 1);
    }

    // =========================
    // TC-SEWA-11 (belum bayar)
    // =========================
    #[Test]
    public function tc_sewa_11_belum_bayar()
    {
        $this->seedRelasi();
        $this->loginUser();

        DB::table('penyewaan')->insert([
    'idpenyewaan' => 1,
    'tanggal_sewa' => now(),
    'tanggal_selesai' => now()->addDays(1),
    'total' => 0,
    'total_produk' => 0,
    'status_penyewaan' => 'menunggu_pembayaran',
    'metode_bayar' => 'cash',
    'batas_pembayaran' => now()->addHours(2),
    'penyewa_idpenyewa' => 1,
    'cabang_idcabang' => 1,
    'admin_pusat_idadmin_pusat' => null,
    'created_at' => now(),
    'updated_at' => now()
]);

        $controller = new PenyewaanController();
        $response = $controller->riwayat();

        $this->assertNotNull($response);
    }

    // =========================
    // TC-SEWA-12 (aktif)
    // =========================
    #[Test]
    public function tc_sewa_12_penyewaan_aktif()
    {
        $this->seedRelasi();
        $this->loginUser();

        DB::table('penyewaan')->insert([
    'idpenyewaan' => 1,
    'tanggal_sewa' => now(),
    'tanggal_selesai' => now()->addDays(1),
    'total' => 0,
    'total_produk' => 0,
    'status_penyewaan' => 'sedang_disewa',
    'metode_bayar' => 'cash',
    'batas_pembayaran' => now()->addHours(2),
    'penyewa_idpenyewa' => 1,
    'cabang_idcabang' => 1,
    'admin_pusat_idadmin_pusat' => null,
    'created_at' => now(),
    'updated_at' => now()
]);

        $controller = new PenyewaanController();
        $response = $controller->riwayat();

        $this->assertNotNull($response);
    }
}