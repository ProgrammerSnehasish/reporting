<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid DCR.";
    header("Location: dcr-list.php");
    exit;
}

$id = (int) $_GET['id'];

// Main DCR
$row = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT
        d.*,
        a.area_name,
        hq.area_name AS hq_name,
        wt.working_type_name,
        CONCAT(emp.first_name,' ',emp.last_name) AS mr_name,
        emp.employee_code
    FROM tbl_dcr d
    LEFT JOIN tbl_areas a    ON a.id  = d.area_id
    LEFT JOIN tbl_areas hq   ON hq.id = d.hq_id
    LEFT JOIN tbl_working_types wt ON wt.id = d.working_type_id
    LEFT JOIN tbl_users u    ON u.id  = d.mr_id
    LEFT JOIN tbl_employees emp ON emp.id = u.employee_id
    WHERE d.id = '$id'
"));

if (!$row) {
    $_SESSION['error'] = "DCR not found.";
    header("Location: dcr-list.php");
    exit;
}

// Working with names (comma separated ids)
$working_with_names = '-';
if (!empty($row['working_with_user_id']) && $row['working_with_user_id'] != '0') {
    $ids = $row['working_with_user_id'];
    $ww  = mysqli_query($conn, "
        SELECT CONCAT(e.first_name,' ',e.last_name) AS name, r.role_name
        FROM tbl_users u
        INNER JOIN tbl_employees e ON e.id = u.employee_id
        INNER JOIN tbl_roles r     ON r.id = u.role_id
        WHERE u.id IN ($ids)
    ");
    $names = [];
    while ($w = mysqli_fetch_assoc($ww)) {
        $names[] = $w['name'] . ' (' . strtoupper($w['role_name']) . ')';
    }
    $working_with_names = implode(', ', $names);
} elseif ($row['working_with_user_id'] == '0') {
    $working_with_names = 'Individual';
}

// Doctor calls
$doctorQuery = mysqli_query($conn, "
    SELECT dc.*, d.doctor_name
    FROM tbl_dcr_doctor_calls dc
    LEFT JOIN tbl_doctors d ON d.id = dc.doctor_id
    WHERE dc.dcr_id = '$id'
    ORDER BY dc.id ASC
");

// Chemist calls
$chemistQuery = mysqli_query($conn, "
    SELECT cc.*, c.chemist_name
    FROM tbl_dcr_chemist_calls cc
    LEFT JOIN tbl_chemists c ON c.id = cc.chemist_id
    WHERE cc.dcr_id = '$id'
    ORDER BY cc.id ASC
");

// Stockist calls
$stockistQuery = mysqli_query($conn, "
    SELECT sc.*, c.chemist_name AS stockist_name
    FROM tbl_dcr_stockist_calls sc
    LEFT JOIN tbl_chemists c ON c.id = sc.stockist_id
    WHERE sc.dcr_id = '$id'
    ORDER BY sc.id ASC
");

// Expense
$expense = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM tbl_dcr_expenses WHERE dcr_id = '$id' LIMIT 1
"));

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
                        <h4 class="mb-0">DCR Details</h4>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            <?php echo $row['dcr_no']; ?> &nbsp;|&nbsp;
                            <?php echo date('d M Y', strtotime($row['visit_date'])); ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="dcr-list.php" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="row g-3">

                    <!-- LEFT -->
                    <div class="col-lg-4">

                        <!-- DCR Info -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-file-info-line me-1"></i> General Info</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;width:45%;">DCR No</td>
                                        <td style="font-size:13px;font-weight:500;"><?php echo $row['dcr_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">MR Name</td>
                                        <td style="font-size:13px;"><?php echo $row['mr_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Emp Code</td>
                                        <td style="font-size:13px;"><?php echo $row['employee_code']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Visit Date</td>
                                        <td style="font-size:13px;"><?php echo date('d M Y', strtotime($row['visit_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">HQ</td>
                                        <td style="font-size:13px;"><?php echo $row['hq_name'] ?? '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Working Area</td>
                                        <td style="font-size:13px;"><?php echo $row['area_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Working Type</td>
                                        <td style="font-size:13px;"><?php echo $row['working_type_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Working With</td>
                                        <td style="font-size:13px;"><?php echo $working_with_names; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Total KM</td>
                                        <td style="font-size:13px;"><?php echo number_format($row['total_km'], 1); ?> km</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Submitted At</td>
                                        <td style="font-size:13px;"><?php echo date('d M Y h:i A', strtotime($row['submitted_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-3" style="font-size:13px;">Status</td>
                                        <?php
                                        $badgeClass = [
                                            'Draft'         => 'bg-secondary',
                                            'Submitted'     => 'bg-primary',
                                            'ABM Approved'  => 'bg-info',
                                            'RBM Approved'  => 'bg-warning text-dark',
                                            'ZSM Approved'  => 'bg-primary',
                                            'Approved'      => 'bg-success',
                                        ];

                                        $status = $row['status'];
                                        $class = $badgeClass[$status] ?? 'bg-dark';
                                        ?>

                                        <td>
                                            <span class="badge <?= $class; ?>">
                                                <?= htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Call Summary -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-bar-chart-line me-1"></i> Call Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background:#eef2ff;">
                                    <span style="font-size:13px;"><i class="ri-user-heart-line me-1 text-primary"></i> Doctor Calls</span>
                                    <span class="badge bg-primary"><?php echo $row['doctor_call_count']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background:#d1e7dd;">
                                    <span style="font-size:13px;"><i class="ri-capsule-line me-1 text-success"></i> Chemist Calls</span>
                                    <span class="badge bg-success"><?php echo $row['chemist_call_count']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#fff3cd;">
                                    <span style="font-size:13px;"><i class="ri-store-2-line me-1 text-warning"></i> Stockist Calls</span>
                                    <span class="badge bg-warning text-dark"><?php echo $row['stockist_call_count']; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Expense Summary -->
                        <?php if ($expense) { ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="ri-money-rupee-circle-line me-1"></i> Expense Summary</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted ps-3" style="font-size:13px;">Travel</td>
                                            <td class="text-end pe-3" style="font-size:13px;">₹ <?php echo number_format($expense['travel_expense'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3" style="font-size:13px;">DA</td>
                                            <td class="text-end pe-3" style="font-size:13px;">₹ <?php echo number_format($expense['da_expense'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3" style="font-size:13px;">Hotel</td>
                                            <td class="text-end pe-3" style="font-size:13px;">₹ <?php echo number_format($expense['hotel_expense'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3" style="font-size:13px;">Food</td>
                                            <td class="text-end pe-3" style="font-size:13px;">₹ <?php echo number_format($expense['food_expense'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3" style="font-size:13px;">Other</td>
                                            <td class="text-end pe-3" style="font-size:13px;">₹ <?php echo number_format($expense['other_expense'], 2); ?></td>
                                        </tr>
                                        <tr class="table-success">
                                            <td class="ps-3 fw-bold" style="font-size:13px;">Total</td>
                                            <td class="text-end pe-3 fw-bold" style="font-size:14px;">₹ <?php echo number_format($expense['total_expense'], 2); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Remarks & Achievement -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-quill-pen-line me-1"></i> Remarks & Achievement</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-1" style="font-size:12px;">Day Remarks</p>
                                <p style="font-size:13px;"><?php echo !empty($row['remarks']) ? nl2br(htmlspecialchars($row['remarks'])) : '-'; ?></p>
                                <hr>
                                <p class="text-muted mb-1" style="font-size:12px;">Achievement</p>
                                <p style="font-size:13px;"><?php echo !empty($row['achievement']) ? nl2br(htmlspecialchars($row['achievement'])) : '-'; ?></p>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-lg-8">

                        <!-- Doctor Calls -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="ri-user-heart-line me-1 text-primary"></i> Doctor Calls</h6>
                                <span class="badge bg-primary"><?php echo $row['doctor_call_count']; ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:4%;">#</th>
                                                <th>Doctor</th>
                                                <th>Visit Time</th>
                                                <th>Samples</th>
                                                <th>Pain Products</th>
                                                <th>Gifts</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sl = 1;
                                            if (mysqli_num_rows($doctorQuery) == 0) { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">No doctor calls found.</td>
                                                </tr>
                                            <?php }
                                            while ($doctor = mysqli_fetch_assoc($doctorQuery)) {

                                                // Samples for this doctor call
                                                $samples = mysqli_query($conn, "
                                                    SELECT dp.quantity, p.product_name
                                                    FROM tbl_dcr_doctor_products dp
                                                    LEFT JOIN tbl_products p ON p.id = dp.product_id
                                                    WHERE dp.doctor_call_id = '{$doctor['id']}'
                                                ");
                                                $sample_list = [];
                                                while ($s = mysqli_fetch_assoc($samples)) {
                                                    $sample_list[] = $s['product_name'] . ' (' . $s['quantity'] . ')';
                                                }

                                                // Gifts for this doctor call
                                                $gifts = mysqli_query($conn, "
                                                    SELECT dg.quantity, g.gift_name
                                                    FROM tbl_dcr_gifts dg
                                                    LEFT JOIN tbl_gifts g ON g.id = dg.gift_id
                                                    WHERE dg.dcr_doctor_call_id = '{$doctor['id']}'
                                                ");
                                                $gift_list = [];
                                                while ($g = mysqli_fetch_assoc($gifts)) {
                                                    $gift_list[] = $g['gift_name'] . ' (' . $g['quantity'] . ')';
                                                }

                                                // Pain Products
                                                $pains = mysqli_query($conn, "
                                                SELECT
                                                pm.quantity,
                                                p.pain_product_name
                                                FROM tbl_dcr_doctor_call_pain_management pm
                                                LEFT JOIN tbl_pain_management_products p
                                                ON p.id = pm.pain_product_id
                                                WHERE pm.doctor_call_id = '{$doctor['id']}'
                                               ");

                                                $pain_list = [];

                                                while ($pp = mysqli_fetch_assoc($pains)) {

                                                    $pain_list[] = $pp['pain_product_name'] . ' (' . $pp['quantity'] . ')';
                                                }

                                            ?>
                                                <tr>
                                                    <td><?php echo $sl++; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($doctor['doctor_name']); ?></strong></td>
                                                    <td><?php echo !empty($doctor['visit_time']) ? date('h:i A', strtotime($doctor['visit_time'])) : '-'; ?></td>
                                                    <td style="font-size:12px;">
                                                        <?php echo !empty($sample_list) ? implode('<br>', $sample_list) : '<span class="text-muted">-</span>'; ?>
                                                    </td>
                                                    <td style="font-size:12px;">

                                                        <?php

                                                        echo !empty($pain_list)
                                                            ? implode("<br>", $pain_list)
                                                            : '<span class="text-muted">-</span>';

                                                        ?>

                                                    </td>
                                                    <td style="font-size:12px;">
                                                        <?php echo !empty($gift_list) ? implode('<br>', $gift_list) : '<span class="text-muted">-</span>'; ?>
                                                    </td>
                                                    <td style="font-size:12px;"><?php echo !empty($doctor['remarks']) ? nl2br(htmlspecialchars($doctor['remarks'])) : '-'; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Chemist Calls -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="ri-capsule-line me-1 text-success"></i> Chemist Calls</h6>
                                <span class="badge bg-success"><?php echo $row['chemist_call_count']; ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:4%;">#</th>
                                                <th>Chemist</th>
                                                <th>Visit Time</th>
                                                <th>POB</th>
                                                <th>Booking Value</th>
                                                <th>Products</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sl = 1;
                                            if (mysqli_num_rows($chemistQuery) == 0) { ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-3">No chemist calls found.</td>
                                                </tr>
                                            <?php }
                                            while ($chemist = mysqli_fetch_assoc($chemistQuery)) {

                                                // Products for this chemist call
                                                $products = mysqli_query($conn, "
                                                    SELECT p.product_name
                                                    FROM tbl_dcr_chemist_products cp
                                                    LEFT JOIN tbl_products p ON p.id = cp.product_id
                                                    WHERE cp.chemist_call_id = '{$chemist['id']}'
                                                ");
                                                $product_list = [];
                                                while ($p = mysqli_fetch_assoc($products)) {
                                                    $product_list[] = $p['product_name'];
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $sl++; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($chemist['chemist_name']); ?></strong></td>
                                                    <td><?php echo !empty($chemist['visit_time']) ? date('h:i A', strtotime($chemist['visit_time'])) : '-'; ?></td>
                                                    <td>₹ <?php echo number_format($chemist['pob'] ?? 0, 2); ?></td>
                                                    <td>₹ <?php echo number_format($chemist['booking_value'] ?? 0, 2); ?></td>
                                                    <td style="font-size:12px;">
                                                        <?php echo !empty($product_list) ? implode('<br>', $product_list) : '<span class="text-muted">-</span>'; ?>
                                                    </td>
                                                    <td style="font-size:12px;"><?php echo !empty($chemist['remarks']) ? htmlspecialchars($chemist['remarks']) : '-'; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Stockist Calls -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="ri-store-2-line me-1 text-warning"></i> Stockist Calls</h6>
                                <span class="badge bg-warning text-dark"><?php echo $row['stockist_call_count']; ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:4%;">#</th>
                                                <th>Stockist</th>
                                                <th>Visit Time</th>
                                                <th>Primary Order</th>
                                                <th>Products</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sl = 1;
                                            if (mysqli_num_rows($stockistQuery) == 0) { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">No stockist calls found.</td>
                                                </tr>
                                            <?php }
                                            while ($stockist = mysqli_fetch_assoc($stockistQuery)) {

                                                // Products for this stockist call
                                                $products = mysqli_query($conn, "
                                                    SELECT p.product_name
                                                    FROM tbl_dcr_stockist_products sp
                                                    LEFT JOIN tbl_products p ON p.id = sp.product_id
                                                    WHERE sp.stockist_call_id = '{$stockist['id']}'
                                                ");
                                                $product_list = [];
                                                while ($p = mysqli_fetch_assoc($products)) {
                                                    $product_list[] = $p['product_name'];
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $sl++; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($stockist['stockist_name']); ?></strong></td>
                                                    <td><?php echo !empty($stockist['visit_time']) ? date('h:i A', strtotime($stockist['visit_time'])) : '-'; ?></td>
                                                    <td>₹ <?php echo number_format($stockist['primary_order'] ?? 0, 2); ?></td>
                                                    <td style="font-size:12px;">
                                                        <?php echo !empty($product_list) ? implode('<br>', $product_list) : '<span class="text-muted">-</span>'; ?>
                                                    </td>
                                                    <td style="font-size:12px;"><?php echo !empty($stockist['remarks']) ? htmlspecialchars($stockist['remarks']) : '-'; ?></td>
                                                </tr>
                                            <?php } ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php include('./includes/footer.php'); ?>
    </div>
</div>

<?php include('./includes/scripts.php'); ?>