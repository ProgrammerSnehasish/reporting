<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: area.php");
    exit;
}

$area_name  = trim($_POST['area_name']);
$area_type  = trim($_POST['area_type']);
$hq_id      = NULL;
$km_from_hq = 0;
$created_by = $_SESSION['user_id'];

if ($area_type != "HQ") {

    $hq_id = !empty($_POST['hq_id']) ? (int) $_POST['hq_id'] : NULL;

    if (empty($hq_id)) {
        $_SESSION['error'] = "Please select Head Quarter.";
        header("Location: area.php");
        exit;
    }

    // KM only for sub areas
    $km_from_hq = !empty($_POST['km_from_hq']) ? (float) $_POST['km_from_hq'] : 0;
}

mysqli_begin_transaction($conn);

try {

    // Duplicate Area Name check
    $check = mysqli_query($conn, "
        SELECT id FROM tbl_areas
        WHERE area_name = '$area_name'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Area Name already exists.");
    }

    // Generate Area Code
    $get  = mysqli_query($conn, "SELECT id FROM tbl_areas ORDER BY id DESC LIMIT 1");
    $next = (mysqli_num_rows($get) > 0) ? (mysqli_fetch_assoc($get)['id'] + 1) : 1;
    $area_code = "AR" . str_pad($next, 5, "0", STR_PAD_LEFT);

    // Insert
    $sql = "
        INSERT INTO tbl_areas
        (
            area_code,
            area_name,
            area_type,
            hq_id,
            km_from_hq,
            status,
            created_by,
            created_at
        )
        VALUES
        (
            '$area_code',
            '$area_name',
            '$area_type',
            " . ($hq_id ? "'$hq_id'" : "NULL") . ",
            '$km_from_hq',
            1,
            '$created_by',
            NOW()
        )
    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Area added successfully.";
    header("Location: area.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
    header("Location: area.php");
    exit;
}
