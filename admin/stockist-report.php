<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$from_date  = $_GET['from_date'] ?? '';
$to_date    = $_GET['to_date'] ?? '';
$stockist_id = $_GET['stockist_id'] ?? '';

$sql = "

SELECT

sc.id,

sc.visit_time,
sc.remarks,
sc.status,

c.chemist_code,
c.chemist_name,

ct.type_name,

a.area_name,

dr.dcr_no,
dr.visit_date,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name

FROM tbl_dcr_stockist_calls sc

INNER JOIN tbl_chemists c
ON c.id = sc.stockist_id

INNER JOIN tbl_chemist_types ct
ON ct.id = c.chemist_type_id

INNER JOIN tbl_dcr dr
ON dr.id = sc.dcr_id

INNER JOIN tbl_users u
ON u.id = dr.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_areas a
ON a.id = dr.area_id

WHERE LOWER(ct.type_name)='stockist'

";

if (!empty($from_date)) {
    $sql .= " AND dr.visit_date >= '$from_date' ";
}

if (!empty($to_date)) {
    $sql .= " AND dr.visit_date <= '$to_date' ";
}

if (!empty($stockist_id)) {
    $sql .= " AND sc.stockist_id='$stockist_id' ";
}

$sql .= " ORDER BY dr.visit_date DESC, sc.visit_time DESC";

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
                            <h4 class="mb-sm-0">Stockist Report</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Stockist Report</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Row 1 -->
                <div class="row">

                    <?php

                    $totalStockistVisit = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_chemists c
                    ON c.id = sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id = c.chemist_type_id

                    WHERE LOWER(ct.type_name)='stockist'

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Total Stockist Visit</p>
                                        <h4 class="mb-2"><?php echo $totalStockistVisit['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $todayStockist = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_dcr d
                    ON d.id=sc.dcr_id

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE

                    LOWER(ct.type_name)='stockist'

                    AND d.visit_date=CURDATE()

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Today's Stockist Calls</p>
                                        <h4 class="mb-2"><?php echo $todayStockist['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                    $currentMonth = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_dcr d
                    ON d.id=sc.dcr_id

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE

                    LOWER(ct.type_name)='stockist'

                    AND MONTH(d.visit_date)=MONTH(CURDATE())

                    AND YEAR(d.visit_date)=YEAR(CURDATE())

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Current Month Stockist Calls</p>
                                        <h4 class="mb-2"><?php echo $currentMonth['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                    $uniqueStockist = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(DISTINCT sc.stockist_id) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE LOWER(ct.type_name)='stockist'

                    "));


                    ?>

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Unique Stockists</p>
                                        <h4 class="mb-2"><?php echo $uniqueStockist['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>



                </div>
                <!-- Row 1 -->

                <div class="row">

                    <h4 class="card-title">Stockist Filter</h4>

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-3">

                                <label>From Date</label>

                                <input
                                    type="date"
                                    name="from_date"
                                    class="form-control"
                                    value="<?= htmlspecialchars($from_date); ?>">

                            </div>

                            <div class="col-md-3">

                                <label>To Date</label>

                                <input
                                    type="date"
                                    name="to_date"
                                    class="form-control"
                                    value="<?= htmlspecialchars($to_date); ?>">

                            </div>

                            <div class="col-md-3">

                                <label>Stockists</label>

                                <select name="stockist_id" class="form-select">

                                    <option value="">All Stockists</option>

                                    <?php

                                    $q = mysqli_query($conn, "

                                            SELECT

                                            c.id,
                                            c.chemist_name

                                            FROM tbl_chemists c

                                            INNER JOIN tbl_chemist_types t
                                            ON t.id=c.chemist_type_id

                                            WHERE LOWER(t.type_name)='stockist'

                                            ORDER BY c.chemist_name

                                            ");

                                    while ($r = mysqli_fetch_assoc($q)) {

                                    ?>

                                        <option
                                            value="<?= $r['id']; ?>"
                                            <?= ($stockist_id == $r['id']) ? 'selected' : ''; ?>>

                                            <?= $r['chemist_name']; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label>&nbsp;</label>

                                <button class="btn btn-primary w-100">

                                    Search

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <table id="datatable-buttons"
                                    class="table table-bordered table-striped">

                                    <thead>

                                        <tr>

                                            <th>SL</th>
                                            <th>Date</th>
                                            <th>DCR No</th>
                                            <th>MR</th>
                                            <th>Stockist Code</th>
                                            <th>Stockist Name</th>
                                            <th>Area</th>
                                            <th>Visit Time</th>
                                            <th>Remarks</th>
                                            <th>Status</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= date("d-m-Y", strtotime($row['visit_date'])); ?></td>

                                                <td><?= $row['dcr_no']; ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td><?= $row['chemist_code']; ?></td>

                                                <td><?= $row['chemist_name']; ?></td>

                                                <td><?= $row['area_name']; ?></td>

                                                <td><?= date("h:i A", strtotime($row['visit_time'])); ?></td>

                                                <td><?= !empty($row['remarks']) ? $row['remarks'] : "-"; ?></td>

                                                <td>

                                                    <?= $row['status'] == 1

                                                        ? '<span class="badge bg-success">Active</span>'

                                                        : '<span class="badge bg-danger">Inactive</span>'; ?>

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