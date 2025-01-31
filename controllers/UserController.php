<?php

namespace controllers;

use models\User;
require_once __DIR__ . '/../models/user.php';
class UserController
{
    public function getUserNumber(): ?array
    {
        return user::getCountAll();
    }
}