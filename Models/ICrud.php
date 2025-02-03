<?php

namespace App\Models;

interface ICrud
{
    /**
     * Create a new record in the database.
     *
     * @return int The ID of the newly created record.
     */
    function create(): int;

    /**
     * Update an existing record in the database.
     *
     * @return int The number of rows affected by the update.
     */
    function update(): int;

    /**
     * Delete a specific record from the database by its ID.
     *
     * @param int $id The ID of the record to delete.
     * @return int The number of rows affected by the deletion.
     */
    static function delete(int $id): int;

    /**
     * Retrieve a specific record from the database by its ID.
     *
     * @param int $id The ID of the record to retrieve.
     * @return mixed The record data, or null if not found.
     */
    static function get(int $id): mixed;

    /**
     * Retrieve all records from the database.
     *
     * @return array An array of all records.
     */
    static function getAll(): array;
}