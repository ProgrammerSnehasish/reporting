<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

// Target Month

$monthResult = mysqli_query($conn, "
SELECT *
FROM tbl_target_master
WHERE status='1'
ORDER BY target_year DESC,target_month DESC
");

// MR

$mrResult = mysqli_query($conn, "

SELECT

u.id,

e.employee_code,

CONCAT(e.first_name,' ',e.last_name) AS employee_name

FROM tbl_users u

INNER JOIN tbl_employees e
ON e.id=u.employee_id

INNER JOIN tbl_roles r
ON r.id=u.role_id

WHERE
r.role_code='mr'

AND u.status='1'

ORDER BY e.first_name

");

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
                            Assigned Products
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

                                    Assigned Products

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


                                    <form action="target-assign-process.php" method="POST">

                                        <div class="row">

                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">

                                                    Target Month <span class="text-danger">*</span>

                                                </label>

                                                <select name="target_master_id"
                                                    id="target_master_id"
                                                    class="form-select"
                                                    required>

                                                    <option value="">Select Month</option>

                                                    <?php while ($month = mysqli_fetch_assoc($monthResult)) { ?>

                                                        <option value="<?= $month['id']; ?>">

                                                            <?= date("F", mktime(0, 0, 0, $month['target_month'], 1)); ?>

                                                            -

                                                            <?= $month['target_year']; ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>


                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">

                                                    Medical Representative <span class="text-danger">*</span>

                                                </label>

                                                <select name="mr_user_id"
                                                    id="mr_user_id"
                                                    class="form-select"
                                                    required>

                                                    <option value="">Select MR</option>

                                                    <?php while ($mr = mysqli_fetch_assoc($mrResult)) { ?>

                                                        <option value="<?= $mr['id']; ?>">

                                                            <?= $mr['employee_code']; ?>

                                                            -

                                                            <?= $mr['employee_name']; ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>


                                            <div class="col-md-2 d-grid">

                                                <label>&nbsp;</label>

                                                <button
                                                    type="button"
                                                    id="loadProducts"
                                                    class="btn btn-primary">

                                                    <i class="ri-search-line"></i>

                                                    Load

                                                </button>

                                            </div>

                                        </div>


                                        <div id="productArea">

                                            <div class="card">

                                                <div class="card-header">

                                                    <h5 class="mb-0">
                                                        Assigned Products
                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <!-- table -->

                                                </div>

                                            </div>

                                        </div>

                                        <button class="btn btn-success">

                                            Save Target

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


<script>
    $("#loadProducts").click(function() {

        var mr = $("#mr_user_id").val();
        var target = $("#target_master_id").val();

        if (target == "") {
            alert("Select Target Month");
            return;
        }

        if (mr == "") {
            alert("Select MR");
            return;
        }

        $.ajax({

            url: "loadProducts.php",

            type: "POST",

            data: {
                mr_user_id: mr,
                target_master_id: target
            },

            success: function(data) {

                $("#productArea").html(data);

            }

        });

    });
</script>