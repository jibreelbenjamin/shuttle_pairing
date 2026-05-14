<?php

namespace Database\Seeders;

use App\Models\AppPassword;
use Illuminate\Database\Seeder;

class AppPasswordSeeder extends Seeder
{
    public function run(): void
    {
        AppPassword::create([
            'password' => 'APPOLO12',
        ]);
    }
}
