<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

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
                            Add Area
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="user-mapping-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View All User Mapping

                        </a>


                    </div>

                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Add User Mapping

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

                                    <form action="user-mapping-process.php" method="POST">

                                        <!-- Employee -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Employee

                                                <span class="text-danger">*</span>

                                            </label>

                                            <select
                                                name="employee_user_id"
                                                id="employee_user_id"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Employee --</option>

                                                <?php

                                                $emp = mysqli_query($conn, "

                                            SELECT

                                            u.id user_id,
                                            e.employee_code,
                                            CONCAT(e.first_name,' ',e.last_name) employee_name,
                                            r.role_code

                                            FROM tbl_users u

                                            INNER JOIN tbl_employees e
                                            ON e.id=u.employee_id

                                            INNER JOIN tbl_roles r
                                            ON r.id=u.role_id

                                            WHERE u.status=1

                                            ORDER BY e.first_name

                                            ");

                                                while ($e = mysqli_fetch_assoc($emp)) {

                                                ?>

                                                    <option
                                                        value="<?= $e['user_id']; ?>"
                                                        data-role="<?= strtolower($e['role_code']); ?>">

                                                        <?= $e['employee_code']; ?>

                                                        -

                                                        <?= $e['employee_name']; ?>

                                                        (<?= strtoupper($e['role_code']); ?>)

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Reporting Manager -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Reporting Manager

                                                <span class="text-danger">*</span>

                                            </label>

                                            <select
                                                name="manager_user_id"
                                                id="manager_user_id"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Reporting Manager --</option>

                                                <?php

                                                mysqli_data_seek($emp, 0);

                                                while ($m = mysqli_fetch_assoc($emp)) {

                                                ?>

                                                    <option
                                                        value="<?= $m['user_id']; ?>"
                                                        data-role="<?= strtolower($m['role_code']); ?>">

                                                        <?= $m['employee_code']; ?>

                                                        -

                                                        <?= $m['employee_name']; ?>

                                                        (<?= strtoupper($m['role_code']); ?>)

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Status -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Status

                                            </label>

                                            <select
                                                name="status"
                                                class="form-select">

                                                <option value="1">

                                                    Active

                                                </option>

                                                <option value="0">

                                                    Inactive

                                                </option>

                                            </select>

                                        </div>

                                        <button class="btn btn-primary">

                                            <i class="ri-save-line"></i>

                                            Save & Exit

                                        </button>

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

<script>
    document.getElementById("employee_id").addEventListener("change", function() {

        let roleCode = this.options[this.selectedIndex].dataset.role;

        let role = document.getElementById("role_id");

        role.selectedIndex = 0;

        for (let i = 0; i < role.options.length; i++) {

            if (role.options[i].dataset.role === roleCode) {

                role.selectedIndex = i;
                break;

            }

        }

    });
</script>