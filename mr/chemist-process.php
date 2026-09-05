<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: chemist-add.php");
    exit;
}

/* Form Data */
$hq_id                  = (int)$_POST['hq_id'];
$area_id                = (int)$_POST['area_id'];
$chemist_type_id  = trim($_POST['chemist_type_id']);
$chemist_code     = trim($_POST['chemist_code']);
$chemist_name     = trim($_POST['chemist_name']);
$mobile           = trim($_POST['mobile']);
$aadhar_no        = trim($_POST['aadhar_no']);
$address          = trim($_POST['address']);
$remarks          = trim($_POST['remarks']);

/* Validation */

if (
    empty($area_id) ||
    empty($chemist_type_id) ||
    empty($chemist_name)
) {

    $_SESSION['error'] = "Area, Chemist Type and Chemist Name are required.";

    header("Location: chemist-add.php");
    exit;
}

/* Generate Chemist Code Automatically */

if (empty($chemist_code)) {

    $type = mysqli_query($conn, "
        SELECT type_name
        FROM tbl_chemist_types
        WHERE id = '$chemist_type_id'
    ");

    $typeRow = mysqli_fetch_assoc($type);

    $prefix = ($typeRow['type_name'] == "Stockist") ? "ST" : "CH";

    $code = mysqli_query($conn, "SELECT MAX(id) AS last_id FROM tbl_chemists");

    $last = mysqli_fetch_assoc($code);

    $chemist_code = $prefix . str_pad($last['last_id'] + 1, 3, "0", STR_PAD_LEFT);
}

/* Duplicate Code */

$check = mysqli_prepare($conn, "
    SELECT id
    FROM tbl_chemists
    WHERE chemist_code = ?
");

mysqli_stmt_bind_param($check, "s", $chemist_code);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    $_SESSION['error'] = "Chemist Code already exists.";

    header("Location: chemist-add.php");
    exit;
}

/* Insert */

$sql = "INSERT INTO tbl_chemists (

            area_id,
            hq_id,
            chemist_type_id,
            chemist_code,
            chemist_name,
            mobile,
            aadhar_no,
            address,
            remarks,
            status,
            created_by

        )

        VALUES (

            ?,?,?,?,?,?,?,?,?,1,?

        )";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "iiissssssi",

    $area_id,
    $hq_id,
    $chemist_type_id,
    $chemist_code,
    $chemist_name,
    $mobile,
    $aadhar_no,
    $address,
    $remarks,
    $_SESSION['user_id']

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Chemist added successfully.";
} else {

    $_SESSION['error'] = "Unable to add Chemist.";
}

header("Location: chemist-add.php");
exit;
