<?php

namespace models;

use DateMalformedStringException;
use DateTime;

/**
 * Represents a user in the system with their username, email, avatar, password hash, ID, interests,
 * and timestamp fields for creation and updates.
 */
class User
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
     * @var string The role of the user (e.g., 'admin', 'user').
     */
    private string $role;

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
     * @param string $password_hash The hashed password of the user.
     * @param DateTime|null $created_at The timestamp when the user was created (optional).
     * @param DateTime|null $updated_at The timestamp when the user was last updated (optional).
     * @param array $skills An optional array of Skill objects representing the user's skills (default empty).
     * @param string $role The role of the user (default 'user')
     */
    private function __construct(int $id, string $username, string $email, string $avatar, string $password_hash = '', ?DateTime $created_at = null, ?DateTime $updated_at = null, array $skills = [], string $role = 'user')
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->avatar = $avatar;
        $this->password_hash = $password_hash;
        $this->created_at = $created_at ?? new DateTime();
        $this->skills = $skills;
        $this->role = $role;
        $this->updated_at = $updated_at ?? new DateTime();
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
     * @return string The role of the user.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Sets the role of the user.
     *
     * @param string $role The new role to set.
     * @return void
     */
    public function setRole(string $role): void
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
        $user = new User(
            -1,
            $username,
            $email,
            $avatar
        );
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $user->setPasswordHash(base64_encode($hashedPassword));

        if ($user->create()) {
            return $user;
        }

        return null;
    }

    /**
     * Inserts a new user record into the database.
     *
     * @return bool True if the user was created successfully, false otherwise.
     */
    public function create(): bool
    {
        $user_crud = new Crud('users');

        try {
            $id = $user_crud->create([
                'username' => $this->username,
                'email' => $this->email,
                'avatar' => $this->avatar,
                'password' => $this->password_hash,
                'role' => $this->role,
            ]);
            return $id > 0;

        } catch (\Exception $e) {
            // TODO -> implement logging

            // DEBUG
            print($e->getMessage());

            return false;
        }
    }

    /**
     * Updates the user's details in the database.
     *
     * @return bool True if the user was updated successfully, false otherwise.
     */
    public function update(): bool
{
    $user_crud = new Crud('users');

        try {
            $rowCount = $user_crud->update(
                [
                    'username' => $this->username,
                    'email' => $this->email,
                    'avatar' => $this->avatar,
                    'password_hash' => $this->password_hash
                ],
                [ 'id' => $this->id ]
            );
            return $rowCount > 0;

        } catch (\PDOException $e) {
            // TODO -> implement logging

            // DEBUG
            echo $e->getMessage();

            return false;
        }
    }

    /**
     * Deletes a user record from the database by ID.
     *
     * @param int $id The unique identifier of the user to delete.
     * @return bool True if the user was deleted successfully, false otherwise.
     */
    public static function delete(int $id): bool
    {
        $user_crud = new Crud('users');

        try {
            $rowCount = $user_crud->delete([ 'id' => $id ]);
            return $rowCount === 1;

        } catch (\Exception $e) {
            // TODO -> implement logging
            // DEBUG
            echo $e->getMessage();
            return false;
        }
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
        $user_crud = new Crud('users u');

        $result = $user_crud->findAllBy(
            conditions: [ 'id'=> $id ],
            columns: 'u.id, u.username, u.email, u.avatar, u.role, u.created_at, u.updated_at, us.id, us.skill_id, us.level, us.created_at, us.updated_at, s.name, s.description',
            joins: [
                [
                    'type' => 'left',
                    'table' => 'user_skills us',
                    'on' => 'us.user_id = u.id'
                ],
                [
                    'type' => 'left',
                    'table' => 'skills s',
                    'on' => 's.id = us.skill_id'
                ]
            ]
        );

        // DEBUG
        print_r($result);

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

            // DEBUG: Log or display when no user matches the provided email
            // print('No results found.');

            return null;
        }

        $id = $result['id'];
        $hashedPassword = base64_decode($result['password_hash']);

        // Validate the supplied password against the hashed password
        if (password_verify($password, $hashedPassword)) {
            try {
                // Fetch and return the User object on successful password validation
                $user = User::get($id);

                // DEBUG: Print user details (for development/testing purposes)
                print_r($user);
                return $user;
            } catch (\Exception $e) {

                // Handle unexpected errors during the User fetch process, e.g., database issues
                // TODO -> implement logging for the caught exception

                // DEBUG: Display the error message
                print($e->getMessage());
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
        $user_crud = new Crud('users');
        $results = $user_crud->findAllBy(
            columns: 'u.id, u.username, u.email, u.avatar, u.role, u.created_at, u.updated_at, us.id, us.skill_id, us.level, us.created_at, us.updated_at, s.name, s.description',
            joins: [
                [
                    'type' => 'left',
                    'table' => 'user_skills us',
                    'on' => 'us.user_id = u.id'
                ],
                [
                    'type' => 'left',
                    'table' => 'skills s',
                    'on' => 's.id = us.skill_id'
                ]
            ]
        );

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
     * This helper method takes a single row of user data fetched from
     * the database and transforms it into an instance of the User class.
     *
     * @param array|null $result The database record representing the user, or null.
     * @return User|null A User object if the result is valid, or null otherwise.
     * @throws DateMalformedStringException
     */
    private static function toUser(?array $result): User|null
    {
        if (empty($result)) {
            return null;
        }

        if (!isset($result['u.id'])) {
            $userId = $result['id'];
            return new User(
                $userId,
                $result['username'],
                $result['email'],
                $result['avatar'] ?? null,
                '',
                new DateTime($result['created_at']),
                new DateTime($result['updated_at']),
                [],
                $result['role'],
            );
        }

        $skills = [];
        foreach ($result as $row) {
            $skills[] = Skill::newUserSkill($row);
        }

        return new User(
            $result[0]['u.id'],
            $result[0]['u.username'],
            $result[0]['u.email'],
            $result[0]['u.avatar'] ?? null,
            '',
            new DateTime($result[0]['u.created_at']),
            new DateTime($result[0]['u.updated_at']),
            $skills,
            $result[0]['u.role'],
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
        if (empty($results)) {
            return [];
        }

        $users = [];

        foreach ($results as $row) {
            $user = User::toUser($row);

            if (!empty($user)) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function getAllUsers(string $search, int $offset): array
    {
        $user_crud = new Crud('users');
        $results = $user_crud -> search($search,10, $offset);
        return User::toUserArray($results);
    }
}