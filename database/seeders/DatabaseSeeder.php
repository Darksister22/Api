<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeed::class);
        $this->call(StudentSeed::class);
        $this->call(SemesterSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}