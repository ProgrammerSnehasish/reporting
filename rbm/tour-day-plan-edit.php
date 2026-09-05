<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Day Plan.";

    header("Location: tour-day-plan-list.php");
    exit;
}

$id = (int)$_GET['id'];
//exit;


$sql = "

SELECT

dp.*,

tpd.area_id,
tpd.plan_date,
tpd.night_halt,
tpd.objective,
tpd.working_with_user_id,

a.area_name,
a.area_code,
hq.area_name  AS hq_name,
hq.area_code  AS hq_code,

wt.working_type_name,

CONCAT(e.first_name,' ',e.last_name) AS working_with_name

FROM tbl_day_plans dp

INNER JOIN tbl_tour_plan_details tpd
ON tpd.id = dp.tour_plan_detail_id

LEFT JOIN tbl_areas a
ON a.id = tpd.area_id
LEFT JOIN tbl_areas hq
ON hq.id  = a.hq_id


LEFT JOIN tbl_working_types wt
ON wt.id = tpd.working_type_id

LEFT JOIN tbl_users u
ON u.id = tpd.working_with_user_id

LEFT JOIN tbl_employees e
ON e.id = u.employee_id

WHERE dp.id = '$id'

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    die("Day Plan Not Found");
}

// ── Fetch working_with user names ────────────────────────────────────────────
// working_with_user_id is stored as "2,5,9" (comma-separated)
$workingWithNames = [];

if (!empty($row['working_with_user_id'])) {

    // sanitize: keep only integers
    $userIds = array_filter(
        array_map('intval', explode(',', $row['working_with_user_id']))
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

        while ($wrow = mysqli_fetch_assoc($nameResult)) {
            $workingWithNames[] = $wrow['employee_name'] . ' (' . strtoupper($wrow['role_name']) . ')';
        }
    }
}

$workingWithDisplay = !empty($workingWithNames)
    ? implode(', ', $workingWithNames)
    : '-';

$dayplan = mysqli_query($conn, "SELECT * FROM `tbl_day_plans` WHERE id= $id");
$dayplanresult = mysqli_fetch_assoc($dayplan);

?>

<!-- DataTables -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<!-- Responsive datatable examples -->
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<!-- ========== Header Start ========== -->
<?php include('./includes/header.php'); ?>
<!-- ========== Header End ========== -->

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ========== Topnavbar Start ========== -->
    <?php include('./includes/navbar.php'); ?>
    <!-- ========== Topnavbar End ========== -->

    <!-- ========== Left Sidebar Start ========== -->
    <?php include('./includes/sidebar.php'); ?>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <!-- Page Title -->

                <div class="row mb-3">

                    <div class="col-md-6">

                        <h4 class="mb-0">
                            View Day Plan
                        </h4>

                    </div>


                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title mb-0">Tour Day Plan Details</h4>
                            </div>

                            <div class="card-body">


                                <?php if (isset($_SESSION['success'])) { ?>

                                    <div class="alert alert-success alert-dismissible fade show">

                                        <?php
                                        echo $_SESSION['success'];
                                        unset($_SESSION['success']);
                                        ?>

                                        <button class="btn-close" data-bs-dismiss="alert"></button>

                                    </div>

                                <?php } ?>


                                <?php if (isset($_SESSION['error'])) { ?>

                                    <div class="alert alert-danger alert-dismissible fade show">

                                        <?php
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?>

                                        <button class="btn-close" data-bs-dismiss="alert"></button>

                                    </div>

                                <?php } ?>


                                <form action="tour-day-plan-update.php" method="POST">

                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Plan Date</label>
                                            <input type="text"
                                                class="form-control"
                                                value="<?= date('d-m-Y', strtotime($row['plan_date'])); ?>"
                                                readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">HQ</label>
                                            <input type="text" class="form-control"
                                                value="<?= htmlspecialchars(($row['hq_code'] ?? '') ? $row['hq_code'] . ' - ' . $row['hq_name'] : ($row['hq_name'] ?? '-')) ?>"
                                                readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Area</label>
                                            <input type="text" class="form-control"
                                                value="<?= htmlspecialchars(($row['area_code'] ?? '') ? $row['area_code'] . ' - ' . $row['area_name'] : ($row['area_name'] ?? '-')) ?>"
                                                readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Working Type</label>
                                            <input type="text"
                                                class="form-control"
                                                value="<?= $row['working_type_name']; ?>"
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
                                            <input type="text"
                                                class="form-control"
                                                value="<?= $row['night_halt']; ?>"
                                                readonly>
                                        </div>



                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Doctor Visit Target</label>
                                            <input type="number"
                                                name="doctor_target"
                                                class="form-control"
                                                value="<?= $row['doctor_target']; ?>"
                                                min="0"
                                                required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Chemist Visit Target</label>
                                            <input type="number"
                                                name="chemist_target"
                                                class="form-control"
                                                value="<?= $row['chemist_target']; ?>"
                                                min="0"
                                                required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stockist Visit Target</label>
                                            <input type="number"
                                                name="stockist_target"
                                                class="form-control"
                                                value="<?= $row['stockist_target']; ?>"
                                                min="0"
                                                required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Time</label>
                                            <input type="time"
                                                name="start_time"
                                                class="form-control"
                                                value="<?= $row['start_time']; ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">End Time</label>
                                            <input type="time"
                                                name="end_time"
                                                class="form-control"
                                                value="<?= $row['end_time']; ?>">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Remarks</label>
                                            <textarea
                                                name="remarks"
                                                rows="4"
                                                class="form-control"><?= htmlspecialchars($row['remarks']); ?></textarea>
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i>
                                        Update Day Plan
                                    </button>

                                    <a href="tour-day-plan-view.php?id=<?= $row['id']; ?>" class="btn btn-secondary">
                                        Cancel
                                    </a>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- End Page-content -->

    <?php include('./includes/footer.php'); ?>

</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<?php include('./includes/scripts.php'); ?>