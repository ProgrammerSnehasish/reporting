<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";
require_once "../vendor/autoload.php";

checkRole(['admin']);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Products");

/*====================================
HEADERS
====================================*/

$headers = [

    "Product Code",
    "Product Name",
    "Brand Name",
    "Generic Name",
    "Dosage Form",
    "Pack",
    "MRP",
    "PTR",
    "PTS",
    "Category",
    "Division",
    "Strength",
    "HSN Code",
    "GST %",
    "Bonus Offer",
    "Manufacturer",
    "Focus Product",
    "Display Order",
    "Stock Quantity",
    "Remarks",
    "Status"

];

$col = 'A';

foreach ($headers as $header) {

    $sheet->setCellValue($col . "1", $header);

    $col++;
}

/*====================================
HEADER STYLE
====================================*/

$sheet->getStyle("A1:U1")->applyFromArray([

    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],

    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0D6EFD']
    ],

    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ],

    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]

]);

/*====================================
FETCH PRODUCTS
====================================*/

$sql = "

SELECT

p.*,

c.category_name,

d.division_name

FROM tbl_products p

LEFT JOIN tbl_product_categories c
ON c.id = p.category_id

LEFT JOIN tbl_divisions d
ON d.id = p.division_id

ORDER BY p.product_name ASC

";

$result = mysqli_query($conn, $sql);

$rowNo = 2;

while ($row = mysqli_fetch_assoc($result)) {

    $sheet->setCellValue("A" . $rowNo, $row['product_code']);
    $sheet->setCellValue("B" . $rowNo, $row['product_name']);
    $sheet->setCellValue("C" . $rowNo, $row['brand_name']);
    $sheet->setCellValue("D" . $rowNo, $row['generic_name']);
    $sheet->setCellValue("E" . $rowNo, $row['dosage_form']);
    $sheet->setCellValue("F" . $rowNo, $row['pack']);
    $sheet->setCellValue("G" . $rowNo, $row['mrp']);
    $sheet->setCellValue("H" . $rowNo, $row['ptr']);
    $sheet->setCellValue("I" . $rowNo, $row['pts']);
    $sheet->setCellValue("J" . $rowNo, $row['category_name']);
    $sheet->setCellValue("K" . $rowNo, $row['division_name']);
    $sheet->setCellValue("L" . $rowNo, $row['strength']);
    $sheet->setCellValue("M" . $rowNo, $row['hsn_code']);
    $sheet->setCellValue("N" . $rowNo, $row['gst']);
    $sheet->setCellValue("O" . $rowNo, $row['bonus_offer']);
    $sheet->setCellValue("P" . $rowNo, $row['manufacturer']);
    $sheet->setCellValue("Q" . $rowNo, $row['is_focus_product'] ? "Yes" : "No");
    $sheet->setCellValue("R" . $rowNo, $row['display_order']);
    $sheet->setCellValue("S" . $rowNo, $row['stock_quantity']);
    $sheet->setCellValue("T" . $rowNo, $row['remarks']);
    $sheet->setCellValue("U" . $rowNo, ($row['status'] == 1 ? "Active" : "Inactive"));

    $rowNo++;
}

/*====================================
AUTO SIZE
====================================*/

foreach (range('A', 'U') as $column) {

    $sheet->getColumnDimension($column)->setAutoSize(true);
}

/*====================================
DOWNLOAD
====================================*/

$file = "Products_" . date("Ymd_His") . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit;
