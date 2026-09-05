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

// ── Filters ───────────────────────────────────────────────────────────────────
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to   = isset($_GET['date_to'])   ? $_GET['date_to']   : date('Y-m-d');
$mr_id     = isset($_GET['mr_id'])     ? (int) $_GET['mr_id'] : 0;
$area_id   = isset($_GET['area_id'])   ? (int) $_GET['area_id'] : 0;
$status    = isset($_GET['status'])    ? $_GET['status'] : '';

$where = "WHERE d.visit_date BETWEEN '$date_from' AND '$date_to'";
if ($mr_id)         $where .= " AND d.mr_id = '$mr_id'";
if ($area_id)       $where .= " AND d.area_id = '$area_id'";
if ($status !== '') $where .= " AND d.status = '$status'";

// ── Helper functions ──────────────────────────────────────────────────────────

function statusLabel($status)
{
    $map = [0 => 'Draft', 1 => 'Submitted', 2 => 'Approved', 3 => 'Rejected'];
    return $map[$status] ?? 'Unknown';
}

function headerStyle($color = '1F4E79')
{
    return [
        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $color]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFAAAAAA']]],
    ];
}

function rowStyle($even = false)
{
    return [
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $even ? 'FFF5F5F5' : 'FFFFFFFF']],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    ];
}

