<?php

namespace App\Controllers;

use App\Models\ResetPassword;

class PasswordResetController
{

    public function generateResetToken(string $user_email): ?string
    {
       return ResetPassword::generate($user_email);
    }

    public function isTokenValid(mixed $reset_password_token): bool
    {
        return ResetPassword::isResetPasswordTokenExistAndValid($reset_password_token);
    }

    public function findTokenMail(string $reset_password_token): string
    {
        return ResetPassword::findTokenMail($reset_password_token);
    }
}