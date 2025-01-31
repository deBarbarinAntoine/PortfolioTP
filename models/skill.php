<?php

namespace models;

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
     * Constructs a Skill object with the specified ID, name, and description.
     *
     * @param int $id The unique identifier for the skill. A value of -1 indicates an unsaved skill.
     * @param string $name The name of the skill.
     * @param string $description A detailed description of the skill.
     */
    private function __construct(int $id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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
     * Retrieves a skill by its name from the database.
     *
     * @param string $name The name of the skill to retrieve.
     * @return Skill|null The Skill object if found, or null if not found or an error occurs.
     */
    public static function get(string $name): Skill|null
    {
        $skill_crud = new Crud('skills');

        try {
            $result = $skill_crud->findBy(['name' => $name]);
            $skill = new Skill(
                $result['id'],
                $result['name'],
                $result['description'],
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
}