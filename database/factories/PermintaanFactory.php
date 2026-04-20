<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Permintaan;
use App\Models\Cabang;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permintaan>
 */
class PermintaanFactory extends Factory
{
    protected $model = Permintaan::class;

    public function definition(): array
    {
        return [
            'cabang_idcabang' => Cabang::factory(),
            'tanggal_permintaan' => now(),
            'status' => 'menunggu',
            'keterangan' => 'test',
        ];
    }
}