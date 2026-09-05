<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: area-list.php");
    exit;
}

$id         = (int) $_POST['id'];
$area_name  = trim($_POST['area_name']);
$area_type  = trim($_POST['area_type']);
$updated_by = $_SESSION['user_id'];
$hq_id      = NULL;
$km_from_hq = 0;

if ($area_type != "HQ") {

    $hq_id = !empty($_POST['hq_id']) ? (int) $_POST['hq_id'] : NULL;

    if (empty($hq_id)) {
        $_SESSION['error'] = "Please select Head Quarter.";
        header("Location: area-edit.php?id=" . $id);
        exit;
    }

    // KM only for sub areas
    $km_from_hq = !empty($_POST['km_from_hq']) ? (float) $_POST['km_from_hq'] : 0;
}

mysqli_begin_transaction($conn);

try {

    // Duplicate check
    $check = mysqli_query($conn, "
        SELECT id FROM tbl_areas
        WHERE area_name = '$area_name'
        AND id <> '$id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Area Name already exists.");
    }

    // Update
    mysqli_query($conn, "
        UPDATE tbl_areas SET
            area_name   = '$area_name',
            area_type   = '$area_type',
            hq_id       = " . ($hq_id ? "'$hq_id'" : "NULL") . ",
            km_from_hq  = '$km_from_hq',
            updated_by  = '$updated_by',
            updated_at  = NOW()
        WHERE id = '$id'
    ");

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Area updated successfully.";
    header("Location: area-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
    header("Location: area-edit.php?id=" . $id);
    exit;
}
