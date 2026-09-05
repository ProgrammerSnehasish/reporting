<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: target-assign.php");
    exit;
}

$target_master_id = $_POST['target_master_id'];
$mr_user_id       = $_POST['mr_user_id'];

$product_ids   = $_POST['product_id'];
$target_qtys   = $_POST['target_qty'];
$target_values = $_POST['target_value'];

$created_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    // পুরানো Target Delete
    mysqli_query($conn, "
        DELETE FROM tbl_target_details
        WHERE
        target_master_id='$target_master_id'
        AND mr_user_id='$mr_user_id'
    ");

    $insert = mysqli_prepare($conn, "
        INSERT INTO tbl_target_details
        (
            target_master_id,
            mr_user_id,
            product_id,
            target_qty,
            target_value,
            created_by
        )
        VALUES
        (
            ?,?,?,?,?,?
        )
    ");

    for ($i = 0; $i < count($product_ids); $i++) {

        $product_id   = $product_ids[$i];
        $target_qty   = $target_qtys[$i];
        $target_value = $target_values[$i];

        // Qty এবং Value দুটোই 0 হলে Skip
        if ($target_qty == 0 && $target_value == 0) {
            continue;
        }

        mysqli_stmt_bind_param(
            $insert,
            "iiiidi",
            $target_master_id,
            $mr_user_id,
            $product_id,
            $target_qty,
            $target_value,
            $created_by
        );

        mysqli_stmt_execute($insert);
    }

    mysqli_commit($conn);

    $_SESSION['success'] = "Target Assigned Successfully.";
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
}

header("Location: target-assign.php");
exit;
