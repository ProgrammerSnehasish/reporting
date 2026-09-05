<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: product-list.php");
    exit;
}

$id = (int)$_POST['id'];

/*==========================
GET DATA
==========================*/

$product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
$brand_name   = mysqli_real_escape_string($conn, trim($_POST['brand_name']));

$category_id  = (int)$_POST['category_id'];
$division_id  = (int)$_POST['division_id'];

$dosage_form  = mysqli_real_escape_string($conn, trim($_POST['dosage_form']));
$strength     = mysqli_real_escape_string($conn, trim($_POST['strength']));
$pack         = mysqli_real_escape_string($conn, trim($_POST['pack']));

$mrp = !empty($_POST['mrp']) ? $_POST['mrp'] : 0;
$ptr = !empty($_POST['ptr']) ? $_POST['ptr'] : 0;
$pts = !empty($_POST['pts']) ? $_POST['pts'] : 0;

$stock_quantity = (int)$_POST['stock_quantity'];

$remarks = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status  = (int)$_POST['status'];

$updated_by = $_SESSION['user_id'];

/*==========================
CHECK PRODUCT
==========================*/

$get = mysqli_query($conn, "

SELECT id

FROM tbl_products

WHERE id='$id'

LIMIT 1

");

if (mysqli_num_rows($get) == 0) {

    $_SESSION['error'] = "Product not found.";

    header("Location: product-list.php");
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

    FROM tbl_products

    WHERE product_name='$product_name'

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

    UPDATE tbl_products SET

    product_name='$product_name',

    brand_name='$brand_name',

    category_id='$category_id',

    division_id='$division_id',

    dosage_form='$dosage_form',

    strength='$strength',

    pack='$pack',

    mrp='$mrp',

    ptr='$ptr',

    pts='$pts',

    stock_quantity='$stock_quantity',

    remarks='$remarks',

    status='$status',

    updated_by='$updated_by',

    updated_at=NOW()

    WHERE id='$id'

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Product updated successfully.";

    header("Location: product-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: product-edit.php?id=" . $id);
    exit;
}
