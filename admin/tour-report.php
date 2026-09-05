<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$month  = $_GET['month'] ?? '';
$year   = $_GET['year'] ?? '';
$mr_id  = $_GET['mr_id'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "

SELECT

tp.*,

e.employee_code,

CONCAT(e.first_name,' ',e.last_name) employee_name

FROM tbl_tour_plans tp

INNER JOIN tbl_users u
ON u.id=tp.mr_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

WHERE 1=1

";

if ($month != '') {

    $sql .= " AND tp.month='$month' ";
}

if ($year != '') {

    $sql .= " AND tp.year='$year' ";
}

if ($mr_id != '') {

    $sql .= " AND tp.mr_id='$mr_id' ";
}

if ($status != '') {

    $sql .= " AND tp.status='$status' ";
}

$sql .= " ORDER BY tp.year DESC,tp.month DESC,e.first_name";

$result = mysqli_query($conn, $sql);


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

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">TP Report</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">TP Report</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Row 1 -->
                <?php

                // Total Tour Plans
                $totalPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                "));

                // Pending Approval
                $pendingPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=1
                "));

                // Approved Tour Plans
                $approvedPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=3
                "));

                // Rejected Tour Plans
                $rejectedPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=4
                "));

                ?>

                <div class="row">

                    <!-- Total Tour Plans -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Total Tour Plans

                                        </p>

                                        <h4 class="mb-2">

                                            <?= $totalPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-primary rounded">

                                            <i class="ri-road-map-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Pending -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Pending Approval

                                        </p>

                                        <h4 class="mb-2 text-warning">

                                            <?= $pendingPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-warning rounded">

                                            <i class="ri-time-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Approved -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Approved Plans

                                        </p>

                                        <h4 class="mb-2 text-success">

                                            <?= $approvedPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-success rounded">

                                            <i class="ri-checkbox-circle-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Rejected -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Rejected Plans

                                        </p>

                                        <h4 class="mb-2 text-danger">

                                            <?= $rejectedPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-danger rounded">

                                            <i class="ri-close-circle-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Row 1 -->

                <div class="row">

                    <h4 class="card-title">TP Filter</h4>

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-2">
                                <label>Month</label>

                                <select name="month" class="form-select">

                                    <option value="">All Month</option>

                                    <?php

                                    for ($m = 1; $m <= 12; $m++) {

                                    ?>

                                        <option
                                            value="<?= $m; ?>"
                                            <?= (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : ''; ?>>

                                            <?= date("F", mktime(0, 0, 0, $m, 1)); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Year</label>

                                <select name="year" class="form-select">

                                    <option value="">All Year</option>

                                    <?php

                                    for ($y = date("Y"); $y >= 2022; $y--) {

                                    ?>

                                        <option
                                            value="<?= $y; ?>"
                                            <?= (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : ''; ?>>

                                            <?= $y; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label>MR</label>

                                <select name="mr_id" class="form-select">

                                    <option value="">All MR</option>

                                    <?php

                                    $mr = mysqli_query($conn, "

                                SELECT

                                u.id,

                                e.employee_code,

                                CONCAT(e.first_name,' ',e.last_name) employee_name

                                FROM tbl_users u

                                INNER JOIN tbl_employees e
                                ON e.id=u.employee_id

                                INNER JOIN tbl_roles r
                                ON r.id=u.role_id

                                WHERE LOWER(r.role_code)='mr'

                                ORDER BY e.first_name

                                ");

                                    while ($m = mysqli_fetch_assoc($mr)) {

                                    ?>

                                        <option
                                            value="<?= $m['id']; ?>"
                                            <?= (isset($_GET['mr_id']) && $_GET['mr_id'] == $m['id']) ? 'selected' : ''; ?>>

                                            <?= $m['employee_code']; ?>

                                            -

                                            <?= $m['employee_name']; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Status</label>

                                <select name="status" class="form-select">

                                    <option value="">All</option>

                                    <option value="0">Draft</option>

                                    <option value="1">Submitted</option>

                                    <option value="2">ABM Approved</option>

                                    <option value="3">RBM Approved</option>

                                    <option value="4">Rejected</option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label>&nbsp;</label>

                                <div>

                                    <button class="btn btn-primary">

                                        Search

                                    </button>

                                    <a href="tour-report.php"
                                        class="btn btn-secondary">

                                        Reset

                                    </a>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>SL</th>

                                            <th>Employee</th>

                                            <th>Month</th>

                                            <th>Year</th>

                                            <th>Total Days</th>

                                            <th>Submitted On</th>

                                            <th>Status</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                            $days = mysqli_fetch_assoc(mysqli_query($conn, "

                                            SELECT COUNT(*) total_days

                                            FROM tbl_tour_plan_details

                                            WHERE tour_plan_id='" . $row['id'] . "'

                                        "));

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td>

                                                    <?= date("F", mktime(0, 0, 0, $row['month'], 1)); ?>

                                                </td>

                                                <td>

                                                    <?= $row['year']; ?>

                                                </td>

                                                <td>

                                                    <span class="badge bg-info">

                                                        <?= $days['total_days']; ?> Days

                                                    </span>

                                                </td>

                                                <td>

                                                    <?= !empty($row['submitted_at'])

                                                        ? date("d M Y h:i A", strtotime($row['submitted_at']))

                                                        : "-"; ?>

                                                </td>

                                                <td>

                                                    <?php

                                                    switch ($row['status']) {

                                                        case 0:

                                                            echo '<span class="badge bg-secondary">Draft</span>';

                                                            break;

                                                        case 1:

                                                            echo '<span class="badge bg-primary">Submitted</span>';

                                                            break;

                                                        case 2:

                                                            echo '<span class="badge bg-info">ABM Approved</span>';

                                                            break;

                                                        case 3:

                                                            echo '<span class="badge bg-success">RBM Approved</span>';

                                                            break;

                                                        case 4:

                                                            echo '<span class="badge bg-danger">Rejected</span>';

                                                            break;

                                                        default:

                                                            echo '<span class="badge bg-dark">Unknown</span>';
                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <a href="tour-plan-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php include('./includes/footer.php'); ?>

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->



<?php include('./includes/scripts.php'); ?>

<!-- Required datatable js -->
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/libs/jszip/jszip.min.js"></script>
<script src="../assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="../assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

<script src="../assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="../assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>

<!-- Responsive examples -->
<script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Datatable init js -->
<script src="../assets/js/pages/datatables.init.js"></script>