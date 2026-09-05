<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

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
                            Add Clinic
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="clinic-list.php" class="btn btn-primary btn-sm">

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

                                    Add Clinic

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

                                    <form action="clinic-process.php" method="POST">

                                        <div class="mb-3">
                                            <label>HQ <span class="text-danger">*</span></label>
                                            <select name="hq_id" id="hq_id" class="form-select" required>
                                                <option value="">-- Select HQ --</option>

                                                <?php
                                                $hq = mysqli_query($conn, "
                                                    SELECT id,area_name
                                                    FROM tbl_areas
                                                    WHERE area_type='HQ'
                                                    AND status=1
                                                    ORDER BY area_name
                                                    ");

                                                while ($row = mysqli_fetch_assoc($hq)) {
                                                ?>
                                                    <option value="<?= $row['id']; ?>">
                                                        <?= $row['area_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Area <span class="text-danger">*</span></label>
                                            <select name="area_id" id="area_id" class="form-select" required>
                                                <option value="">-- Select Area --</option>
                                            </select>
                                        </div>


                                        <!-- Doctor -->
                                        <div class="mb-3">
                                            <label>Doctor <span class="text-danger">*</span></label>

                                            <select name="doctor_id"
                                                id="doctor_id"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Doctor --</option>

                                            </select>

                                        </div>

                                        <!-- Clinic Code -->
                                        <div class="mb-3">
                                            <label>Clinic Code</label>
                                            <input type="text"
                                                name="clinic_code"
                                                class="form-control"
                                                placeholder="Auto Generated"
                                                readonly>
                                        </div>

                                        <!-- Clinic Name -->
                                        <div class="mb-3">
                                            <label>Clinic Name <span class="text-danger">*</span></label>

                                            <input type="text"
                                                name="clinic_name"
                                                class="form-control"
                                                required>
                                        </div>

                                        <!-- Contact Person -->
                                        <div class="mb-3">
                                            <label>Contact Person</label>

                                            <input type="text"
                                                name="contact_person"
                                                class="form-control">
                                        </div>

                                        <!-- Mobile -->
                                        <div class="mb-3">
                                            <label>Mobile Number</label>

                                            <input type="text"
                                                name="mobile"
                                                class="form-control"
                                                maxlength="10">
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label>Email</label>

                                            <input type="email"
                                                name="email"
                                                class="form-control">
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-3">
                                            <label>Address</label>

                                            <textarea
                                                name="address"
                                                class="form-control"
                                                rows="3"></textarea>
                                        </div>

                                        <!-- Latitude -->
                                        <div class="mb-3">
                                            <label>Latitude</label>

                                            <input type="text"
                                                name="latitude"
                                                class="form-control"
                                                placeholder="22.5726">
                                        </div>

                                        <!-- Longitude -->
                                        <div class="mb-3">
                                            <label>Longitude</label>

                                            <input type="text"
                                                name="longitude"
                                                class="form-control"
                                                placeholder="88.3639">
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">
                                            <label>Remarks</label>

                                            <textarea
                                                name="remarks"
                                                class="form-control"
                                                rows="3"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
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
    $("#area_id").change(function() {

        var area_id = $(this).val();

        $.ajax({

            url: "get-doctors.php",

            type: "POST",

            data: {
                area_id: area_id
            },

            success: function(data) {

                $("#doctor_id").html(data);

            }

        });

    });
</script>

<script type="text/javascript">
    $('#hq_id').change(function() {

        var hq_id = $(this).val();

        $.ajax({

            url: 'get-area.php',

            type: 'POST',

            data: {
                hq_id: hq_id
            },

            success: function(data) {

                $('#area_id').html(data);

            }

        });

    });
</script>