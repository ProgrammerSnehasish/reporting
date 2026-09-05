<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: user-mapping-list.php");
    exit;
}

$id               = (int)$_POST['id'];
$manager_user_id  = (int)$_POST['manager_user_id'];
$employee_user_id = (int)$_POST['employee_user_id'];
$status           = (int)$_POST['status'];

mysqli_begin_transaction($conn);

try {

    // Self Mapping Check

    if ($manager_user_id == $employee_user_id) {

        throw new Exception("Employee cannot report to himself.");
    }

    // Check Mapping Exists

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_user_mapping

        WHERE id='$id'

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Mapping not found.");
    }

    // Duplicate Employee Mapping

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_user_mapping

        WHERE employee_user_id='$employee_user_id'
        AND id<>'$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("This employee is already mapped.");
    }

    // Manager Exists

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_users

        WHERE id='$manager_user_id'
        AND status=1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Reporting Manager not found.");
    }

    // Employee Exists

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_users

        WHERE id='$employee_user_id'
        AND status=1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Employee not found.");
    }

    // Update Mapping

    mysqli_query($conn, "

        UPDATE tbl_user_mapping

        SET

            manager_user_id='$manager_user_id',
            employee_user_id='$employee_user_id',
            status='$status',
            updated_at=NOW()

        WHERE id='$id'

    ");

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "User Mapping updated successfully.";

    header("Location: user-mapping-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: user-mapping-edit.php?id=" . $id);
    exit;
}
