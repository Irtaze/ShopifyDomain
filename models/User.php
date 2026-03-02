<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $input): int
    {
        $tenantId = $this->generateTenantId($input['full_name'] ?? 'tenant');
        $stmt = $this->db->prepare(
            'INSERT INTO users (tenant_id, full_name, email, password, know_about_us, terms_accepted, referral_name)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $tenantId,
            $input['full_name'],
            $input['email'],
            password_hash($input['password'], PASSWORD_DEFAULT),
            $input['know_about_us'] ?? null,
            !empty($input['terms_accepted']) ? 1 : 0,
            $input['referral_name'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function generateTenantId(string $seed): string
    {
        $base = strtolower(trim($seed));
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim((string) $base, '-');
        if ($base === '') {
            $base = 'tenant';
        }

        do {
            $candidate = $base . '-' . bin2hex(random_bytes(3));
            $stmt = $this->db->prepare('SELECT id FROM users WHERE tenant_id = ? LIMIT 1');
            $stmt->execute([$candidate]);
            $exists = $stmt->fetch();
        } while ($exists);

        return $candidate;
    }
}
