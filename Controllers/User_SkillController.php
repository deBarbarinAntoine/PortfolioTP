<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\UserSkill;
use App\Models\UserSkillLevel;
use DateMalformedStringException;
use PDOException;

class User_SkillController
{
    public function getUserSkills(mixed $user_id): array
    {
        $user = [];
        try {
            $user = User::get($user_id);
        } catch (PDOException|DateMalformedStringException $e) {
            $errorMessage = $e->getMessage();
        }
        if ($user != []) {
            return $user->getSkills();
        }
        return [];
    }

    public function deleteSkillFromUser(int $UserSkillId): string|bool
    {
        $userSkill = [];
        $errorMessage = "";
        try {
            $userSkill = UserSkill::get($UserSkillId);
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
        }
        if ($userSkill != []) {
            return $userSkill->delete($UserSkillId);
        }
        return $errorMessage;
    }

    public function updateUserSkillLevel(mixed $userSkillId, array|int|string $skillId, string $newLevel): string|bool
    {
        $userSkill = [];
        $errorMessage = "";
        try {
            $userSkill = UserSkill::get($userSkillId);
        }catch (PDOException $e) {
            $errorMessage = $e->getMessage();
        }
        if ($userSkill != []) {
            // Check if $newLevel is a valid value from the UserSkillLevel enum
            $level = UserSkillLevel::tryFrom($newLevel);

            if ($level) {
                // Set the level if it's a valid UserSkillLevel enum instance
                $userSkill->setLevel($level);
                $userSkill->update();
            }
        }

        return $errorMessage;
    }

    public function addSkillToUser(mixed $user_id, int $newSkillId, string $newSkillLevel): string|bool
    {
        $errorMessage = "";
        $userSkill = false;
        try {
            $userSkill = UserSkill::new($user_id, $newSkillId, $newSkillLevel);
        }catch (PDOException|DateMalformedStringException $e) {
            $errorMessage = $e->getMessage();
        }
        if ($userSkill === true) {
            return true;
        }

        return $errorMessage;
    }
}