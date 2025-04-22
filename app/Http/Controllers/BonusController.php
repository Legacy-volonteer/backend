<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\BonusClaim;
use App\Models\VolunteerRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class BonusController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/company/bonuses",
     *     operationId="createBonus",
     *     summary="Создание бонуса компанией",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","level"},
     *             @OA\Property(property="name",  type="string", example="Скидка 10%"),
     *             @OA\Property(property="level", type="string", example="max", enum={"max","medium","min"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Бонус успешно создан", @OA\JsonContent(ref="#/components/schemas/Bonus")),
     *     @OA\Response(response=400, description="Неправильные данные"),
     *     @OA\Response(response=401, description="Не аутентифицирован"),
     *     @OA\Response(response=403, description="Нет прав")
     * )
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string',
            'level' => 'required|in:max,medium,min',
        ]);

        $bonus = Bonus::create([
            'name'       => $validated['name'],
            'level'      => $validated['level'],
            'company_id' => Auth::guard('company')->id(),
            'is_used'    => false,
        ]);

        return response()->json($bonus, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/company/bonuses",
     *     operationId="getCompanyBonuses",
     *     summary="Бонусы текущей компании",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список бонусов компании",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     )
     * )
     */
    public function companyBonuses()
    {
        $bonuses = Bonus::where('company_id', Auth::guard('company')->id())->get();

        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/bonuses",
     *     operationId="getUserBonuses",
     *     summary="Все активированные бонусы пользователя (по убыванию времени активации)",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список бонусов пользователя",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $volunteer = VolunteerRecipient::where('user_id', $user->id)->first();

        if (! $volunteer) {
            return response()->json(['bonuses' => []], 200);
        }

        $bonuses = BonusClaim::with('bonus')
            ->where('volunteer_recipient_id', $volunteer->id)
            ->orderBy('claimed_at', 'desc')
            ->get()
            ->pluck('bonus');

        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/bonuses/available",
     *     operationId="getAvailableBonuses",
     *     summary="Доступные бонусы для текущего пользователя",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список доступных бонусов",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     )
     * )
     */
    public function available()
    {
        $user = Auth::user();

        // Найдём волонтёра по user_id, email или ИНН
        $volunteer = VolunteerRecipient::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->when($user->inn, function($q) use ($user) {
                $q->orWhere('inn', $user->inn);
            })
            ->first();

        if (! $volunteer) {
            return response()->json(['bonuses' => []], 200);
        }

        $bonuses = Bonus::where('company_id', $volunteer->company_id)
            ->where('is_used', false)
            ->get();

        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/bonuses/by-inn",
     *     operationId="getBonusesByInn",
     *     summary="Доступные бонусы по введённому ИНН",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="inn",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", example="772456789012")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список бонусов пользователя по ИНН",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     )
     * )
     */
    public function byInn(Request $request)
    {
        $data = $request->validate([
            'inn' => 'required|string',
        ]);

        $volunteer = VolunteerRecipient::where('inn', $data['inn'])->first();
        if (! $volunteer) {
            return response()->json(['bonuses' => []], 200);
        }

        $bonuses = BonusClaim::with('bonus')
            ->where('volunteer_recipient_id', $volunteer->id)
            ->orderBy('claimed_at', 'desc')
            ->get()
            ->pluck('bonus');

        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/bonuses",
     *     operationId="getAllBonuses",
     *     summary="Список всех бонусов (только для админов)",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Все бонусы",
     *         @OA\JsonContent(
     *             @OA\Property(property="bonuses", type="array", @OA\Items(ref="#/components/schemas/Bonus"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Запрещено")
     * )
     */
    public function allBonuses()
    {
        $user = Auth::user();
        if (! $user || ! $user->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $bonuses = Bonus::all();
        return response()->json(['bonuses' => $bonuses], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user/bonuses/{bonus}/claim",
     *     operationId="claimBonus",
     *     summary="Взять бонус",
     *     tags={"Bonus"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="bonus",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(response=201, description="Бонус взят", @OA\JsonContent(ref="#/components/schemas/BonusClaim")),
     *     @OA\Response(response=400, description="Бонус уже был использован"),
     *     @OA\Response(response=403, description="Нет прав на этот бонус"),
     *     @OA\Response(response=404, description="Бонус не найден")
     * )
     */
    public function claim(Bonus $bonus)
    {
        $user = Auth::user();

        $volunteer = VolunteerRecipient::where('company_id', $bonus->company_id)
            ->where(function($q) use ($user) {
                $q->where('email', $user->email);
                if ($user->inn) {
                    $q->orWhere('inn', $user->inn);
                }
            })
            ->first();

        if (! $volunteer) {
            return response()->json(['message' => 'Вы не загружены как волонтёр этой компании'], 403);
        }

        if ($bonus->is_used) {
            return response()->json(['message' => 'Этот бонус уже был использован'], 400);
        }

        if (is_null($volunteer->user_id)) {
            $volunteer->update(['user_id' => $user->id]);
        }
        $bonus->update(['is_used' => true]);

        $claim = BonusClaim::create([
            'bonus_id'               => $bonus->id,
            'volunteer_recipient_id' => $volunteer->id,
            'claimed_at'             => now(),
        ]);

        return response()->json($claim, 201);
    }
}
