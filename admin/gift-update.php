<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: gift-list.php");
    exit;
}

$id = (int)$_POST['id'];

/*==========================
GET DATA
==========================*/

$gift_name      = mysqli_real_escape_string($conn, trim($_POST['gift_name']));
$gift_category  = mysqli_real_escape_string($conn, trim($_POST['gift_category']));
$brand_name     = mysqli_real_escape_string($conn, trim($_POST['brand_name']));
$gift_value     = mysqli_real_escape_string($conn, trim($_POST['gift_value']));
$qty            = mysqli_real_escape_string($conn, trim($_POST['qty']));
$remarks      = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status       = (int)$_POST['status'];

$updated_by = $_SESSION['user_id'];


/*==========================
CHECK PRODUCT EXISTS
==========================*/

$get = mysqli_query($conn, "

SELECT id

FROM tbl_gifts

WHERE id='$id'

LIMIT 1

");

if (mysqli_num_rows($get) == 0) {

    $_SESSION['error'] = "Gift not found.";

    header("Location: gift-list.php");
    exit;
}


/*==========================
START TRANSACTION
==========================*/

mysqli_begin_transaction($conn);

try {

    /*==================================
    DUPLICATE PRODUCT NAME
    ==================================*/

    if (!empty($product_name)) {

        $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_gifts

        WHERE gift_name='$gift_name'

        AND id<>'$id'

        LIMIT 1

        ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("Gift Name already exists.");
        }
    }

    /*==================================
    UPDATE PRODUCT
    ==================================*/

    $sql = "

    UPDATE tbl_gifts SET

    gift_name='$gift_name',

    brand_name='$brand_name',

    gift_category='$gift_category',

    gift_value='$gift_value',

    stock_quantity='$qty',

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

    /*==================================
    COMMIT
    ==================================*/

    mysqli_commit($conn);

    $_SESSION['success'] = "Gift updated successfully.";

    header("Location: gift-edit.php?id=" . $id);
    exit;
}

/*==================================
ERROR
==================================*/ catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: gift-edit.php?id=" . $id);
    exit;
}
