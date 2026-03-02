<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function current_tenant_id(): string
{
    return (string) $_SESSION['tenant_id'];
}

function current_user_id(): int
{
    return (int) $_SESSION['user_id'];
}
