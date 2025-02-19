<?php

namespace Database\Seeders;

use App\Models\Administrator;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Profil;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrator::factory(10)->create();

        if (Administrator::query()->count() < 1) {
            $admin = Administrator::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            Profil::factory(1)->withUser($admin->id)->create();
        }

        $this->call(ProfilSeeder::class);
    }
}
