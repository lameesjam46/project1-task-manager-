<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order respects FK rules:
     * categories → users/profiles → companies/pivots → projects/teams → tasks/related
     *
     * Exactly one Admin user is created (admin@taskmanager.test).
     * Default password for all seeded users: password
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
