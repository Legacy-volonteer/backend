<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Company;
use App\Models\VolunteerRecipient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/volunteers/upload",
     *     summary="Загрузить CSV-файл со списком волонтёров для всех компаний",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="CSV: full_name,inn,phone,email,birth_date (d.m.Y),achievements"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный импорт",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Volunteers uploaded successfully"),
     *             @OA\Property(property="imported", type="integer", example=20)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Неверный файл")
     * )
     */
    public function uploadVolunteers(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path     = $request->file('file')->getRealPath();
        $handle   = fopen($path, 'r');
        $header   = fgetcsv($handle, 1000, ","); // Пропускаем заголовок
        $imported = 0;

        while ($row = fgetcsv($handle, 1000, ",")) {
            // пропустить пустые строки
            if (! array_filter($row)) {
                continue;
            }
            // пропустить если колонок меньше шести
            if (count($row) < 6) {
                continue;
            }

            // безопасно распаковываем
            [$fullName, $inn, $phone, $email, $birthDate, $achievements] = array_map('trim', $row);

            // приводим дату рождения
            $bd = $birthDate
                ? Carbon::createFromFormat('d.m.Y', $birthDate)->toDateString()
                : null;

            $volData = [
                'full_name'    => $fullName,
                'inn'          => $inn,
                'phone'        => $phone ?: null,
                'email'        => $email ?: null,
                'birth_date'   => $bd,
                'achievements' => $achievements ?: null,
                'access_level' => 'минимальный',
            ];

            // для каждой компании
            foreach (Company::all() as $company) {
                $exists = VolunteerRecipient::where('company_id', $company->id)
                    ->where(function($q) use ($email, $inn) {
                        $q->where('email', $email)
                            ->orWhere('inn', $inn);
                    })
                    ->exists();

                if (! $exists) {
                    VolunteerRecipient::create($volData + ['company_id' => $company->id]);
                    $imported++;
                }
            }
        }

        fclose($handle);

        return response()->json([
            'message'  => 'Volunteers uploaded successfully',
            'imported' => $imported,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/bonuses",
     *     summary="Все бонусы (админ)",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Список бонусов",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     )
     * )
     */
    public function listBonuses(): JsonResponse
    {
        return response()->json(['bonuses' => Bonus::all()]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/volunteers",
     *     summary="Все волонтёры (админ)",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Список волонтёров",
     *         @OA\JsonContent(
     *             @OA\Property(property="volunteers", type="array", @OA\Items(ref="#/components/schemas/VolunteerRecipient"))
     *         )
     *     )
     * )
     */
    public function listVolunteers(): JsonResponse
    {
        return response()->json(['volunteers' => VolunteerRecipient::all()]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/companies",
     *     summary="Все компании (админ)",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Список компаний",
     *         @OA\JsonContent(
     *             @OA\Property(property="companies", type="array", @OA\Items(ref="#/components/schemas/Company"))
     *         )
     *     )
     * )
     */
    public function listCompanies(): JsonResponse
    {
        return response()->json(['companies' => Company::all()]);
    }
}
