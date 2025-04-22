<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\User;
use App\Models\VolunteerRecipient;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::truncate();
        VolunteerRecipient::truncate();

        User::create([
            'username'          => 'admin',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('root'),
            'confirmation_code' => '00000',
        ]);

        $csv = <<<CSV
Иванов Иван Иванович,772456789012,+7 916 555 82 19,aleksandr.ivanov1985@yandex.ru,12.05.1988,Помощь в организации благотворительного забега
Петров Петр Петрович,500123456789,+7 925 123 45 67,mariya.petrova2000@yandex.ru,28.02.2001,Сбор средств для местного приюта для животных
Сидоров Сидор Сидорович,667101122334,+7 903 987 65 43,sergey.smirnov77@yandex.ru,03.09.1975,Участие в уборке парка
Смирнов Алексей Иванович,780234567890,+7 968 234 56 78,elena.kuznetsova92@yandex.ru,16.07.1992,Организация мастер-класса для детей из малообеспеченных семей
Кузнецов Дмитрий Петрович,230987654321,+7 985 345 67 89,dmitriy.vasiliev88@yandex.ru,21.11.2005,Помощь пожилым людям с покупками
Попов Сергей Сидорович,345678901234,+7 915 456 78 90,natalia.popova75@yandex.ru,08.04.1963,Проведение занятий по английскому языку для мигрантов
Васильев Андрей Алексеевич,456789012345,+7 926 567 89 01,andrey.sokolov63@yandex.ru,19.10.1981,Участие в акции по сдаче крови
Федоров Михаил Дмитриевич,567890123456,+7 905 678 90 12,olga.lebedeva99@yandex.ru,05.01.2010,Организация концерта в доме престарелых
Соколов Александр Сергеевич,678901234567,+7 967 789 01 23,ivan.egorov51@yandex.ru,30.06.1997,Помощь в проведении фестиваля уличной еды в поддержку бездомных
Михайлов Николай Андреевич,789012345678,+7 980 890 12 34,tatiana.volkova86@yandex.ru,14.03.1958,Сбор одежды для нуждающихся
CSV;

        $lines = preg_split('/\r\n|\r|\n/', trim($csv));

        $companyIds   = Company::pluck('id')->toArray();
        $companyCount = count($companyIds);

        foreach ($lines as $index => $line) {
            if (! trim($line)) {
                continue;
            }

            $cols = str_getcsv($line, ',');
            $cols = array_map('trim', $cols);
            list($fullName, $inn, $phone, $email, $birthDate, $achievements) = $cols;

            $confirmationCode = (string)random_int(10000, 99999);

            $user = User::create([
                'username'          => Str::before($email, '@'),
                'email'             => $email,
                'password'          => Hash::make('password'),
                'confirmation_code' => $confirmationCode,
            ]);

            VolunteerRecipient::create([
                'company_id'   => $companyIds[$index % $companyCount],
                'full_name'    => $fullName,
                'inn'          => $inn,
                'phone'        => $phone ?: null,
                'email'        => $email,
                'birth_date'   => Carbon::createFromFormat('d.m.Y', $birthDate)->toDateString(),
                'achievements' => $achievements,
                'access_level' => $index < 5 ? 'максимальный' : 'минимальный',
                'user_id'      => $user->id,
            ]);
        }
    }
}
