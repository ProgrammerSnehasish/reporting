<?php

/**
 * ============================================================================
 * Database Configuration & NeonDB PostgreSQL Connector
 * Luminia Lifecare - Reporting & DCR System
 * ============================================================================
 */

require_once __DIR__ . '/db_bridge.php';

// 1. Load environment variables from .env if available
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// 2. Parse NeonDB Connection Parameters
$dbUrl = getenv('DATABASE_URL') ?: "postgresql://neondb_owner:npg_HOch14EmMaDQ@ep-lingering-shape-b302s22g-pooler.c-4.ap-southeast-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require";

$dbParts = parse_url($dbUrl);

$host     = $dbParts['host'] ?? "ep-lingering-shape-b302s22g-pooler.c-4.ap-southeast-1.aws.neon.tech";
$port     = $dbParts['port'] ?? 5432;
$user     = $dbParts['user'] ?? "neondb_owner";
$pass     = $dbParts['pass'] ?? "npg_HOch14EmMaDQ";
$dbname   = ltrim($dbParts['path'] ?? 'neondb', '/');
$sslmode  = "require";

// 3. PostgreSQL Connection String
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$pass} sslmode={$sslmode}";

// 4. Initialize PDO Connection ($pdo)
$pdo = null;
try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// 5. Initialize Compatibility Connection ($conn) for Legacy PHP Pages
$conn = new NeonPgConnection($pdo);

// 6. Global Helper Functions for Clean DB Operations
if (!function_exists('db_query')) {
    function db_query($sql, $params = []) {
        global $pdo;
        if ($pdo) {
            $stmt = $pdo->prepare(NeonPgConnection::translateSql($sql));
            $stmt->execute($params);
            return $stmt;
        }
        return false;
    }
}

if (!function_exists('db_fetch_all')) {
    function db_fetch_all($sql, $params = []) {
        $stmt = db_query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}

if (!function_exists('db_fetch_one')) {
    function db_fetch_one($sql, $params = []) {
        $stmt = db_query($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }
}

if (!function_exists('db_insert_id')) {
    function db_insert_id() {
        global $pdo;
        return $pdo ? $pdo->lastInsertId() : null;
    }
}
