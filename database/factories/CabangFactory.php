<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cabang;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cabang>
 */
class CabangFactory extends Factory
{
    protected $model = Cabang::class;

    public function definition(): array
    {
        return [
            'nama_cabang' => 'Cabang ' . $this->faker->city(),
            'status_cabang' => 'aktif',
            'lokasi' => $this->faker->address(),
        ];
    }
}