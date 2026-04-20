<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\User;
use App\Models\Penyewaan;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use App\Models\StokCabang;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   
public function index(Request $request)
{
    $user = Auth::user();

    /* ================= PENYEWA ================= */
    if ($user->status === 'penyewa') {

        $perPage = $request->get('per_page', 6); // jumlah card per halaman

        $cabang = Cabang::where('status_cabang', 'aktif')
            ->paginate($perPage)
            ->withQueryString();

        $adminpusat = User::where('status', 'admin_pusat')
            ->select('idusers', 'nama', 'no_telepon', 'alamat')
            ->first();

        return view('dashboard_penyewa', compact('cabang', 'adminpusat'));
    }


        /* ================= ADMIN CABANG ================= */
        if ($user->status === 'admin_cabang') {

    $cabangId = $user->adminCabang->cabang_idcabang ?? null;

    if (!$cabangId) {
        abort(404, 'Cabang tidak ditemukan');
    }

    // ================= CARD =================
    $totalPenyewa = Penyewaan::where('cabang_idcabang', $cabangId)
        ->distinct('penyewa_idpenyewa')
        ->count('penyewa_idpenyewa');

    $totalPenyewaan = Penyewaan::where('cabang_idcabang', $cabangId)->count();

    $totalAlat = StokCabang::where('cabang_idcabang', $cabangId)->count();

    $totalKategori = Kategori::count();

    // ================= CHART BULANAN =================
    $tahun = $request->tahun ?? date('Y');

    $pendapatan = Penyewaan::selectRaw('MONTH(tanggal_sewa) as bulan, SUM(total) as total')
        ->where('cabang_idcabang', $cabangId)
        ->where('status_penyewaan', 'selesai')
        ->whereYear('tanggal_sewa', $tahun)
        ->groupBy('bulan')
        ->pluck('total','bulan');

    $dataBulanan = [];
    for ($i=1; $i<=12; $i++) {
        $dataBulanan[] = $pendapatan[$i] ?? 0;
    }

    // ================= TOP PRODUK =================
    $alat = DB::table('item_penyewaan')
    ->join('produk', 'produk.idproduk', '=', 'item_penyewaan.produk_idproduk')
    ->join('penyewaan', 'penyewaan.idpenyewaan', '=', 'item_penyewaan.penyewaan_idpenyewaan')
    ->where('penyewaan.cabang_idcabang', $cabangId)
    ->select('produk.nama_produk', DB::raw('SUM(item_penyewaan.qty) as total'))
    ->groupBy('produk.nama_produk')
    ->orderByDesc('total')
    ->limit(3)
    ->get();

    $totalQty = $alat->sum('total');

    $alatPersen = $alat->map(function($a) use ($totalQty){
        return [
            'nama' => $a->nama_produk,
            'persen' => $totalQty > 0 ? round(($a->total / $totalQty) * 100) : 0
        ];
    });

    return view('/dashboard_cabang', compact(
        'totalPenyewa',
        'totalPenyewaan',
        'totalAlat',
        'totalKategori',
        'dataBulanan',
        'alatPersen',
        'tahun'
    ));
}

        /* ================= ADMIN PUSAT ================= */
        if ($user->status === 'admin_pusat') {

            return view('/dashboard_pusat');
        }

        /* ================= OWNER ================= */
        if ($user->status === 'owner') {

            return view('/dashboard_owner');
        }

        abort(403);
    }
 
}