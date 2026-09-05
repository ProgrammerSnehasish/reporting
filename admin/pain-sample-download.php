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

    "Pain Product Name *",
    "Description",
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

$sheet->getStyle("A1:D1")->applyFromArray([

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

/*====================================
REQUIRED / OPTIONAL COLOR
====================================*/

$sheet->getStyle("A1")->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'DC3545']
    ]
]);

$sheet->getStyle("B1:D1")->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '198754']
    ]
]);

/*====================================
SAMPLE DATA
====================================*/

$sheet->fromArray([

    "Epidural Kit",
    "Used for epidural pain management procedure.",
    "Sample Pain Product",
    "Active"

], NULL, "A2");

/*====================================
AUTO SIZE
====================================*/

foreach (range('A', 'D') as $col) {

    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/*====================================
FREE ROWS
====================================*/

for ($i = 3; $i <= 1002; $i++) {

    $sheet->setCellValue("D" . $i, "");
}

/*====================================
DOWNLOAD
====================================*/

$file = "Pain_Product_Sample.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit;
