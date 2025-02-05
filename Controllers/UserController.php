<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserRole;
use DateMalformedStringException;

class UserController
{
    public function getUserNumber(): ?array
    {
        return User::getCountAll();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function updateRole(int $user_id, mixed $new_role): bool
    {
        $user = User::get($user_id);
        $user->setRole($new_role);
        return $user->update();
    }

    public function login(string $email, string $password): ?User
    {
        return User::login($email, $password);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getUserId(User $user): ?User
    {
        return User::get($user->getId());
    }

    public function getUserRole(User $user): UserRole
    {
        return $user->getRole();
    }
}