<?php

namespace App\Controllers;

use App\Models\Level;
use App\Models\Logger;
use App\Models\User;
use App\Models\UserRole;
use DateMalformedStringException;
use DateTime;
use Exception;

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

    public function getUserId(User $user): int
    {
        return $user->getId();
    }

    public function getUserRole(User $user): UserRole
    {
        return $user->getRole();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getUser(mixed $user_id): ?User
    {
        return User::get($user_id);
    }

    public function getUserName(User $user): string
    {
        return $user->getUsername();
    }

    public function getUserAvatar(User $user): string
    {
        return $user->getAvatar();
    }

    public function getUserEmail(User $user): string
    {
        return $user->getEmail();
    }

    public function getUserCreationDate(User $user): DateTime
    {
        return $user->getCreatedAt();
    }

    public function getUserModificationDate(User $user): DateTime
    {
        return $user->getUpdatedAt();
    }

    public function getUserSkills(User $user): array
    {
        return $user->getSkills();
    }

    public function updateUser(mixed $user_id, mixed $updatedName, mixed $updatedEmail, string $avatarPath): bool
    {
        try {
            // Attempt to retrieve the user from the database
            $user = User::get($user_id);

            // If no user was found, return false
            if (!$user) {
                return false;
            }

            // Update the user details
            $user->setUsername($updatedName);
            $user->setEmail($updatedEmail);
            $user->setAvatar($avatarPath);

            // Perform the update in the database
            $user->update();

            // If update is successful, return true
            return true;
        } catch (Exception $e) {
            // Log the exception or handle it as necessary
            Logger::log("Error updating user: " . $e->getMessage(), __METHOD__, Level::ERROR);

            // Return false if an error occurred
            return false;
        }
    }

    public function verifyPassword(mixed $user_id, $updatedPassword): bool
    {
        $user = User::get($user_id);
        if ($user) {
            $decodedPassword = base64_decode($user->getPasswordHash());
            if (password_verify($updatedPassword, $decodedPassword)) {
                return true;
            }
        }
        return false;
    }

    public function hashPassword(mixed $user_id, string $newPasswordHash): string
    {
        $user = User::get($user_id);
        if ($user) {
            return password_hash($newPasswordHash, PASSWORD_ARGON2ID);
        }
        return "";
    }

    public function validatePassword(mixed $user_id, string $Password): bool|string
    {
        $user = User::get($user_id);
        if ($user) {
            return $user->validatePassword($Password);
        }
        return false;
    }

}