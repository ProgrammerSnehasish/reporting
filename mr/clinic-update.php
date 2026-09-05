<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: clinic-list.php");
    exit;
}

$id = $_POST['id'];

$hq_id                    = (int)$_POST['hq_id'];
$area_id                  = (int)$_POST['area_id'];
$doctor_id      = trim($_POST['doctor_id']);
$clinic_name    = trim($_POST['clinic_name']);
$contact_person = trim($_POST['contact_person']);
$mobile         = trim($_POST['mobile']);
$email          = trim($_POST['email']);
$address        = trim($_POST['address']);
$latitude       = trim($_POST['latitude']);
$longitude      = trim($_POST['longitude']);
$remarks        = trim($_POST['remarks']);

/* Validation */

if (
    empty($area_id) ||
    empty($doctor_id) ||
    empty($clinic_name)
) {

    $_SESSION['error'] = "Please fill all mandatory fields.";

    header("Location: clinic-edit.php?id=" . $id);
    exit;
}

/* Check Duplicate Clinic Name */

$check = mysqli_prepare($conn, "
    SELECT id
    FROM tbl_clinics
    WHERE clinic_name = ?
    AND doctor_id = ?
    AND id != ?
");

mysqli_stmt_bind_param(
    $check,
    "sii",
    $clinic_name,
    $doctor_id,
    $id
);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    $_SESSION['error'] = "Clinic already exists for this doctor.";

    header("Location: clinic-edit.php?id=" . $id);
    exit;
}

/* Update */

$sql = "UPDATE tbl_clinics
SET

    area_id = ?,
    hq_id = ?,
    doctor_id = ?,
    clinic_name = ?,
    contact_person = ?,
    mobile = ?,
    email = ?,
    address = ?,
    latitude = ?,
    longitude = ?,
    remarks = ?,
    updated_by = ?,
    updated_at = NOW()

WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "iiissssssssii",

    $area_id,
    $hq_id,
    $doctor_id,
    $clinic_name,
    $contact_person,
    $mobile,
    $email,
    $address,
    $latitude,
    $longitude,
    $remarks,
    $_SESSION['user_id'],
    $id

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Clinic updated successfully.";
} else {

    $_SESSION['error'] = "Unable to update clinic.";
}

header("Location: clinic-edit.php?id=" . $id);
exit;
