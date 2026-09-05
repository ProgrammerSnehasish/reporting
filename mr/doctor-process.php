<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: doctor-add.php");
    exit;
}

$hq_id                  = (int)$_POST['hq_id'];
$area_id                = (int)$_POST['area_id'];
$doctor_name            = trim($_POST['doctor_name']);
$gender                 = trim($_POST['gender']);
$mobile                 = trim($_POST['mobile']);
$alternate_mobile       = trim($_POST['alternate_mobile']);
$email                  = trim($_POST['email']);
$aadhar_no              = trim($_POST['aadhar_no']);
$date_of_birth          = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
$anniversary_date       = !empty($_POST['anniversary_date']) ? $_POST['anniversary_date'] : NULL;
$category_id            = (int)$_POST['category_id'];
$degree_id              = (int)$_POST['degree_id'];
$specialization_id      = (int)$_POST['specialization_id'];
$business_potential_id  = !empty($_POST['business_potential_id']) ? (int)$_POST['business_potential_id'] : "NULL";
$hospital_name          = trim($_POST['hospital_name']);
$clinic_name            = trim($_POST['clinic_name']);
$address                = trim($_POST['address']);
$latitude               = trim($_POST['latitude']);
$longitude              = trim($_POST['longitude']);
$remarks                = trim($_POST['remarks']);
$status                 = (int)$_POST['status'];

$created_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    // Duplicate Doctor Name in Same Area

    $check = mysqli_query($conn, "
        SELECT id
        FROM tbl_doctors
        WHERE doctor_name='$doctor_name'
        AND area_id='$area_id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Doctor already exists in this Area.");
    }

    // Duplicate Mobile

    if (!empty($mobile)) {

        $check = mysqli_query($conn, "
            SELECT id
            FROM tbl_doctors
            WHERE mobile='$mobile'
            LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            throw new Exception("Mobile number already exists.");
        }
    }

    // Doctor Code Generate

    $get = mysqli_query($conn, "
        SELECT id
        FROM tbl_doctors
        ORDER BY id DESC
        LIMIT 1
    ");

    if (mysqli_num_rows($get) > 0) {

        $last = mysqli_fetch_assoc($get);

        $next = $last['id'] + 1;
    } else {

        $next = 1;
    }

    $doctor_code = "DOC" . str_pad($next, 5, "0", STR_PAD_LEFT);

    mysqli_query($conn, "
        INSERT INTO tbl_doctors(

            area_id,
            doctor_code,
            doctor_name,
            gender,
            mobile,
            alternate_mobile,
            email,
            date_of_birth,
            anniversary_date,
            aadhar_no,
            category_id,
            degree_id,
            specialization_id,
            business_potential_id,
            hospital_name,
            clinic_name,
            address,
            latitude,
            longitude,
            remarks,
            status,
            created_by,
            created_at

        )

        VALUES(

            '$area_id',
            '$doctor_code',
            '$doctor_name',
            '$gender',
            '$mobile',
            '$alternate_mobile',
            '$email',
            " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ",
            " . ($anniversary_date ? "'$anniversary_date'" : "NULL") . ",
            '$aadhar_no',
            '$category_id',
            '$degree_id',
            '$specialization_id',
            " . ($business_potential_id == "NULL" ? "NULL" : "'$business_potential_id'") . ",
            '$hospital_name',
            '$clinic_name',
            '$address',
            '$latitude',
            '$longitude',
            '$remarks',
            '$status',
            '$created_by',
            NOW()

        )
    ");

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Doctor added successfully.";

    header("Location: doctor-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: doctor-add.php");
    exit;
}
