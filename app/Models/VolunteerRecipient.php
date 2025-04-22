<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="VolunteerRecipient",
 *     type="object",
 *     title="VolunteerRecipient",
 *     required={"id","full_name","inn","email","access_level","confirmation_code"},
 *     @OA\Property(property="id",                type="integer", format="int64", example=15),
 *     @OA\Property(property="full_name",         type="string",                example="Иванов И. И."),
 *     @OA\Property(property="inn",               type="string",                example="772456789012"),
 *     @OA\Property(property="email",             type="string",                example="volunteer@example.com"),
 *     @OA\Property(property="access_level",      type="string",                example="max"),
 *     @OA\Property(property="confirmation_code", type="string",                example="12345")
 * )
 */
class VolunteerRecipient extends Model
{
    protected $table = 'volunteer_recipients';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'full_name',
        'inn',
        'phone',
        'email',
        'birth_date',
        'achievements',
        'access_level',
        'user_id',
    ];

    protected $appends = ['confirmation_code'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function claims()
    {
        return $this->hasMany(BonusClaim::class, 'volunteer_recipient_id');
    }

    public function getConfirmationCodeAttribute(): ?string
    {
        return $this->user ? $this->user->confirmation_code : null;
    }
}
