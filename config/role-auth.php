<?php

require_once __DIR__ . '/auth.php';

function checkRole($roles = [])
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
        header("Location: " . BASE_URL . "auth/unauthorized.php");
        exit;
    }
}
