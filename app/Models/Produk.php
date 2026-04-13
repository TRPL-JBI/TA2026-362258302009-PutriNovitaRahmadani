<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
     use HasFactory;
    protected $table = 'produk';
    protected $primaryKey = 'idproduk';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_produk',
        'stok_pusat',
        'harga',
        'jenis_skala',
        'gambar_produk',
        'kategori_idkategori',          // ✅
        'admin_pusat_idadmin_pusat',
    ];

    // ================= RELATION =================
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_idkategori', 'idkategori');
    }

    public function stokCabang()
{
    return $this->hasMany(
        StokCabang::class,
        'produk_idproduk', // FK di stok_cabang
        'idproduk'         // PK di produk
    );
}

}
