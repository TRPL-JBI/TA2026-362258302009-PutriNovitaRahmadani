<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokCabang extends Model
{
    use HasFactory;
    protected $table = 'stok_cabang';

    protected $primaryKey = 'idstok';   // 🔥 INI YANG HILANG

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'produk_idproduk',
        'cabang_idcabang',
        'jumlah',
        'is_active'
    ];

    public function produk()
    {
        return $this->belongsTo(
            Produk::class,
            'produk_idproduk',
            'idproduk'
        );
    }
}