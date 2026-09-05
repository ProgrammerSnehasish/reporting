<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";
require_once "../vendor/autoload.php";

checkRole(['admin']);

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: pain-import.php");
    exit;
}

if (empty($_FILES['excel_file']['tmp_name'])) {

    $_SESSION['error'] = "Please select an Excel file.";

    header("Location: pain-import.php");
    exit;
}

$created_by = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);

    $sheet = $spreadsheet->getActiveSheet()->toArray();

    $success = 0;
    $errors = [];

    foreach ($sheet as $index => $row) {

        if ($index == 0) {
            continue;
        }

        $excelRow = $index + 1;

        $pain_product_name = trim($row[0]);
        $description       = trim($row[1]);
        $remarks           = trim($row[2]);
        $status            = trim($row[3]);

        if (
            empty($pain_product_name) &&
            empty($description) &&
            empty($remarks)
        ) {
            continue;
        }

        if (empty($pain_product_name)) {

            $errors[] = "Row {$excelRow} : Pain Product Name is required.";
            continue;
        }

        $status = (strtolower($status) == "inactive") ? 0 : 1;

        /* Duplicate Check */

        $dup = mysqli_query($conn, "
        SELECT id
        FROM tbl_pain_management_products
        WHERE pain_product_name='" . mysqli_real_escape_string($conn, $pain_product_name) . "'
        LIMIT 1
        ");

        if (mysqli_num_rows($dup)) {

            $errors[] = "Row {$excelRow} : Pain Product already exists.";
            continue;
        }

        /* Insert */

        mysqli_query($conn, "

        INSERT INTO tbl_pain_management_products(

            pain_product_name,
            description,
            remarks,
            status,
            created_by,
            created_at

        )

        VALUES(

            '" . mysqli_real_escape_string($conn, $pain_product_name) . "',

            '" . mysqli_real_escape_string($conn, $description) . "',

            '" . mysqli_real_escape_string($conn, $remarks) . "',

            '$status',

            '$created_by',

            NOW()

        )

        ");

        if (mysqli_error($conn)) {
            throw new Exception(mysqli_error($conn));
        }

        $pain_id = mysqli_insert_id($conn);

        $product_code = "PM" . str_pad($pain_id, 5, "0", STR_PAD_LEFT);

        $update = mysqli_query($conn, "
        UPDATE tbl_pain_management_products
        SET product_code='$product_code'
        WHERE id='$pain_id'
        ");

        if (!$update) {
            throw new Exception(mysqli_error($conn));
        }

        $success++;
    }

    mysqli_commit($conn);

    $_SESSION['success'] = $success . " Pain Product(s) imported successfully.";

    if (!empty($errors)) {

        $_SESSION['error'] = implode("<br>", array_slice($errors, 0, 20));

        if (count($errors) > 20) {

            $_SESSION['error'] .= "<br><br>...and " . (count($errors) - 20) . " more error(s).";
        }
    }
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
}

header("Location: pain-import.php");
exit;
