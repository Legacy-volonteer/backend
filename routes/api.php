<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BonusClaimsController;
use App\Http\Controllers\CompaniesController;

/*
|--------------------------------------------------------------------------
| Волонтёры (пользовательские маршруты)
|--------------------------------------------------------------------------
*/

// Регистрация / логин
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login',    [AuthenticatedSessionController::class, 'store']);

// Восстановленный маршрут для получения текущего пользователя по токену
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'profile']);

// Для обратной совместимости: история бонусов без префикса /user
Route::middleware('auth:sanctum')->get('/bonuses/history', [BonusClaimsController::class, 'userHistory']);

// Группа маршрутов под префиксом /user
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Профиль + выход
    Route::post('logout',            [AuthenticatedSessionController::class, 'destroy']);

    // Все активированные бонусы пользователя (сортировка по убыванию времени)
    Route::get('bonuses',            [BonusController::class, 'index']);

    // Доступные для активации бонусы (по email или ИНН)
    Route::get('bonuses/available',  [BonusController::class, 'available']);

    // Доступные бонусы по введённому ИНН
    Route::get('bonuses/by-inn',     [BonusController::class, 'byInn']);

    // Взять бонус
    Route::post('bonuses/{bonus}/claim', [BonusController::class, 'claim']);

    // История полученных бонусов (волонтёр)
    Route::get('bonuses/history',    [BonusClaimsController::class, 'userHistory']);
});

/*
|--------------------------------------------------------------------------
| Компании
|--------------------------------------------------------------------------
*/
Route::prefix('company')->group(function () {
    // Регистрация + логин компании
    Route::post('register',         [CompanyAuthController::class, 'register']);
    Route::post('login',            [CompanyAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Профиль компании
        Route::get('me',            [CompanyAuthController::class, 'me']);

        // Волонтёры своей компании
        Route::get('volunteers',          [CompaniesController::class, 'volunteers']);
        Route::post('volunteers/upload',  [CompaniesController::class, 'uploadVolunteers']);

        // Создание бонуса
        Route::post('bonuses',            [BonusController::class, 'create']);

        // Список бонусов текущей компании
        Route::get('bonuses',             [BonusController::class, 'companyBonuses']);

        // История выдачи бонусов компанией
        Route::get('bonuses/history',     [BonusClaimsController::class, 'companyHistory']);
    });
});

/*
|--------------------------------------------------------------------------
| Админка (доступ по admin-токену)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Загрузка CSV волонтёров для всех компаний
    Route::post('volunteers/upload',  [AdminController::class, 'uploadVolunteers']);

    // Список всех бонусов
    Route::get('bonuses',             [AdminController::class, 'listBonuses']);

    // Список всех волонтёров
    Route::get('volunteers',          [AdminController::class, 'listVolunteers']);

    // Список всех компаний
    Route::get('companies',           [AdminController::class, 'listCompanies']);
});
