<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: tour-day-plan-list.php");
    exit;
}

$id = (int)$_POST['id'];

$doctor_target   = (int)$_POST['doctor_target'];
$chemist_target  = (int)$_POST['chemist_target'];
$stockist_target = (int)$_POST['stockist_target'];

$start_time = !empty($_POST['start_time'])
    ? mysqli_real_escape_string($conn, $_POST['start_time'])
    : NULL;

$end_time = !empty($_POST['end_time'])
    ? mysqli_real_escape_string($conn, $_POST['end_time'])
    : NULL;

$remarks = mysqli_real_escape_string(
    $conn,
    trim($_POST['remarks'])
);

mysqli_begin_transaction($conn);

try {

    /* Check Day Plan */

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_day_plans

        WHERE id='$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Day Plan not found.");
    }

    $sql = "

    UPDATE tbl_day_plans

    SET

        doctor_target='$doctor_target',

        chemist_target='$chemist_target',

        stockist_target='$stockist_target',

        start_time=" . ($start_time ? "'$start_time'" : "NULL") . ",

        end_time=" . ($end_time ? "'$end_time'" : "NULL") . ",

        remarks='$remarks',

        updated_at=NOW()

    WHERE id='$id'

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Day Plan updated successfully.";

    header("Location: tour-day-plan-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: tour-day-plan-edit.php?id=" . $id);
    exit;
}
