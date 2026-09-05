<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

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
                            Add Target Month
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="target-list.php" class="btn btn-primary btn-sm">

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

                                    Add Target Month

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



                                    <form action="target-month-process.php" method="POST">

                                        <div class="row">

                                            <div class="col-md-3">
                                                <label>Month</label>

                                                <select name="target_month" class="form-select" required>

                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                    ?>

                                                        <option value="<?= $i ?>">

                                                            <?= date("F", mktime(0, 0, 0, $i, 1)); ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>


                                            <div class="col-md-3">

                                                <label>Year</label>

                                                <select name="target_year" class="form-select" required>

                                                    <?php

                                                    for ($y = date('Y'); $y <= date('Y') + 2; $y++) {

                                                    ?>

                                                        <option value="<?= $y ?>">

                                                            <?= $y ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>


                                            <div class="col-md-4">

                                                <label>Title</label>

                                                <input
                                                    type="text"
                                                    name="title"
                                                    class="form-control"
                                                    placeholder="July Target">

                                            </div>


                                            <div class="col-md-2">

                                                <label>Status</label>

                                                <select
                                                    name="status"
                                                    class="form-select">

                                                    <option value="1">Active</option>

                                                    <option value="0">Inactive</option>

                                                </select>

                                            </div>

                                        </div>

                                        <div class="mt-3">

                                            <button
                                                class="btn btn-primary">

                                                Save

                                            </button>

                                        </div>

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
    $("#receiver_type").change(function() {

        if ($(this).val() == "individual" || $(this).val() == "multiple") {
            $("#mrSection").removeClass("d-none");
        } else {
            $("#mrSection").addClass("d-none");
        }

    });
</script>