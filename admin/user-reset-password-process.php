<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location:user-list.php");
    exit;
}

$id = (int)$_POST['id'];

$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password != $confirm) {

    $_SESSION['error'] = "Password and Confirm Password do not match.";

    header("Location:user-reset-password.php?id=" . $id);
    exit;
}

$password = password_hash($password, PASSWORD_DEFAULT);

mysqli_query($conn, "

UPDATE tbl_users

SET

password='$password',
failed_login_attempts=0,
is_locked=0,
is_first_login=1,
password_changed_at=NULL,
updated_at=NOW()

WHERE id='$id'

");

if (mysqli_error($conn)) {

    $_SESSION['error'] = mysqli_error($conn);
} else {

    $_SESSION['success'] = "Password reset successfully.";
}

header("Location: user-reset-password.php?id=" . $id);

exit;
