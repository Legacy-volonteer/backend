<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class CompanyAuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/company/register",
     *     summary="Регистрация новой компании",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="MyCompany"),
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешная регистрация компании",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", ref="#/components/schemas/Company"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|unique:companies,name',
            'email'    => 'required|email|unique:companies,email',
            'password' => 'required|string|min:4',
        ]);

        $company = Company::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $company->createToken('company_token')->plainTextToken;

        return response()->json([
            'company'      => $company,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/company/login",
     *     summary="Авторизация компании",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вход",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", ref="#/components/schemas/Company"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Неверные учётные данные")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $company = Company::where('email', $data['email'])->first();

        if (! $company || ! Hash::check($data['password'], $company->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $company->createToken('company_token')->plainTextToken;

        return response()->json([
            'company'      => $company,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/company/me",
     *     summary="Получить текущую компанию",
     *     tags={"Company"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Профиль текущей компании",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
