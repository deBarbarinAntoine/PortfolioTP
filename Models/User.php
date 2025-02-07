<?php

namespace App\Models;

use DateMalformedStringException;
use DateTime;
use Exception;

/**
 * Represents a user in the system with their username, email, avatar, password hash, ID, interests,
 * and timestamp fields for creation and updates.
 */
class User implements ICrud
{
    /**
     * @var int The unique identifier of the user.
     */
    private int $id;

    /**
     * @var array An array of Skill objects representing the user's skills.
     */
    private array $skills;

    /**
     * @var UserRole The role of the user (e.g., 'admin', 'user').
     */
    private UserRole $role;

    /**
     * @var string The username of the user.
     */
    private string $username;

    /**
     * @var string The email address of the user.
     */
    private string $email;

    /**
     * @var string The hashed password of the user.
     */
    private string $password_hash;

    /**
     * @var string The URL or path to the user's avatar.
     */
    private string $avatar;

    /**
     * @var DateTime The timestamp when the user was created.
     */
    private DateTime $created_at;

    /**
     * @var DateTime The timestamp when the user was last updated.
     */
    private DateTime $updated_at;

    /**
     * Initializes the User object with the provided details.
     *
     * @param int $id The unique identifier of the user.
     * @param string $username The username of the user.
     * @param string $email The email address of the user.
     * @param string $avatar The avatar of the user.
     * @param DateTime|null $created_at The timestamp when the user was created (optional).
     * @param DateTime|null $updated_at The timestamp when the user was last updated (optional).
     * @param array $skills An optional array of Skill objects representing the user's skills (default empty).
     * @param UserRole $role The role of the user (default `UserRole::USER`)
     */
    private function __construct(
        int $id,
        string $username,
        string $email,
        string $avatar,
        ?DateTime $created_at = null,
        ?DateTime $updated_at = null,
        array $skills = [],
        UserRole $role = UserRole::USER)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->avatar = $avatar;
        $this->password_hash = '';
        $this->created_at = $created_at ?? new DateTime();
        $this->skills = $skills;
        $this->role = $role;
        $this->updated_at = $updated_at ?? new DateTime();
    }

    public static function findUserIdFromMail(mixed $TokenMail): int
    {
        $user_crud = new Crud('user');
        $result = $user_crud->findBy(['email' => $TokenMail], 'id');
        return $result ? $result['id'] : -1;
    }


    /**
     * Gets the ID of the user.
     *
     * @return int The unique identifier of the user.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the user.
     *
     * @param int $id The new ID to set.
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the creation timestamp of the user.
     *
     * @return DateTime The creation date of the user.
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * Sets the creation timestamp of the user.
     *
     * @param DateTime $created_at The new creation timestamp.
     * @return void
     */
    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * Gets the last updated timestamp of the user.
     *
     * @return DateTime The timestamp when the user was last updated.
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * Sets the last updated timestamp of the user.
     *
     * @param DateTime $updated_at The new last updated timestamp.
     * @return void
     */
    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Gets the username of the user.
     *
     * @return string The username of the user.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Sets the username of the user.
     *
     * @param string $username The new username to set.
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Gets the email address of the user.
     *
     * @return string The email address of the user.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Gets the hashed password of the user.
     *
     * @return string The hashed password of the user.
     */
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    /**
     * Gets the skills of the user.
     *
     * @return array An array of Skill objects.
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * Sets the skills of the user.
     *
     * @param array $skills An array of Skill objects to set.
     * @return void
     */
    public function setSkills(array $skills): void
    {
        $this->skills = $skills;
    }

    /**
     * Gets the role of the user.
     *
     * @return UserRole The role of the user.
     */
    public function getRole(): UserRole
    {
        return $this->role;
    }

    /**
     * Gets the role of the user as a string.
     *
     * @return string The role of the user.
     */
    public function getRoleStr(): string
    {
        return $this->role->value;
    }

    /**
     * Sets the role of the user.
     *
     * @param UserRole $role The new role to set.
     * @return void
     */
    public function setRole(UserRole $role): void
    {
        $this->role = $role;
    }

    /**
     * Sets the hashed password of the user.
     *
     * @param string $password_hash The hashed password to set for the user.
     * @return void
     */
    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    /**
     * Sets the email address of the user.
     *
     * @param string $email The new email to set.
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Gets the avatar of the user.
     *
     * @return string The avatar URL or path of the user.
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Sets the avatar of the user.
     *
     * @param string $avatar The new avatar URL or path to set.
     * @return void
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * Creates a new User object with a generated avatar, hashed password, and default values
     * and push it to the database.
     *
     * @param string $username The username of the user.
     * @param string $email The email address of the user.
     * @param string $password The plain text password of the user.
     * @return User|null A new User object if it was successfully created and null otherwise.
     */
    public static function new(string $username, string $email, string $password): ?User
    {
        $avatar = 'https://ui-avatars.com/api/?name=' . $username . '&background=random&size=256&rounded=true';
        $user = new self(
            -1,
            $username,
            $email,
            $avatar
        );
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $user->setPasswordHash(base64_encode($hashedPassword));

        if ($user->create() > 0) {
            return $user;
        }

        return null;
    }

    /**
     * Inserts a new user record into the database.
     *
     * @return int The newly created id if the user was created successfully, -1 otherwise.
     */
    public function create(): int
    {
        $user_crud = new Crud('users');

        return $user_crud->create([
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'password' => $this->password_hash,
            'role' => $this->role->value,
        ]);
    }

    /**
     * Updates the user's details in the database.
     *
     * @return int The number of rows affected by the update if successful, -1 if an error occurs.
     */
    public function update(): int
    {
        $user_crud = new Crud('users');

        return $user_crud->update(
            [
                'username' => $this->username,
                'email' => $this->email,
                'avatar' => $this->avatar,
                'password_hash' => $this->password_hash,
                'role' => $this->role->value,
            ],
            [ 'id' => $this->id ]
        );
    }

    /**
     * Deletes a user record from the database by ID.
     *
     * @param int $id The unique identifier of the user to delete.
     * @return int The number of rows affected by the deletion if successful, -1 if an error occurs.
     */
    public static function delete(int $id): int
    {
        $user_crud = new Crud('users');

        $rowCount = $user_crud->delete([ 'id' => $id ]);

        return $rowCount === 1;
    }

    /**
     * Retrieves a user from the database along with their related interests.
     *
     * @param int $id The unique identifier of the user.
     * @return User|null The User object if found, or null if not found.
     * @throws DateMalformedStringException
     */
    public static function get(int $id): ?User
    {
        // Create a CRUD instance for the 'users' table (aliased as 'u') to prepare for querying.
        $user_crud = new Crud('users u');

        // Query the database to fetch user information with related interests and skills.
        // Joins 'user_skills' and 'skills' tables to include user's skill details if available.
        $result = $user_crud->findAllBy(
            conditions: ['id' => $id], // Search for the user based on their unique ID.
            columns: 'u.id AS u_id, u.username AS u_username, u.email AS u_email, u.avatar AS u_avatar, u.role AS u_role, u.created_at AS u_created_at, u.updated_at AS u_updated_at, us.id AS us_id, us.skill_id AS us_skill_id, us.level AS us_level, us.created_at AS us_created_at, us.updated_at AS us_updated_at, s.name AS s_name, s.description AS s_description',
            joins: [
                [
                    'type' => 'left', // Perform LEFT JOIN with 'user_skills' to link user and skills.
                    'table' => 'user_skills us',
                    'on' => 'us.user_id = u.id' // Join condition: match 'user_id' from 'user_skills' with 'id' from 'users'.
                ],
                [
                    'type' => 'left', // Perform another LEFT JOIN to fetch skill details from 'skills' table.
                    'table' => 'skills s',
                    'on' => 's.id = us.skill_id' // Join condition: match 'skill_id' in 'user_skills' with 'id' in 'skills'.
                ]
            ]
        );

        // Debug: Display the fetched result for debugging purposes.
        Logger::log($result, __METHOD__, Level::DEBUG);

        // Convert the raw database result into a User object and return it. Null if no user is found.
        return User::toUser($result);
    }

    /**
     * Authenticates a user by their email and password.
     *
     * This function verifies if the provided email exists in the database and
     * performs password verification using the supplied plain text password.
     * If authentication succeeds, the corresponding User object is fetched
     * and returned. In case of failure (e.g., incorrect credentials or errors during
     * the fetch process), null is returned.
     *
     * @param string $email The user's email address.
     * @param string $password The plain text password for verification.
     * @return User|null The User object if login is successful, or null otherwise.
     */
    public static function login(string $email, string $password): ?User
    {
        $user_crud = new Crud('users');

        $result = $user_crud->findBy([ 'email' => $email ]);

        // If no matching email is found, return null, signaling authentication failure
        if (empty($result)) {

            // Debug: Log when no user matches the provided email
            Logger::log("User '$email' not found", __METHOD__, Level::DEBUG);

            return null;
        }

        $id = $result['id'];
        $hashedPassword = base64_decode($result['password_hash']);

        // Validate the supplied password against the hashed password
        if (password_verify($password, $hashedPassword)) {
            try {
                // Fetch and return the User object on successful password validation
                $user = User::get($id);

                // Debug: Print user details
                Logger::log($user->__toString(), __METHOD__, Level::DEBUG);

                return $user;
            } catch (Exception $e) {

                // LOGGING
                Logger::log($e->getMessage(), __METHOD__);
            }
        }

        // If password verification fails, authentication is unsuccessful
        return null;
    }

    /**
     * Retrieves all users from the database along with their related interests.
     *
     * @return array An array of User objects representing all users in the database.
     * @throws DateMalformedStringException
     */
    public static function getAll(): array
    {
        // Initialize the CRUD instance for the 'users' table.
        $user_crud = new Crud('users u');

        // Execute a query to fetch all user records, joining with related 'user_skills' and 'skills' tables.
        $results = $user_crud->findAllBy(
            columns: 'u.id AS u_id, u.username AS u_username, u.email AS u_email, u.avatar AS u_avatar, u.role AS u_role, u.created_at AS u_created_at, u.updated_at AS u_updated_at, us.id AS us_id, us.skill_id AS us_skill_id, us.level AS us_level, us.created_at AS us_created_at, us.updated_at AS us_updated_at, s.name AS s_name, s.description AS s_description',
            joins: [
                [
                    'type' => 'left', // Perform a LEFT JOIN to include user skills, even if no matching skills exist.
                    'table' => 'user_skills us',
                    'on' => 'us.user_id = u.id'
                ],
                [
                    'type' => 'left', // Perform a LEFT JOIN to include skill details, even if no matching skills exist.
                    'table' => 'skills s',
                    'on' => 's.id = us.skill_id'
                ]
            ]
        );

        // Convert the raw database results into an array of User objects and return it.
        return User::toUserArray($results);
    }

    /**
     * Retrieves the last 5 users created within the last 24 hours.
     *
     * This method queries the database for the most recent 5 users and
     * converts the result set into an array of User objects.
     *
     * @return array An array of User objects, or an empty array if none are found.
     * @throws DateMalformedStringException
     */
    public static function get5LastUsers(): array
    {
        $user_crud = new Crud('users');
        // Set the condition for the query
        $conditions = [
            'created_at' => 'NOW() - INTERVAL 24 HOUR'
        ];

        // Call the findAllBy method with the necessary parameters
        $results = $user_crud->findAllBy($conditions, "*", 'created_at', false, 5);

        return User::toUserArray($results);
    }

    /**
     * Retrieves the total number of users in the database.
     *
     * This method queries the database to count the total number
     * of user records available.
     *
     * @return mixed The total number of users as an integer.
     */
    public static function getCountAll(): mixed
    {
        $count_user_crud = new Crud('users');
        return $count_user_crud->findSingleValueBy();
    }

    /**
     * Retrieves the count of users created within the last 24 hours.
     *
     * This method uses a database query to calculate how many users
     * were created within the past day.
     *
     * @return mixed The number of new users as an integer.
     */
    public static function getCountLastUsers(): mixed
    {
        $count_user_crud = new Crud('users');
        $conditions = [
            'created_at' => 'NOW() - INTERVAL 24 HOUR'
        ];
        return $count_user_crud->findSingleValueBy($conditions);
    }

    /**
     * Converts a database record into a User object.
     *
     * This method transforms a single row of user information from the database
     * into an instance of the User class. If the additional fields for skills are not
     * present in the result, it initializes the object without skills.
     *
     * @param array|null $result The database record representing the user, or null if no data exists.
     *                           Expected format varies based on the presence of skills data.
     * @return User|null A new User object if $result is valid; null otherwise.
     * @throws DateMalformedStringException If either 'created_at' or 'updated_at' fields are invalid.
     */
    private static function toUser(?array $result): ?User
    {
        if (empty($result)) {
            return null;
        }

        // Debug
        Logger::log($result, __METHOD__, Level::DEBUG);

        // If the result is not a multi-row dataset, it means no skills are included.
        if (!isset($result[0]['u_id'])) {
            return new User(
                $result['id'],
                $result['username'],
                $result['email'],
                $result['avatar'] ?? null,
                new DateTime($result['created_at']),
                new DateTime($result['updated_at']),
                [],
                UserRole::from($result['role'])
            );
        }

        // Process the skills.
        $skills = [];
        foreach ($result as $row) {
            if (isset($row['skill_id'])) {  // Ensure the skill data exists
                $skills[] = UserSkill::newUserSkill($row);
            }
        }

        // Create a User object from the first row
        return new User(
            $result[0]['u_id'],
            $result[0]['u_username'],
            $result[0]['u_email'],
            $result[0]['u_avatar'] ?? null,
            new DateTime($result[0]['u_created_at']),
            new DateTime($result[0]['u_updated_at']),
            $skills,
            UserRole::from($result[0]['u_role'])
        );
    }

    /**
     * Converts an array of database records into an array of User objects.
     *
     * This helper method takes multiple rows of user data fetched from
     * the database and transforms them into User class instances.
     *
     * @param array $results The database records for users.
     * @return array An array of User objects, or an empty array if no records are provided.
     * @throws DateMalformedStringException
     */
    private static function toUserArray(array $results): array
    {
        // If no records are found, return an empty array immediately.
        if (empty($results)) {
            return [];
        }
    
        $users = [];

        // Check if the results contain a direct list of 'id' keys (i.e., one user per row).
        if (isset($results[0]['id'])) {

            // Iterate through each record in the results.
            foreach ($results as $row) {

                // Convert each row into a User object.
                $user = User::toUser($row);
                if (!empty($user)) {

                    // Add the User object to the resulting array if it is valid.
                    $users[] = $user;
                }
            }
        } else {

            // If results don't directly contain 'id', group rows by user ID.
            foreach ($results as $row) {

                // Group rows under their respective user ID.
                $users[$row['u.id']][] = $row;
            }
            $userList = [];

            // Process each group of rows to create User objects.
            foreach ($users as $user) {

                // Convert each group of rows corresponding to a user to a User object.
                $userList[] = User::toUser($user);
            }
            $users = $userList;
        }

        // Return the fully-processed array of User objects.
        return $users;
    }

    /**
     * Retrieves a list of users based on a search query and offset for pagination.
     *
     * This method interacts with the database to fetch user records
     * that match the given search criteria and are limited to a specific page size.
     * The result set is then converted into an array of `User` objects.
     *
     * @param string $search The search query to filter users by name, email, etc.
     * @param int $offset The offset for paginated results.
     * @return array An array of `User` objects matching the search criteria.
     * @throws DateMalformedStringException
     */
    public static function getAllUsers(string $search, int $offset): array
    {
        $user_crud = new Crud('users');
        $results = $user_crud->search($search, 10, $offset);
        return User::toUserArray($results);
    }

    /**
     * Returns a string representation of the User object.
     *
     * This method provides information about the user's ID, username, email, role, and creation/update timestamp.
     *
     * @return string A string containing user details.
     */
    public function __toString(): string
    {
        return sprintf(
            "User { ID: '%d', Username: '%s', Email: '%s', Role: '%s', Created At: '%s', Updated At: '%s' }",
            $this->id,
            $this->username,
            $this->email,
            $this->role->value,
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s')
        );
    }

    public function validatePassword($password): true|string
    {
        // Minimum length 8, must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter.";
        }

        if (!preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter.";
        }

        if (!preg_match('/\d/', $password)) {
            return "Password must contain at least one number.";
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return "Password must contain at least one special character.";
        }

        // Check for common passwords
        $commonPasswords = ['123456', 'password', 'qwerty', 'abc123', 'letmein'];
        if (in_array($password, $commonPasswords)) {
            return "This password is too common.";
        }

        return true; // Valid password
    }

    public static function doesEmailExist(mixed $userEmail)
    {
        $user_crud = new Crud('users');
        return $user_crud->findSingleValueBy(['email' => $userEmail]);
    }

}