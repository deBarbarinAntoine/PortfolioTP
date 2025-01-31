<?php

namespace models;

use DateTime;
use PDOException;

/**
 * The Skill class represents a specific skill with attributes like name and description.
 * It provides methods to perform various CRUD operations such as create, read, update, and delete
 * skills from persistent storage using a basic CRUD utility class.
 */
class Skill
{

    /**
     * The unique identifier of the skill.
     */
    private int $id;

    /**
     * The name of the skill.
     */
    private string $name;

    /**
     * A detailed description of the skill.
     */
    private string $description;

    /**
     * The proficiency level of the skill (e.g., beginner, intermediate, expert).
     */
    private string $level;

    /**
     * The timestamp of when the skill was created.
     */
    private DateTime $created_at;

    /**
     * The timestamp of when the skill was last updated.
     */
    private DateTime $updated_at;

    /**
     * Gets the ID of the skill.
     *
     * @return int The ID of the skill.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the skill.
     *
     * @param int $id The ID to set.
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the name of the skill.
     *
     * @return string The name of the skill.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the skill.
     *
     * @param string $name The name to set.
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the description of the skill.
     *
     * @return string The description of the skill.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description of the skill.
     *
     * @param string $description The description to set.
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Gets the proficiency level of the skill.
     *
     * @return string|null The proficiency level of the skill.
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * Sets the proficiency level of the skill.
     *
     * @param string|null $level The level to set.
     * @return void
     */
    public function setLevel(?string $level): void
    {
        $this->level = $level;
    }

    /**
     * Gets the creation timestamp of the skill.
     *
     * @return DateTime|null The creation timestamp.
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * Sets the creation timestamp of the skill.
     *
     * @param DateTime|null $created_at The creation timestamp to set.
     * @return void
     */
    public function setCreatedAt(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * Gets the last updated timestamp of the skill.
     *
     * @return DateTime|null The last updated timestamp.
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    /**
     * Sets the last updated timestamp of the skill.
     *
     * @param DateTime|null $updated_at The last updated timestamp to set.
     * @return void
     */
    public function setUpdatedAt(?DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Constructs a Skill object with the specified attributes.
     *
     * @param int $id The unique identifier for the skill. A value of -1 indicates an unsaved skill.
     * @param string $name The name of the skill.
     * @param string $description A detailed description of the skill.
     * @param string|null $level The proficiency level of the skill (e.g., beginner, intermediate, expert).
     * @param DateTime|null $created_at The timestamp of when the skill was created.
     * @param DateTime|null $updated_at The timestamp of when the skill was last updated.
     */
    private function __construct(int $id, string $name, string $description, ?string $level = null, ?DateTime $created_at = null, ?DateTime $updated_at = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * Creates a new Skill object based on user skill data from the database.
     *
     * @param array $result An associative array containing user skill data (e.g., skill ID, name, and level).
     * @return Skill The newly created Skill instance populated with the provided data.
     */
    public static function newUserSkill(array $result): Skill
    {
        return new Skill(
            $result['us.skill_id'],
            $result['s.name'],
            $result['s.description'],
            $result['us.level'],
            $result['us.created_at'],
            $result['us.updated_at']
        );
    }

    /**
     * Creates a new unsaved Skill object with the specified name and description.
     *
     * @param string $name The name of the skill.
     * @param string $description A detailed description of the skill.
     * @return Skill The newly created Skill instance with an ID of -1.
     */
    public static function new(string $name, string $description): Skill
    {
        return new Skill(
            -1,
            $name,
            $description
        );
    }

    /**
     * Creates a new skill in the database.
     *
     * @param string $name The name of the skill.
     * @param string $description The description of the skill.
     * @return int The ID of the newly created skill, or -1 in case of a failure.
     */
    public function create(string $name, string $description): int
    {
        $skill_crud = new Crud('skills');

        try {
            $id = $skill_crud->create(
                [
                    'name' => $name,
                    'description' => $description,
                ]
            );
            return $id;

        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return -1;
        }
    }

    /**
     * Checks whether a skill with the given name exists in the database.
     *
     * @param string $name The name of the skill to check.
     * @return bool True if the skill exists, false otherwise.
     */
    public static function exists(string $name): bool
    {
        $skill_crud = new Crud('skills');

        try {
            $exists = $skill_crud->exists(['name' => $name]);
            return $exists;
        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Updates the current skill's details in the database.
     *
     * @return int The number of rows affected by the update, or 0 if the update fails.
     */
    public function update(): int
    {
        $skill_crud = new Crud('skills');

        try {
            $rowCount = $skill_crud->update(
                [
                    'name' => $this->name,
                    'description' => $this->description
                ],
                ['id' => $this->id],
            );
            return $rowCount;
        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Deletes a skill with the given ID from the database.
     *
     * @param int $id The ID of the skill to delete.
     * @return int The number of rows affected by the deletion, or 0 if the deletion fails.
     */
    public static function delete(int $id): int
    {
        $skill_crud = new Crud('skills');

        try {
            $rowCount = $skill_crud->delete(
                ['id' => $id],
            );
            return $rowCount;
        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Retrieves a skill by its id from the database.
     *
     * @param int $id The id of the skill to retrieve.
     * @return Skill|null The Skill object if found, or null if not found or an error occurs.
     */
    public static function get(int $id): Skill|null
    {
        $skill_crud = new Crud('skills');

        try {
            $result = $skill_crud->findBy(['id' => $id]);
            $skill = new Skill(
                $result['id'],
                $result['name'],
                $result['description']
            );
            return $skill;
        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieves all skills from the database.
     *
     * @return array An array of Skill objects representing all entries in the database, or an empty array if an error occurs.
     */
    public static function getAll(): array
    {
        $skill_crud = new Crud('skills');

        try {
            $results = $skill_crud->findAllBy();
            $skills = [];
            foreach ($results as $skill) {
                $skills[] = new Skill(
                    $skill['id'],
                    $skill['name'],
                    $skill['description'],
                );
            }
            return $skills;
        } catch (PDOException $e) {
            // TODO -> implement logging
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Retrieves the total count of all skills in the database.
     *
     * @return mixed The total count of skills as an integer or other appropriate data type.
     */
    public static function getCountAll(): mixed
    {
        $count_user_crud = new Crud('skills');
        return $count_user_crud->findSingleValueBy();
    }

    /**
     * Retrieves all available skills from the database and maps them to Skill objects.
     *
     * @return array An array of Skill objects representing all available skills in the database.
     */
    public static function getAllSkills(): array
    {
        // Create a new Crud object for 'skills' table
        $user_crud = new Crud('skills');

        // Define the conditions (empty array to get all skills)
        $conditions = [];  // You can modify this based on your use case

        // Get raw results from the findAllBy method
        $results = $user_crud->findAllBy();

        // Initialize an empty array to hold Skill objects
        $skills = [];

        // Map the results to Skill objects
        foreach ($results as $row) {
            // Assuming each row has 'id', 'name', and 'description' keys
            $skills[] = new Skill($row['id'], $row['name'], $row['description']);
        }

        // Return the array of Skill objects
        return $skills;
    }
}