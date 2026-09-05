<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: gift-add.php");
    exit;
}

/*==========================
GET FORM DATA
==========================*/

$gift_name      = mysqli_real_escape_string($conn, trim($_POST['gift_name']));
$gift_category  = mysqli_real_escape_string($conn, trim($_POST['gift_category']));
$brand_name     = mysqli_real_escape_string($conn, trim($_POST['brand_name']));
$gift_value     = mysqli_real_escape_string($conn, trim($_POST['gift_value']));
$qty            = mysqli_real_escape_string($conn, trim($_POST['qty']));
$remarks        = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status         = (int)$_POST['status'];
$created_by = $_SESSION['user_id'];


/*==========================
AUTO PRODUCT CODE
==========================*/

$get = mysqli_query($conn, "
SELECT MAX(id) last_id
FROM tbl_gifts
");

$row = mysqli_fetch_assoc($get);

$next = $row['last_id'] + 1;

if ($next == 0) {

    $next = 1;
}

$gift_code = "GFT" . str_pad($next, 5, "0", STR_PAD_LEFT);


/*==========================
START TRANSACTION
==========================*/

mysqli_begin_transaction($conn);

try {

    /*==================================
    DUPLICATE PRODUCT NAME
    ==================================*/

    $check = mysqli_query($conn, "

    SELECT id

    FROM tbl_gifts

    WHERE gift_name='$gift_name'

    LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Gift Name already exists.");
    }


    /*==================================
    DUPLICATE PRODUCT CODE
    ==================================*/

    $check = mysqli_query($conn, "

    SELECT id

    FROM tbl_gifts

    WHERE gift_code='$gift_code'

    LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Gift Code already exists.");
    }


    /*==================================
    INSERT PRODUCT
    ==================================*/

    $sql = "

    INSERT INTO tbl_gifts(

        gift_code,

        gift_name,

        brand_name,

        gift_category,

        gift_value,

        stock_quantity,

        remarks,

        status,

        created_by,

        created_at

    )

    VALUES(

        '$gift_code',

        '$gift_name',

        '$brand_name',

        '$gift_category',

        '$gift_value',

        '$qty',

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

    $_SESSION['success'] = "Gift added successfully.";

    header("Location: gift-add.php");
    exit;
}

/*==================================
ERROR
==================================*/ catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: gift-add.php");
    exit;
}
