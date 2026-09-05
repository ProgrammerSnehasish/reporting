<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: expense-list.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "
    SELECT
        e.*,
        d.dcr_no,
        d.visit_date,
        a.area_name,
        h.area_name AS hq_name,
        CONCAT(emp.first_name,' ',emp.last_name) AS mr_name,
        emp.employee_code
    FROM tbl_dcr_expenses e
    INNER JOIN tbl_dcr d     ON d.id  = e.dcr_id
    INNER JOIN tbl_areas a   ON a.id  = e.area_id
    LEFT  JOIN tbl_areas h   ON h.id  = e.hq_id
    INNER JOIN tbl_users u   ON u.id  = e.mr_id
    INNER JOIN tbl_employees emp ON emp.id = u.employee_id
    WHERE e.id = ? AND e.mr_id = ?
");

mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Expense not found.";
    header("Location: expense-list.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

?>
<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">
    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0">Expense Details</h4>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            DCR: <?php echo $row['dcr_no']; ?> &nbsp;|&nbsp;
                            <?php echo date('d M Y', strtotime($row['visit_date'])); ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end d-flex gap-2 justify-content-end align-items-center">
                        <!-- <?php if ($row['status'] == 0) { ?>
                            <a href="expense-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="ri-edit-line me-1"></i> Edit
                            </a>
                        <?php } ?> -->
                        <a href="expense-list.php" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Back
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <div class="row g-3">

                    <!-- LEFT — Summary Cards -->
                    <div class="col-lg-4">

                        <!-- MR Info -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-user-line me-1"></i> MR Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" style="width:45%;font-size:13px;">Name</td>
                                        <td style="font-size:13px;font-weight:500;"><?php echo $row['mr_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Emp Code</td>
                                        <td style="font-size:13px;"><?php echo $row['employee_code']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">HQ</td>
                                        <td style="font-size:13px;"><?php echo $row['hq_name'] ?? '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Area</td>
                                        <td style="font-size:13px;"><?php echo $row['area_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Visit Date</td>
                                        <td style="font-size:13px;"><?php echo date('d M Y', strtotime($row['visit_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">DCR No.</td>
                                        <td style="font-size:13px;">
                                            <a href="dcr-view.php?id=<?php echo $row['dcr_id']; ?>">
                                                <?php echo $row['dcr_no']; ?>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-information-line me-1"></i> Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted" style="font-size:13px;">Expense Status</span>
                                    <?php if ($row['status'] == 1) { ?>
                                        <span class="badge bg-success">Submitted</span>
                                    <?php } else { ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php } ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted" style="font-size:13px;">Created On</span>
                                    <span style="font-size:13px;"><?php echo date('d M Y h:i A', strtotime($row['created_at'])); ?></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted" style="font-size:13px;">Last Updated</span>
                                    <span style="font-size:13px;"><?php echo date('d M Y h:i A', strtotime($row['updated_at'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- KM Card -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-car-line me-1"></i> KM Details</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Start KM</td>
                                        <td class="text-end" style="font-size:13px;"><?php echo number_format($row['start_km'], 1); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">End KM</td>
                                        <td class="text-end" style="font-size:13px;"><?php echo number_format($row['end_km'], 1); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Total KM</td>
                                        <td class="text-end fw-bold" style="font-size:13px;"><?php echo number_format($row['total_km'], 1); ?> km</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="font-size:13px;">Per KM Rate</td>
                                        <td class="text-end" style="font-size:13px;">₹ <?php echo number_format($row['per_km_rate'], 2); ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="text-muted fw-bold" style="font-size:13px;">Travel Expense</td>
                                        <td class="text-end fw-bold text-primary" style="font-size:13px;">
                                            ₹ <?php echo number_format($row['travel_expense'], 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT — Expense Breakdown -->
                    <div class="col-lg-8">

                        <!-- Expense Breakdown -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-money-rupee-circle-line me-1"></i> Expense Breakdown</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%;">#</th>
                                            <th>Expense Type</th>
                                            <th class="text-end" style="width:30%;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $expenses = [
                                            ['icon' => 'ri-car-line',        'label' => 'Travel Expense',  'key' => 'travel_expense'],
                                            ['icon' => 'ri-sun-line',        'label' => 'DA Expense',      'key' => 'da_expense'],
                                            ['icon' => 'ri-hotel-line',      'label' => 'Hotel Expense',   'key' => 'hotel_expense'],
                                            ['icon' => 'ri-restaurant-line', 'label' => 'Food Expense',    'key' => 'food_expense'],
                                            ['icon' => 'ri-more-line',       'label' => 'Other Expense',   'key' => 'other_expense'],
                                        ];
                                        $sl = 1;
                                        foreach ($expenses as $exp) { ?>
                                            <tr>
                                                <td class="text-muted"><?php echo $sl++; ?></td>
                                                <td>
                                                    <i class="<?php echo $exp['icon']; ?> me-2 text-muted"></i>
                                                    <?php echo $exp['label']; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($row[$exp['key']] > 0) { ?>
                                                        <span class="fw-500">₹ <?php echo number_format($row[$exp['key']], 2); ?></span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <th colspan="2">
                                                <i class="ri-funds-line me-1"></i> Total Expense
                                            </th>
                                            <th class="text-end fs-5">
                                                ₹ <?php echo number_format($row['total_expense'], 2); ?>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Visual Bar -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-bar-chart-line me-1"></i> Expense Distribution</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $total = $row['total_expense'];
                                $bars  = [
                                    ['label' => 'Travel', 'val' => $row['travel_expense'], 'color' => 'bg-primary'],
                                    ['label' => 'DA',     'val' => $row['da_expense'],     'color' => 'bg-success'],
                                    ['label' => 'Hotel',  'val' => $row['hotel_expense'],  'color' => 'bg-warning'],
                                    ['label' => 'Food',   'val' => $row['food_expense'],   'color' => 'bg-info'],
                                    ['label' => 'Other',  'val' => $row['other_expense'],  'color' => 'bg-danger'],
                                ];
                                foreach ($bars as $b) {
                                    $pct = $total > 0 ? round(($b['val'] / $total) * 100, 1) : 0;
                                    if ($b['val'] <= 0) continue;
                                ?>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span style="font-size:13px;"><?php echo $b['label']; ?></span>
                                            <span style="font-size:13px;" class="text-muted">
                                                ₹ <?php echo number_format($b['val'], 2); ?>
                                                (<?php echo $pct; ?>%)
                                            </span>
                                        </div>
                                        <div class="progress" style="height:8px;">
                                            <div class="progress-bar <?php echo $b['color']; ?>"
                                                style="width:<?php echo $pct; ?>%"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <?php if (!empty($row['remarks'])) { ?>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="ri-quill-pen-line me-1"></i> Remarks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" style="font-size:14px;line-height:1.7;">
                                        <?php echo nl2br(htmlspecialchars($row['remarks'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>

        <?php include('./includes/footer.php'); ?>
    </div>
</div>

<?php include('./includes/scripts.php'); ?>