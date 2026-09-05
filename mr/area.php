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
                            Add Area
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="area-list.php" class="btn btn-primary btn-sm">

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

                                    Add Area

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

                                    <form action="area-process.php" method="POST">

                                        <div class="mb-3">
                                            <label>Area Name <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                name="area_name"
                                                class="form-control"
                                                required>
                                        </div>



                                        <div class="mb-3">
                                            <label class="form-label">
                                                Area Type <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                name="area_type"
                                                id="area_type"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Area Type --</option>

                                                <option value="HQ">HQ</option>
                                                <option value="Ex-station">Ex-station</option>
                                                <option value="Outstation">Outstation</option>
                                                <option value="Urban">Urban</option>
                                                <option value="Rural">Rural</option>
                                                <option value="Mixed">Mixed</option>

                                            </select>

                                        </div>

                                        <!-- HQ Mapping -->

                                        <!-- <div class="mb-3" id="hq_div" style="display:none;">

                                            <label class="form-label">

                                                Head Quarter <span class="text-danger">*</span>

                                            </label>

                                            <select
                                                name="hq_id"
                                                id="hq_id"
                                                class="form-select">

                                                <option value="">-- Select HQ --</option>

                                                <?php

                                                $hq = mysqli_query($conn, "

                                                SELECT
                                                    id,
                                                    area_name

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

                                        </div> -->


                                        <!-- HQ Mapping -->
                                        <div class="mb-3" id="hq_div" style="display:none;">
                                            <label class="form-label">Head Quarter <span class="text-danger">*</span></label>
                                            <select name="hq_id" id="hq_id" class="form-select">
                                                <option value="">-- Select HQ --</option>
                                                <?php
                                                $hq = mysqli_query($conn, "SELECT id, area_name FROM tbl_areas WHERE area_type='HQ' AND status=1 ORDER BY area_name");
                                                while ($row = mysqli_fetch_assoc($hq)) { ?>
                                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['area_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- KM from HQ — only shows when sub area selected -->
                                        <div class="mb-3" id="km_div" style="display:none;">
                                            <label class="form-label">KM from HQ</label>
                                            <div class="input-group">
                                                <input type="number" name="km_from_hq" id="km_from_hq" class="form-control" step="0.1" min="0" value="0" placeholder="0.0">
                                                <span class="input-group-text">km</span>
                                            </div>
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
    document.getElementById("area_type").addEventListener("change", function() {

        var type = this.value;
        var hqDiv = document.getElementById("hq_div");
        var hq = document.getElementById("hq_id");
        var kmDiv = document.getElementById("km_div");

        if (type == "HQ") {

            // HQ select — no km, no parent hq
            hqDiv.style.display = "none";
            kmDiv.style.display = "none";
            hq.removeAttribute("required");
            hq.value = "";
            document.getElementById("km_from_hq").value = 0;

        } else {

            // Sub area — show hq and km
            hqDiv.style.display = "block";
            kmDiv.style.display = "block";
            hq.setAttribute("required", "required");

        }
    });
</script>