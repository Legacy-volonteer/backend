<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     required={"id","name","email"},
 *     @OA\Property(property="id",         type="integer", format="int64",        example=1),
 *     @OA\Property(property="name",       type="string",                       example="MyCompany"),
 *     @OA\Property(property="email",      type="string", format="email",       example="company@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time",   example="2025-04-23T16:55:38.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time",   example="2025-04-23T16:55:38.000000Z")
 * )
 */
class Company extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden   = ['password'];
    
    public function volunteers()
    {
        return $this->hasMany(VolunteerRecipient::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }
}

