<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: pain-add.php");
    exit;
}

/*==========================
GET FORM DATA
==========================*/

$pain_product_name = mysqli_real_escape_string($conn, trim($_POST['pain_product_name']));
$description  = mysqli_real_escape_string($conn, trim($_POST['description']));
$remarks  = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status       = (int)$_POST['status'];

$created_by = $_SESSION['user_id'];

/*==========================
VALIDATION
==========================*/

if (empty($pain_product_name)) {

    $_SESSION['error'] = "Pain Product Name is required.";

    header("Location: pain-add.php");
    exit;
}

/*==========================
AUTO PRODUCT CODE
==========================*/

$get = mysqli_query($conn, "
SELECT MAX(id) AS last_id
FROM tbl_pain_management_products
");

$row = mysqli_fetch_assoc($get);

$next = $row['last_id'] + 1;

if ($next <= 0) {
    $next = 1;
}

$product_code = "PM" . str_pad($next, 5, "0", STR_PAD_LEFT);

/*==========================
START TRANSACTION
==========================*/

mysqli_begin_transaction($conn);

try {

    /* Duplicate Product */

    $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_pain_management_products
    WHERE pain_product_name='$pain_product_name'
    LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Pain Product already exists.");
    }

    /* Insert */

    $sql = "

    INSERT INTO tbl_pain_management_products(

        product_code,
        pain_product_name,
        description,
        remarks,
        status,
        created_by,
        created_at

    )

    VALUES(

        '$product_code',
        '$pain_product_name',
        '$description',
        '$remarks',
        '$status',
        '$created_by',
        NOW()

    )

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Pain Product added successfully.";

    header("Location:pain-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location:pain-add.php");
    exit;
}
