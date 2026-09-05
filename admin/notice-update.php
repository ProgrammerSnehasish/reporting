<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: notice-list.php");
    exit;
}

$id = (int)$_POST['id'];

$title = mysqli_real_escape_string($conn, trim($_POST['title']));
$message = mysqli_real_escape_string($conn, trim($_POST['message']));
$status = (int)$_POST['status'];

$updated_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    /*==================================
    NOTICE EXISTS
    ==================================*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_notices

        WHERE id='$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) == 0) {

        throw new Exception("Notice not found.");
    }

    /*==================================
    DUPLICATE TITLE
    ==================================*/

    $check = mysqli_query($conn, "

        SELECT id

        FROM tbl_notices

        WHERE title='$title'

        AND id<>'$id'

        LIMIT 1

    ");

    if (mysqli_num_rows($check) > 0) {

        throw new Exception("Notice Title already exists.");
    }

    /*==================================
    UPDATE
    ==================================*/

    $sql = "

    UPDATE tbl_notices SET

        title='$title',

        message='$message',

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

    $_SESSION['success'] = "Notice updated successfully.";

    header("Location: notice-edit.php?id=" . $id);
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();

    header("Location: notice-edit.php?id=" . $id);
    exit;
}
