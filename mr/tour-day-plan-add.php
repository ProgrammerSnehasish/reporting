<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: tour-plan-list.php");
    exit;
}

$id = (int)$_GET['id'];

// ── Fetch tour plan detail ───────────────────────────────────────────────────
$stmtTour = mysqli_prepare($conn, "
    SELECT
        tpd.*,
        a.area_name,
        a.area_code,
        hq.area_name  AS hq_name,
        hq.area_code  AS hq_code,
        wt.working_type_name
    FROM tbl_tour_plan_details tpd
    LEFT JOIN tbl_areas         a  ON a.id   = tpd.area_id
    LEFT JOIN tbl_areas         hq ON hq.id  = a.hq_id
    LEFT JOIN tbl_working_types wt ON wt.id  = tpd.working_type_id
    WHERE tpd.id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmtTour, 'i', $id);
mysqli_stmt_execute($stmtTour);
$tour = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTour));
mysqli_stmt_close($stmtTour);

if (!$tour) {
    header("Location: tour-plan-list.php");
    exit;
}

// ── Fetch working_with user names ────────────────────────────────────────────
// working_with_user_id is stored as "2,5,9" (comma-separated)
$workingWithNames = [];

if (!empty($tour['working_with_user_id'])) {

    // sanitize: keep only integers
    $userIds = array_filter(
        array_map('intval', explode(',', $tour['working_with_user_id']))
    );

    if (!empty($userIds)) {
        $placeholders = implode(',', $userIds); // safe — all are int

        $nameResult = mysqli_query($conn, "
            SELECT
                CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                r.role_name
            FROM tbl_users u
            INNER JOIN tbl_employees e ON e.id = u.employee_id
            INNER JOIN tbl_roles r     ON r.id = u.role_id
            WHERE u.id IN ($placeholders)
            ORDER BY e.first_name
        ");

        while ($row = mysqli_fetch_assoc($nameResult)) {
            $workingWithNames[] = $row['employee_name'] . ' (' . strtoupper($row['role_name']) . ')';
        }
    }
}

$workingWithDisplay = !empty($workingWithNames)
    ? implode(', ', $workingWithNames)
    : '-';

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
                        <h4 class="mb-0">Add Day Plan</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="tour-day-plan-list.php" class="btn btn-primary btn-sm">
                            <i class="ri-list-check"></i> View
                        </a>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="tour-day-plan-process.php" method="POST">

                    <input type="hidden" name="tour_plan_detail_id" value="<?= $tour['id'] ?>">
                    <input type="hidden" name="mr_id" value="<?= (int)$_SESSION['user_id'] ?>">
                    <input type="hidden" name="plan_date" value="<?= htmlspecialchars($tour['plan_date']) ?>">

                    <div class="card">

                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Day Plan</h5>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <!-- Read-only info fields -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="text" class="form-control"
                                        value="<?= date('d-m-Y', strtotime($tour['plan_date'])) ?>"
                                        readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">HQ</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars(($tour['hq_code'] ?? '') ? $tour['hq_code'] . ' - ' . $tour['hq_name'] : ($tour['hq_name'] ?? '-')) ?>"
                                        readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Area</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars(($tour['area_code'] ?? '') ? $tour['area_code'] . ' - ' . $tour['area_name'] : ($tour['area_name'] ?? '-')) ?>"
                                        readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Working Type</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($tour['working_type_name'] ?? '-') ?>"
                                        readonly>
                                </div>

                                <!-- Working With — all names comma joined -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Working With</label>
                                    <textarea class="form-control"
                                        rows="3"
                                        style="resize:none; overflow:hidden;"
                                        readonly><?= htmlspecialchars($workingWithDisplay) ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Night Halt</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($tour['night_halt'] ?? '-') ?>"
                                        readonly>
                                </div>

                                <!-- <div class="col-md-6 mb-3">
                                    <label class="form-label">Objective</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($tour['objective'] ?? '-') ?>"
                                        readonly>
                                </div> -->

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- Editable fields -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Doctor Visit Target <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="doctor_target"
                                        class="form-control"
                                        placeholder="0"
                                        required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Chemist Visit Target <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="chemist_target"
                                        class="form-control"
                                        placeholder="0"
                                        required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Stockist Visit Target <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="stockist_target"
                                        class="form-control"
                                        placeholder="0"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time</label>
                                    <input
                                        type="time"
                                        name="start_time"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time</label>
                                    <input
                                        type="time"
                                        name="end_time"
                                        class="form-control">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea
                                        name="remarks"
                                        rows="4"
                                        class="form-control"
                                        placeholder="Enter remarks"></textarea>
                                </div>

                            </div><!-- /.row -->

                        </div><!-- /.card-body -->

                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Save Day Plan
                            </button>
                        </div>

                    </div><!-- /.card -->

                </form>

            </div>
        </div>
    </div>

    <?php include('./includes/footer.php'); ?>

</div>

<?php include('./includes/scripts.php'); ?>