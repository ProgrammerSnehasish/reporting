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

                        <a href="user-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View All User

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

                                    Add User

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

                                    <form action="user-process.php" method="POST">

                                        <!-- Employee -->

                                        <div class="mb-3">
                                            <label class="form-label">Employee <span class="text-danger">*</span></label>

                                            <select
                                                name="employee_id"
                                                id="employee_id"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Employee --</option>

                                                <?php

                                                $emp = mysqli_query($conn, "

                                                SELECT

                                                e.id,
                                                e.employee_code,
                                                CONCAT(e.first_name,' ',e.last_name) employee_name,
                                                d.designation_code

                                                FROM tbl_employees e

                                                INNER JOIN tbl_designations d
                                                ON d.id=e.designation_id

                                                WHERE e.status=1

                                                ORDER BY e.first_name

                                                ");

                                                while ($e = mysqli_fetch_assoc($emp)) {

                                                ?>

                                                    <option
                                                        value="<?= $e['id']; ?>"
                                                        data-role="<?= strtolower($e['designation_code']); ?>">

                                                        <?= $e['employee_code']; ?>

                                                        -

                                                        <?= $e['employee_name']; ?>

                                                        (<?= strtoupper($e['designation_code']); ?>)

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Role -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Role

                                                <span class="text-danger">*</span>

                                            </label>

                                            <select name="role_id" id="role_id" class="form-select" required>

                                                <option value="">-- Select Role --</option>

                                                <?php

                                                $role = mysqli_query($conn, "
                                                    SELECT id,role_name,role_code
                                                    FROM tbl_roles
                                                    WHERE status=1
                                                    ORDER BY role_name
                                                    ");

                                                while ($r = mysqli_fetch_assoc($role)) {

                                                ?>

                                                    <option
                                                        value="<?= $r['id']; ?>"
                                                        data-role="<?= strtolower($r['role_code']); ?>">

                                                        <?= strtoupper($r['role_name']); ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Username -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Username

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="text"
                                                name="username"
                                                class="form-control"
                                                required>

                                        </div>

                                        <!-- Email -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Email

                                            </label>

                                            <input
                                                type="email"
                                                name="email"
                                                class="form-control">

                                        </div>

                                        <!-- Mobile -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Mobile

                                            </label>

                                            <input
                                                type="text"
                                                name="mobile"
                                                class="form-control">

                                        </div>

                                        <!-- Password -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Password

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="password"
                                                name="password"
                                                class="form-control"
                                                required>

                                        </div>

                                        <!-- Confirm Password -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Confirm Password

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="password"
                                                name="confirm_password"
                                                class="form-control"
                                                required>

                                        </div>

                                        <!-- Status -->

                                        <div class="mb-3">

                                            <label class="form-label">

                                                Status

                                            </label>

                                            <select
                                                name="status"
                                                class="form-select">

                                                <option value="1" selected>

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