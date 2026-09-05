<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: user-add.php");
    exit;
}

$employee_id = (int)$_POST['employee_id'];
$role_id     = (int)$_POST['role_id'];

$username = mysqli_real_escape_string($conn, trim($_POST['username']));

$email = trim($_POST['email']);
$email = empty($email) ? NULL : mysqli_real_escape_string($conn, $email);

$mobile = trim($_POST['mobile']);
$mobile = empty($mobile) ? NULL : mysqli_real_escape_string($conn, $mobile);

$password         = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

$status = (int)$_POST['status'];

if ($password != $confirm_password) {

    $_SESSION['error'] = "Password and Confirm Password do not match.";

    header("Location: user-add.php");
    exit;
}

$password = password_hash($password, PASSWORD_DEFAULT);

mysqli_begin_transaction($conn);

try {

    // Employee already mapped

    $check = mysqli_query($conn, "
        SELECT id
        FROM tbl_users
        WHERE employee_id='$employee_id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("User already exists for this employee.");
    }

    // Username Duplicate

    $check = mysqli_query($conn, "
        SELECT id
        FROM tbl_users
        WHERE username='$username'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Username already exists.");
    }

    // Email Duplicate

    if ($email !== NULL) {

        $check = mysqli_query($conn, "
            SELECT id
            FROM tbl_users
            WHERE email='$email'
            LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            throw new Exception("Email already exists.");
        }
    }

    // Mobile Duplicate

    if ($mobile !== NULL) {

        $check = mysqli_query($conn, "
            SELECT id
            FROM tbl_users
            WHERE mobile='$mobile'
            LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            throw new Exception("Mobile already exists.");
        }
    }

    $sql = "

    INSERT INTO tbl_users(

        employee_id,
        role_id,
        username,
        email,
        mobile,
        password,
        failed_login_attempts,
        is_locked,
        is_first_login,
        status,
        created_at

    )

    VALUES(

        '$employee_id',
        '$role_id',
        '$username',
        " . ($email !== NULL ? "'$email'" : "NULL") . ",
        " . ($mobile !== NULL ? "'$mobile'" : "NULL") . ",
        '$password',
        0,
        0,
        1,
        '$status',
        NOW()

    )

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "User created successfully.";

    header("Location: user-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: user-add.php");
    exit;
}
