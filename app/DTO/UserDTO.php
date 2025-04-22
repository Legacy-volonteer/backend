<?php

namespace App\DTO;

use JsonSerializable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserDTO",
 *     type="object",
 *     required={"id","username","email","confirmation_code"},
 *     @OA\Property(property="id",                type="integer", format="int64", example=1),
 *     @OA\Property(property="username",          type="string", example="johndoe"),
 *     @OA\Property(property="email",             type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="confirmation_code", type="string", example="12345")
 * )
 */
class UserDTO implements JsonSerializable
{
    public int $id;
    public string $username;
    public string $email;
    public string $confirmation_code;

    public function __construct(int $id, string $username, string $email, string $confirmation_code)
    {
        $this->id                = $id;
        $this->username          = $username;
        $this->email             = $email;
        $this->confirmation_code = $confirmation_code;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'                => $this->id,
            'username'          => $this->username,
            'email'             => $this->email,
            'confirmation_code' => $this->confirmation_code,
        ];
    }
}
