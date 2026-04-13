<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PermintaanProduk;
use App\Models\Permintaan;
use App\Models\Produk;

class PermintaanProdukFactory extends Factory
{
    protected $model = PermintaanProduk::class;

    public function definition(): array
    {
        return [
            'permintaan_id' => Permintaan::factory(),
            'produk_idproduk' => Produk::factory(),
            'jumlah_diminta' => 3,
        ];
    }
}