<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AdminCabang;
use App\Models\User;
use App\Models\Cabang;

class AdminCabangFactory extends Factory
{
    protected $model = AdminCabang::class;

    public function definition(): array
    {
        return [
            'users_idusers' => User::factory(),
            'cabang_idcabang' => Cabang::factory(),
            'gambar_mou' => null,
        ];
    }
}