<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "

SELECT

ex.*,

d.dcr_no,
d.visit_date,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

a.area_name

FROM tbl_dcr_expenses ex

INNER JOIN tbl_dcr d
ON d.id = ex.dcr_id

INNER JOIN tbl_users u
ON u.id = ex.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_areas a
ON a.id = ex.area_id

ORDER BY
d.visit_date DESC,
ex.id DESC

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
                            <h4 class="mb-sm-0">Expense History</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Expense History</li>
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

                                <h4 class="card-title">Expense History</h4>


                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>SL</th>
                                            <th>DCR No</th>
                                            <th>Employee</th>
                                            <th>Date</th>
                                            <th>Area</th>
                                            <!-- <th>Travel</th>
                                            <th>DA</th>
                                            <th>Hotel</th>
                                            <th>Food</th>
                                            <th>Other</th> -->
                                            <th>Total</th>
                                            <!-- <th>Status</th> -->
                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $sl = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= $row['dcr_no']; ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td>

                                                    <?= date("d-m-Y", strtotime($row['visit_date'])); ?>

                                                </td>

                                                <td>

                                                    <?= $row['area_name']; ?>

                                                </td>

                                                <!-- <td class="text-end">

                                                    ₹<?= number_format($row['travel_expense'], 2); ?>

                                                </td> -->

                                                <!-- <td class="text-end">

                                                    ₹<?= number_format($row['da_expense'], 2); ?>

                                                </td> -->

                                                <!-- <td class="text-end">

                                                    ₹<?= number_format($row['hotel_expense'], 2); ?>

                                                </td>

                                                <td class="text-end">

                                                    ₹<?= number_format($row['food_expense'], 2); ?>

                                                </td> -->

                                                <!-- <td class="text-end">

                                                    ₹<?= number_format($row['other_expense'], 2); ?>

                                                </td> -->

                                                <td class="text-end">

                                                    <strong>

                                                        ₹<?= number_format($row['total_expense'], 2); ?>

                                                    </strong>

                                                </td>

                                                <!-- <td>

                                                    <!?php

                                                    if ($row['status'] == 1) {

                                                        echo '<span class="badge bg-success">Approved</span>';
                                                    } elseif ($row['status'] == 0) {

                                                        echo '<span class="badge bg-warning">Pending</span>';
                                                    } else {

                                                        echo '<span class="badge bg-danger">Rejected</span>';
                                                    }

                                                    ?->

                                                </td> -->

                                                <td>

                                                    <a href="expense-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>

                                                    <!-- <a href="expense-edit.php?id=<?= $row['id']; ?>"
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