function setColumnWidths($sheet, $widths)
{
    foreach ($widths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
}

// ── Fetch all DCRs ────────────────────────────────────────────────────────────
$dcrResult = mysqli_query($conn, "
    SELECT
        d.*,
        e.employee_code,
        CONCAT(e.first_name,' ',e.last_name) AS employee_name,
        a.area_name,
        hq.area_name AS hq_name,
        wt.working_type_name,
        exp.travel_expense AS exp_travel,
        exp.da_expense,
        exp.hotel_expense,
        exp.food_expense,
        exp.other_expense,
        exp.total_expense
    FROM tbl_dcr d
    INNER JOIN tbl_users u          ON u.id   = d.mr_id
    INNER JOIN tbl_employees e      ON e.id   = u.employee_id
    LEFT  JOIN tbl_areas a          ON a.id   = d.area_id
    LEFT  JOIN tbl_areas hq         ON hq.id  = d.hq_id
    LEFT  JOIN tbl_working_types wt ON wt.id  = d.working_type_id
    LEFT  JOIN tbl_dcr_expenses exp ON exp.dcr_id = d.id
    $where
    ORDER BY d.visit_date DESC, d.id DESC
");

// ── Create Spreadsheet ────────────────────────────────────────────────────────
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('DCR Report');

// ── Main Title ────────────────────────────────────────────────────────────────
$sheet->mergeCells('A1:P1');
$sheet->setCellValue('A1', 'DCR Report  |  ' . date('d M Y', strtotime($date_from)) . ' to ' . date('d M Y', strtotime($date_to)));
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1F4E79']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F0FE']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(32);

// ── Column widths ─────────────────────────────────────────────────────────────
setColumnWidths($sheet, [
    'A' => 5,
    'B' => 15,
    'C' => 22,
    'D' => 13,
    'E' => 22,
    'F' => 22,
    'G' => 25,
    'H' => 25,
    'I' => 12,
    'J' => 12,
    'K' => 12,
    'L' => 12,
    'M' => 12,
    'N' => 12,
    'O' => 12,
    'P' => 12,
]);

$currentRow = 2;
$sl = 1;

// ── Loop each DCR ─────────────────────────────────────────────────────────────
while ($dcr = mysqli_fetch_assoc($dcrResult)) {

    /* ── DCR Header ─────────────────────────────────────────── */
    $sheet->mergeCells("A{$currentRow}:P{$currentRow}");
    $sheet->setCellValue(
        "A{$currentRow}",
        "#$sl  |  {$dcr['dcr_no']}  |  " .
            "{$dcr['employee_code']} - {$dcr['employee_name']}  |  " .
            date('d M Y', strtotime($dcr['visit_date'])) . "  |  " .
            "HQ: {$dcr['hq_name']}  |  Area: {$dcr['area_name']}  |  " .
            "Working Type: {$dcr['working_type_name']}  |  " .
            "KM: {$dcr['total_km']}  |  Status: " . statusLabel($dcr['status'])
    );
    $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray([
        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF888888']]],
    ]);
    $sheet->getRowDimension($currentRow)->setRowHeight(22);
    $currentRow++;

    /* ── Expense Row ────────────────────────────────────────── */
    $sheet->mergeCells("A{$currentRow}:P{$currentRow}");
    $sheet->setCellValue(
        "A{$currentRow}",
        "Expense :  " .
            "Travel: ₹" . number_format($dcr['exp_travel'] ?? 0, 2) . "  |  " .
            "DA: ₹"     . number_format($dcr['da_expense']   ?? 0, 2) . "  |  " .
            "Hotel: ₹"  . number_format($dcr['hotel_expense'] ?? 0, 2) . "  |  " .
            "Food: ₹"   . number_format($dcr['food_expense']  ?? 0, 2) . "  |  " .
            "Other: ₹"  . number_format($dcr['other_expense'] ?? 0, 2) . "  |  " .
            "Total: ₹"  . number_format($dcr['total_expense'] ?? 0, 2)
    );
    $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray([
        'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF14532D']],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCFCE7']],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBBBBBB']]],
    ]);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow++;

    /* ── Remarks Row ────────────────────────────────────────── */
    if (!empty($dcr['remarks']) || !empty($dcr['achievement'])) {
        $sheet->mergeCells("A{$currentRow}:P{$currentRow}");
        $sheet->setCellValue(
            "A{$currentRow}",
            "Remarks: " . ($dcr['remarks'] ?? '-') . "  |  Achievement: " . ($dcr['achievement'] ?? '-')
        );
        $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9FAFB']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
        ]);
        $sheet->getRowDimension($currentRow)->setRowHeight(16);
        $currentRow++;
    }

    /* ── Doctor Calls ───────────────────────────────────────── */
    $doctorCalls = mysqli_query($conn, "
        SELECT dc.*, d.doctor_name, d.doctor_code
        FROM tbl_dcr_doctor_calls dc
        INNER JOIN tbl_doctors d ON d.id = dc.doctor_id
        WHERE dc.dcr_id = '{$dcr['id']}'
        ORDER BY dc.visit_time ASC
    ");

    if (mysqli_num_rows($doctorCalls) > 0) {

        // Doctor sub-header
        $dHeaders = ['#', 'Doctor Code', 'Doctor Name', 'Visit Time', 'Samples', 'Gifts', 'Remarks'];
        $dCols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($dCols as $i => $col) {
            $sheet->setCellValue($col . $currentRow, $dHeaders[$i]);
        }
        $sheet->mergeCells("G{$currentRow}:P{$currentRow}");
        $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray(headerStyle('1D4ED8'));
        $sheet->getRowDimension($currentRow)->setRowHeight(18);
        $currentRow++;

        $dsl = 1;
        while ($doc = mysqli_fetch_assoc($doctorCalls)) {

            // Samples
            $samplesRes = mysqli_query($conn, "
                SELECT p.product_name, dp.quantity
                FROM tbl_dcr_doctor_products dp
                LEFT JOIN tbl_products p ON p.id = dp.product_id
                WHERE dp.doctor_call_id = '{$doc['id']}'
            ");
            $samples = [];
            while ($s = mysqli_fetch_assoc($samplesRes)) {
                $samples[] = $s['product_name'] . ' (' . $s['quantity'] . ')';
            }

            // Gifts
            $giftsRes = mysqli_query($conn, "
                SELECT g.gift_name, dg.quantity
                FROM tbl_dcr_gifts dg
                LEFT JOIN tbl_gifts g ON g.id = dg.gift_id
                WHERE dg.dcr_doctor_call_id = '{$doc['id']}'
            ");
            $gifts = [];
            while ($g = mysqli_fetch_assoc($giftsRes)) {
                $gifts[] = $g['gift_name'] . ' (' . $g['quantity'] . ')';
            }

            $even = ($dsl % 2 == 0);
            $sheet->setCellValue('A' . $currentRow, $dsl++);
            $sheet->setCellValue('B' . $currentRow, $doc['doctor_code'] ?? '-');
            $sheet->setCellValue('C' . $currentRow, $doc['doctor_name']);
            $sheet->setCellValue('D' . $currentRow, !empty($doc['visit_time']) ? date('h:i A', strtotime($doc['visit_time'])) : '-');
            $sheet->setCellValue('E' . $currentRow, !empty($samples) ? implode(', ', $samples) : '-');
            $sheet->setCellValue('F' . $currentRow, !empty($gifts)   ? implode(', ', $gifts)   : '-');
            $sheet->mergeCells("G{$currentRow}:P{$currentRow}");
            $sheet->setCellValue('G' . $currentRow, $doc['remarks'] ?? '-');
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray(rowStyle($even));
            $sheet->getRowDimension($currentRow)->setRowHeight(16);
            $currentRow++;
        }
    }

    /* ── Chemist Calls ──────────────────────────────────────── */
    $chemistCalls = mysqli_query($conn, "
        SELECT cc.*, c.chemist_name, c.chemist_code
        FROM tbl_dcr_chemist_calls cc
        INNER JOIN tbl_chemists c ON c.id = cc.chemist_id
        WHERE cc.dcr_id = '{$dcr['id']}'
        ORDER BY cc.visit_time ASC
    ");

    if (mysqli_num_rows($chemistCalls) > 0) {

        // Chemist sub-header
        $cHeaders = ['#', 'Chemist Code', 'Chemist Name', 'Visit Time', 'POB', 'Booking Value', 'Products', 'Remarks'];
        $cCols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($cCols as $i => $col) {
            $sheet->setCellValue($col . $currentRow, $cHeaders[$i]);
        }
        $sheet->mergeCells("H{$currentRow}:P{$currentRow}");
        $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray(headerStyle('15803D'));
        $sheet->getRowDimension($currentRow)->setRowHeight(18);
        $currentRow++;

        $csl = 1;
        while ($chem = mysqli_fetch_assoc($chemistCalls)) {

            $productsRes = mysqli_query($conn, "
                SELECT p.product_name
                FROM tbl_dcr_chemist_products cp
                LEFT JOIN tbl_products p ON p.id = cp.product_id
                WHERE cp.chemist_call_id = '{$chem['id']}'
            ");
            $products = [];
            while ($p = mysqli_fetch_assoc($productsRes)) {
                $products[] = $p['product_name'];
            }

            $even = ($csl % 2 == 0);
            $sheet->setCellValue('A' . $currentRow, $csl++);
            $sheet->setCellValue('B' . $currentRow, $chem['chemist_code'] ?? '-');
            $sheet->setCellValue('C' . $currentRow, $chem['chemist_name']);
            $sheet->setCellValue('D' . $currentRow, !empty($chem['visit_time']) ? date('h:i A', strtotime($chem['visit_time'])) : '-');
            $sheet->setCellValue('E' . $currentRow, '₹' . number_format($chem['pob'] ?? 0, 2));
            $sheet->setCellValue('F' . $currentRow, '₹' . number_format($chem['booking_value'] ?? 0, 2));
            $sheet->setCellValue('G' . $currentRow, !empty($products) ? implode(', ', $products) : '-');
            $sheet->mergeCells("H{$currentRow}:P{$currentRow}");
            $sheet->setCellValue('H' . $currentRow, $chem['remarks'] ?? '-');
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray(rowStyle($even));
            $sheet->getRowDimension($currentRow)->setRowHeight(16);
            $currentRow++;
        }
    }

    /* ── Stockist Calls ─────────────────────────────────────── */
    $stockistCalls = mysqli_query($conn, "
        SELECT sc.*, c.chemist_name AS stockist_name, c.chemist_code AS stockist_code
        FROM tbl_dcr_stockist_calls sc
        INNER JOIN tbl_chemists c ON c.id = sc.stockist_id
        WHERE sc.dcr_id = '{$dcr['id']}'
        ORDER BY sc.visit_time ASC
    ");

    if (mysqli_num_rows($stockistCalls) > 0) {

        // Stockist sub-header
        $sHeaders = ['#', 'Stockist Code', 'Stockist Name', 'Visit Time', 'Primary Order', 'Products', 'Remarks'];
        $sCols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($sCols as $i => $col) {
            $sheet->setCellValue($col . $currentRow, $sHeaders[$i]);
        }
        $sheet->mergeCells("G{$currentRow}:P{$currentRow}");
        $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FF1C1917'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFBBF24']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFAAAAAA']]],
        ]);
        $sheet->getRowDimension($currentRow)->setRowHeight(18);
        $currentRow++;

        $ssl = 1;
        while ($stk = mysqli_fetch_assoc($stockistCalls)) {

            $productsRes = mysqli_query($conn, "
                SELECT p.product_name
                FROM tbl_dcr_stockist_products sp
                LEFT JOIN tbl_products p ON p.id = sp.product_id
                WHERE sp.stockist_call_id = '{$stk['id']}'
            ");
            $products = [];
            while ($p = mysqli_fetch_assoc($productsRes)) {
                $products[] = $p['product_name'];
            }

            $even = ($ssl % 2 == 0);
            $sheet->setCellValue('A' . $currentRow, $ssl++);
            $sheet->setCellValue('B' . $currentRow, $stk['stockist_code'] ?? '-');
            $sheet->setCellValue('C' . $currentRow, $stk['stockist_name']);
            $sheet->setCellValue('D' . $currentRow, !empty($stk['visit_time']) ? date('h:i A', strtotime($stk['visit_time'])) : '-');
            $sheet->setCellValue('E' . $currentRow, '₹' . number_format($stk['primary_order'] ?? 0, 2));
            $sheet->setCellValue('F' . $currentRow, !empty($products) ? implode(', ', $products) : '-');
            $sheet->mergeCells("G{$currentRow}:P{$currentRow}");
            $sheet->setCellValue('G' . $currentRow, $stk['remarks'] ?? '-');
            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray(rowStyle($even));
            $sheet->getRowDimension($currentRow)->setRowHeight(16);
            $currentRow++;
        }
    }

    /* ── Spacer between DCRs ────────────────────────────────── */
    $sheet->getRowDimension($currentRow)->setRowHeight(10);
    $currentRow++;

    $sl++;
}

// ── Download ──────────────────────────────────────────────────────────────────
$filename = 'DCR_Report_' . date('dmY', strtotime($date_from)) . '_to_' . date('dmY', strtotime($date_to)) . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
