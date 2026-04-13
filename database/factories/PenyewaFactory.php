<?php

namespace Database\Factories;

use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenyewaFactory extends Factory
{
    protected $model = Penyewa::class;

    public function definition(): array
    {
        return [
            'users_idusers' => User::factory(),

            'gambar_identitas' => null,

            'status_penyewa' => 'aktif',
        ];
    }
}