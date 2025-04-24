<?php

namespace App\Http\Controllers;

use App\Models\VolunteerRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;
use League\Csv\Exception;
use OpenApi\Annotations as OA;

class CompaniesController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/company/volunteers/upload",
     *   summary="Загрузить CSV волонтёров",
     *   tags={"Company"},
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="file", type="string", format="binary")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="CSV обработан")
     * )
     */
    public function uploadVolunteers(Request $request)
    {
        $request->validate(['file'=>'required|file|mimes:csv,txt']);
        $path = $request->file('file')->getPathname();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords([
                'full_name','inn','phone','email','birth_date','achievements'
            ]);
        } catch (Exception $e) {
            return response()->json(['error'=>'Не удалось прочитать CSV'], 400);
        }

        $companyId = Auth::guard('company')->id();
        $count = 0;

        foreach ($records as $i => $row) {
            $level = $i < 50
              ? 'максимальный'
              : ($i < 100 ? 'средний' : 'минимальный');

            VolunteerRecipient::create([
                'company_id'   => $companyId,
                'full_name'    => $row['full_name'],
                'inn'          => $row['inn'],
                'phone'        => $row['phone'] ?? null,
                'email'        => $row['email'] ?? null,
                'birth_date'   => $row['birth_date']
                                   ? \Carbon\Carbon::createFromFormat('d.m.Y', $row['birth_date'])
                                   : null,
                'achievements' => $row['achievements'] ?? null,
                'access_level' => $level,
            ]);
            $count++;
        }

        return response()->json([
            'company_id'                 => $companyId,
            'uploaded_volunteers_count'  => $count,
        ], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/company/volunteers",
     *   summary="Список волонтёров компании (только confirmation_code)",
     *   tags={"Company"},
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Список кодов подтверждения волонтёров компании",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="volunteers",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="confirmation_code", type="string", example="12345")
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function volunteers()
    {
        $companyId = Auth::guard('company')->id();

        $vols = VolunteerRecipient::with('user')
            ->where('company_id', $companyId)
            ->get();

        // Формируем массив вида [ ['confirmation_code' => '12345'], ... ]
        $result = $vols->map(function ($v) {
            return [
                'confirmation_code' => $v->user->confirmation_code ?? null,
            ];
        });

        return response()->json([
            'volunteers' => $result,
        ], 200);
    }
}
