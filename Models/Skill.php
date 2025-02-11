<?php

namespace App\Models;

use DateTime;
use Exception;

/**
 * The Skill class represents a specific skill with attributes like name and description.
 * It provides methods to perform various CRUD operations such as create, read, update, and delete
 * skills from persistent storage using a basic CRUD utility class.
 */
class Skill implements ICrud
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
     * @param DateTime|null $created_at The timestamp of when the skill was created.
     * @param DateTime|null $updated_at The timestamp of when the skill was last updated.
     */
    private function __construct(int $id, string $name, string $description, ?DateTime $created_at, ?DateTime $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
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
        return new self(
            -1,
            $name,
            $description,
            new DateTime(),
            new DateTime(),
        );
    }

    /**
     * Creates a new skill in the database.
     *
     * @return int The ID of the newly created skill, or -1 in case of a failure.
     */
    public function create(): int
    {
        $skill_crud = new Crud('skills');

        return $skill_crud->create(
            [
                'name' => $this->name,
                'description' => $this->description,
            ]
        );
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

        return $skill_crud->exists(['name' => $name]);
    }

    /**
     * Updates the current skill's details in the database.
     *
     * @return int The number of rows affected by the update, or -1 if the update fails.
     */
    public function update(): int
    {
        $skill_crud = new Crud('skills');

        return $skill_crud->updateString(
            [
                'name' => $this->name,
                'description' => $this->description
            ],
            ['id' => $this->id],
        );
    }

    /**
     * Deletes a skill with the given ID from the database.
     *
     * @param int $id The ID of the skill to delete.
     * @return int The number of rows affected by the deletion, or -1 if the deletion fails.
     */
    public static function delete(int $id): int
    {
        $skill_crud = new Crud('skills');

        return $skill_crud->delete(
            ['id' => $id],
        );
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
            return new self(
                $result['id'],
                $result['name'],
                $result['description'],
                new DateTime($result['created_at']),
                new DateTime($result['updated_at'])

            );
        } catch (Exception $e) {

            // LOGGING
            Logger::log($e->getMessage(), __METHOD__);

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
                $skills[] = new self(
                    $skill['id'],
                    $skill['name'],
                    $skill['description'],
                    new DateTime($skill['created_at']),
                    new DateTime($skill['updated_at'])
                );
            }
            return $skills;
        } catch (Exception $e) {

            // LOGGING
            Logger::log($e->getMessage(), __METHOD__);

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
        $count_skill_crud = new Crud('skills');
        
        return $count_skill_crud->findSingleValueBy();
    }

    /**
     * Retrieves all available skills from the database and maps them to Skill objects.
     *
     * @return array An array of Skill objects representing all available skills in the database.
     */
    public static function getAllSkills(string $search, int $offset): array
    {
        // Create a new Crud object for 'skills' table
        $skill_crud = new Crud('skills');

        // Get raw results from the findAllBy method
        $results = $skill_crud->searchSkill($search, 10, $offset);
        // Return the array of Skill objects
        return self::toSkillArray($results);
    }

    /**
     * Converts raw database query results into an array of Skill objects.
     *
     * This method takes an array of query results, processes them, and returns an array of Skill
     * objects. If the input is empty, it will return an empty array. When the results are a flat
     * list, each row is converted directly into a Skill object. If the results are grouped by ID,
     * it will handle the grouping and return appropriate Skill objects.
     *
     * @param array $results The raw database results containing skill data.
     *                       Each element may represent a single skill or a group of rows identified
     *                       by a skill ID.
     * @return array An array of Skill objects converted from the provided results.
     * @throws \DateMalformedStringException
     */
    private static function toSkillArray(array $results): array
    {
        // If no records are found, return an empty array immediately.
        if (empty($results)) {
            return [];
        }

        $skills = [];

        // Check if the results contain a direct list of 'id' keys (i.e., one skill per row).
        if (isset($results[0]['id'])) {

            // Iterate through each record in the results.
            foreach ($results as $row) {
                // Convert each row into a skill object.
                $skill = self::toSkill($row);

                if (!empty($skill)) {

                    // Add the skill object to the resulting array if it is valid.
                    $skills[] = $skill;
                }
            }
        } else {

            // If results don't directly contain 'id', group rows by skill ID.
            foreach ($results as $row) {

                // Group rows under their respective skill ID.
                $skills[$row['u.id']][] = $row;
            }

            $skillList = [];

            // Process each group of rows to create skill objects.
            foreach ($skills as $skill) {

                // Convert each group of rows corresponding to a skill to a skill object.
                $skillList[] = self::toSkill($skill);
            }

            $skills = $skillList;
        }

        // Return the fully-processed array of skill objects.
        return $skills;
    }

    /**
     * @throws \DateMalformedStringException
     */
    private static function toSkill(array $skill): skill
    {
        // Return a new skill object
        return new self(
            $skill['id'],           // skill id
            $skill['name'],           // skill name
            $skill['description'] ?? null,  // skill description
            new DateTime($skill['created_at']),
            new DateTime($skill['updated_at'])
        );
    }

    /**
     * Creates a new Skill object based on skill data from the database.
     *
     * @param array $result An associative array containing user skill data (e.g., skill ID, name).
     * @return Skill The newly created Skill instance populated with the provided data.
     */
    public static function newSkill(array $result): Skill
    {
        return new self(
            $result['us.skill_id'],
            $result['s.name'],
            $result['s.description'],
            $result['us.created_at'],
            $result['us.updated_at']
        );
    }
}