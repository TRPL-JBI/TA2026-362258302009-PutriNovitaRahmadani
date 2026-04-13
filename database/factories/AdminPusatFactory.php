<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AdminPusat;
use App\Models\User;

class AdminPusatFactory extends Factory
{
    protected $model = AdminPusat::class;

    public function definition(): array
    {
        return [
            'users_idusers' => User::factory(),
        ];
    }
}