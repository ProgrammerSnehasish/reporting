<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: chemist-list.php");
    exit;
}

$id = $_POST['id'];
$hq_id                    = (int)$_POST['hq_id'];
$area_id               = (int)$_POST['area_id'];
$chemist_type_id = trim($_POST['chemist_type_id']);
$chemist_name    = trim($_POST['chemist_name']);
$mobile          = trim($_POST['mobile']);
$aadhar_no       = trim($_POST['aadhar_no']);
$address         = trim($_POST['address']);
$remarks         = trim($_POST['remarks']);

/* Validation */

if (
    empty($area_id) ||
    empty($chemist_type_id) ||
    empty($chemist_name)
) {

    $_SESSION['error'] = "Please fill all mandatory fields.";

    header("Location: chemist-edit.php?id=" . $id);
    exit;
}

/* Check Duplicate Chemist */

$check = mysqli_prepare($conn, "
    SELECT id
    FROM tbl_chemists
    WHERE chemist_name = ?
    AND area_id = ?
    AND hq_id = ?
    AND id != ?
");

mysqli_stmt_bind_param(
    $check,
    "siii",
    $chemist_name,
    $area_id,
    $hq_id,
    $id
);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    $_SESSION['error'] = "Chemist already exists in this area.";

    header("Location: chemist-edit.php?id=" . $id);
    exit;
}

/* Update */

$sql = "UPDATE tbl_chemists
SET

    area_id = ?,
    hq_id = ?,
    chemist_type_id = ?,
    chemist_name = ?,
    mobile = ?,
    aadhar_no = ?,
    address = ?,
    remarks = ?,
    updated_by = ?,
    updated_at = NOW()

WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "iiisssssii",

    $area_id,
    $hq_id,
    $chemist_type_id,
    $chemist_name,
    $mobile,
    $aadhar_no,
    $address,
    $remarks,
    $_SESSION['user_id'],
    $id

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Chemist updated successfully.";
} else {

    $_SESSION['error'] = "Unable to update chemist.";
}

header("Location: chemist-edit.php?id=" . $id);
exit;
