<?php

/**
 * ============================================================================
 * NeonDB PostgreSQL / PDO Compatibility Bridge for MySQLi
 * ============================================================================
 * Allows legacy MySQLi code to seamlessly execute against Neon PostgreSQL.
 */

class NeonPgResult {
    private $rows;
    private $currentIndex = 0;
    public $num_rows = 0;

    public function __construct(array $rows = []) {
        $this->rows = $rows;
        $this->num_rows = count($rows);
        $this->currentIndex = 0;
    }

    public function fetch_assoc() {
        if ($this->currentIndex >= $this->num_rows) {
            return null;
        }
        return $this->rows[$this->currentIndex++];
    }

    public function fetch_array($mode = MYSQLI_BOTH) {
        if ($this->currentIndex >= $this->num_rows) {
            return null;
        }
        $row = $this->rows[$this->currentIndex++];
        if ($mode === MYSQLI_ASSOC) {
            return $row;
        }
        if ($mode === MYSQLI_NUM) {
            return array_values($row);
        }
        // MYSQLI_BOTH
        return array_merge(array_values($row), $row);
    }

    public function fetch_row() {
        if ($this->currentIndex >= $this->num_rows) {
            return null;
        }
        $row = $this->rows[$this->currentIndex++];
        return array_values($row);
    }

    public function data_seek($offset = 0) {
        if ($offset < 0 || ($this->num_rows > 0 && $offset >= $this->num_rows)) {
            return false;
        }
        $this->currentIndex = (int)$offset;
        return true;
    }

    public function free() {
        $this->rows = [];
        $this->num_rows = 0;
    }
}

class NeonPgStmt {
    private $pdo;
    private $stmt;
    private $boundParams = [];
    public $insert_id = 0;
    public $affected_rows = 0;
    public $error = '';

    public function __construct(PDO $pdo, PDOStatement $stmt) {
        $this->pdo = $pdo;
        $this->stmt = $stmt;
    }

    public function bind_param($types, &...$params) {
        $this->boundParams = [];
        foreach ($params as $key => &$val) {
            $this->boundParams[$key] = &$val;
        }
        return true;
    }

