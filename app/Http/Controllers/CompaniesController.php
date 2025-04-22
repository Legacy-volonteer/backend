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
     *   summary="Список волонтёров компании",
     *   tags={"Company"},
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Список волонтёров",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="volunteers",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/VolunteerRecipient")
     *       )
     *     )
     *   )
     * )
     */
    public function volunteers()
    {
        $companyId = Auth::guard('company')->id();

        $vols = VolunteerRecipient::with(['claims.bonus', 'user'])
            ->where('company_id', $companyId)
            ->get();

        return response()->json([
            'volunteers' => $vols,
        ], 200);
    }
}
