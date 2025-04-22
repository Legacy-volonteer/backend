<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Bonus",
 *   type="object",
 *   required={"id","name","level","company_id","is_used","created_at","updated_at"},
 *   @OA\Property(property="id",         type="integer", format="int64", example=1),
 *   @OA\Property(property="name",       type="string",               example="Скидка 10%"),
 *   @OA\Property(property="level",      type="string",               example="max"),
 *   @OA\Property(property="company_id", type="integer", format="int64", example=3),
 *   @OA\Property(property="user_id",    type="integer", format="int64", nullable=true, example=null),
 *   @OA\Property(property="is_used",    type="boolean",              example=false),
 *   @OA\Property(property="created_at", type="string",  format="date-time", example="2025-04-23T15:47:55Z"),
 *   @OA\Property(property="updated_at", type="string",  format="date-time", example="2025-04-23T15:47:55Z")
 * )
 */
class Bonus extends Model
{
    protected $fillable = [
        'company_id','name', 'level','is_used'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function claim()
    {
        return $this->hasOne(BonusClaim::class);
    }
}
