<?php

namespace App\Models;

/**
 * The Guard class is responsible for managing access control
 * and authorization throughout the application. It determines
 * whether the current session or user meets the necessary
 * permissions to access specific parts of the application.
 */
class Guard
{
    /**
     * The type of guard that defines the authorization rules.
     *
     * @var GuardType
     */
    private GuardType $type;

    /**
     * Private constructor to initialize a Guard with a specific type.
     *
     * @param GuardType $type The type of guard to be applied.
     */
    private function __construct(GuardType $type)
    {
        $this->type = $type;
    }

    /**
     * Applies the guard logic based on the provided type.
     * Ensures users have the required session and privilege levels
     * depending on the guard type.
     *
     * @param GuardType $type The type of guard to apply.
     * @return void
     */
    public static function use(GuardType $type): void
    {
        // Create a new Guard instance with the specified type
        $guard = new self($type);

        // Check if the guard type is VISITOR; redirect to / if authenticated
        if ($guard->type === GuardType::VISITOR) {
            if ($guard->checkSession()) {

                // Debug
                Logger::log("User already logged in", __METHOD__, Level::DEBUG);

                $error_message = "Already logged in";
                header("location: /?error_message=". urlencode($error_message));
                exit;
            }
            return;
        }

        // Check if the guard type is PUBLIC; no restrictions
        if ($guard->type === GuardType::PUBLIC) {

            // Debug
            Logger::log("Public resource", __METHOD__, Level::DEBUG);

            return;
        }

        // Redirect to login if the user session is not established
        if (!$guard->checkSession()) {

            // Debug
            Logger::log("Unauthenticated user", __METHOD__, Level::DEBUG);

            $error_message = "Please Log In First";
            header('Location: /login?error_message=' . urlencode($error_message));
            exit;
        }

        // Allow access for regular users without further checks
        if ($guard->type === GuardType::USER) {

            // Debug
            Logger::log("Authenticated user", __METHOD__, Level::DEBUG);

            return;
        }

        // Redirect with an error message if the user lacks ADMIN privileges
        if (!$guard->checkPrivilege()) {

            // Debug
            Logger::log("Unauthorized user", __METHOD__, Level::DEBUG);

            header("Location: /?error_message=Unauthorized");
            exit;
        }

        // Debug
        Logger::log("Authenticated admin", __METHOD__, Level::DEBUG);
    }

    /**
     * Verifies if the user session is active.
     *
     * @return bool True if the session variable 'user_id' exists, false otherwise.
     */
    private function checkSession(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Checks if the user has administrative privileges.
     *
     * @return bool True if the session user role equals ADMIN, false otherwise.
     */
    private function checkPrivilege(): bool
    {
        return $_SESSION['user_role'] === UserRole::ADMIN->value;
    }
}