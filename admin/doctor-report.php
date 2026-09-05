<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$from_date = $_GET['from_date'] ?? '';
$to_date   = $_GET['to_date'] ?? '';
$doctor_id = $_GET['doctor_id'] ?? '';

$sql = "

SELECT

dc.id,
dc.visit_time,
dc.remarks,
dc.status,

d.doctor_code,
d.doctor_name,

sp.specialization_name,

a.area_name,

dr.dcr_no,
dr.visit_date,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name

FROM tbl_dcr_doctor_calls dc

INNER JOIN tbl_doctors d
ON d.id = dc.doctor_id

LEFT JOIN tbl_specializations sp
ON sp.id = d.specialization_id

INNER JOIN tbl_dcr dr
ON dr.id = dc.dcr_id

INNER JOIN tbl_users u
ON u.id = dr.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_areas a
ON a.id = dr.area_id

WHERE 1=1

";

if (!empty($from_date)) {
    $sql .= " AND dr.visit_date >= '$from_date' ";
}

if (!empty($to_date)) {
    $sql .= " AND dr.visit_date <= '$to_date' ";
}

if (!empty($doctor_id)) {
    $sql .= " AND dc.doctor_id = '$doctor_id' ";
}

$sql .= " ORDER BY dr.visit_date DESC, dc.visit_time DESC";

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
                            <h4 class="mb-sm-0">Doctor Report</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Doctor Report</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">

                    <?php

                    $totalDoctorVisit = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls

                "));

                    $todayDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls dc

                INNER JOIN tbl_dcr d
                ON d.id = dc.dcr_id

                WHERE d.visit_date = CURDATE()

                "));

                    $currentMonthDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls dc

                INNER JOIN tbl_dcr d
                ON d.id = dc.dcr_id

                WHERE

                MONTH(d.visit_date)=MONTH(CURDATE())

                AND YEAR(d.visit_date)=YEAR(CURDATE())

                "));

                    $uniqueDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(DISTINCT doctor_id) total

                FROM tbl_dcr_doctor_calls

                "));

                    ?>


                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Total Doctor Visits</h6>

                                <h2><?= $totalDoctorVisit['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Today's Calls</h6>

                                <h2><?= $todayDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Current Month Calls</h6>

                                <h2><?= $currentMonthDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Unique Doctors</h6>

                                <h2><?= $uniqueDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">
                    <h4 class="card-title">Doctor Filter Report</h4>
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

                                <label>Doctor</label>

                                <select name="doctor_id" class="form-select">

                                    <option value="">All Doctor</option>

                                    <?php

                                    $q = mysqli_query($conn, "
                                                SELECT id,doctor_name
                                                FROM tbl_doctors
                                                ORDER BY doctor_name
                                                ");

                                    while ($r = mysqli_fetch_assoc($q)) {

                                    ?>

                                        <option value="<?= $r['id']; ?>"
                                            <?= ($doctor_id == $r['id']) ? 'selected' : ''; ?>>

                                            <?= $r['doctor_name']; ?>

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

                                            <th>Doctor Code</th>

                                            <th>Doctor Name</th>

                                            <th>Speciality</th>

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

                                                <td>

                                                    <?= date("d-m-Y", strtotime($row['visit_date'])); ?>

                                                </td>

                                                <td>

                                                    <?= $row['dcr_no']; ?>

                                                </td>

                                                <td>

                                                    <strong>

                                                        <?= $row['employee_code']; ?>

                                                    </strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td>

                                                    <?= $row['doctor_code']; ?>

                                                </td>

                                                <td>

                                                    <?= $row['doctor_name']; ?>

                                                </td>

                                                <td>

                                                    <?= !empty($row['specialization_name'])

                                                        ? $row['specialization_name']

                                                        : "-"; ?>

                                                </td>

                                                <td>

                                                    <?= $row['area_name']; ?>

                                                </td>

                                                <td>

                                                    <?= date("h:i A", strtotime($row['visit_time'])); ?>

                                                </td>

                                                <td>

                                                    <?= !empty($row['remarks'])

                                                        ? $row['remarks']

                                                        : "-"; ?>

                                                </td>

                                                <td>

                                                    <?php

                                                    echo ($row['status'] == 1)

                                                        ? '<span class="badge bg-success">Active</span>'

                                                        : '<span class="badge bg-danger">Inactive</span>';

                                                    ?>

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