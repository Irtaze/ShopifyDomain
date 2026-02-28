<?php
/**
 * Store Model
 * Handles all store-related database operations (SQLite via PDO)
 */

require_once __DIR__ . '/Database.php';

class Store
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a store by its custom domain
     */
    public function findByDomain(string $domain): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM stores WHERE custom_domain = ? AND is_active = 1",
            [$domain]
        );
    }

    /**
     * Get all stores
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM stores ORDER BY created_at DESC");
    }

    /**
     * Get a store by ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM stores WHERE id = ?", [$id]);
    }

    /**
     * Create a new store
     */
    public function create(array $data): int|false
    {
        try {
            $this->db->query(
                "INSERT INTO stores (store_name, custom_domain, owner_email, description, theme_color, is_verified, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [
                    $data['store_name'],
                    $data['custom_domain'],
                    $data['owner_email'],
                    $data['description'] ?? '',
                    $data['theme_color'] ?? '#4F46E5',
                    $data['is_verified'] ?? 0
                ]
            );
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update a store
     */
    public function update(int $id, array $data): bool
    {
        try {
            $this->db->query(
                "UPDATE stores SET store_name = ?, custom_domain = ?, owner_email = ?, description = ?, theme_color = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                [
                    $data['store_name'],
                    $data['custom_domain'],
                    $data['owner_email'],
                    $data['description'] ?? '',
                    $data['theme_color'] ?? '#4F46E5',
                    $id
                ]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a store
     */
    public function delete(int $id): bool
    {
        try {
            $this->db->query("DELETE FROM stores WHERE id = ?", [$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verify a domain (check if it points to our server)
     */
    public function verifyDomain(string $domain): array
    {
        $ip = gethostbyname($domain);
        $allowedIPs = array_map('trim', explode(',', SERVER_IPS));
        $isVerified = in_array($ip, $allowedIPs);

        return [
            'domain'      => $domain,
            'resolved_ip' => $ip,
            'server_ip'   => SERVER_IP,
            'allowed_ips' => $allowedIPs,
            'is_verified'  => $isVerified,
            'message'     => $isVerified
                ? "✅ Domain is correctly pointing to your server ({$ip})"
                : "❌ Domain points to {$ip}, expected one of: " . SERVER_IPS
        ];
    }

    /**
     * Mark a store's domain as verified
     */
    public function markVerified(int $id, bool $verified = true): bool
    {
        $val = $verified ? 1 : 0;
        try {
            $this->db->query(
                "UPDATE stores SET is_verified = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                [$val, $id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Toggle store active status
     */
    public function toggleActive(int $id): bool
    {
        try {
            $this->db->query(
                "UPDATE stores SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                [$id]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
