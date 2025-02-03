<?php

namespace App\Controllers;

use App\Models\User;
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
}