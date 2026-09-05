<?php

require_once "../config/config.php";
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['error'] = "Username and Password are required.";
    header("Location: login.php");
    exit;
}

$sql = "SELECT
            u.*,
            r.role_name,
            r.role_code,
            e.first_name,
            e.last_name
        FROM tbl_users u
        INNER JOIN tbl_roles r
            ON r.id = u.role_id
        LEFT JOIN tbl_employees e
            ON e.id = u.employee_id
        WHERE u.username = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['error'] = "Invalid Username or Password.";
    header("Location: login.php");
    exit;
}

if ((int)$user['status'] !== 1) {
    $_SESSION['error'] = "Your account is inactive.";
    header("Location: login.php");
    exit;
}

if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Invalid Username or Password.";
    header("Location: login.php");
    exit;
}

/* Prevent session fixation */
session_regenerate_id(true);

/* Update Login Info */
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$device = $_SERVER['HTTP_USER_AGENT'] ?? '';

$update = mysqli_prepare($conn, "
    UPDATE tbl_users
    SET last_login = NOW(),
        login_ip = ?,
        login_device = ?
    WHERE id = ?
");

mysqli_stmt_bind_param($update, "ssi", $ip, $device, $user['id']);
mysqli_stmt_execute($update);

/* Create Session */
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['employee_id'] = $user['employee_id'];
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['role'] = $user['role_code'];
$_SESSION['username'] = $user['username'];
$_SESSION['employee_name'] = trim(($user['first_name'] ?? '') . " " . ($user['last_name'] ?? ''));

/* Redirect */
switch ($user['role_code']) {

    case 'superadmin':
        header("Location: ../superadmin/dashboard.php");
        break;

    case 'admin':
        header("Location: ../admin/dashboard.php");
        break;

    case 'mr':
        header("Location: ../mr/dashboard.php");
        break;

    case 'abm':
        header("Location: ../abm/dashboard.php");
        break;

    case 'rbm':
        header("Location: ../rbm/dashboard.php");
        break;

    case 'zsm':
        header("Location: ../zsm/dashboard.php");
        break;

    case 'nsm':
        header("Location: ../nsm/dashboard.php");
        break;

    case 'franchise':
        header("Location: ../franchise/dashboard.php");
        break;

    default:
        session_destroy();
        $_SESSION['error'] = "Invalid role.";
        header("Location: login.php");
        break;
}

exit;
