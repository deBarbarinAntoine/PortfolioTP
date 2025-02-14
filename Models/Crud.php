<?php

namespace App\Models;

use DateTime;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Crud
 *
 * EN: A simple CRUD (Create, Read, Update, Delete) class to interact with a MySQL database using PDO.
 * 
 * FR: Une classe CRUD simple (Créer, Lire, Mettre à jour, Supprimer) pour interagir avec une base de données MySQL à l'aide de PDO.
 */
class Crud {
    /**
     * @var PDO $pdo
     * EN: PDO instance for database connection.
     * 
     * FR: Instance PDO pour la connexion à la base de données.
     */
    private PDO $pdo;

    /**
     * @var string $table
     * EN: The name of the database table.
     * 
     * FR: Le nom de la table de la base de données.
     */
    protected string $table;

    /**
     * Constructor: Initialize database connection
     *
     * EN: Sets up the PDO connection and initializes the table name.
     * 
     * FR: Configure la connexion PDO et initialise le nom de la table.
     *
     * @param string $table EN: Table name | FR: Nom de la table
     */
    public function __construct(string $table) {
        $this->table = $table;
        $this->pdo = DB::getPDO();
    }

    /**
     * Create: Insert a new record into a table
     *
     * EN: Inserts a new record into the database.
     * 
     * FR: Insère un nouvel enregistrement dans la base de données.
     *
     * @param array $data EN: Associative array of column-value pairs | FR: Tableau associatif de paires colonne-valeur
     * @return int EN: ID value on success, -1 on failure | FR: Valeur de l'ID si réussi, -1 sinon
     */
    public function create(array $data): int {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";

        // Debug
        Logger::log($sql, __METHOD__, Level::DEBUG);
        Logger::log($data, __METHOD__, Level::DEBUG);

        // Convert DateTime objects to strings
        foreach ($data as $key => $value) {
            if (in_array($key, ['created_at', 'updated_at'], true) && $value instanceof DateTime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->bindConditions($stmt, $data);
            $ok = $stmt->execute();

            // Debug
            $message = $ok ? 'Created Successfully' : 'Create Failed';
            Logger::log($message, __METHOD__, Level::DEBUG);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            Logger::log("Create Error: " . $e->getMessage(), __METHOD__);
            return -1;
        }
    }

    /**
     * Find: Fetch records from a table
     *
     * EN: Retrieves records from the database based on conditions.
     * 
     * FR: Récupère des enregistrements de la base de données en fonction des conditions.
     *
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne → valeur)
     * @param string $columns EN: Columns to select, default is "*" | FR: Colonnes à sélectionner, par défaut "*"
     * @param string|null $orderBy EN: Column to order by, optional | FR: Colonne de tri, optionnel
     * @param int|null $limit EN: Limit of rows to fetch, optional | FR: Limite du nombre de lignes à récupérer, optionnel
     * @return array EN: Array of results | FR: Tableau des résultats
     * @example Usage:
     *  $conditions = ['status' => 'active'];
     *  $joins = [
     *      ['type' => 'left', 'table' => 'users', 'on' => 'orders.user_id = users.id']
     *  ];
     *  $orderBy = 'created_at';
     *  $isAscend = true;
     *  $limit = 10;
     *  $results = $yourClassInstance->findAllBy(
     *      $conditions,
     *      "*",
     *      $orderBy,
     *      $isAscend,
     *      $limit,
     *      $joins
     *  );
     *  // $results will contain an array of matching rows or an empty array if no matches found
     *
     */
    public function findAllBy(
        array   $conditions = [],
        string  $columns = "*",
        ?string $orderBy = null,
        ?string $Ascend = null,
        ?int    $limit = null,
        ?int    $offset = null,
        ?array  $joins = null,
        string $symbole = "",
        ?bool $isString = false
    ): array
    {
        $sql = $this->getSql($columns, $joins, $conditions, $orderBy, $Ascend, $symbole, $isString);

        // Handle LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }

        // Debugging output for the query (you can remove this in production)
        Logger::log($sql, __METHOD__, Level::DEBUG);


        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Read Error: " . $e->getMessage(), __METHOD__);

            return []; // Return an empty array in case of error
        }
    }

