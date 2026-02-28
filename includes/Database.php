<?php
/**
 * Database Connection Class
 * Singleton pattern using SQLite (zero config — no MySQL needed!)
 */

require_once __DIR__ . '/../config/config.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        // Create database directory if it doesn't exist
        $dbDir = dirname(DB_PATH);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        try {
            $this->connection = new PDO('sqlite:' . DB_PATH);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->exec("PRAGMA journal_mode=WAL");
            $this->connection->exec("PRAGMA foreign_keys=ON");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        // Auto-initialize tables if they don't exist
        $this->initializeDatabase();
    }

    /**
     * Create tables and seed demo data on first run
     */
    private function initializeDatabase(): void
    {
        // Check if stores table exists
        $check = $this->connection->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='stores'"
        )->fetch();

        if (!$check) {
            // Create stores table
            $this->connection->exec("
                CREATE TABLE stores (
                    id              INTEGER PRIMARY KEY AUTOINCREMENT,
                    store_name      TEXT NOT NULL,
                    custom_domain   TEXT NOT NULL UNIQUE,
                    owner_email     TEXT NOT NULL,
                    description     TEXT DEFAULT '',
                    logo_url        TEXT DEFAULT NULL,
                    theme_color     TEXT DEFAULT '#4F46E5',
                    is_verified     INTEGER DEFAULT 0,
                    is_active       INTEGER DEFAULT 1,
                    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");

            // Seed one example store (remove this block after first deploy)
            $seed = $this->connection->prepare("
                INSERT INTO stores (store_name, custom_domain, owner_email, description, theme_color, is_verified, is_active)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");

            $stores = [
                ['Example Store', 'example.com', 'admin@example.com', 'This is an example store. Replace it with real stores.', '#4F46E5', 0],
            ];

            foreach ($stores as $s) {
                $seed->execute($s);
            }
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Run a prepared query and return the statement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            die("Query preparation failed.");
        }

        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Escape string (for display only — use prepared statements for queries)
     */
    public function escape(string $str): string
    {
        return $this->connection->quote($str);
    }
}
