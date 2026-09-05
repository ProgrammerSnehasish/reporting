<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: user-mapping-add.php");
    exit;
}

$manager_user_id  = (int)$_POST['manager_user_id'];
$employee_user_id = (int)$_POST['employee_user_id'];
$status           = (int)$_POST['status'];

mysqli_begin_transaction($conn);

try {

    // Employee cannot report to himself

    if ($manager_user_id == $employee_user_id) {

        throw new Exception("Employee cannot report to himself.");
    }

    // Employee already mapped

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_user_mapping

        WHERE employee_user_id='$employee_user_id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("This employee is already mapped.");
    }

    // Manager must exist

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_users

        WHERE id='$manager_user_id'
        AND status=1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Invalid Reporting Manager.");
    }

    // Employee must exist

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_users

        WHERE id='$employee_user_id'
        AND status=1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Invalid Employee.");
    }

    // Save Mapping

    mysqli_query($conn, "

        INSERT INTO tbl_user_mapping(

            manager_user_id,
            employee_user_id,
            status,
            created_at

        )

        VALUES(

            '$manager_user_id',
            '$employee_user_id',
            '$status',
            NOW()

        )

    ");

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "User Mapping created successfully.";

    header("Location: user-mapping-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: user-mapping-add.php");
    exit;
}
