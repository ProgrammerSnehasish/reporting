<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid User.";

    header("Location: user-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "

SELECT

u.id,
u.username,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

r.role_name

FROM tbl_users u

INNER JOIN tbl_employees e
ON e.id=u.employee_id

INNER JOIN tbl_roles r
ON r.id=u.role_id

WHERE u.id='$id'

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "User not found.";

    header("Location:user-list.php");
    exit;
}

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
                            My Profile
                        </h4>

                    </div>



                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Change Password

                                </h4>

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

                                <div class="row">

                                    <form action="user-reset-password-process.php" method="POST">

                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                        <div class="mb-3">

                                            <label>User</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                readonly
                                                value="<?= $row['employee_code']; ?> - <?= $row['employee_name']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Username</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                readonly
                                                value="<?= $row['username']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Role</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                readonly
                                                value="<?= strtoupper($row['role_name']); ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>

                                                New Password

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="password"
                                                name="password"
                                                class="form-control"
                                                required>

                                        </div>

                                        <div class="mb-3">

                                            <label>

                                                Confirm Password

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="password"
                                                name="confirm_password"
                                                class="form-control"
                                                required>

                                        </div>

                                        <button class="btn btn-primary">

                                            <i class="ri-lock-password-line"></i>

                                            Reset Password

                                        </button>

                                        <a href="user-list.php" class="btn btn-secondary">

                                            Back

                                        </a>

                                    </form>




                                </div>



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