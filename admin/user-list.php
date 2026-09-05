<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "

SELECT

u.id,

u.username,
u.email,
u.mobile,
u.status,
u.last_login,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

r.role_name

FROM tbl_users u

INNER JOIN tbl_employees e
ON e.id=u.employee_id

INNER JOIN tbl_roles r
ON r.id=u.role_id

ORDER BY

r.role_name,
e.first_name

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
                            <h4 class="mb-sm-0">User List</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">User List</li>
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

                                <h4 class="card-title">User List</h4>

                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>SL</th>
                                            <th>Employee Code</th>
                                            <th>Employee Name</th>
                                            <th>Role</th>
                                            <th>Username</th>
                                            <th>Mobile</th>
                                            <th>Status</th>
                                            <th width="180">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= $row['employee_code']; ?></td>

                                                <td><?= $row['employee_name']; ?></td>

                                                <td>

                                                    <span class="badge bg-primary">

                                                        <?= strtoupper($row['role_name']); ?>

                                                    </span>

                                                </td>

                                                <td><?= $row['username']; ?></td>


                                                <td><?= $row['mobile']; ?></td>

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

                                                    <!-- <a href="user-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        <i class="ri-eye-line"></i>

                                                    </a> -->

                                                    <!-- <a href="user-edit.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-primary btn-sm">

                                                        <i class="ri-pencil-line"></i>

                                                    </a> -->

                                                    <a href="user-reset-password.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-warning btn-sm">

                                                        <i class="ri-lock-password-line"></i>

                                                    </a>

                                                    <?php if ($row['status'] == 1) { ?>

                                                        <a
                                                            href="user-ban.php?id=<?= $row['id']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to ban this user?');">

                                                            <i class="ri-forbid-line"></i>

                                                            Ban

                                                        </a>

                                                    <?php } else { ?>

                                                        <a
                                                            href="user-unban.php?id=<?= $row['id']; ?>"
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to activate this user?');">

                                                            <i class="ri-checkbox-circle-line"></i>

                                                            Unban

                                                        </a>

                                                    <?php } ?>

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