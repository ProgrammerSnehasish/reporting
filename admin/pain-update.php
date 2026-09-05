<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: pain-list.php");
    exit;
}

$id = (int)$_POST['id'];

/*==========================
GET DATA
==========================*/

$pain_product_name = mysqli_real_escape_string($conn, trim($_POST['pain_product_name']));
$description  = mysqli_real_escape_string($conn, trim($_POST['description']));
$remarks  = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status       = (int)$_POST['status'];

$updated_by = $_SESSION['user_id'];

/*==========================
CHECK PRODUCT
==========================*/

$get = mysqli_query($conn, "

SELECT id

FROM tbl_pain_management_products

WHERE id='$id'

LIMIT 1

");

if (mysqli_num_rows($get) == 0) {

    $_SESSION['error'] = "Product not found.";

    header("Location: pain-list.php");
    exit;
}

/*==========================
START TRANSACTION
==========================*/

mysqli_begin_transaction($conn);

try {

    /*==========================
    DUPLICATE PRODUCT NAME
    ==========================*/

    $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_pain_management_products
    WHERE pain_product_name='$pain_product_name'
    AND id<>'$id'
    LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Product Name already exists.");
    }

    /*==========================
    UPDATE PRODUCT
    ==========================*/

    $sql = "
    UPDATE tbl_pain_management_products
    SET
    pain_product_name = '$pain_product_name',
    description = '$description',
    remarks = '$remarks',
    status = '$status',
    updated_by = '$updated_by',
    updated_at = NOW()
    WHERE id = '$id'
    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Pain updated successfully.";

    header("Location: pain-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: pain-edit.php?id=" . $id);
    exit;
}
