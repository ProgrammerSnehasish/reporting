<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: product-add.php");
    exit;
}

/*==========================
GET FORM DATA
==========================*/

$product_name      = mysqli_real_escape_string($conn, trim($_POST['product_name']));
$brand_name        = mysqli_real_escape_string($conn, trim($_POST['brand_name']));
$generic_name      = mysqli_real_escape_string($conn, trim($_POST['generic_name']));

$category_id       = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : "NULL";
$division_id       = !empty($_POST['division_id']) ? (int)$_POST['division_id'] : "NULL";

$dosage_form       = mysqli_real_escape_string($conn, trim($_POST['dosage_form']));
$strength          = mysqli_real_escape_string($conn, trim($_POST['strength']));
$pack              = mysqli_real_escape_string($conn, trim($_POST['pack']));

$mrp               = !empty($_POST['mrp']) ? $_POST['mrp'] : 0;
$ptr               = !empty($_POST['ptr']) ? $_POST['ptr'] : 0;
$pts               = !empty($_POST['pts']) ? $_POST['pts'] : 0;

$gst               = !empty($_POST['gst']) ? $_POST['gst'] : 0;

$hsn_code          = mysqli_real_escape_string($conn, trim($_POST['hsn_code']));
$bonus_offer       = mysqli_real_escape_string($conn, trim($_POST['bonus_offer']));
$manufacturer      = mysqli_real_escape_string($conn, trim($_POST['manufacturer']));

$stock_quantity    = !empty($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;

$is_focus_product  = (int)$_POST['is_focus_product'];

$remarks           = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status            = (int)$_POST['status'];

$created_by = $_SESSION['user_id'];

/*==========================
AUTO PRODUCT CODE
==========================*/

$get = mysqli_query($conn, "
SELECT MAX(id) last_id
FROM tbl_products
");

$row = mysqli_fetch_assoc($get);

$next = $row['last_id'] + 1;

if ($next == 0) {
    $next = 1;
}

$product_code = "PRD" . str_pad($next, 5, "0", STR_PAD_LEFT);

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
    FROM tbl_products
    WHERE product_name='$product_name'
    LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Product Name already exists.");
    }

    /*==========================
    DUPLICATE PRODUCT CODE
    ==========================*/

    $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_products
    WHERE product_code='$product_code'
    LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        throw new Exception("Product Code already exists.");
    }

    /*==========================
    INSERT PRODUCT
    ==========================*/

    $sql = "

    INSERT INTO tbl_products(

        product_code,
        product_name,
        brand_name,
        generic_name,
        category_id,
        division_id,
        dosage_form,
        strength,
        pack,
        mrp,
        ptr,
        pts,
        gst,
        hsn_code,
        bonus_offer,
        manufacturer,
        stock_quantity,
        is_focus_product,
        remarks,
        status,
        created_by,
        created_at

    )

    VALUES(

        '$product_code',

        '$product_name',

        '$brand_name',

        '$generic_name',

        " . ($category_id == "NULL" ? "NULL" : "'$category_id'") . ",

        " . ($division_id == "NULL" ? "NULL" : "'$division_id'") . ",

        '$dosage_form',

        '$strength',

        '$pack',

        '$mrp',

        '$ptr',

        '$pts',

        '$gst',

        '$hsn_code',

        '$bonus_offer',

        '$manufacturer',

        '$stock_quantity',

        '$is_focus_product',

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

    $_SESSION['success'] = "Product added successfully.";

    header("Location: product-list.php");
    exit;
}

/*==========================
ERROR
==========================*/ catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: product-add.php");
    exit;
}
