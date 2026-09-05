<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: dcr-list.php");
    exit;
}

/* Form Data */

$dcr_id           = trim($_POST['dcr_id']);
$area_id          = trim($_POST['area_id']);

$travel_expense   = trim($_POST['travel_expense']);
$da_expense       = trim($_POST['da_expense']);
$hotel_expense    = trim($_POST['hotel_expense']);
$food_expense     = trim($_POST['food_expense']);
$other_expense    = trim($_POST['other_expense']);

$remarks          = trim($_POST['remarks']);

/* Empty value = 0 */

$travel_expense = !empty($travel_expense) ? $travel_expense : 0;
$da_expense     = !empty($da_expense) ? $da_expense : 0;
$hotel_expense  = !empty($hotel_expense) ? $hotel_expense : 0;
$food_expense   = !empty($food_expense) ? $food_expense : 0;
$other_expense  = !empty($other_expense) ? $other_expense : 0;

/* Total Expense */

$total_expense =
    $travel_expense +
    $da_expense +
    $hotel_expense +
    $food_expense +
    $other_expense;

/* Validation */

// if (empty($dcr_id) || empty($area_id)) {

//     $_SESSION['error'] = "DCR and Area are required.";

//     header("Location: expense-add.php?id=" . $dcr_id);
//     exit;
// }

/* Check Duplicate Expense */

$check = mysqli_prepare($conn, "
    SELECT id
    FROM tbl_dcr_expenses
    WHERE dcr_id=?
");

mysqli_stmt_bind_param($check, "i", $dcr_id);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    $_SESSION['error'] = "Expense already submitted for this DCR.";

    header("Location: expense-add.php?id=" . $dcr_id);
    exit;
}

/* Insert */

$sql = "INSERT INTO tbl_dcr_expenses(

            dcr_id,
            mr_id,
            area_id,
            travel_expense,
            da_expense,
            hotel_expense,
            food_expense,
            other_expense,
            total_expense,
            remarks,
            status

        )

        VALUES(

            ?,?,?,?,?,?,?,?,?,?,1

        )";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "iiidddddds",

    $dcr_id,
    $_SESSION['user_id'],
    $area_id,
    $travel_expense,
    $da_expense,
    $hotel_expense,
    $food_expense,
    $other_expense,
    $total_expense,
    $remarks

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Expense submitted successfully.";
} else {

    $_SESSION['error'] = "Unable to submit expense.";
}

header("Location: expense-add.php?id=" . $dcr_id);
exit;
