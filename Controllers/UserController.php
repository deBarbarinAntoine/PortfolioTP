<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{
    public function getUserNumber(): ?array
    {
        return User::getCountAll();
    }
}