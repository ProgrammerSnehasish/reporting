<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);


$sql = "

SELECT

tp.id,
tp.month,
tp.year,
tp.status,
tp.submitted_at,
tp.created_at,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) AS employee_name,

d.designation_name,
d.designation_code,

COUNT(tpd.id) AS total_days

FROM tbl_tour_plans tp

INNER JOIN tbl_users u
ON u.id = tp.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_designations d
ON d.id = e.designation_id

LEFT JOIN tbl_tour_plan_details tpd
ON tpd.tour_plan_id = tp.id

GROUP BY
tp.id,
tp.month,
tp.year,
tp.status,
tp.submitted_at,
tp.created_at,
e.employee_code,
employee_name,
d.designation_name,
d.designation_code

ORDER BY
tp.year DESC,
tp.month DESC,
e.employee_code ASC

";

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
                            <h4 class="mb-sm-0">Tour Plans</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Tour Plans</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->



                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">Tour Plans</h4>


                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%">

                                    <thead>

                                        <tr>

                                            <th>Sl</th>

                                            <th>Employee</th>

                                            <th>Designation</th>

                                            <th>Month</th>

                                            <th>Year</th>

                                            <th>Total Days</th>

                                            <th>Status</th>

                                            <th>Submitted</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $sl = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td>

                                                    <?= strtoupper($row['designation_code']); ?>

                                                </td>

                                                <td>

                                                    <?= date("F", mktime(0, 0, 0, $row['month'], 1)); ?>

                                                </td>

                                                <td>

                                                    <?= $row['year']; ?>

                                                </td>

                                                <td>

                                                    <span class="badge bg-info">

                                                        <?= $row['total_days']; ?>

                                                    </span>

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

                                                            echo '<span class="badge bg-success">Approved</span>';

                                                            break;

                                                        case 3:

                                                            echo '<span class="badge bg-danger">Rejected</span>';

                                                            break;

                                                        default:

                                                            echo '<span class="badge bg-dark">Unknown</span>';
                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <?= !empty($row['submitted_at'])
                                                        ? date("d-m-Y", strtotime($row['submitted_at']))
                                                        : "-"; ?>

                                                </td>

                                                <td>

                                                    <a href="tour-plan-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>

                                                    <a href="tour-day-plan-list.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-warning btn-sm">

                                                        Day Plan

                                                    </a>

                                                    <!-- <a href="tour-plan-edit.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-primary btn-sm">

                                                        Edit

                                                    </a> -->

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