<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="BonusClaim",
 *   type="object",
 *   required={"id","bonus_id","volunteer_recipient_id","claimed_at"},
 *   @OA\Property(property="id",   type="integer", format="int64", example=1),
 *   @OA\Property(property="bonus_id",               type="integer", format="int64", example=2),
 *   @OA\Property(property="volunteer_recipient_id", type="integer", format="int64", example=15),
 *   @OA\Property(property="claimed_at",             type="string",  format="date-time", example="2025-04-23T20:47:55.000000Z")
 * )
 */
class BonusClaim extends Model
{
    protected $fillable = [
        'bonus_id',
        'volunteer_recipient_id',
        'claimed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

    public function volunteerRecipient()
    {
        return $this->belongsTo(VolunteerRecipient::class, 'volunteer_recipient_id');
    }
}
