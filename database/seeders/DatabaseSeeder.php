<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->withPersonalPortal()->create([
            'name' => 'ADMIN',
            'password' => 'password',
            'email' => 'admin@politicalflow.com.br',
        ]);
    }
}
