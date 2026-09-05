<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
