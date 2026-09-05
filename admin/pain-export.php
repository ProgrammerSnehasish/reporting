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
$sheet->setTitle("Pain Products");

/*====================================
HEADERS
====================================*/

$headers = [

    "Product Code",
    "Pain Product Name",
    "Description",
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

$sheet->getStyle("A1:E1")->applyFromArray([

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
FETCH PAIN PRODUCTS
====================================*/

$sql = "

SELECT *

FROM tbl_pain_management_products

ORDER BY pain_product_name ASC

";

$result = mysqli_query($conn, $sql);

$rowNo = 2;

while ($row = mysqli_fetch_assoc($result)) {

    $sheet->setCellValue("A" . $rowNo, $row['product_code']);
    $sheet->setCellValue("B" . $rowNo, $row['pain_product_name']);
    $sheet->setCellValue("C" . $rowNo, $row['description']);
    $sheet->setCellValue("D" . $rowNo, $row['remarks']);
    $sheet->setCellValue("E" . $rowNo, ($row['status'] == 1 ? "Active" : "Inactive"));

    $rowNo++;
}

/*====================================
AUTO SIZE
====================================*/

foreach (range('A', 'E') as $column) {

    $sheet->getColumnDimension($column)->setAutoSize(true);
}

/*====================================
DOWNLOAD
====================================*/

$file = "Pain_Products_" . date("Ymd_His") . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit;
