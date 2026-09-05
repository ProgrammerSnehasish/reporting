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
                            Add Notice
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

                                    Add Notice

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



                                    <form action="message-process.php" method="POST">

                                        <div class="row">

                                            <!-- Subject -->
                                            <div class="col-md-12">

                                                <div class="mb-3">

                                                    <label>
                                                        Subject
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="subject"
                                                        class="form-control"
                                                        required>

                                                </div>

                                            </div>

                                            <!-- Send To -->
                                            <div class="col-md-6">

                                                <div class="mb-3">

                                                    <label>
                                                        Send To
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <select
                                                        name="receiver_type"
                                                        id="receiver_type"
                                                        class="form-select"
                                                        required>

                                                        <option value="">Select</option>

                                                        <option value="individual">
                                                            Individual MR
                                                        </option>

                                                        <option value="multiple">
                                                            Multiple MR
                                                        </option>

                                                        <option value="all">
                                                            All MR
                                                        </option>

                                                    </select>

                                                </div>

                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-6">

                                                <div class="mb-3">

                                                    <label>Status</label>

                                                    <select
                                                        name="status"
                                                        class="form-select">

                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <!-- MR List -->

                                            <div class="col-md-12 d-none" id="mrSection">

                                                <div class="mb-3">

                                                    <label>Select MR</label>

                                                    <select
                                                        name="receiver_ids[]"
                                                        class="form-select select2"
                                                        multiple>

                                                        <?php

                                                        $mr = mysqli_query($conn, "
                        SELECT
                            u.id,
                            e.employee_code,
                            CONCAT(e.first_name,' ',e.last_name) AS employee_name
                        FROM tbl_users u
                        INNER JOIN tbl_employees e
                            ON e.id=u.employee_id
                        WHERE u.role_id='3'
                        AND u.status='1'
                        ORDER BY e.first_name
                    ");

                                                        while ($row = mysqli_fetch_assoc($mr)) {
                                                        ?>

                                                            <option value="<?php echo $row['id']; ?>">

                                                                <?php
                                                                echo $row['employee_code'] . " - " . $row['employee_name'];
                                                                ?>

                                                            </option>

                                                        <?php
                                                        }
                                                        ?>

                                                    </select>

                                                    <small class="text-muted">
                                                        Hold Ctrl to select multiple MR.
                                                    </small>

                                                </div>

                                            </div>

                                            <!-- Message -->

                                            <div class="col-md-12">

                                                <div class="mb-3">

                                                    <label>
                                                        Message
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <textarea
                                                        name="message"
                                                        rows="6"
                                                        class="form-control"
                                                        required></textarea>

                                                </div>

                                            </div>

                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-primary">

                                            <i class="ri-send-plane-fill"></i>

                                            Send Message

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
    $("#receiver_type").change(function() {

        if ($(this).val() == "individual" || $(this).val() == "multiple") {
            $("#mrSection").removeClass("d-none");
        } else {
            $("#mrSection").addClass("d-none");
        }

    });
</script>