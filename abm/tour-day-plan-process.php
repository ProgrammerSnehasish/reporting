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

$mr_id = $_SESSION['user_id'];

$tour_plan_detail_id = (int)$_POST['tour_plan_detail_id'];
$plan_date          = mysqli_real_escape_string($conn, $_POST['plan_date']);

$doctor_target   = (int)$_POST['doctor_target'];
$chemist_target  = (int)$_POST['chemist_target'];
$stockist_target = (int)$_POST['stockist_target'];

$start_time = !empty($_POST['start_time'])
    ? "'" . mysqli_real_escape_string($conn, $_POST['start_time']) . "'"
    : "NULL";

$end_time = !empty($_POST['end_time'])
    ? "'" . mysqli_real_escape_string($conn, $_POST['end_time']) . "'"
    : "NULL";

$remarks = mysqli_real_escape_string(
    $conn,
    trim($_POST['remarks'])
);

mysqli_begin_transaction($conn);

try {

    /*------------------------------------
    Check Duplicate Day Plan
    ------------------------------------*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_day_plans

        WHERE mr_id='$mr_id'

        AND tour_plan_detail_id='$tour_plan_detail_id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Day Plan already exists for this date.");
    }

    /*------------------------------------
    Insert Day Plan
    ------------------------------------*/

    mysqli_query($conn, "

        INSERT INTO tbl_day_plans
        (

            mr_id,

            tour_plan_detail_id,

            plan_date,

            doctor_target,

            chemist_target,

            stockist_target,

            start_time,

            end_time,

            remarks,

            status,

            created_at

        )

        VALUES
        (

            '$mr_id',

            '$tour_plan_detail_id',

            '$plan_date',

            '$doctor_target',

            '$chemist_target',

            '$stockist_target',

            $start_time,

            $end_time,

            '$remarks',

            1,

            NOW()

        )

    ");

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Day Plan saved successfully.";

    header("Location: tour-day-plan-add.php?id=" . $tour_plan_detail_id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: tour-day-plan-add.php?id=" . $tour_plan_detail_id);
    exit;
}
