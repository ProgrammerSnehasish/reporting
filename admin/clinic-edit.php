<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Clinic.";

    header("Location: clinic-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "
SELECT
    c.*,
    a.hq_id

FROM tbl_clinics c

LEFT JOIN tbl_areas a
    ON a.id = c.area_id

WHERE c.id = ?

LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Clinic not found.";

    header("Location: clinic-list.php");
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
                            Edit Clinic Details
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

                                    Clinic Details

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

                                    <form action="clinic-update.php" method="POST">

                                        <input type="hidden"
                                            name="id"
                                            value="<?php echo $row['id']; ?>">

                                        <!-- Area -->

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

                                                while ($h = mysqli_fetch_assoc($hq)) {

                                                ?>

                                                    <option
                                                        value="<?= $h['id']; ?>"
                                                        <?= ($row['hq_id'] == $h['id']) ? 'selected' : ''; ?>>
                                                        <?= $h['area_name']; ?>
                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label>Area</label>

                                            <select name="area_id" id="area_id" class="form-select" required>

                                                <?php

                                                $area = mysqli_query($conn, "
                                                    SELECT id,area_name
                                                    FROM tbl_areas
                                                    WHERE hq_id='" . $row['hq_id'] . "'
                                                    AND status=1
                                                    ORDER BY area_name
                                                    ");

                                                while ($a = mysqli_fetch_assoc($area)) {

                                                ?>

                                                    <option
                                                        value="<?= $a['id']; ?>"
                                                        <?= ($row['area_id'] == $a['id']) ? 'selected' : ''; ?>>
                                                        <?= $a['area_name']; ?>
                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Doctor -->

                                        <div class="mb-3">

                                            <label>Doctor <span class="text-danger">*</span></label>

                                            <select name="doctor_id"
                                                id="doctor_id"
                                                class="form-select"
                                                required>

                                                <?php

                                                $doctor = mysqli_query($conn, "
                                                    SELECT id,doctor_name
                                                    FROM tbl_doctors
                                                    WHERE status=1
                                                    ORDER BY doctor_name
                                                ");

                                                while ($d = mysqli_fetch_assoc($doctor)) {
                                                ?>

                                                    <option
                                                        value="<?php echo $d['id']; ?>"

                                                        <?php
                                                        if ($row['doctor_id'] == $d['id'])
                                                            echo "selected";
                                                        ?>>

                                                        <?php echo $d['doctor_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label>Clinic Code</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['clinic_code']; ?>"
                                                readonly>

                                        </div>

                                        <div class="mb-3">

                                            <label>Clinic Name *</label>

                                            <input
                                                type="text"
                                                name="clinic_name"
                                                class="form-control"
                                                value="<?php echo $row['clinic_name']; ?>"
                                                required>

                                        </div>

                                        <div class="mb-3">

                                            <label>Contact Person</label>

                                            <input
                                                type="text"
                                                name="contact_person"
                                                class="form-control"
                                                value="<?php echo $row['contact_person']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Mobile</label>

                                            <input
                                                type="text"
                                                name="mobile"
                                                class="form-control"
                                                value="<?php echo $row['mobile']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Email</label>

                                            <input
                                                type="email"
                                                name="email"
                                                class="form-control"
                                                value="<?php echo $row['email']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Address</label>

                                            <textarea
                                                name="address"
                                                class="form-control"><?php echo $row['address']; ?></textarea>

                                        </div>

                                        <div class="mb-3">

                                            <label>Latitude</label>

                                            <input
                                                type="text"
                                                name="latitude"
                                                class="form-control"
                                                value="<?php echo $row['latitude']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Longitude</label>

                                            <input
                                                type="text"
                                                name="longitude"
                                                class="form-control"
                                                value="<?php echo $row['longitude']; ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label>Remarks</label>

                                            <textarea
                                                name="remarks"
                                                class="form-control"><?php echo $row['remarks']; ?></textarea>

                                        </div>

                                        <button class="btn btn-primary">

                                            Update & Exit

                                        </button>

                                        <a href="clinic-list.php"
                                            class="btn btn-secondary">

                                            Cancel

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
    $("#area_id").change(function() {

        var area_id = $(this).val();

        $.ajax({

            url: "get-doctors.php",

            type: "POST",

            data: {
                area_id: area_id
            },

            success: function(response) {

                $("#doctor_id").html(response);

            }

        });

    });
</script>

<script>
    $('#hq_id').change(function() {

        $.post('get-area.php',

            {
                hq_id: $(this).val()
            },

            function(data) {

                $('#area_id').html(data);

            });

    });
</script>