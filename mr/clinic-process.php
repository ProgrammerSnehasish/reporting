<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: clinic-add.php");
    exit;
}

/* Form Data */

$hq_id                  = (int)$_POST['hq_id'];
$area_id                = (int)$_POST['area_id'];
$doctor_id      = trim($_POST['doctor_id']);
$clinic_code    = trim($_POST['clinic_code']);
$clinic_name    = trim($_POST['clinic_name']);
$contact_person = trim($_POST['contact_person']);
$mobile         = trim($_POST['mobile']);
$email          = trim($_POST['email']);
$address        = trim($_POST['address']);
$latitude       = trim($_POST['latitude']);
$longitude      = trim($_POST['longitude']);
$remarks        = trim($_POST['remarks']);

/* Validation */

if (empty($area_id) || empty($doctor_id) || empty($clinic_name)) {

    $_SESSION['error'] = "Area, Doctor and Clinic Name are required.";

    header("Location: clinic-add.php");
    exit;
}

/* Generate Clinic Code Automatically */

if (empty($clinic_code)) {

    $code = mysqli_query($conn, "SELECT MAX(id) AS last_id FROM tbl_clinics");

    $last = mysqli_fetch_assoc($code);

    $clinic_code = "CL" . str_pad($last['last_id'] + 1, 3, "0", STR_PAD_LEFT);
}

/* Duplicate Clinic Code */

$check = mysqli_prepare($conn, "
    SELECT id
    FROM tbl_clinics
    WHERE clinic_code=?
");

mysqli_stmt_bind_param($check, "s", $clinic_code);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    $_SESSION['error'] = "Clinic Code already exists.";

    header("Location: clinic-add.php");
    exit;
}

/* Insert */

$sql = "INSERT INTO tbl_clinics (

            area_id,
            hq_id,
            doctor_id,
            clinic_code,
            clinic_name,
            contact_person,
            mobile,
            email,
            address,
            latitude,
            longitude,
            remarks,
            status,
            created_by

        )

        VALUES (

            ?,?,?,?,?,?,?,?,?,?,?,?,1,?

        )";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "iiisssssssssi",

    $area_id,
    $hq_id,
    $doctor_id,
    $clinic_code,
    $clinic_name,
    $contact_person,
    $mobile,
    $email,
    $address,
    $latitude,
    $longitude,
    $remarks,
    $_SESSION['user_id']

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Clinic added successfully.";
} else {

    $_SESSION['error'] = "Unable to add clinic.";
}

header("Location: clinic-add.php");
exit;
