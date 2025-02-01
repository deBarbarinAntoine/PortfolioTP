<?php
namespace App\Models;

use Exception;
use PDO;
use PDOException;

/**
 * Class Crud
 *
 * EN : A simple CRUD (Create, Read, Update, Delete) class to interact with a MySQL database using PDO.
 * FR : Une classe CRUD simple (Créer, Lire, Mettre à jour, Supprimer) pour interagir avec une base de données MySQL à l'aide de PDO.
 */
class Crud {
    /**
     * @var PDO $pdo
     * EN : PDO instance for database connection.
     * FR : Instance PDO pour la connexion à la base de données.
     */
    private PDO $pdo;

    /**
     * @var string $table
     * EN: The name of the database table.
     * FR: Le nom de la table de la base de données.
     */
    protected string $table;

    /**
     * Constructor: Initialize database connection
     *
     * EN: Sets up the PDO connection and initializes the table name.
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
     * FR: Insère un nouvel enregistrement dans la base de données.
     *
     * @param array $data EN: Associative array of column-value pairs | FR: Tableau associatif de paires colonne-valeur
     * @return int EN : True on success, false on failure | FR : Vrai si réussi, faux sinon
     */
    public function create(array $data): int {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($data);
            return $this->pdo->lastInsertId() ;
        } catch (PDOException $e) {
            error_log("Create Error: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Find : Fetch records from a table
     *
     * EN: Retrieves records from the database based on conditions.
     * FR: Récupère des enregistrements de la base de données en fonction des conditions.
     *
     * @param array $conditions EN : Associative array of conditions (column → value) | FR : Tableau associatif de conditions (colonne → valeur)
     * @param string $columns EN : Columns to select, default is "*" | FR : Colonnes à sélectionner, par défaut "*"
     * @param string|null $orderBy EN: Column to order by, optional | FR: Colonne de tri, optionnel
     * @param int|null $limit EN : Limit of rows to fetch, optional | FR : Limite du nombre de lignes à récupérer, optionnel
     * @return array EN: Array of results | FR: Tableau des résultats
     */
    public function findAllBy(
        // exemple usage
        //$conditions = ['status' => 'active'];
        //$joins = [
        //    ['type' => 'left', 'table' => 'users', 'on' => 'orders.user_id = users.id']
        //];
        //$orderBy = 'created_at';
        //$isAscend = true;
        //$limit = 10;
        //$results = $yourClassInstance->findAllBy(
        //    $conditions,
        //    "*",
        //    $orderBy,
        //    $isAscend,
        //    $limit,
        //    $joins
        //);
        //// $results will contain an array of matching rows or an empty array if no matches found
        array $conditions = [],
        string $columns = "*",
        ?string $orderBy = null,
        ?string $Ascend = null,
        ?int $limit = null,
        ?int $offset =null,
        ?array $joins = null
    ): array {
        $sql = $this->getSql($columns, $joins, $conditions, $orderBy, $Ascend);

        // Handle LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }

        // Debugging output for the query (you can remove this in production)
        echo $sql;

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters safely
            $this->bindConditions($stmt, $conditions);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
        } catch (PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            return []; // Return an empty array in case of error
        }
    }

    public function findBy(
        // example usage
        //$conditions = ['id' => 123, 'status' => 'active'];
        //$joins = [
        //    ['type' => 'left', 'table' => 'users', 'on' => 'orders.user_id = users.id']
        //];
        //$orderBy = 'created_at';
        //$isAscend = true;
        //$result = $yourClassInstance->findBy(
        //    $conditions,
        //    "*",
        //    $orderBy,
        //    $isAscend,
        //    $joins
        //);
        //// $result will contain the first matching row or null if no match
        array $conditions,
        string $columns = "*",
        ?string $orderBy = null,
        ?string $Ascend = null,
        ?array $joins = null
    ): ?array {
        $sql = $this->getSql($columns, $joins, $conditions, $orderBy, $Ascend);

        // Limit to 1 result since it's findBy
        $sql .= " LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters using bindValue to avoid passing by reference
            $this->bindConditions($stmt, $conditions);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as an associative array
        } catch (PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            return null;
        }
    }

