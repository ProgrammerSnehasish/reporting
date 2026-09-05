<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";

header('Content-Type: application/json');

$type = $_POST['type'] ?? '';
$id   = $_POST['id'] ?? 0;
$qty  = $_POST['qty'] ?? 0;

// No stock validation
echo json_encode([
    'success' => true
]);

exit;
