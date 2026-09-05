<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: notice-add.php");
    exit;
}

/*==========================
GET DATA
==========================*/

$title       = mysqli_real_escape_string($conn, trim($_POST['title']));
$message     = mysqli_real_escape_string($conn, trim($_POST['message']));
$notice_type = !empty($_POST['notice_type']) ? $_POST['notice_type'] : NULL;
$start_date  = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
$end_date    = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
$status      = (int)$_POST['status'];
$created_by  = $_SESSION['user_id'];
mysqli_begin_transaction($conn);

try {

    /*==========================
    DUPLICATE TITLE
    ==========================*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_notices

        WHERE title='$title'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Notice Title already exists.");
    }

    /*==========================
    INSERT
    ==========================*/

    $sql = "

    INSERT INTO tbl_notices(

        title,
        message,
        notice_type,
        start_date,
        end_date,
        status,
        created_by,
        created_at

    )

    VALUES(

        '$title',
        '$message',
        '$notice_type',

        " . ($start_date ? "'$start_date'" : "NULL") . ",

        " . ($end_date ? "'$end_date'" : "NULL") . ",

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

    $_SESSION['success'] = "Notice added successfully.";

    header("Location: notice-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: notice-add.php");
    exit;
}
