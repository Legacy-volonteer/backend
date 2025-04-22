<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BonusClaim;
use Carbon\Carbon;

class BonusClaimSeeder extends Seeder
{
    public function run()
    {
        BonusClaim::truncate();

        // Пользователь 1 получил первые два бонуса компании 1
        BonusClaim::create([
            'bonus_id'               => 1,        // «Сертификат благодарности»
            'volunteer_recipient_id' => 1,        // Иванов Иван
            'claimed_at'             => Carbon::now()->subDays(2),
        ]);
        BonusClaim::create([
            'bonus_id'               => 2,        // «Проездной на трамвай»
            'volunteer_recipient_id' => 1,
            'claimed_at'             => Carbon::now()->subDay(),
        ]);

        // Пользователь 2 получил один бонус компании 2
        BonusClaim::create([
            'bonus_id'               => 3,        // «Скидка 5%»
            'volunteer_recipient_id' => 2,        // Петров Петр
            'claimed_at'             => Carbon::now()->subHours(5),
        ]);
    }
}
