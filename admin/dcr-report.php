<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$mr_id = $_GET['mr_id'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "

SELECT

d.*,

a.area_name,

e.employee_code,

CONCAT(e.first_name,' ',e.last_name) employee_name

FROM tbl_dcr d

INNER JOIN tbl_users u
ON u.id=d.mr_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

LEFT JOIN tbl_areas a
ON a.id=d.area_id

WHERE 1=1

";

if ($from_date != '') {

    $sql .= " AND d.visit_date>='$from_date' ";
}

if ($to_date != '') {

    $sql .= " AND d.visit_date<='$to_date' ";
}

if ($mr_id != '') {

    $sql .= " AND d.mr_id='$mr_id' ";
}

if ($status != '') {

    $sql .= " AND d.status='$status' ";
}

$sql .= " ORDER BY d.visit_date DESC";

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
                            <h4 class="mb-sm-0">DCR Report</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">DCR Report</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Row 1 -->
                <div class="row">

                    <?php

                    // Total DCR

                    $totalDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    "));

                    // Today's DCR

                    $todayDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE visit_date=CURDATE()
                    "));

                    // Current Month DCR

                    $currentMonthDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE MONTH(visit_date)=MONTH(CURDATE())
                    AND YEAR(visit_date)=YEAR(CURDATE())
                    "));

                    // Approved DCR

                    $approvedDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE status=1
                    "));

                    ?>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Total DCR
                                </p>

                                <h3><?= $totalDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Today's DCR
                                </p>

                                <h3><?= $todayDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Current Month
                                </p>

                                <h3><?= $currentMonthDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Approved DCR
                                </p>

                                <h3><?= $approvedDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Row 1 -->

                <div class="row">

                    <h4 class="card-title">DCR Filter</h4>

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-2">

                                <label>From Date</label>

                                <input
                                    type="date"
                                    name="from_date"
                                    class="form-control"
                                    value="<?= $_GET['from_date'] ?? ''; ?>">

                            </div>

                            <div class="col-md-2">

                                <label>To Date</label>

                                <input
                                    type="date"
                                    name="to_date"
                                    class="form-control"
                                    value="<?= $_GET['to_date'] ?? ''; ?>">

                            </div>

                            <div class="col-md-3">

                                <label>MR</label>

                                <select name="mr_id" class="form-select">

                                    <option value="">All MR</option>

                                    <?php

                                    $q = mysqli_query($conn, "

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

                                    while ($r = mysqli_fetch_assoc($q)) {

                                    ?>

                                        <option
                                            value="<?= $r['id']; ?>">

                                            <?= $r['employee_code']; ?>

                                            -

                                            <?= $r['employee_name']; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Status</label>

                                <select
                                    name="status"
                                    class="form-select">

                                    <option value="">All</option>

                                    <option value="0">Draft</option>

                                    <option value="1">Submitted</option>

                                    <option value="2">Approved</option>

                                    <option value="3">Rejected</option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label>&nbsp;</label>

                                <div>

                                    <button class="btn btn-primary">

                                        Search

                                    </button>

                                    <a
                                        href="dcr-report.php"
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

                                <table
                                    id="datatable-buttons"
                                    class="table table-bordered table-striped">

                                    <thead>

                                        <tr>

                                            <th>SL</th>

                                            <th>DCR No</th>

                                            <th>Date</th>

                                            <th>MR</th>

                                            <th>Area</th>

                                            <th>Doctor</th>

                                            <th>Chemist</th>

                                            <th>Stockist</th>

                                            <th>Status</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= $row['dcr_no']; ?></td>

                                                <td><?= date("d-m-Y", strtotime($row['visit_date'])); ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td><?= $row['area_name']; ?></td>

                                                <td><?= $row['doctor_call_count']; ?></td>

                                                <td><?= $row['chemist_call_count']; ?></td>

                                                <td><?= $row['stockist_call_count']; ?></td>

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

                                                            echo '<span class="badge bg-success">Approved</span>';

                                                            break;

                                                        case 3:

                                                            echo '<span class="badge bg-danger">Rejected</span>';

                                                            break;
                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <a
                                                        href="dcr-view.php?id=<?= $row['id']; ?>"
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