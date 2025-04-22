<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\Repositories\UserRepository;

class UserService
{
    protected UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUserProfile(int $userId): ?UserDTO
    {
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            return null;
        }

        return new UserDTO(
            $user->id,
            $user->username,
            $user->email,
            $user->confirmation_code
        );
    }
}
