<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

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
                            Add Tour Plan
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="tour-plan-list.php" class="btn btn-primary btn-sm">

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

                                    Add Tour Plan

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

                                    <form action="tour-plan-process.php" method="POST">

                                        <div class="mb-3">
                                            <label>Plan Month <span class="text-danger">*</span></label>
                                            <input type="month"
                                                name="plan_month"
                                                class="form-control"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Plan Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                name="plan_date"
                                                class="form-control"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Area <span class="text-danger">*</span></label>

                                            <select name="area_id"
                                                class="form-select"
                                                required>

                                                <option value="">Select Area</option>

                                                <?php

                                                $area = mysqli_query($conn, "
                                                SELECT id,area_name
                                                FROM tbl_areas
                                                WHERE status=1
                                                ORDER BY area_name");

                                                while ($a = mysqli_fetch_assoc($area)) {
                                                ?>

                                                    <option value="<?= $a['id']; ?>">

                                                        <?= $a['area_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label>HQ</label>

                                            <input type="text"
                                                name="hq_name"
                                                class="form-control">

                                        </div>

                                        <div class="mb-3">

                                            <label>Remarks</label>

                                            <textarea
                                                name="remarks"
                                                rows="3"
                                                class="form-control"></textarea>

                                        </div>

                                        <hr>

                                        <h5>Doctor Planning</h5>

                                        <table class="table table-bordered">

                                            <thead>

                                                <tr>

                                                    <th width="45%">Doctor</th>

                                                    <th width="20%">Priority</th>

                                                    <th>Remarks</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                <?php

                                                $doctor = mysqli_query($conn, "
SELECT id,doctor_name
FROM tbl_doctors
WHERE status=1
ORDER BY doctor_name");

                                                for ($i = 0; $i < 10; $i++) {

                                                ?>

                                                    <tr>

                                                        <td>

                                                            <select
                                                                name="doctor_id[]"
                                                                class="form-select">

                                                                <option value="">Select Doctor</option>

                                                                <?php

                                                                mysqli_data_seek($doctor, 0);

                                                                while ($d = mysqli_fetch_assoc($doctor)) {

                                                                ?>

                                                                    <option value="<?= $d['id']; ?>">

                                                                        <?= $d['doctor_name']; ?>

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </td>

                                                        <td>

                                                            <select
                                                                name="doctor_priority[]"
                                                                class="form-select">

                                                                <option value="High">High</option>

                                                                <option value="Medium">Medium</option>

                                                                <option value="Low">Low</option>

                                                            </select>

                                                        </td>

                                                        <td>

                                                            <input
                                                                type="text"
                                                                name="doctor_remarks[]"
                                                                class="form-control">

                                                        </td>

                                                    </tr>

                                                <?php } ?>

                                            </tbody>

                                        </table>


                                        <hr>

                                        <h5>Chemist Planning</h5>

                                        <table class="table table-bordered">

                                            <thead>

                                                <tr>

                                                    <th>Chemist</th>

                                                    <th>Remarks</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                <?php

                                                $chemist = mysqli_query($conn, "
SELECT id,chemist_name
FROM tbl_chemists
WHERE status=1
ORDER BY chemist_name");

                                                for ($i = 0; $i < 5; $i++) {

                                                ?>

                                                    <tr>

                                                        <td>

                                                            <select
                                                                name="chemist_id[]"
                                                                class="form-select">

                                                                <option value="">Select Chemist</option>

                                                                <?php

                                                                mysqli_data_seek($chemist, 0);

                                                                while ($c = mysqli_fetch_assoc($chemist)) {

                                                                ?>

                                                                    <option value="<?= $c['id']; ?>">

                                                                        <?= $c['chemist_name']; ?>

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </td>

                                                        <td>

                                                            <input
                                                                type="text"
                                                                name="chemist_remarks[]"
                                                                class="form-control">

                                                        </td>

                                                    </tr>

                                                <?php } ?>

                                            </tbody>

                                        </table>


                                        <hr>

                                        <h5>Stockist Planning</h5>

                                        <table class="table table-bordered">

                                            <thead>

                                                <tr>

                                                    <th>Stockist</th>

                                                    <th>Remarks</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                <?php

                                                $stockist = mysqli_query($conn, "
SELECT id,chemist_name
FROM tbl_chemists
WHERE chemist_type_id=2
AND status=1
ORDER BY chemist_name");

                                                for ($i = 0; $i < 3; $i++) {

                                                ?>

                                                    <tr>

                                                        <td>

                                                            <select
                                                                name="stockist_id[]"
                                                                class="form-select">

                                                                <option value="">Select Stockist</option>

                                                                <?php

                                                                mysqli_data_seek($stockist, 0);

                                                                while ($s = mysqli_fetch_assoc($stockist)) {

                                                                ?>

                                                                    <option value="<?= $s['id']; ?>">

                                                                        <?= $s['chemist_name']; ?>

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </td>

                                                        <td>

                                                            <input
                                                                type="text"
                                                                name="stockist_remarks[]"
                                                                class="form-control">

                                                        </td>

                                                    </tr>

                                                <?php } ?>

                                            </tbody>

                                        </table>

                                        <div class="mt-4">

                                            <button
                                                class="btn btn-primary">

                                                Save Tour Plan

                                            </button>

                                            <a href="tour-plan-list.php"
                                                class="btn btn-secondary">

                                                Cancel

                                            </a>

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