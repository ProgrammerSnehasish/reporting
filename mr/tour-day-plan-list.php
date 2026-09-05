<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$sql = "

SELECT

dp.*,

tpd.plan_date,
tpd.objective,
tpd.night_halt,

a.area_name,

wt.working_type_name,

CONCAT(e.first_name,' ',e.last_name) AS working_with_name

FROM tbl_day_plans dp

INNER JOIN tbl_tour_plan_details tpd
ON tpd.id = dp.tour_plan_detail_id

LEFT JOIN tbl_areas a
ON a.id = tpd.area_id

LEFT JOIN tbl_working_types wt
ON wt.id = tpd.working_type_id

LEFT JOIN tbl_users u
ON u.id = tpd.working_with_user_id

LEFT JOIN tbl_employees e
ON e.id = u.employee_id

WHERE dp.mr_id = ?

ORDER BY dp.plan_date DESC

";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
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
                            <h4 class="mb-sm-0">Tour Day Plan History</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Tour Day Plan</li>
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

                                <h4 class="card-title"> Tour Day Plan History</h4>


                                <table id="datatable-buttons"
                                    class="table table-bordered table-striped dt-responsive nowrap">

                                    <thead>

                                        <tr>

                                            <th>SL</th>

                                            <th>Date</th>

                                            <th>Area</th>

                                            <th>Working Type</th>

                                            <th>Doctor</th>

                                            <th>Chemist</th>

                                            <th>Stockist</th>

                                            <th>Status</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $sl = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td>
                                                    <?= date('d-m-Y', strtotime($row['plan_date'])); ?>
                                                </td>

                                                <td><?= $row['area_name']; ?></td>

                                                <td><?= $row['working_type_name']; ?></td>

                                                <td class="text-center">
                                                    <?= $row['doctor_target']; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= $row['chemist_target']; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= $row['stockist_target']; ?>
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
                                                            echo '<span class="badge bg-success">ABM Approved</span>';
                                                            break;

                                                        case 3:
                                                            echo '<span class="badge bg-success">RBM Approved</span>';
                                                            break;

                                                        case 4:
                                                            echo '<span class="badge bg-danger">Rejected</span>';
                                                            break;
                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <a href="tour-day-plan-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">
                                                        View
                                                    </a>

                                                    <a href="tour-day-plan-edit.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-primary btn-sm">
                                                        Edit
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