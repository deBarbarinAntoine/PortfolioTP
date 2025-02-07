<?php

namespace App\Models;

use DateMalformedStringException;
use DateTime;
use Exception;
use Random\RandomException;

class ResetPassword
{

    public static function generate(string $user_email): ?string
    {
        $resetPassword = new Crud('password_resets'); // Ensure table name matches your schema

        try {
            $token = bin2hex(random_bytes(32)); // Secure random token
        } catch (Exception $e) { // Catch broader Exception to handle potential issues
            Logger::log("Token Generation Error: " . $e->getMessage(), __METHOD__);
            return null; // Return null instead of an error message
        }

        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        $insertId = $resetPassword->create([
            'user_id' => $user_email,
            'token' => password_hash($token, PASSWORD_DEFAULT), // Store securely
            'expires_at' => $expiresAt
        ]);

        return ($insertId !== -1) ? $token : null; // Return token if successful, else return null
    }

    public static function isResetPasswordTokenExistAndValid(mixed $reset_password_token): bool
    {
        // Assuming the Crud class has the necessary functionality to handle the table and conditions
        $restorePassword = new Crud('password_resets');

        // Fetch the password reset record where the token matches
        $result = $restorePassword->findBy([
            'token' => $reset_password_token
        ]);

        // If no result found, return false
        if (!$result) {
            return false;
        }

        // Check if the token has expired
        $currentTime = new DateTime(); // Get the current time
        try {
            $expiresAt = new DateTime($result['expires_at']); // Get the expiration time from the record
        } catch (DateMalformedStringException $e) {
            Logger::log("Token Expired Error: " . $e->getMessage(), __METHOD__);
            return false;
        }

        // If the token is expired, return false
        if ($currentTime > $expiresAt) {
            return false;
        }

        // If no issues, return true (token is valid)
        return true;
    }

    public static function findTokenMail(string $reset_password_token) : string
    {
        $resetPassword = new Crud('password_resets');

        // Define the conditions, specifying the reset password token
        $conditions = ['token' => $reset_password_token];

        // Call the findBy method, requesting only the 'email' column
        $result = $resetPassword->findBy($conditions, 'email');

        // Check if a result was found and return the email
        return $result ? $result['email'] : "";
    }
}