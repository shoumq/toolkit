<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Declaration;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();

        Declaration::factory(100)->create();

        User::factory()->create([
            'name' => 'Admin user',
            'email' => 'root@mail.ru',
            'password' => 'root',
            'number' => '89161112233',
            'address' => 'Проспект мира, 5',
            'is_admin' => true,
        ]);
    }
}
