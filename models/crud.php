<?php
namespace models;

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
        array $conditions = [],
        string $columns = "*",
        ?string $orderBy = null,
        ?bool $isAscend = null,
        ?int $limit = null,
        ?array $joins = null
    ): array {
        $sql = "SELECT $columns FROM $this->table";

        // Handle joins if provided
        if ($joins) {
            foreach ($joins as $join) {
                $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }

        // Handle conditions (WHERE)
        if (!empty($conditions)) {
            $conditionClauses = [];
            foreach ($conditions as $key => $value) {
                $conditionClauses[] = "$key = $value";
            }
            $sql .= " WHERE " . implode(" AND ", $conditionClauses);
        }

        // Handle ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Handle ASC or DESC
        if ($isAscend !== null){
            if ($isAscend) {
                $sql .= " ASC";
            } else {
                $sql .= " DESC";
            }
        }


        // Handle LIMIT
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        echo $sql;

        // Execute the query with conditions
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            return [];
        }
    }

    public function findBy(
        array $conditions,
        string $columns = "*",
        ?string $orderBy = null,
        ?bool $isAscend = null,
        ?array $joins = null
    ): ?array {
        $sql = "SELECT $columns FROM $this->table";

        // Handle joins if provided
        if ($joins) {
            foreach ($joins as $join) {
                $sql .= " " . strtoupper($join['type']) . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }

        // Handle conditions (WHERE)
        if (!empty($conditions)) {
            $conditionClauses = [];
            foreach ($conditions as $key => $value) {
                $conditionClauses[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(" AND ", $conditionClauses);
        }

        // Handle ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Handle ASC or DESC
        if ($isAscend !== null) {
            $sql .= $isAscend ? " ASC" : " DESC";
        }

        // Limit to 1 result since it's findBy
        $sql .= " LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters
            foreach ($conditions as $key => &$value) {
                $stmt->bindParam(":" . $key, $value);
            }

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            return null;
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
}
