<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run()
    {
        Company::truncate();

        Company::create([
            'name'     => 'Благотворительный Фонд «Помощь»',
            'email'    => 'help_fund@example.com',
            'password' => Hash::make('peresekin'),
        ]);

        Company::create([
            'name'     => 'Центр Добрых Дел',
            'email'    => 'good_center@example.com',
            'password' => Hash::make('chooprin'),
        ]);
    }
}
