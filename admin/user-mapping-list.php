<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "

SELECT

um.id,

um.status,
um.created_at,

eu.username AS employee_username,
ee.employee_code,
CONCAT(ee.first_name,' ',ee.last_name) AS employee_name,
er.role_name AS employee_role,

mu.username AS manager_username,
me.employee_code AS manager_code,
CONCAT(me.first_name,' ',me.last_name) AS manager_name,
mr.role_name AS manager_role

FROM tbl_user_mapping um

INNER JOIN tbl_users eu
ON eu.id = um.employee_user_id

INNER JOIN tbl_employees ee
ON ee.id = eu.employee_id

INNER JOIN tbl_roles er
ON er.id = eu.role_id

INNER JOIN tbl_users mu
ON mu.id = um.manager_user_id

INNER JOIN tbl_employees me
ON me.id = mu.employee_id

INNER JOIN tbl_roles mr
ON mr.id = mu.role_id

ORDER BY ee.employee_code ASC

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
                            <h4 class="mb-sm-0">Area List</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Area List</li>
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

                                <h4 class="card-title">User Mapping List</h4>
                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>Sl. No</th>
                                            <th>Employee</th>
                                            <th>Employee Role</th>
                                            <th>Reporting Manager</th>
                                            <th>Manager Role</th>
                                            <th>Status</th>
                                            <th>Created On</th>
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

                                                    <span class="badge bg-primary">

                                                        <?= strtoupper($row['employee_role']); ?>

                                                    </span>

                                                </td>

                                                <td>

                                                    <strong><?= $row['manager_code']; ?></strong>

                                                    <br>

                                                    <?= $row['manager_name']; ?>

                                                </td>

                                                <td>

                                                    <span class="badge bg-info">

                                                        <?= strtoupper($row['manager_role']); ?>

                                                    </span>

                                                </td>

                                                <td>

                                                    <?php if ($row['status'] == 1) { ?>

                                                        <span class="badge bg-success">

                                                            Active

                                                        </span>

                                                    <?php } else { ?>

                                                        <span class="badge bg-danger">

                                                            Inactive

                                                        </span>

                                                    <?php } ?>

                                                </td>

                                                <td>

                                                    <?= date('d-m-Y', strtotime($row['created_at'])); ?>

                                                </td>

                                                <td>

                                                    <a href="user-mapping-edit.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">

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