    /**
     * Find a single record from a table
     *
     * EN: Retrieves the first record that matches the given conditions.
     * 
     * FR: Récupère le premier enregistrement correspondant aux conditions données.
     *
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif des conditions (colonne → valeur)
     * @param string $columns EN: Columns to select, default is "*" | FR: Colonnes à sélectionner, par défaut "*"
     * @param string|null $orderBy EN: Column to order by, optional | FR: Colonne de tri, optionnel
     * @param string|null $Ascend EN: Sort direction ("ASC" or "DESC"), optional | FR: Direction de tri ("ASC" ou "DESC"), optionnel
     * @param array|null $joins EN: Joins to include, optional | FR: Joins à inclure, optionnel
     * @return array|null EN: Associative array of the first record if found, null otherwise | FR: Tableau associatif du premier enregistrement si trouvé, null sinon
     * @example Usage:
     *  $conditions = ['id' => 123, 'status' => 'active'];
     *  $joins = [
     *      ['type' => 'left', 'table' => 'users', 'on' => 'orders.user_id = users.id']
     *  ];
     *  $orderBy = 'created_at';
     *  $isAscend = true;
     *  $result = $yourClassInstance->findBy(
     *      $conditions,
     *      "*",
     *      $orderBy,
     *      $isAscend,
     *      $joins
     *  );
     *  // $result will contain the first matching row as an array, or null if no match is found.
     */
    public function findBy(
        array   $conditions,
        string  $columns = "*",
        ?string $orderBy = null,
        ?string $Ascend = null,
        ?array  $joins = null,
        string $symbole = "",
        ?bool $isString = false
    ): array | false | null
    {
        $sql = $this->getSql($columns, $joins, $conditions, $orderBy, $Ascend, $symbole, $isString);

        // Limit to 1 result since it's findBy
        $sql .= " LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as an associative array
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Read Error: " . $e->getMessage(), __METHOD__);

            return null;
        }
    }

    /**
     * Find Single Value by Aggregate Function
     *
     * EN: Retrieves a single value from the database using an aggregate function (e.g., COUNT, SUM, AVG).
     * 
     * FR: Récupère une valeur unique de la base de données en utilisant une fonction d'agrégat (par ex., COUNT, SUM, AVG).
     *
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne → valeur)
     * @param string $aggregateFunction EN: Aggregate function to use (e.g., COUNT, SUM) | FR: Fonction d'agrégat à utiliser (par ex., COUNT, SUM)
     * @param string $column EN: Column name to apply the aggregate function on, default is "*" | FR: Nom de la colonne sur laquelle appliquer la fonction d'agrégat, "*" par défaut
     * @param string|null $orderBy EN: Column to order by, optional | FR: Colonne pour trier, optionnel
     * @param bool|null $isAscend EN: Sort direction ("true" for ASC, "false" for DESC), optional | FR: Direction de tri ("true" pour ASC, "false" pour DESC), optionnel
     * @param int|null $limit EN: Limit of rows to fetch, optional | FR: Limite du nombre de lignes à récupérer, optionnel
     * @param array|null $joins EN: Joins to include, optional | FR: Joins à inclure, optionnel
     * @return mixed EN: Single aggregate value or null on error | FR: Valeur unique agrégée ou null en cas d'erreur
     *
     * @example Usage:
     *  $conditions = ['id' => 1];
     *  $joins = [
     *      ['type' => 'inner', 'table' => 'users', 'on' => 'orders.user_id = users.id']
     *  ];
     *  $orderBy = 'created_at';
     *  $isAscend = true;
     *  $result = $yourClassInstance->findSingleValueBy(
     *      $conditions,
     *      "SUM",
     *      "price",
     *      $orderBy,
     *      $isAscend,
     *      10,
     *      $joins
     *  );
     *  // $result will contain the aggregate sum of "price" column for matching rows.
     */
    public function findSingleValueBy(
        array   $conditions = [],
        string  $aggregateFunction = "COUNT",
        string  $column = "*",
        ?string $orderBy = null,
        ?bool   $isAscend = null,
        ?int    $limit = null,
        ?array  $joins = null
    ): mixed
    {
        // Default aggregate function
        $sql = "SELECT $aggregateFunction($column) FROM $this->table";

        // Handle joins if provided
        $sql = $this->addJoinsToQuery($sql, $joins);

        // Handle conditions (WHERE)
        if (!empty($conditions)) {
            $sql = $this->addConditionsToQuery($sql, $conditions, '');
        }

        // Handle ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Handle ASC or DESC
        if ($isAscend !== null) {
            $sql .= $isAscend ? " ASC" : " DESC";
        }

        // Handle LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        // Debug
        Logger::log($sql, __METHOD__, Level::DEBUG);

        // Execute the query and fetch the result
        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            return $stmt->fetchColumn(); // Fetch the single column value (e.g., COUNT, SUM)
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Read Error: " . $e->getMessage(), __METHOD__);

            return null; // Return null in case of an error
        }
    }

    /**
     * Add Joins to the SQL Query
     *
     * EN: Modifies the given SQL query by adding JOIN clauses based on the provided array of joins.
     *
     * FR: Modifie la requête SQL donnée en ajoutant des clauses JOIN basées sur le tableau des joins fourni.
     *
     * @param string $sql EN: Base SQL query to be modified | FR: Requête SQL de base à modifier.
     * @param array|null $joins EN: Array of joins, each containing "type" (INNER, LEFT, etc.), "table" (table name),
     *                          and "on" (conditions for the join) | FR: Tableau des joins, chacun contenant "type",
     *                          "table" et "on".
     * @return string EN: Modified SQL query with JOIN clauses | FR: Requête SQL modifiée avec des clauses JOIN.
     */
    private function addJoinsToQuery(string $sql, ?array $joins): string
    {
        if ($joins) {
            foreach ($joins as $join) {
                $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }
        return $sql;
    }

    /**
     * Add Conditions to the SQL Query
     *
     * EN: Appends WHERE clauses to the SQL query based on the given associative array of conditions.
     *
     * FR: Ajoute des clauses WHERE à la requête SQL sur la base du tableau associatif des conditions.
     *
     * @param string $sql EN: Base SQL query to be modified | FR: Requête SQL de base à modifier.
     * @param array $conditions EN: Associative array of conditions (e.g., column → value)
     *                          | FR: Tableau associatif des conditions (par ex., colonne → valeur).
     * @return string EN: Modified SQL query with WHERE conditions | FR: Requête SQL modifiée avec des conditions WHERE.
     */
    private function addConditionsToQuery(string $sql, array $conditions, string $symbole, bool $isString = false): string
    {
        if ($symbole == '') {
            $symbole = '=';
        }
        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            // Add the condition to the clauses array
            if ($isString) {
                $conditionClauses[] = "$key $symbole '$value'";
            } else {

                $conditionClauses[] = "$key $symbole $value";
            }
        }
        return $sql . " WHERE " . implode(" AND ", $conditionClauses);
    }

    /**
     * Bind Conditions to a Prepared Statement
     *
     * EN: Binds the provided conditions as parameters to a prepared PDO statement.
     *
     * FR: Lie les conditions fournies en tant que paramètres à une déclaration PDO préparée.
     *
     * @param PDOStatement $stmt EN: Prepared statement to bind parameters to | FR: Déclaration préparée à laquelle lier les paramètres.
     * @param array $conditions EN: Associative array of conditions (key → value) to be bound | FR: Tableau associatif des conditions (clé → valeur) à lier.
     * @return void
     */
    private function bindConditions(PDOStatement $stmt, array $conditions): void
    {
        foreach ($conditions as $key => $value) {

            // replacing '.' by '_' for the placeholder
            $placeHolder = str_replace('.', '_', $key);

            // Debug
            Logger::log("Binding :$placeHolder to '$value'", __METHOD__, Level::DEBUG);

            $stmt->bindValue(":$placeHolder", $value); // Bind values safely
        }
    }

    /**
     * Check if a Record Exists in the Table
     *
     * EN: Uses a SELECT EXISTS query to check if a record exists based on specific conditions.
     *
     * FR: Utilise une requête SELECT EXISTS pour vérifier si un enregistrement existe en fonction des conditions spécifiées.
     *
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne → valeur).
     * @return bool EN: True if a matching record exists, false otherwise | FR: Vrai si un enregistrement correspondant existe, faux sinon.
     */
    public function exists(array $conditions): bool
    {
        // Construire la partie WHERE de la requête en fonction des conditions
        $conditionStrings = [];
        foreach ($conditions as $key => $value) {
            $conditionStrings[] = "$key = :$key";
        }

        // Construire la requête SELECT EXISTS
        $sql = "SELECT EXISTS(SELECT 1 FROM $this->table WHERE " . implode(" AND ", $conditionStrings) . ") AS exists";

        // Préparer et exécuter la requête
        $stmt = $this->pdo->prepare($sql);

        // Lier les valeurs des conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Exécuter la requête
        $stmt->execute();

        // Retourner le résultat de la requête (true si l'utilisateur existe, false sinon)
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool)$result['exists'];
    }

    /**
     * Search Records in the Table
     *
     * EN: Searches records in the table by matching the "name", "username", or "email" fields to a search term.
     *
     * FR: Recherche des enregistrements dans la table en faisant correspondre les champs "name", "username" ou "email" à un terme de recherche.
     *
     * @param string|null $search EN: Value to search for, optional | FR: Valeur à rechercher, optionnel.
     * @param int $limit EN: Number of records to return, default is 10 | FR: Nombre d'enregistrements à retourner, par défaut 10.
     * @param int $offset EN: Offset for the query result, default is 0 | FR: Décalage pour le résultat de la requête, par défaut 0.
     * @return array EN: Array of matching records or empty array if none found | FR: Tableau des enregistrements correspondants ou tableau vide s'il n'y en a aucun.
     */
    public function search(?string $search = '', int $limit = 10, int $offset = 0): array
    {
        // Base SQL query
        $sql = "SELECT * FROM $this->table";

        // Add search conditions only if $search is not empty
        if (!empty($search)) {
            $sql .= " WHERE name LIKE :search OR username LIKE :search OR email LIKE :search";
        }

        // Append LIMIT and OFFSET
        $sql .= " LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters
            if (!empty($search)) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Search Error: " . $e->getMessage(), __METHOD__);

            return [];
        }
    }

    /**
     * Search Skills in the Table
     *
     * EN: Searches skill records in the table by matching "name" or "description" fields to a search term.
     *
     * FR: Recherche des compétences dans la table en faisant correspondre les champs "name" ou "description" à un terme de recherche.
     *
     * @param string|null $search EN: Value to search for, optional | FR: Valeur à rechercher, optionnel.
     * @param int $limit EN: Number of records to return, default is 10 | FR: Nombre d'enregistrements à retourner, par défaut 10.
     * @param int $offset EN: Offset for the query result, default is 0 | FR: Décalage pour le résultat de la requête, par défaut 0.
     * @return array EN: Array of matching skill records or empty array if none found | FR: Tableau des compétences correspondantes ou tableau vide s'il n'y en a aucune.
     */
    public function searchSkill(?string $search = '', int $limit = 10, int $offset = 0): array
    {
        // Base SQL query
        $sql = "SELECT * FROM $this->table";

        // Add search conditions only if $search is not empty
        if (!empty($search)) {
            $sql .= " WHERE name LIKE :search OR description LIKE :search";
        }

        // Append LIMIT and OFFSET
        $sql .= " LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters
            if (!empty($search)) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Search Error: " . $e->getMessage(), __METHOD__);

            return [];
        }
    }

    /**
     * Update: Update records in a table
     *
     * EN: Updates records in the database.
     *
     * FR: Met à jour des enregistrements dans la base de données.
     *
     * @param array $data EN: Associative array of column-valus pairs to update | FR: Tableau associatif de paires colonne-valeur à mettre à jour.
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne => valeur)
     * @return int EN: The number of lines affected by the update or -1 if an error occurs | FR: Le nombre de lignes affectées par la mise-à-jour ou -1 en cas d'erreur.
     */
    public function update(array $data, array $conditions): int {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = $value";
        }

        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = $value";
        }

        $sql = "UPDATE $this->table SET " . implode(", ", $setClauses) . " WHERE " . implode(" AND ", $conditionClauses);
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute();
            return $stmt->rowCount() ;
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Update Error: " . $e->getMessage(), __METHOD__);

            return -1;
        }
    }
    public function updateString(array $data, array $conditions): int {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = '$value'";
        }

        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = $value";
        }

        $sql = "UPDATE $this->table SET " . implode(", ", $setClauses) . " WHERE " . implode(" AND ", $conditionClauses);
        $stmt = $this->pdo->prepare($sql);


        try {
            $stmt->execute();
            return $stmt->rowCount() ;
        } catch (PDOException $e) {
            // LOGGING
            Logger::log("Update Error: " . $e->getMessage(), __METHOD__);

            return -1;
        }
    }

    /**
     * Delete: Delete records from a table
     *
     * EN: Deletes records from the database based on conditions.
     *
     * FR: Supprime des enregistrements de la base de données en fonction des conditions.
     *
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne => valeur)
     * @return int EN: The number of lines affected by the deletion or -1 if an error occurs | FR: Le nombre de lignes affectées par la suppression ou -1 en cas d'erreur.
     */
    public function delete(array $conditions): int {
        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = $value";
        }

        $sql = "DELETE FROM $this->table WHERE " . implode(" AND ", $conditionClauses);

        // Debug
        Logger::log($sql, __METHOD__, Level::DEBUG);

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {

            // LOGGING
            Logger::log("Delete Error: " . $e->getMessage(), __METHOD__);

            return -1;
        }
    }

    /**
     * Transaction Support
     *
     * EN: Executes a series of database operations within a transaction.
     *
     * FR: Exécute une série d'opérations de base de données dans une transaction.
     *
     * @param callable $callback EN: Callback function containing transactional logic | FR: Fonction de rappel contenant la logique transactionnelle
     * @return void
     * @throws Exception EN: Throws exception if transaction fails | FR: Lance une exception en cas d'échec de la transaction
     */
    public function transaction(callable $callback): void {
        try {
            $this->pdo->beginTransaction();
            $callback($this->pdo);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Constructs and returns a SQL query string.
     *
     * EN: This method builds a SQL query dynamically based on the specified columns, joins, conditions,
     *     ordering, and sorting direction.
     *
     * FR: Cette méthode génère dynamiquement une requête SQL en fonction des colonnes spécifiées,
     *     des jointures, des conditions, de l'ordre et de la direction du tri.
     *
     * @param string $columns EN: Columns to select | FR: Colonnes à sélectionner.
     * @param array|null $joins EN: Array of joins with 'type', 'table', and 'on' keys or null | FR: Tableau des jointures avec "type", "table", "on" ou null.
     * @param array $conditions EN: Associative array of conditions (column → value) | FR: Tableau associatif des conditions (colonne → valeur).
     * @param string|null $orderBy EN: Column to order by or null for no order | FR: Colonne pour trier ou null pour aucun tri.
     * @param bool|null $isAscend EN: True for ASC, False for DESC, or null for default order | FR: True pour ASC, False pour DESC, ou null pour l'ordre par défaut.
     * @return string EN: The constructed SQL query string | FR: La chaîne de requête SQL construite.
     */
    public function getSql(string $columns, ?array $joins, array $conditions, ?string $orderBy, ?bool $isAscend , string $symbole, ?bool $isString): string
    {
        $sql = "SELECT $columns FROM $this->table";

        // Handle joins if provided
        if ($joins) {
            foreach ($joins as $join) {
                $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }

        // Handle conditions (WHERE)
        if (!empty($conditions)) {
            $sql = $this->addConditionsToQuery($sql, $conditions ,$symbole, $isString);
        }

        // Handle ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Handle ASC or DESC
        if ($isAscend !== null) {
            $sql .= $isAscend ? " ASC" : " DESC";
        }
        return $sql;
    }

    public function findAllByUser(int $user_id, array $conditions = [], string $columns = "*", ?string $orderBy = null, ?string $Ascend = null, ?int $limit = null, ?int $offset = null, string $symbole = '', ?bool $isString = false): array
    {
        $joins = [
            "LEFT JOIN project_users pu ON projects.id = pu.project_id"
        ];

        // Add condition for public projects OR projects where user has a role
        $conditions[] = "(projects.visibility = 'public' OR pu.user_id = :user_id)";

        $sql = $this->getSql($columns, $joins, $conditions, $orderBy, $Ascend, $symbole, $isString);

        // Handle LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }

        // Debugging output for the query
        Logger::log($sql, __METHOD__, Level::DEBUG);

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind user_id manually since it's not inside $conditions
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            // Bind other conditions safely
            $this->bindConditions($stmt, $conditions);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
        } catch (PDOException $e) {
            Logger::log("Read Error: " . $e->getMessage(), __METHOD__);
            return []; // Return an empty array in case of error
        }
    }

}




// exemple usage

// $crud = new Crud('users');

//// Create a new user
//$data = ['name' => 'John Doe', 'email' => 'johndoe@example.com'];
//$userId = $crud->create($data);

//// Fetch all users
//$users = $crud->findAllBy([], '*', 'name', true);

//// Update user
//$updateData = ['email' => 'newemail@example.com'];
//$updateConditions = ['id' => $userId];
//$updatedRows = $crud->update($updateData, $updateConditions);

//// Delete user
//$deleteConditions = ['id' => $userId];
//$deletedRows = $crud->delete($deleteConditions);