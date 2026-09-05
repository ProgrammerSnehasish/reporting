<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id'])) {

    header("Location: gift-list.php");
    exit;
}

$id = (int)$_GET['id'];

$updated_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    /*==============================
    CHECK PRODUCT
    ==============================*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_gifts

        WHERE id='$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Gift not found.");
    }

    /*==============================
    SOFT DELETE
    ==============================*/

    mysqli_query($conn, "

        UPDATE tbl_gifts

        SET

        status='0',

        updated_by='$updated_by',

        updated_at=NOW()

        WHERE id='$id'

    ");

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Gift deleted successfully.";

    header("Location: gift-list.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: gift-list.php");
    exit;
}
