<?php

namespace App\Http\Controllers;

use App\Models\BonusClaim;
use App\Models\VolunteerRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class BonusClaimsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/company/bonuses/history",
     *     operationId="getCompanyHistory",
     *     summary="История всех выданных бонусов (компания)",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="История выдачи бонусов компанией",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="bonuses", type="array",
     *                 @OA\Items(
     *                     @OA\Schema(
     *                         @OA\Property(property="id",         type="integer", example=1),
     *                         @OA\Property(property="name",       type="string",  example="Скидка 10%"),
     *                         @OA\Property(property="level",      type="string",  example="max"),
     *                         @OA\Property(property="claimed_at", type="string",  example="2025-04-23T20:47:55"),
     *                         @OA\Property(
     *                             property="volunteer", type="object",
     *                             @OA\Property(property="id",           type="integer", example=15),
     *                             @OA\Property(property="full_name",    type="string",  example="Иванов И. И."),
     *                             @OA\Property(property="inn",          type="string",  example="772456789012"),
     *                             @OA\Property(property="email",        type="string",  example="volunteer@example.com"),
     *                             @OA\Property(property="access_level", type="string",  example="max")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function companyHistory(): JsonResponse
    {
        $companyId = Auth::guard('company')->id();

        $claims = BonusClaim::with(['bonus', 'volunteerRecipient'])
            ->whereHas('bonus', fn($q) => $q->where('company_id', $companyId))
            ->get();

        $bonuses = $claims->map(fn($claim) => [
            'id'         => $claim->bonus->id,
            'name'       => $claim->bonus->name,
            'level'      => $claim->bonus->level,
            'claimed_at' => $claim->claimed_at->toDateTimeString(),
            'volunteer'  => [
                'id'           => $claim->volunteerRecipient->id,
                'full_name'    => $claim->volunteerRecipient->full_name,
                'inn'          => $claim->volunteerRecipient->inn,
                'email'        => $claim->volunteerRecipient->email,
                'access_level' => $claim->volunteerRecipient->access_level,
            ],
        ]);

        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/bonuses/history",
     *     operationId="getUserHistory",
     *     summary="История полученных бонусов (пользователь)",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="История получения бонусов пользователем",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="bonuses", type="array",
     *                 @OA\Items(
     *                     @OA\Schema(
     *                         @OA\Property(property="id",         type="integer", example=1),
     *                         @OA\Property(property="name",       type="string",  example="Скидка 10%"),
     *                         @OA\Property(property="level",      type="string",  example="max"),
     *                         @OA\Property(property="claimed_at", type="string",  example="2025-04-23T20:47:55"),
     *                         @OA\Property(
     *                             property="volunteer", type="object",
     *                             @OA\Property(property="id",           type="integer", example=15),
     *                             @OA\Property(property="full_name",    type="string",  example="Иванов И. И."),
     *                             @OA\Property(property="inn",          type="string",  example="772456789012"),
     *                             @OA\Property(property="email",        type="string",  example="volunteer@example.com"),
     *                             @OA\Property(property="access_level", type="string",  example="max")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function userHistory(): JsonResponse
    {
        $user = Auth::user();

        $volunteer = VolunteerRecipient::where('user_id', $user->id)->first();
        if (! $volunteer) {
            return response()->json(['bonuses' => []], 200);
        }

        $claims = BonusClaim::with(['bonus', 'volunteerRecipient'])
            ->where('volunteer_recipient_id', $volunteer->id)
            ->get();

        $bonuses = $claims->map(fn($claim) => [
            'id'         => $claim->bonus->id,
            'name'       => $claim->bonus->name,
            'level'      => $claim->bonus->level,
            'claimed_at' => $claim->claimed_at->toDateTimeString(),
            'volunteer'  => [
                'id'           => $claim->volunteerRecipient->id,
                'full_name'    => $claim->volunteerRecipient->full_name,
                'inn'          => $claim->volunteerRecipient->inn,
                'email'        => $claim->volunteerRecipient->email,
                'access_level' => $claim->volunteerRecipient->access_level,
            ],
        ]);

        return response()->json(['bonuses' => $bonuses], 200);
    }
}
