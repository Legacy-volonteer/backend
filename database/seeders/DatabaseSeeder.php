<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            BonusSeeder::class,
            BonusClaimSeeder::class,
        ]);
    }
}
