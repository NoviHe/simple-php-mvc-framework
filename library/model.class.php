<?php

declare(strict_types=1);

/**
 * Base Model Class (PDO with Prepared Statements)
 * Provides secure data access and basic Active Record/Query helper methods.
 */
class Model
{
    protected static ?PDO $_pdo = null;
    protected string $_table = '';
    protected string $_primaryKey = 'id';

    public function __construct()
    {
        $this->connect();
        if (empty($this->_table)) {
            $this->_table = $this->guessTableName();
        }
    }

    /**
     * Establish or reuse a shared PDO connection
     */
    public function connect(): ?PDO
    {
        if (self::$_pdo !== null) {
            return self::$_pdo;
        }

        $driver = defined('DB_CONNECTION') ? DB_CONNECTION : 'mysql';
        $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
        $port = defined('DB_PORT') ? DB_PORT : '3306';
        $database = defined('DB_NAME') ? DB_NAME : '';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $password = defined('DB_PASSWORD') ? DB_PASSWORD : '';

        $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$_pdo = new PDO($dsn, $user, $password, $options);
            return self::$_pdo;
        } catch (PDOException $e) {
            // Log connection error if debug enabled
            if (defined('APP_DEBUG') && APP_DEBUG === true) {
                error_log("Database Connection Error: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Check if database connection is alive
     */
    public static function isConnected(): bool
    {
        return self::$_pdo !== null;
    }

    /**
     * Get raw PDO instance
     */
    public function getPdo(): ?PDO
    {
        return self::$_pdo;
    }

    /**
     * Auto-detect table name from class name if not specified
     */
    protected function guessTableName(): string
    {
        $className = (new ReflectionClass($this))->getShortName();
        $baseName = preg_replace('/Model$/', '', $className);
        return strtolower($baseName ?? 'items') . 's';
    }

    /**
     * Execute a prepared SQL query safely
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = $this->connect();
        if ($pdo === null) {
            throw new RuntimeException("Cannot execute query: Database connection is not available.");
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all records
     */
    public function all(string $orderBy = '', string $order = 'ASC', ?int $limit = null): array
    {
        $sql = "SELECT * FROM `{$this->_table}`";

        if (!empty($orderBy)) {
            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy);
            $sql .= " ORDER BY `{$safeCol}` {$order}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return $this->query($sql)->fetchAll();
    }

    /**
     * Find single record by primary key
     */
    public function find(mixed $id): ?array
    {
        $sql = "SELECT * FROM `{$this->_table}` WHERE `{$this->_primaryKey}` = :id LIMIT 1";
        $stmt = $this->query($sql, ['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch records matching conditions
     */
    public function where(array $conditions, string $orderBy = '', string $order = 'ASC', ?int $limit = null): array
    {
        if (empty($conditions)) {
            return $this->all($orderBy, $order, $limit);
        }

        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
            $paramKey = 'p_' . $safeCol;
            $whereClauses[] = "`{$safeCol}` = :{$paramKey}";
            $params[$paramKey] = $value;
        }

        $sql = "SELECT * FROM `{$this->_table}` WHERE " . implode(' AND ', $whereClauses);

        if (!empty($orderBy)) {
            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy);
            $sql .= " ORDER BY `{$safeCol}` {$order}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single record matching conditions
     */
    public function first(array $conditions): ?array
    {
        $results = $this->where($conditions, '', 'ASC', 1);
        return $results[0] ?? null;
    }

    /**
     * Insert new record using prepared statements
     */
    public function create(array $data): int|string
    {
        if (empty($data)) {
            throw new InvalidArgumentException("Data to insert cannot be empty.");
        }

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $column => $value) {
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
            $paramKey = 'ins_' . $safeCol;
            $columns[] = "`{$safeCol}`";
            $placeholders[] = ":{$paramKey}";
            $params[$paramKey] = $value;
        }

        $sql = "INSERT INTO `{$this->_table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $this->query($sql, $params);

        return (int)self::$_pdo->lastInsertId();
    }

    /**
     * Update existing record matching conditions
     */
    public function update(array $data, array $conditions): int
    {
        if (empty($data) || empty($conditions)) {
            throw new InvalidArgumentException("Update requires both data and conditions.");
        }

        $setClauses = [];
        $params = [];

        foreach ($data as $column => $value) {
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
            $paramKey = 'set_' . $safeCol;
            $setClauses[] = "`{$safeCol}` = :{$paramKey}";
            $params[$paramKey] = $value;
        }

        $whereClauses = [];
        foreach ($conditions as $column => $value) {
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
            $paramKey = 'whr_' . $safeCol;
            $whereClauses[] = "`{$safeCol}` = :{$paramKey}";
            $params[$paramKey] = $value;
        }

        $sql = "UPDATE `{$this->_table}` SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $whereClauses);
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Delete records matching conditions
     */
    public function delete(array $conditions): int
    {
        if (empty($conditions)) {
            throw new InvalidArgumentException("Delete conditions cannot be empty to prevent accidental mass deletion.");
        }

        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
            $paramKey = 'del_' . $safeCol;
            $whereClauses[] = "`{$safeCol}` = :{$paramKey}";
            $params[$paramKey] = $value;
        }

        $sql = "DELETE FROM `{$this->_table}` WHERE " . implode(' AND ', $whereClauses);
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Count total records
     */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as total FROM `{$this->_table}`";
            $stmt = $this->query($sql);
        } else {
            $whereClauses = [];
            $params = [];
            foreach ($conditions as $column => $value) {
                $safeCol = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
                $paramKey = 'cnt_' . $safeCol;
                $whereClauses[] = "`{$safeCol}` = :{$paramKey}";
                $params[$paramKey] = $value;
            }
            $sql = "SELECT COUNT(*) as total FROM `{$this->_table}` WHERE " . implode(' AND ', $whereClauses);
            $stmt = $this->query($sql, $params);
        }

        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }
}
