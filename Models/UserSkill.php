<?php

namespace App\Models;

use DateMalformedStringException;
use DateTime;
use Exception;
use InvalidArgumentException;

class UserSkill implements ICrud
{
    /**
     * @var int The unique identifier of the user.
     */
    private int $id;

    /**
     * @var int The user ID representing the user this skill is associated with.
     */
    private int $user_id;

    /**
     * @var int The skill ID of the skill.
     */
    private int $skill_id;

    /**
     * @var UserSkillLevel The level of the user in that skill.
     */
    private UserSkillLevel $level;

    /**
     * @var DateTime The timestamp when the user skill was created.
     */
    private DateTime $created_at;

    /**
     * @var DateTime The timestamp when the user skill was last updated.
     */
    private DateTime $updated_at;

    /**
     * Initializes the UserSkill object with the provided details.
     *
     * @param int $id The unique identifier for the UserSkill object.
     * @param int $user_id The ID of the user.
     * @param int $skill_id The ID of the skill.
     * @param UserSkillLevel $level The skill level.
     * @param DateTime|null $created_at Optional timestamp for when the skill was created.
     * @param DateTime|null $updated_at Optional timestamp for the last update.
     */
    private function __construct(
        int $id,
        int $user_id,
        int $skill_id,
        UserSkillLevel $level,
        ?DateTime $created_at = null,
        ?DateTime $updated_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->skill_id = $skill_id;
        $this->level = $level;
        $this->created_at = $created_at ?? new DateTime();
        $this->updated_at = $updated_at ?? new DateTime();
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function newUserSkill(array $row): UserSkill
    {
        return new UserSkill(
            $row['us_id'],
            $row['us_user_id'],
            $row['us_skill_id'],
            UserSkillLevel::from($row['us_level']),
            new DateTime($row['us_created_at']),
            new DateTime($row['us_updated_at'])
        );
    }

    public static function new(int $user_id, int $newSkillId, string $newSkillLevel): bool
    {
        $level = UserSkillLevel::from($newSkillLevel);
        $userSkill = new self(
            -1,
            $user_id,
            $newSkillId,
            $level
        );

        if ($userSkill->create() > 0) {
            return true;
        }
        return false;
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
     * Gets the user ID associated with the user skill.
     *
     * @return int The user ID.
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Sets the user ID associated with the user skill.
     *
     * @param int $user_id The new user ID to set.
     * @return void
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * Gets the skill ID associated with the user skill.
     *
     * @return int The skill ID.
     */
    public function getSkillId(): int
    {
        return $this->skill_id;
    }

    /**
     * Sets the skill ID associated with the user skill.
     *
     * @param int $skill_id The new skill ID to set.
     * @return void
     */
    public function setSkillId(int $skill_id): void
    {
        $this->skill_id = $skill_id;
    }

    /**
     * Gets the level of the user's skill.
     *
     * @return UserSkillLevel The level of the user in that skill.
     */
    public function getLevel(): UserSkillLevel
    {
        return $this->level;
    }

    /**
     * Sets the level of the user's skill.
     *
     * @param UserSkillLevel $level The new level to set.
     * @return void
     */
    public function setLevel(UserSkillLevel $level): void
    {
        $this->level = $level;
    }

    /**
     * Updates the 'updated_at' timestamp to the current time.
     *
     * @return void
     */
    public function updateTimestamp(): void
    {
        $this->updated_at = new DateTime();
    }

    /**
     * Converts the UserSkill object to an array for easier manipulation or storage.
     *
     * @return array The user skill details as an associative array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'skill_id' => $this->skill_id,
            'level' => $this->level->value,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Creates a new instance of the UserSkill object from an array.
     *
     * @param array $data The user skill data as an associative array.
     * @return UserSkill The created UserSkill object.
     * @throws InvalidArgumentException If date parsing fails.
     */
    public static function fromArray(array $data): UserSkill
    {
        try {
            $created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : null;
            $updated_at = isset($data['updated_at']) ? new DateTime($data['updated_at']) : null;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid date format: ' . $e->getMessage());
        }

        // Convert the level value from string to the corresponding enum instance
        $level = UserSkillLevel::from($data['level']);

        return new self(
            $data['id'],
            $data['user_id'],
            $data['skill_id'],
            $level,
            $created_at,
            $updated_at
        );
    }

    function create(): int
    {
        $user_skill_crud = new Crud('user_skills');
        return $user_skill_crud->create([
            'user_id' => $this->user_id,
            'skill_id' => $this->skill_id,
            'level' => $this->level->value,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    function update(): int
    {
        $user_skill_crud = new Crud('user_skills');

        return $user_skill_crud->updateString(
            [
                'user_id' => $this->user_id,
                'skill_id' => $this->skill_id,
                'level' => $this->level->value,
            ],
            [ 'id' => $this->id ]
        );
    }

    static function delete(int $id): int
    {
        $user_skill_crud = new Crud('user_skills');
        return $user_skill_crud->delete(['id' => $id]);
    }

    static function get(int $id): ?UserSkill
    {
        $user_skill_crud = new Crud('user_skills');

        $result = $user_skill_crud->findBy(
            conditions: ['id' => $id],
        );

        return self::toUserSkill($result);
    }

    /**
     * @throws DateMalformedStringException
     */
    /**
     * Converts database result into a UserSkill object.
     *
     * @param array $result The database query result.
     * @return UserSkill|null The UserSkill object or null if empty.
     * @throws InvalidArgumentException If date parsing fails.
     */
    private static function toUserSkill(array $result): ?UserSkill
    {
        if (empty($result)) {
            return null;
        }

        $keys = ['id', 'user_id', 'skill_id', 'level', 'created_at', 'updated_at'];
        $normalized = [];

        foreach ($keys as $key) {
            $normalized[$key] = $result[$key] ?? $result["a.$key"] ?? null;
        }

        try {
            return new UserSkill(
                $normalized['id'],
                $normalized['user_id'],
                $normalized['skill_id'],
                UserSkillLevel::from($normalized['level']),
                !empty($normalized['created_at']) ? new DateTime($normalized['created_at']) : new DateTime(),
                !empty($normalized['updated_at']) ? new DateTime($normalized['updated_at']) : new DateTime()
            );
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid date format: ' . $e->getMessage());
        }
    }


    /**
     * Retrieves all user skills from the database.
     *
     * @return UserSkill[] List of UserSkill objects.
     */
    static function getAll(): array
    {
        $user_skill_crud = new Crud('user_skills');
        $result = $user_skill_crud->findAllBy();

        return array_filter(array_map(fn($row) => self::toUserSkill($row), $result));
    }

    public static function getByUser(mixed $user_id): false|array|null
    {
        $user_skill_crud = new Crud('user_skills');
        return $user_skill_crud->findAllBy(['user_id' => $user_id]);
    }

}