<?php
require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$mr_id = $_SESSION['user_id'];

// Get employee_id from user
$emp_result  = mysqli_query($conn, "SELECT employee_id FROM tbl_users WHERE id = '$mr_id'");
$emp_row     = mysqli_fetch_assoc($emp_result);
$employee_id = $emp_row['employee_id'];

// Filters
$month  = isset($_GET['month'])  ? $_GET['month']  : date('m');
$year   = isset($_GET['year'])   ? $_GET['year']   : date('Y');
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build where
$where = "WHERE a.employee_id = '$employee_id'
          AND MONTH(a.attendance_date) = '$month'
          AND YEAR(a.attendance_date)  = '$year'";

if ($status != '') {
    $status_esc = mysqli_real_escape_string($conn, $status);
    $where .= " AND a.attendance_status = '$status_esc'";
}

// Fetch records
$result = mysqli_query($conn, "
    SELECT
        a.*,
        e.employee_code,
        CONCAT(e.first_name,' ',e.last_name) AS employee_name,
        d.dcr_no
    FROM tbl_attendance a
    LEFT JOIN tbl_employees e ON e.id = a.employee_id
    LEFT JOIN tbl_dcr d       ON d.id = a.dcr_id
    $where
    ORDER BY a.attendance_date DESC
");

// Count summary
$present = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM tbl_attendance
    WHERE employee_id = '$employee_id'
    AND MONTH(attendance_date) = '$month'
    AND YEAR(attendance_date)  = '$year'
    AND attendance_status = 'Present'
"));

$absent = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM tbl_attendance
    WHERE employee_id = '$employee_id'
    AND MONTH(attendance_date) = '$month'
    AND YEAR(attendance_date)  = '$year'
    AND attendance_status = 'Absent'
"));

$leave = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM tbl_attendance
    WHERE employee_id = '$employee_id'
    AND MONTH(attendance_date) = '$month'
    AND YEAR(attendance_date)  = '$year'
    AND attendance_status = 'Leave'
"));

$holiday = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM tbl_attendance
    WHERE employee_id = '$employee_id'
    AND MONTH(attendance_date) = '$month'
    AND YEAR(attendance_date)  = '$year'
    AND attendance_status = 'Holiday'
"));

$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
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
                        <h4 class="mb-0">My Attendance</h4>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?> <?php echo $year; ?>
                        </p>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-3 g-3">
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Total Days</p>
                                <h3 class="mb-0 text-primary"><?php echo $total_days; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Present</p>
                                <h3 class="mb-0 text-success"><?php echo $present; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Absent</p>
                                <h3 class="mb-0 text-danger"><?php echo $absent; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Leave</p>
                                <h3 class="mb-0 text-warning"><?php echo $leave; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Holiday</p>
                                <h3 class="mb-0 text-info"><?php echo $holiday; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Not Marked</p>
                                <h3 class="mb-0 text-secondary"><?php echo $total_days - $present - $absent - $leave - $holiday; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select form-select-sm">
                                    <?php for ($m = 1; $m <= 12; $m++) { ?>
                                        <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-select form-select-sm">
                                    <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--) { ?>
                                        <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="Present" <?php echo ($status == 'Present')  ? 'selected' : ''; ?>>Present</option>
                                    <option value="Absent" <?php echo ($status == 'Absent')   ? 'selected' : ''; ?>>Absent</option>
                                    <option value="Leave" <?php echo ($status == 'Leave')    ? 'selected' : ''; ?>>Leave</option>
                                    <option value="Holiday" <?php echo ($status == 'Holiday')  ? 'selected' : ''; ?>>Holiday</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="ri-search-line me-1"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="mr-attendance-list.php" class="btn btn-secondary btn-sm w-100">
                                    <i class="ri-refresh-line me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Attendance List —
                            <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?> <?php echo $year; ?>
                        </h5>
                        <span class="badge bg-primary">
                            <?php echo mysqli_num_rows($result); ?> Records
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:4%;">#</th>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Source</th>
                                        <th>DCR No.</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) == 0) { ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="ri-calendar-close-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                                No attendance records found.
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php $i = 1;
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>

                                            <td>
                                                <strong><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></strong>
                                            </td>

                                            <td>
                                                <?php
                                                $day = date('l', strtotime($row['attendance_date']));
                                                $day_color = ($day == 'Sunday') ? 'text-danger' : 'text-muted';
                                                ?>
                                                <span class="<?php echo $day_color; ?>" style="font-size:13px;">
                                                    <?php echo $day; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($row['check_in_time']) { ?>
                                                    <span class="text-success">
                                                        <i class="ri-login-circle-line me-1"></i>
                                                        <?php echo date('h:i A', strtotime($row['check_in_time'])); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if ($row['check_out_time']) { ?>
                                                    <span class="text-danger">
                                                        <i class="ri-logout-circle-line me-1"></i>
                                                        <?php echo date('h:i A', strtotime($row['check_out_time'])); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php
                                                if ($row['check_in_time'] && $row['check_out_time']) {
                                                    $in  = strtotime($row['check_in_time']);
                                                    $out = strtotime($row['check_out_time']);
                                                    $diff = $out - $in;
                                                    $hrs  = floor($diff / 3600);
                                                    $mins = floor(($diff % 3600) / 60);
                                                    echo "<span style='font-size:13px;'>{$hrs}h {$mins}m</span>";
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                $status_map = [
                                                    'Present' => 'success',
                                                    'Absent'  => 'danger',
                                                    'Leave'   => 'warning',
                                                    'Holiday' => 'info',
                                                ];
                                                $badge = $status_map[$row['attendance_status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $badge; ?>">
                                                    <?php echo $row['attendance_status']; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($row['source'] == 'DCR') { ?>
                                                    <span class="badge bg-primary">
                                                        <i class="ri-file-list-line me-1"></i> DCR
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="ri-user-line me-1"></i> Manual
                                                    </span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if ($row['dcr_no']) { ?>
                                                    <a href="dcr-view.php?id=<?php echo $row['dcr_id']; ?>"
                                                        class="text-primary" style="font-size:12px;">
                                                        <?php echo $row['dcr_no']; ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>

                                            <td style="font-size:13px;">
                                                <?php echo $row['remarks'] ?? '-'; ?>
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
    </div>

    <?php include('./includes/footer.php'); ?>
</div>

<?php include('./includes/scripts.php'); ?>