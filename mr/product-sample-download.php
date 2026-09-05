<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";
require_once "../vendor/autoload.php";

checkRole(['mr']);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Product Sample");

/*====================================
HEADERS
====================================*/

$headers = [

    "Product Name *",
    "Brand Name *",
    "Generic Name *",
    "Dosage Form *",
    "Pack *",
    "MRP *",
    "PTR *",
    "PTS *",
    "Category",
    "Division",
    "Strength",
    "HSN Code",
    "GST %",
    "Bonus Offer",
    "Manufacturer",
    "Focus Product (Yes/No)",
    "Display Order",
    "Stock Quantity",
    "Remarks",
    "Status (Active/Inactive)"

];

$column = 'A';

foreach ($headers as $header) {

    $sheet->setCellValue($column . "1", $header);

    $column++;
}

/*====================================
HEADER STYLE
====================================*/

$sheet->getStyle("A1:T1")->applyFromArray([

    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11
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

$sheet->getStyle('A1:H1')->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'DC3545']
    ]
]);

$sheet->getStyle('I1:T1')->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '198754']
    ]
]);

/*====================================
SAMPLE DATA
====================================*/

$sheet->fromArray([

    "LUMIGESIC GEL",
    "Luminia",
    "Diclofenac Diethylamine",
    "Gel",
    "30 GM",
    145,
    118,
    110,
    "Pain Management",
    "Luminia Pharma",
    "1%",
    "30049099",
    12,
    "Buy 10 Get 1",
    "Luminia Lifecare Pvt. Ltd.",
    "Yes",
    1,
    100,
    "Sample Product",
    "Active"

], NULL, "A2");

/*====================================
AUTO SIZE
====================================*/

foreach (range('A', 'T') as $col) {

    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/*====================================
FREE ROWS FOR USER
====================================*/

for ($i = 3; $i <= 1002; $i++) {

    $sheet->setCellValue("T" . $i, "");
}

/*====================================
DOWNLOAD
====================================*/

$file = "Product_Sample.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit;
