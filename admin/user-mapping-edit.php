<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Mapping.";

    header("Location:user-mapping-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "

SELECT *

FROM tbl_user_mapping

WHERE id='$id'

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Mapping not found.";

    header("Location:user-mapping-list.php");
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
                            Edit User Mapping
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="user-mapping-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View

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

                                    Edit User Mapping

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

                                    <form action="user-mapping-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

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

                                                        data-role="<?= strtolower($e['role_code']); ?>"

                                                        <?= ($row['employee_user_id'] == $e['user_id']) ? 'selected' : ''; ?>>

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

                                                        data-role="<?= strtolower($m['role_code']); ?>"

                                                        <?= ($row['manager_user_id'] == $m['user_id']) ? 'selected' : ''; ?>>

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

                                            <label>Status</label>

                                            <select
                                                name="status"
                                                class="form-select">

                                                <option value="1" <?= ($row['status'] == 1) ? 'selected' : ''; ?>>

                                                    Active

                                                </option>

                                                <option value="0" <?= ($row['status'] == 0) ? 'selected' : ''; ?>>

                                                    Inactive

                                                </option>

                                            </select>

                                        </div>

                                        <button class="btn btn-primary">

                                            <i class="ri-save-line"></i>

                                            Update

                                        </button>

                                        <a href="user-mapping-list.php" class="btn btn-secondary">

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

<script>
    document.getElementById("employee_user_id").addEventListener("change", function() {

        let role = this.options[this.selectedIndex].dataset.role;

        let manager = document.getElementById("manager_user_id");

        Array.from(manager.options).forEach(function(option) {

            if (!option.dataset.role) return;

            option.hidden = true;

            switch (role) {

                case "mr":

                    option.hidden = !["abm", "rbm", "zsm", "nsm"].includes(option.dataset.role);

                    break;

                case "abm":

                    option.hidden = !["rbm", "zsm", "nsm"].includes(option.dataset.role);

                    break;

                case "rbm":

                    option.hidden = !["zsm", "nsm"].includes(option.dataset.role);

                    break;

                case "zsm":

                    option.hidden = option.dataset.role != "nsm";

                    break;

                case "nsm":

                case "admin":

                    option.hidden = true;

                    break;

            }

        });

    });

    document.getElementById("employee_user_id").dispatchEvent(new Event("change"));
</script>