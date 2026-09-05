<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";

require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: change-password.php");
    exit;
}

$current = trim($_POST['current_password']);
$new = trim($_POST['new_password']);
$confirm = trim($_POST['confirm_password']);

if ($new != $confirm) {

    $_SESSION['error'] = "New password and confirm password do not match.";

    header("Location: change-password.php");
    exit;
}

$sql = "SELECT password
        FROM tbl_users
        WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!password_verify($current, $user['password'])) {

    $_SESSION['error'] = "Current password is incorrect.";

    header("Location: change-password.php");
    exit;
}

$password = password_hash($new, PASSWORD_DEFAULT);

$sql = "UPDATE tbl_users
        SET password=?
        WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "si", $password, $_SESSION['user_id']);

mysqli_stmt_execute($stmt);

$_SESSION['success'] = "Password changed successfully.";

header("Location: employee-profile.php");

exit;