    public function findSingleValueBy(
        // exemple usage
        //$conditions = ['id' => 1];
        //$joins = [
        //    ['type' => 'inner', 'table' => 'users', 'on' => 'orders.user_id = users.id']
        //];
        //$orderBy = 'created_at';
        //$isAscend = true;
        //$result = $yourClassInstance->findSingleValueBy(
        //    $conditions,
        //    "COUNT",
        //    "id",
        //    $orderBy,
        //    $isAscend,
        //    10,
        //    $joins
        //);
        array $conditions = [],
        string $aggregateFunction = "COUNT",
        string $column = "*",
        ?string $orderBy = null,
        ?bool $isAscend = null,
        ?int $limit = null,
        ?array $joins = null
    ): mixed {
        // Default aggregate function
        $sql = "SELECT $aggregateFunction($column) FROM $this->table";

        // Handle joins if provided
        $sql = $this->addJoinsToQuery($sql, $joins);

        // Handle conditions (WHERE)
        if (!empty($conditions)) {
            $sql = $this->addConditionsToQuery($sql, $conditions);
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

        echo $sql; // Debugging

        // Execute the query and fetch the result
        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind the parameters if necessary
            $this->bindConditions($stmt, $conditions);

            $stmt->execute();
            return $stmt->fetchColumn(); // Fetch the single column value (COUNT, SUM, etc.)
        } catch (PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    // Add joins to the query
    private function addJoinsToQuery(string $sql, ?array $joins): string {
        if ($joins) {
            foreach ($joins as $join) {
                $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }
        return $sql;
    }

    // Add conditions to the query
    private function addConditionsToQuery(string $sql, array $conditions): string {
        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = :$key"; // Use named placeholders for binding
        }
        return $sql . " WHERE " . implode(" AND ", $conditionClauses);
    }

    // Bind conditions to the prepared statement
    private function bindConditions($stmt, array $conditions): void {
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value); // Bind values safely
        }
    }

    // Méthode pour vérifier si un utilisateur existe (utilisation de EXISTS)
    public function exists($conditions): bool
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
        return (bool) $result['exists'];
    }

    public function search(?string $search = '', int $limit = 10, int $offset = 0): array {
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
            error_log("Search Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update : Update records in a table
     *
     * EN : Updates records in the database.
     * FR : Met à jour des enregistrements dans la base de données.
     *
     * @param array $data EN : Associative array of column-valus pairs to update | FR : Tableau associatif de paires colonne-valeur à mettre à jour.
     * @param array $conditions EN : Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne => valeur)
     * @return int EN : True on success, false on failure | FR : Vrai si réussi, faux sinon
     */
    public function update(array $data, array $conditions): int {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
        }

        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = :$key";
        }

        $sql = "UPDATE $this->table SET " . implode(", ", $setClauses) . " WHERE " . implode(" AND ", $conditionClauses);
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute(array_merge($data, $conditions));
            return $stmt->rowCount() ;
        } catch (PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete: Delete records from a table
     *
     * EN: Deletes records from the database based on conditions.
     * FR: Supprime des enregistrements de la base de données en fonction des conditions.
     *
     * @param array $conditions EN : Associative array of conditions (column → value) | FR: Tableau associatif de conditions (colonne => valeur)
     * @return int EN : True on success, false on failure | FR : Vrai si réussi, faux sinon
     */
    public function delete(array $conditions): int {
        $conditionClauses = [];
        foreach ($conditions as $key => $value) {
            $conditionClauses[] = "$key = :$key";
        }

        $sql = "DELETE FROM $this->table WHERE " . implode(" AND ", $conditionClauses);
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($conditions);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Delete Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Transaction Support
     *
     * EN : Executes a series of database operations within a transaction.
     * FR : Exécute une série d'opérations de base de données dans une transaction.
     *
     * @param callable $callback EN : Callback function containing transactional logic | FR : Fonction de rappel contenant la logique transactionnelle
     * @return void
     * @throws Exception EN : Throws exception if transaction fails | FR : Lance une exception en cas d'échec de la transaction
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
     * @param string $columns
     * @param array|null $joins
     * @param array $conditions
     * @param string|null $orderBy
     * @param bool|null $isAscend
     * @return string
     */
    public function getSql(string $columns, ?array $joins, array $conditions, ?string $orderBy, ?bool $isAscend): string
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
            $sql = $this->addConditionsToQuery($sql, $conditions);
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