    public function execute() {
        try {
            $success = $this->stmt->execute($this->boundParams);
            $this->affected_rows = $this->stmt->rowCount();
            $this->insert_id = 0;
            if (preg_match('/^\s*INSERT\b/i', $this->stmt->queryString ?? '')) {
                try {
                    $this->insert_id = (int)$this->pdo->lastInsertId();
                } catch (Throwable $e) {
                    $this->insert_id = 0;
                }
            }
            return $success;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function get_result() {
        try {
            $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            return new NeonPgResult($rows);
        } catch (Exception $e) {
            return new NeonPgResult([]);
        }
    }

    public function close() {
        $this->stmt = null;
        return true;
    }
}

class NeonPgConnection {
    public $pdo;
    public $error = '';
    public $insert_id = 0;
    public $affected_rows = 0;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function translateSql($sql) {
        // 1. Remove backticks
        $sql = str_replace('`', '', $sql);

        // 2. Translate MySQL LIMIT x, y to PostgreSQL LIMIT y OFFSET x
        $sql = preg_replace_callback('/LIMIT\s+(\d+)\s*,\s*(\d+)/i', function($matches) {
            return 'LIMIT ' . $matches[2] . ' OFFSET ' . $matches[1];
        }, $sql);

        // 3. Translate IFNULL to COALESCE
        $sql = preg_replace('/\bIFNULL\s*\(/i', 'COALESCE(', $sql);

        // 4. Translate date & time functions
        $sql = preg_replace('/\bCURDATE\s*\(\s*\)/i', 'CURRENT_DATE', $sql);
        $sql = preg_replace('/\bCURTIME\s*\(\s*\)/i', 'CURRENT_TIME', $sql);
        $sql = preg_replace('/\bMONTH\s*\((.*?)\)/i', 'EXTRACT(MONTH FROM ($1))::integer', $sql);
        $sql = preg_replace('/\bYEAR\s*\((.*?)\)/i', 'EXTRACT(YEAR FROM ($1))::integer', $sql);
        $sql = preg_replace('/\bDAY\s*\((.*?)\)/i', 'EXTRACT(DAY FROM ($1))::integer', $sql);
        $sql = preg_replace('/\bDAYOFMONTH\s*\((.*?)\)/i', 'EXTRACT(DAY FROM ($1))::integer', $sql);

        // 5. Status comparison on varchar fields (tbl_dcr, tbl_tour_plans, tbl_dcr_expenses, tbl_attendance)
        $sql = preg_replace('/\bstatus\s*=\s*(\d+)\b/i', "status::text = '$1'", $sql);

        return $sql;
    }

    public function query($sql) {
        $this->error = '';
        try {
            $translatedSql = self::translateSql($sql);
            $stmt = $this->pdo->query($translatedSql);
            if ($stmt === false) {
                return false;
            }
            $this->affected_rows = $stmt->rowCount();
            $this->insert_id = 0;

            if (preg_match('/^\s*INSERT\b/i', $translatedSql)) {
                try {
                    $this->insert_id = (int)$this->pdo->lastInsertId();
                } catch (Throwable $e) {
                    $this->insert_id = 0;
                }
            }

            if (preg_match('/^\s*(SELECT|SHOW|EXPLAIN|DESCRIBE)\b/i', $translatedSql)) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return new NeonPgResult($rows);
            }
            return true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function prepare($sql) {
        $this->error = '';
        try {
            $translatedSql = self::translateSql($sql);
            $pdoStmt = $this->pdo->prepare($translatedSql);
            return new NeonPgStmt($this->pdo, $pdoStmt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function escape_string($string) {
        if ($string === null) return '';
        // Escape quotes safely
        return str_replace("'", "''", $string);
    }

    public function real_escape_string($string) {
        return $this->escape_string($string);
    }
}

// Define mysqli constants if not loaded
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
if (!defined('MYSQLI_NUM'))   define('MYSQLI_NUM', 2);
if (!defined('MYSQLI_BOTH'))  define('MYSQLI_BOTH', 3);

// Polyfill mysqli global functions when mysqli extension is not loaded
if (!function_exists('mysqli_query')) {
    function mysqli_query($conn, $sql) {
        if ($conn instanceof NeonPgConnection) return $conn->query($sql);
        if ($conn instanceof PDO) {
            $c = new NeonPgConnection($conn);
            return $c->query($sql);
        }
        return false;
    }

    function mysqli_prepare($conn, $sql) {
        if ($conn instanceof NeonPgConnection) return $conn->prepare($sql);
        if ($conn instanceof PDO) {
            $c = new NeonPgConnection($conn);
            return $c->prepare($sql);
        }
        return false;
    }

    function mysqli_stmt_bind_param($stmt, $types, &...$params) {
        if ($stmt instanceof NeonPgStmt) return $stmt->bind_param($types, ...$params);
        return false;
    }

    function mysqli_stmt_execute($stmt) {
        if ($stmt instanceof NeonPgStmt) return $stmt->execute();
        return false;
    }

    function mysqli_stmt_get_result($stmt) {
        if ($stmt instanceof NeonPgStmt) return $stmt->get_result();
        return false;
    }

    function mysqli_stmt_close($stmt) {
        if ($stmt instanceof NeonPgStmt) return $stmt->close();
        return true;
    }

    function mysqli_fetch_assoc($result) {
        if ($result instanceof NeonPgResult) return $result->fetch_assoc();
        return null;
    }

    function mysqli_fetch_array($result, $mode = MYSQLI_BOTH) {
        if ($result instanceof NeonPgResult) return $result->fetch_array($mode);
        return null;
    }

    function mysqli_fetch_row($result) {
        if ($result instanceof NeonPgResult) return $result->fetch_row();
        return null;
    }

    function mysqli_num_rows($result) {
        if ($result instanceof NeonPgResult) return $result->num_rows;
        return 0;
    }

    function mysqli_data_seek($result, $offset = 0) {
        if ($result instanceof NeonPgResult) return $result->data_seek($offset);
        return false;
    }

    function mysqli_insert_id($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->insert_id;
        return 0;
    }

    function mysqli_affected_rows($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->affected_rows;
        return 0;
    }

    function mysqli_error($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->error;
        return '';
    }

    function mysqli_real_escape_string($conn, $string) {
        if ($conn instanceof NeonPgConnection) return $conn->escape_string($string);
        return str_replace("'", "''", (string)$string);
    }

    function mysqli_set_charset($conn, $charset) {
        return true;
    }

    function mysqli_begin_transaction($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->pdo->beginTransaction();
        return false;
    }

    function mysqli_commit($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->pdo->commit();
        return false;
    }

    function mysqli_rollback($conn) {
        if ($conn instanceof NeonPgConnection) return $conn->pdo->rollBack();
        return false;
    }

    function mysqli_close($conn) {
        return true;
    }
}
