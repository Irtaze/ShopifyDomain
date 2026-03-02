<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Store
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function getByTenantId(string $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM stores WHERE tenant_id = ? LIMIT 1');
        $stmt->execute([$tenantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsertForTenant(string $tenantId, string $storeName, string $description, string $themeColor): bool
    {
        $existing = $this->getByTenantId($tenantId);
        if ($existing) {
            $stmt = $this->db->prepare(
                'UPDATE stores SET store_name = ?, description = ?, theme_color = ? WHERE tenant_id = ?'
            );
            return $stmt->execute([$storeName, $description, $themeColor, $tenantId]);
        }

        $stmt = $this->db->prepare(
            'INSERT INTO stores (tenant_id, store_name, description, theme_color) VALUES (?, ?, ?, ?)'
        );
        return $stmt->execute([$tenantId, $storeName, $description, $themeColor]);
    }
}
