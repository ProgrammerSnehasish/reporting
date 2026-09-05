<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {

    $_SESSION['error'] = "Invalid Product.";

    header("Location: product-list.php");
    exit;
}

$id = (int)$_GET['id'];

$updated_by = $_SESSION['user_id'];

/*==========================
START TRANSACTION
==========================*/

mysqli_begin_transaction($conn);

try {

    /*==========================
    CHECK PRODUCT
    ==========================*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_products

        WHERE id='$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Product not found.");
    }

    /*==========================
    SOFT DELETE
    ==========================*/

    $sql = "

        UPDATE tbl_products

        SET

        status='0',

        updated_by='$updated_by',

        updated_at=NOW()

        WHERE id='$id'

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    /*==========================
    COMMIT
    ==========================*/

    mysqli_commit($conn);

    $_SESSION['success'] = "Product deleted successfully.";

    header("Location: product-list.php");
    exit;
}

/*==========================
ERROR
==========================*/ catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: product-list.php");
    exit;
}
