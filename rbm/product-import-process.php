<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";
require_once "../vendor/autoload.php";

checkRole(['rbm']);

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: product-import.php");
    exit;
}

if (empty($_FILES['excel_file']['tmp_name'])) {

    $_SESSION['error'] = "Please select an Excel file.";

    header("Location: product-import.php");
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

        $product_name     = trim($row[0]);
        $brand_name       = trim($row[1]);
        $generic_name     = trim($row[2]);
        $dosage_form      = trim($row[3]);
        $pack             = trim($row[4]);
        $mrp              = trim($row[5]);
        $ptr              = trim($row[6]);
        $pts              = trim($row[7]);
        $category         = trim($row[8]);
        $division         = trim($row[9]);
        $strength         = trim($row[10]);
        $hsn              = trim($row[11]);
        $gst              = trim($row[12]);
        $bonus_offer      = trim($row[13]);
        $manufacturer     = trim($row[14]);
        $focus_product    = trim($row[15]);
        $display_order    = trim($row[16]);
        $stock            = trim($row[17]);
        $remarks          = trim($row[18]);
        $status           = trim($row[19]);

        if (
            empty($product_name) &&
            empty($brand_name) &&
            empty($generic_name)
        ) {
            continue;
        }

        if (

            empty($product_name) ||

            empty($brand_name) ||

            empty($generic_name) ||

            empty($dosage_form) ||

            empty($pack) ||

            $mrp == "" ||

            $ptr == "" ||

            $pts == ""

        ) {

            $errors[] = "Row {$excelRow} : Required fields missing.";

            continue;
        }

        $status = (strtolower($status) == "inactive") ? 0 : 1;

        $stock = ($stock == "") ? 0 : $stock;

        $category_id = "NULL";

        if (!empty($category)) {

            $cat = mysqli_query($conn, "
                SELECT id
                FROM tbl_product_categories
                WHERE category_name='" . mysqli_real_escape_string($conn, $category) . "'
                LIMIT 1
            ");

            if (mysqli_num_rows($cat)) {

                $category_id = mysqli_fetch_assoc($cat)['id'];
            }
        }

        if (!empty($category) && $category_id == "NULL") {
            $errors[] = "Row {$excelRow} : Category '{$category}' not found.";
            continue;
        }

        $division_id = "NULL";

        if (!empty($division)) {

            $div = mysqli_query($conn, "
                SELECT id
                FROM tbl_divisions
                WHERE division_name='" . mysqli_real_escape_string($conn, $division) . "'
                LIMIT 1
            ");

            if (mysqli_num_rows($div)) {

                $division_id = mysqli_fetch_assoc($div)['id'];
            }
        }

        if (!empty($division) && $division_id == "NULL") {
            $errors[] = "Row {$excelRow} : Division '{$division}' not found.";
            continue;
        }

        $dup = mysqli_query($conn, "
            SELECT id
            FROM tbl_products
            WHERE product_name='" . mysqli_real_escape_string($conn, $product_name) . "'
            LIMIT 1
        ");

        if (mysqli_num_rows($dup)) {

            $errors[] = "Row {$excelRow} : Product Name already exists.";

            continue;
        }

        $dup = mysqli_query($conn, "
            SELECT id
            FROM tbl_products
            WHERE
            brand_name='" . mysqli_real_escape_string($conn, $brand_name) . "'
            AND generic_name='" . mysqli_real_escape_string($conn, $generic_name) . "'
            AND dosage_form='" . mysqli_real_escape_string($conn, $dosage_form) . "'
            AND pack='" . mysqli_real_escape_string($conn, $pack) . "'
            LIMIT 1
        ");

        if (mysqli_num_rows($dup)) {

            $errors[] = "Row {$excelRow} : Duplicate Brand + Generic Name + Dosage Form + Packing.";

            continue;
        }

        $focus_product = strtolower(trim($focus_product));

        $is_focus_product =
            (
                $focus_product == "yes" ||
                $focus_product == "1"
            ) ? 1 : 0;

        $display_order = empty($display_order)
            ? 0
            : (int)$display_order;

        mysqli_query($conn, "

        INSERT INTO tbl_products(

        product_name,
        brand_name,
        generic_name,
        dosage_form,
        category_id,
        division_id,
        pack,
        strength,
        mrp,
        hsn_code,
        gst,
        ptr,
        pts,
        bonus_offer,
        manufacturer,
        is_focus_product,
        display_order,
        stock_quantity,
        remarks,
        status,
        created_by,
        created_at

        )

        VALUES(

        '" . mysqli_real_escape_string($conn, $product_name) . "',

        '" . mysqli_real_escape_string($conn, $brand_name) . "',

        '" . mysqli_real_escape_string($conn, $generic_name) . "',

        '" . mysqli_real_escape_string($conn, $dosage_form) . "',

        $category_id,

        $division_id,

        '" . mysqli_real_escape_string($conn, $pack) . "',

        '" . mysqli_real_escape_string($conn, $strength) . "',

        '$mrp',

        '" . mysqli_real_escape_string($conn, $hsn) . "',

        '$gst',

        '$ptr',

        '$pts',

        '" . mysqli_real_escape_string($conn, $bonus_offer) . "',

        '" . mysqli_real_escape_string($conn, $manufacturer) . "',

        '$is_focus_product',

        '$display_order',

        '$stock',

        '" . mysqli_real_escape_string($conn, $remarks) . "',

        '$status',

        '$created_by',

        NOW()

        )

        ");

        // if (!mysqli_query($conn)) {
        //     throw new Exception(mysqli_error($conn));
        // }

        $product_id = mysqli_insert_id($conn);

        $product_code = "PRD" . str_pad($product_id, 5, "0", STR_PAD_LEFT);

        $update = mysqli_query($conn, "
        UPDATE tbl_products
        SET product_code='$product_code'
        WHERE id='$product_id'
        ");

        if (!$update) {
            throw new Exception(mysqli_error($conn));
        }

        $success++;
    }

    mysqli_commit($conn);

    $_SESSION['success'] = $success . " Product(s) imported successfully.";

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

header("Location: product-import.php");
exit;
