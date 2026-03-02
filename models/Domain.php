<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Domain
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function addDomain(string $tenantId, string $domainName): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO custom_domains (tenant_id, domain_name, status) VALUES (?, ?, "pending")'
        );
        return $stmt->execute([$tenantId, $domainName]);
    }

    public function allForTenant(string $tenantId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM custom_domains WHERE tenant_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function findTenantIdByVerifiedDomain(string $domainName): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT tenant_id FROM custom_domains WHERE domain_name = ? AND status = "verified" LIMIT 1'
        );
        $stmt->execute([$domainName]);
        $row = $stmt->fetch();
        return $row['tenant_id'] ?? null;
    }

    public function markVerifiedForTenant(string $tenantId, string $domainName): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE custom_domains SET status = "verified", verified_at = NOW() WHERE tenant_id = ? AND domain_name = ?'
        );
        $stmt->execute([$tenantId, $domainName]);
        return $stmt->rowCount() > 0;
    }

    public function belongsToTenant(string $tenantId, string $domainName): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM custom_domains WHERE tenant_id = ? AND domain_name = ? LIMIT 1'
        );
        $stmt->execute([$tenantId, $domainName]);
        return (bool) $stmt->fetch();
    }

    public function deleteForTenant(string $tenantId, string $domainName): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM custom_domains WHERE tenant_id = ? AND domain_name = ?'
        );
        $stmt->execute([$tenantId, $domainName]);
        return $stmt->rowCount() > 0;
    }
}
