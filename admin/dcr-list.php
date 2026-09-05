<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

// Filters
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$filter_date_to   = isset($_GET['date_to'])   ? $_GET['date_to']   : date('Y-m-d');
$filter_status    = isset($_GET['status'])    ? $_GET['status']    : '';
$filter_mr        = isset($_GET['mr_id'])     ? (int) $_GET['mr_id'] : 0;
$filter_area      = isset($_GET['area_id'])   ? (int) $_GET['area_id'] : 0;

// Build WHERE
$where = "WHERE 1=1";
$where .= " AND dcr.visit_date BETWEEN '$filter_date_from' AND '$filter_date_to'";
if ($filter_status !== '') $where .= " AND dcr.status = '$filter_status'";
if ($filter_mr)            $where .= " AND dcr.mr_id = '$filter_mr'";
if ($filter_area)          $where .= " AND dcr.area_id = '$filter_area'";

// MR list for filter
$mrList = [];
$res = mysqli_query($conn, "
    SELECT u.id, e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name
    FROM tbl_users u
    INNER JOIN tbl_employees e ON e.id = u.employee_id
    INNER JOIN tbl_roles r     ON r.id = u.role_id
    WHERE r.role_code = 'mr' AND u.status = 1
    ORDER BY e.first_name
");
while ($r = mysqli_fetch_assoc($res)) $mrList[] = $r;

// Area list for filter
$areaList = [];
$res = mysqli_query($conn, "SELECT id, area_name FROM tbl_areas WHERE status = 1 ORDER BY area_name");
while ($r = mysqli_fetch_assoc($res)) $areaList[] = $r;

// Main query
$result = mysqli_query($conn, "
    SELECT
        dcr.id,
        dcr.dcr_no,
        dcr.visit_date,
        dcr.total_km,
        dcr.travel_expense,
        dcr.doctor_call_count,
        dcr.chemist_call_count,
        dcr.stockist_call_count,
        dcr.status,
        dcr.submitted_at,
        e.employee_code,
        CONCAT(e.first_name,' ',e.last_name) AS employee_name,
        a.area_name,
        hq.area_name AS hq_name,
        wt.working_type_name
    FROM tbl_dcr dcr
    INNER JOIN tbl_users u      ON u.id   = dcr.mr_id
    INNER JOIN tbl_employees e  ON e.id   = u.employee_id
    LEFT  JOIN tbl_areas a      ON a.id   = dcr.area_id
    LEFT  JOIN tbl_areas hq     ON hq.id  = dcr.hq_id
    LEFT  JOIN tbl_working_types wt ON wt.id = dcr.working_type_id
    $where
    ORDER BY dcr.visit_date DESC, dcr.id DESC
");

// Summary counts
$summary = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT
        COUNT(*)                                    AS total,
        SUM(dcr.status = 0)                         AS draft,
        SUM(dcr.status = 1)                         AS submitted,
        SUM(dcr.status = 2)                         AS approved,
        SUM(dcr.status = 3)                         AS rejected,
        SUM(dcr.doctor_call_count)                  AS total_doctors,
        SUM(dcr.chemist_call_count)                 AS total_chemists,
        SUM(dcr.stockist_call_count)                AS total_stockists
    FROM tbl_dcr dcr
    INNER JOIN tbl_users u     ON u.id  = dcr.mr_id
    INNER JOIN tbl_employees e ON e.id  = u.employee_id
    $where
"));
?>

<?php include('./includes/header.php'); ?>

<!-- DataTables CSS -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet">
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet">

<div id="layout-wrapper">
    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0">DCR List</h4>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            <?php echo date('d M Y', strtotime($filter_date_from)); ?> —
                            <?php echo date('d M Y', strtotime($filter_date_to)); ?>
                        </p>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-3 g-2">
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Total DCR</p>
                                <h3 class="mb-0 text-primary"><?php echo $summary['total']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Submitted</p>
                                <h3 class="mb-0 text-info"><?php echo $summary['submitted']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Approved</p>
                                <h3 class="mb-0 text-success"><?php echo $summary['approved']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Rejected</p>
                                <h3 class="mb-0 text-danger"><?php echo $summary['rejected']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Doctor Calls</p>
                                <h3 class="mb-0 text-primary"><?php echo $summary['total_doctors']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Chemist Calls</p>
                                <h3 class="mb-0 text-success"><?php echo $summary['total_chemists']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="<?php echo $filter_date_from; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="<?php echo $filter_date_to; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">MR</label>
                                <select name="mr_id" class="form-select form-select-sm">
                                    <option value="">All MR</option>
                                    <?php foreach ($mrList as $mr) { ?>
                                        <option value="<?php echo $mr['id']; ?>"
                                            <?php echo ($filter_mr == $mr['id']) ? 'selected' : ''; ?>>
                                            <?php echo $mr['employee_code']; ?> - <?php echo $mr['employee_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Area</label>
                                <select name="area_id" class="form-select form-select-sm">
                                    <option value="">All Areas</option>
                                    <?php foreach ($areaList as $area) { ?>
                                        <option value="<?php echo $area['id']; ?>"
                                            <?php echo ($filter_area == $area['id']) ? 'selected' : ''; ?>>
                                            <?php echo $area['area_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="0" <?php echo ($filter_status === '0') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="1" <?php echo ($filter_status === '1') ? 'selected' : ''; ?>>Submitted</option>
                                    <option value="2" <?php echo ($filter_status === '2') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="3" <?php echo ($filter_status === '3') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="ri-search-line"></i>
                                </button>
                                <a href="dcr-list.php" class="btn btn-secondary btn-sm w-100">
                                    <i class="ri-refresh-line"></i>
                                </a>
                            </div>
                        </form>


                    </div>
                </div>

                <!-- Table -->
                <div class="card">

                    <div class="mb-3">

                        <a href="dcr-download.php?date_from=<?php echo $filter_date_from; ?>&date_to=<?php echo $filter_date_to; ?>&mr_id=<?php echo $filter_mr; ?>&area_id=<?php echo $filter_area; ?>&status=<?php echo $filter_status; ?>"
                            class="btn btn-success btn-sm">
                            <i class="ri-file-excel-line me-1"></i> Download Excel
                        </a>

                    </div>


                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">DCR Records</h6>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($result); ?> Records</span>


                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="datatable-buttons"
                                class="table table-striped table-bordered dt-responsive nowrap mb-0"
                                style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>DCR No</th>
                                        <th>MR</th>
                                        <th>Date</th>
                                        <th>HQ / Area</th>
                                        <th>Working Type</th>
                                        <th>KM</th>
                                        <th>Calls</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sl = 1;
                                    if (mysqli_num_rows($result) == 0) { ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="ri-file-list-3-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                                No DCR records found.
                                            </td>
                                        </tr>
                                    <?php }
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo $sl++; ?></td>

                                            <td>
                                                <a href="dcr-view.php?id=<?php echo $row['id']; ?>"
                                                    class="text-primary fw-500" style="font-size:13px;">
                                                    <?php echo $row['dcr_no']; ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo date('h:i A', strtotime($row['submitted_at'])); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong style="font-size:13px;"><?php echo $row['employee_code']; ?></strong>
                                                <br>
                                                <span style="font-size:12px;"><?php echo $row['employee_name']; ?></span>
                                            </td>

                                            <td style="font-size:13px;">
                                                <?php echo date('d M Y', strtotime($row['visit_date'])); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo date('l', strtotime($row['visit_date'])); ?>
                                                </small>
                                            </td>

                                            <td style="font-size:12px;">
                                                <span class="text-muted"><?php echo $row['hq_name'] ?? '-'; ?></span>
                                                <br>
                                                <?php echo $row['area_name']; ?>
                                            </td>

                                            <td style="font-size:12px;">
                                                <?php echo $row['working_type_name'] ?? '-'; ?>
                                            </td>

                                            <td style="font-size:13px;">
                                                <?php echo number_format($row['total_km'], 1); ?> km
                                            </td>

                                            <td>
                                                <span class="badge bg-primary me-1" title="Doctor">
                                                    D: <?php echo $row['doctor_call_count']; ?>
                                                </span>
                                                <span class="badge bg-success me-1" title="Chemist">
                                                    C: <?php echo $row['chemist_call_count']; ?>
                                                </span>
                                                <span class="badge bg-warning text-dark" title="Stockist">
                                                    S: <?php echo $row['stockist_call_count']; ?>
                                                </span>
                                            </td>

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

                                            <td>
                                                <a href="dcr-view.php?id=<?php echo $row['id']; ?>"
                                                    class="btn btn-info btn-sm">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <?php if ($row['status'] == 1) { ?>
                                                    <a href="dcr-approve.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-success btn-sm"
                                                        onclick="return confirm('Approve this DCR?')">
                                                        <i class="ri-check-line"></i>
                                                    </a>
                                                    <a href="dcr-reject.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Reject this DCR?')">
                                                        <i class="ri-close-line"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include('./includes/footer.php'); ?>
    </div>
</div>

<?php include('./includes/scripts.php'); ?>

<!-- DataTables JS -->
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/libs/jszip/jszip.min.js"></script>
<script src="../assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="../assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/js/pages/datatables.init.js"></script>