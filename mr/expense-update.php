<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: expense-list.php");
    exit;
}

/* Form Data */

$id = (int)$_POST['id'];

$dcr_id = trim($_POST['dcr_id']);
$area_id = trim($_POST['area_id']);

$travel_expense = !empty($_POST['travel_expense']) ? $_POST['travel_expense'] : 0;
$da_expense = !empty($_POST['da_expense']) ? $_POST['da_expense'] : 0;
$hotel_expense = !empty($_POST['hotel_expense']) ? $_POST['hotel_expense'] : 0;
$food_expense = !empty($_POST['food_expense']) ? $_POST['food_expense'] : 0;
$other_expense = !empty($_POST['other_expense']) ? $_POST['other_expense'] : 0;

$remarks = trim($_POST['remarks']);

$status       = (int)$_POST['status'];

/* Validation */

if (empty($dcr_id) || empty($area_id)) {

    $_SESSION['error'] = "Please select DCR.";

    header("Location: expense-edit.php?id=" . $id);
    exit;
}

/* Total Expense */

$total_expense =
    $travel_expense +
    $da_expense +
    $hotel_expense +
    $food_expense +
    $other_expense;

/* Update */

$sql = "UPDATE tbl_dcr_expenses
SET

    dcr_id=?,
    area_id=?,
    travel_expense=?,
    da_expense=?,
    hotel_expense=?,
    food_expense=?,
    other_expense=?,
    total_expense=?,
    remarks=?,
    status=?,
    updated_at=NOW()

WHERE id=?
AND mr_id=?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(

    $stmt,

    "iiddddddsiii",

    $dcr_id,
    $area_id,
    $travel_expense,
    $da_expense,
    $hotel_expense,
    $food_expense,
    $other_expense,
    $total_expense,
    $remarks,
    $status,
    $id,
    $_SESSION['user_id']

);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Expense updated successfully.";
} else {

    $_SESSION['error'] = "Unable to update expense.";
}

header("Location: expense-edit.php?id=" . $id);
exit;
