<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (empty($_GET['id'])) {

    header("Location: notice-list.php");
    exit;
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "

SELECT

n.*,

CONCAT(c.first_name,' ',c.last_name) AS created_by_name,

CONCAT(u.first_name,' ',u.last_name) AS updated_by_name

FROM tbl_notices n

LEFT JOIN tbl_users cu
ON cu.id = n.created_by

LEFT JOIN tbl_employees c
ON c.id = cu.employee_id

LEFT JOIN tbl_users uu
ON uu.id = n.updated_by

LEFT JOIN tbl_employees u
ON u.id = uu.employee_id

WHERE n.id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Notice not found.";

    header("Location: notice-list.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

?>
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
                            Notice Update
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="notice-list.php" class="btn btn-primary btn-sm">

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

                                    Notice Update

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



                                    <form action="notice-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                        <div class="row">

                                            <!-- Notice Title -->
                                            <div class="col-md-12">

                                                <div class="mb-3">

                                                    <label>
                                                        Notice Title
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="title"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($row['title']); ?>"
                                                        required>

                                                </div>

                                            </div>

                                            <!-- Notice Message -->
                                            <div class="col-md-12">

                                                <div class="mb-3">

                                                    <label>
                                                        Notice Message
                                                    </label>

                                                    <textarea
                                                        name="message"
                                                        rows="5"
                                                        class="form-control"
                                                        placeholder="Write notice details here..."><?= htmlspecialchars($row['message']); ?></textarea>

                                                </div>

                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-12">

                                                <div class="mb-3">

                                                    <label>
                                                        Status
                                                    </label>

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

                                            </div>

                                        </div>

                                        <button class="btn btn-primary">

                                            <i class="ri-save-line"></i>

                                            Update & Exit

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