<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Bonus;

class BonusSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            Bonus::create([
                'company_id' => $company->id,
                'name'       => 'Сертификат благодарности',
                'level'      => 'min',
                'is_used'    => true,
            ]);

            // Средний бонус
            Bonus::create([
                'company_id' => $company->id,
                'name'       => 'Проездной на трамвай',
                'level'      => 'medium',
                'is_used'    => false,
            ]);

            // Максимальный бонус
            Bonus::create([
                'company_id' => $company->id,
                'name'       => 'Абонемент в спортивный зал',
                'level'      => 'max',
                'is_used'    => true,
            ]);
        }
    }
}
