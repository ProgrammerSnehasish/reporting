<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: doctor-list.php");
    exit;
}

$id                    = (int)$_POST['id'];
$area_id               = (int)$_POST['area_id'];
$doctor_name           = mysqli_real_escape_string($conn, trim($_POST['doctor_name']));
$gender                = mysqli_real_escape_string($conn, trim($_POST['gender']));
$mobile                = mysqli_real_escape_string($conn, trim($_POST['mobile']));
$alternate_mobile      = mysqli_real_escape_string($conn, trim($_POST['alternate_mobile']));
$email                 = mysqli_real_escape_string($conn, trim($_POST['email']));
$date_of_birth         = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
$anniversary_date      = !empty($_POST['anniversary_date']) ? $_POST['anniversary_date'] : NULL;
$aadhar_no             = mysqli_real_escape_string($conn, trim($_POST['aadhar_no']));
$category_id           = (int)$_POST['category_id'];
$degree_id             = (int)$_POST['degree_id'];
$specialization_id     = (int)$_POST['specialization_id'];
$business_potential_id = !empty($_POST['business_potential_id']) ? (int)$_POST['business_potential_id'] : "NULL";
$hospital_name         = mysqli_real_escape_string($conn, trim($_POST['hospital_name']));
$clinic_name           = mysqli_real_escape_string($conn, trim($_POST['clinic_name']));
$address               = mysqli_real_escape_string($conn, trim($_POST['address']));
$latitude              = mysqli_real_escape_string($conn, trim($_POST['latitude']));
$longitude             = mysqli_real_escape_string($conn, trim($_POST['longitude']));
$remarks               = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status                = (int)$_POST['status'];

$updated_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    // Duplicate Doctor Name in Same Area

    $check = mysqli_query($conn, "
        SELECT id
        FROM tbl_doctors
        WHERE doctor_name='$doctor_name'
        AND area_id='$area_id'
        AND id<>'$id'
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
            AND id<>'$id'
        ");

        if (mysqli_num_rows($check) > 0) {
            throw new Exception("Mobile number already exists.");
        }
    }

    mysqli_query($conn, "

        UPDATE tbl_doctors

        SET

            area_id='$area_id',
            doctor_name='$doctor_name',
            gender='$gender',
            mobile='$mobile',
            alternate_mobile='$alternate_mobile',
            email='$email',
            date_of_birth=" . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ",
            anniversary_date=" . ($anniversary_date ? "'$anniversary_date'" : "NULL") . ",
            aadhar_no='$aadhar_no',
            category_id='$category_id',
            degree_id='$degree_id',
            specialization_id='$specialization_id',
            business_potential_id=" . ($business_potential_id == "NULL" ? "NULL" : "'$business_potential_id'") . ",
            hospital_name='$hospital_name',
            clinic_name='$clinic_name',
            address='$address',
            latitude='$latitude',
            longitude='$longitude',
            remarks='$remarks',
            status='$status',
            updated_by='$updated_by',
            updated_at=NOW()

        WHERE id='$id'

    ");

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Doctor updated successfully.";

    header("Location: doctor-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: doctor-edit.php?id=" . $id);
    exit;
}
