<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StokCabang;
use App\Models\Produk;
use App\Models\Paket;

class CartController extends Controller
{
    /* ================= PRODUK ADD ================= */
    public function add(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer', 'exists:stok_cabang,idstok'],
    ]);

    $cabangId = session('cabang_id');

    if (!$cabangId) {
        return response()->json([
            'error' => 'Cabang belum dipilih',
            'cart'  => session('cart', [])
        ], 403);
    }

    $stok = StokCabang::with('produk')
        ->where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak ditemukan',
            'cart'  => session('cart', [])
        ], 403);
    }

    $cart = session()->get('cart', []);
    $currentQty = $cart[$stok->idstok]['qty'] ?? 0;

    if ($currentQty >= $stok->jumlah) {
        return response()->json([
            'error' => 'Stok tidak mencukupi',
            'cart'  => $cart
        ], 422);
    }

    $cart[$stok->idstok] = [
        'type'   => 'produk',
        'idstok' => $stok->idstok,
        'nama'   => $stok->produk->nama_produk,
        'harga'  => $stok->produk->harga,
        'qty'    => $currentQty + 1
    ];

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function update(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer'],
        'qty'    => ['required', 'integer', 'min:0', 'max:1000'],
    ]);

    $cabangId = session('cabang_id');

    if (!$cabangId) {
        return response()->json([
            'error' => 'Cabang belum dipilih',
            'cart'  => session('cart', [])
        ], 403);
    }

    $cart = session('cart', []);

    if (!isset($cart[$request->idstok])) {
        return response()->json([
            'error' => 'Item tidak ditemukan',
            'cart'  => $cart
        ], 404);
    }

    $stok = StokCabang::where('idstok', $request->idstok)
        ->where('cabang_idcabang', $cabangId)
        ->first();

    if (!$stok) {
        return response()->json([
            'error' => 'Stok tidak valid',
            'cart'  => $cart
        ], 403);
    }

    if ($stok->jumlah <= 0) {
        unset($cart[$request->idstok]);
        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Stok habis',
            'cart'  => $cart
        ]);
    }

    if ($request->qty <= 0) {
        unset($cart[$request->idstok]);
        session(['cart' => $cart]);

        return response()->json([
            'cart' => $cart
        ]);
    }

    if ($request->qty > $stok->jumlah) {
        $cart[$request->idstok]['qty'] = $stok->jumlah;
        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Qty melebihi stok',
            'max'   => $stok->jumlah,
            'cart'  => $cart
        ]);
    }

    $cart[$request->idstok]['qty'] = $request->qty;
    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function delete(Request $request)
{
    $request->validate([
        'idstok' => ['required', 'integer'],
    ]);

    $cart = session('cart', []);

    unset($cart[$request->idstok]);

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}

    /* ================= PAKET ADD ================= */
    public function addPaket(Request $request)
{
    $paket = Paket::with('detail.stokCabang.produk')
        ->findOrFail($request->paket_id);

    $cart = session('cart', []);

    $items = [];

    foreach ($paket->detail as $d) {
        $items[] = [
            'type'   => 'produk_dalam_paket',
            'idstok' => $d->stokCabang->idstok,
            'nama'   => $d->stokCabang->produk->nama_produk,
            'qty'    => $d->qty,
            'harga'  => $d->stokCabang->produk->harga,
        ];
    }

    $cart['paket_'.$paket->id] = [
        'type'      => 'paket',
        'paket_id'  => $paket->id,
        'nama'      => $paket->nama_paket,
        'harga'     => $paket->harga_paket,
        'qty'       => 1,
        'items'     => $items
    ];

    session(['cart' => $cart]);

    // 🔥 FIX DI SINI
    return response()->json([
        'cart' => $cart
    ]);
}
public function updatePaket(Request $request)
{


    $request->validate([
        'paket_id' => ['required', 'integer'],
        'qty'      => ['required', 'integer', 'min:0', 'max:100'],
    ]);

    $cart = session('cart', []);
    $key = 'paket_'.$request->paket_id;

    if (!isset($cart[$key])) {
        return response()->json([
            'error' => 'Paket tidak ditemukan',
            'cart'  => $cart
        ], 404);
    }

    $paket = Paket::with('detail.stokCabang')->find($request->paket_id);

    if (!$paket) {
        return response()->json([
            'error' => 'Data paket tidak valid',
            'cart'  => $cart
        ], 403);
    }

    // 🔥 hapus kalau qty 0
    if ($request->qty <= 0) {
        unset($cart[$key]);
        session(['cart' => $cart]);

        return response()->json([
            'cart' => $cart
        ]);
    }

    // 🔥 HITUNG MAX PAKET GLOBAL
    $maxPaket = PHP_INT_MAX;

    foreach ($paket->detail as $d) {

        $stok = $d->stokCabang;

        if (!$stok || $stok->is_active == 0) {
            return response()->json([
                'error' => 'Ada item paket tidak tersedia',
                'cart'  => $cart
            ]);
        }

        if ($d->qty <= 0) continue;

        $maxItem = floor($stok->jumlah / $d->qty);

        $maxPaket = min($maxPaket, $maxItem);
    }

    // 🔥 kalau melebihi stok
    if ($request->qty > $maxPaket) {

        $cart[$key]['qty'] = $maxPaket;
        $cart[$key]['max'] = $maxPaket;

        session(['cart' => $cart]);

        return response()->json([
            'error' => 'Melebihi stok paket',
            'max'   => $maxPaket,
            'cart'  => $cart
        ]);
    }

    // ✅ update normal
    $cart[$key]['qty'] = $request->qty;
    $cart[$key]['max'] = $maxPaket;

    session(['cart' => $cart]);

    return response()->json([
        'cart' => $cart
    ]);
}
public function deletePaket(Request $request)
{
    $request->validate([
        'paket_id' => ['required', 'integer'],
    ]);

    $cart = session('cart', []);

    unset($cart['paket_'.$request->paket_id]);

    session(['cart' => $cart]);

    // 🔥 FIX DI SINI
    return response()->json([
        'cart' => $cart
    ]);
}
   public function addPusat(Request $request)
{
    $produk = Produk::findOrFail($request->idproduk);

    $cart = session()->get('cart', []);

    $currentQty = $cart[$produk->idproduk]['qty'] ?? 0;

    // cek stok pusat
    if ($currentQty >= $produk->stok_pusat) {
        return response()->json([
            'error' => 'Stok tidak mencukupi'
        ], 422);
    }

    $cart[$produk->idproduk] = [
        'idproduk' => $produk->idproduk,
        'nama'     => $produk->nama_produk,
        'harga'    => $produk->harga,
        'qty'      => $currentQty + 1
    ];

    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function updatePusat(Request $request)
{
    $produk = Produk::findOrFail($request->idproduk);
    $cart = session('cart', []);

    if ($request->qty <= 0) {
        unset($cart[$request->idproduk]);
        session(['cart' => $cart]);
        return response()->json($cart);
    }

    if ($request->qty > $produk->stok_pusat) {
        return response()->json([
            'error' => 'Qty melebihi stok'
        ], 422);
    }

    $cart[$request->idproduk]['qty'] = $request->qty;
    session(['cart' => $cart]);

    return response()->json($cart);
}

    public function deletePusat(Request $request)
    {
        $cart = session('cart');
        unset($cart[$request->idproduk]);

        session(['cart' => $cart]);

        return response()->json($cart);
    }
}
