<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

mysqli_begin_transaction($conn);

try {

    $sender_user_id = $_SESSION['user_id'];

    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $receiver_type = $_POST['receiver_type'];

    //==========================
    // Insert Message
    //==========================

    $sql = "
    INSERT INTO tbl_messages
    (
        sender_user_id,
        subject,
        message
    )
    VALUES
    (
        '$sender_user_id',
        '$subject',
        '$message'
    )
    ";

    if (!mysqli_query($conn, $sql)) {
        throw new Exception(mysqli_error($conn));
    }

    $message_id = mysqli_insert_id($conn);

    //==========================
    // Individual / Multiple MR
    //==========================

    if ($receiver_type == "individual" || $receiver_type == "multiple") {

        foreach ($_POST['receiver_ids'] as $receiver_user_id) {

            $receiver_user_id = (int)$receiver_user_id;

            $sql = "
            INSERT INTO tbl_message_receivers
            (
                message_id,
                receiver_user_id
            )
            VALUES
            (
                '$message_id',
                '$receiver_user_id'
            )
            ";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception(mysqli_error($conn));
            }
        }
    }

    //==========================
    // ALL MR
    //==========================

    if ($receiver_type == "all") {

        $mrs = mysqli_query($conn, "
        SELECT id
        FROM tbl_users
        WHERE role_id='3'
        AND status='1'
        ");

        while ($mr = mysqli_fetch_assoc($mrs)) {

            $receiver_user_id = $mr['id'];

            $sql = "
            INSERT INTO tbl_message_receivers
            (
                message_id,
                receiver_user_id
            )
            VALUES
            (
                '$message_id',
                '$receiver_user_id'
            )
            ";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception(mysqli_error($conn));
            }
        }
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Message sent successfully.";
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
}

header("Location:message-add.php");
exit;
