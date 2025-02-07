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

    public function hashPassword(mixed $user_id, string $newPasswordHash): string
    {
        try {
            $user = User::get($user_id);
        } catch (DateMalformedStringException $e)
        {
            return "";
        }
        if ($user) {
            return password_hash($newPasswordHash, PASSWORD_ARGON2ID);
        }
        return "";
    }

    public function validatePassword(mixed $user_id, string $Password): bool|string
    {
        try {
            $user = User::get($user_id);
        } catch (DateMalformedStringException $e) {
            return false;
        }

        if ($user) {
            return $user->validatePassword($Password);
        }
        return false;
    }

    public function checkAnyUserHaveEmail(mixed $userEmail): bool
    {
        if(User::doesEmailExist($userEmail) == 1)
        {
            return true;
        } else if (User::doesEmailExist($userEmail) > 1) {
            Logger::log("Multiple users avec same email", __METHOD__, Level::WARNING);
            return true;
        }
        return false;
    }

    public function getUserIdFromMail(string $TokenMail): int
    {
        if ($this->checkAnyUserHaveEmail($TokenMail)) {
            return User::findUserIdFromMail($TokenMail);
        } else {
            return -1;
        }
    }

    /**
     */
    public function checkOldPassword($oldPassword, string $TokenMail, int $user_id) : bool
    {
        try {
            $user = User::get($user_id);
        } catch (DateMalformedStringException $e)
        {
            return false;
        }
        if ($user) {
            $isHisMail = $user->getEmail();
            if ($isHisMail == $TokenMail) {
                $hashedPassword = base64_decode($user->getPasswordHash());
                if (password_verify($oldPassword, $hashedPassword)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function updateUserPassword(int $user_id, $newPassword): bool
    {
        try {
            $user = User::get($user_id);
        } catch (DateMalformedStringException $e)
        {
            return false;
        }
        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
            $user->setPasswordHash($hashedPassword);
            $affectedRow = $user->update();
            if ($affectedRow > 0) {
                return true;
            }
            return false;
        }
        return false;
    }
}