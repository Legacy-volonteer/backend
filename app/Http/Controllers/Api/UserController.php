<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Получить профиль текущего пользователя",
     *     security={{"sanctum": {}}},
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно, возвращает данные пользователя и его confirmation_code",
     *         @OA\JsonContent(
     *             required={"id","username","email","confirmation_code"},
     *             @OA\Property(property="id", type="integer", format="int64", example=1),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="confirmation_code", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Ошибка Unauthorized")
     * )
     */
    public function profile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userDTO = $this->userService->getUserProfile($user->id);

        return response()->json([
            'id'                => $userDTO->id,
            'username'          => $userDTO->username,
            'email'             => $userDTO->email,
            'confirmation_code' => $user->confirmation_code,
        ]);
    }
}
