<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Doctor.";

    header("Location: doctor-list.php");
    exit;
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "

SELECT
d.*,
a.hq_id

FROM tbl_doctors d

LEFT JOIN tbl_areas a
ON a.id=d.area_id

WHERE d.id='$id'

");

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Doctor not found.";

    header("Location: doctor-list.php");
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
                            Edit Doctor Details
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="doctor-list.php" class="btn btn-primary btn-sm">

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

                                    Doctor Details

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

                                    <form action="doctor-update.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                        <!-- Row 1 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">

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

                                            <div class="col-md-6 mb-3">

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

                                        </div>

                                        <!-- Row 2 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">

                                                <label>Doctor Code</label>

                                                <input

                                                    type="text"

                                                    class="form-control"

                                                    value="<?= $row['doctor_code']; ?>"

                                                    readonly>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label>Doctor Name</label>

                                                <input

                                                    type="text"

                                                    name="doctor_name"

                                                    class="form-control"

                                                    value="<?= htmlspecialchars($row['doctor_name']); ?>"

                                                    required>

                                            </div>

                                        </div>

                                        <!-- Row 3 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Gender</label>
                                                <select name="gender" class="form-select">

                                                    <option value="">--Select--</option>

                                                    <option value="Male" <?= ($row['gender'] == "Male") ? 'selected' : ''; ?>>

                                                        Male

                                                    </option>

                                                    <option value="Female" <?= ($row['gender'] == "Female") ? 'selected' : ''; ?>>

                                                        Female

                                                    </option>

                                                    <option value="Other" <?= ($row['gender'] == "Other") ? 'selected' : ''; ?>>

                                                        Other

                                                    </option>

                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Mobile Number</label>
                                                <input

                                                    type="text"

                                                    name="mobile"

                                                    class="form-control"

                                                    value="<?= $row['mobile']; ?>">
                                            </div>

                                        </div>

                                        <!-- Row 4 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Alternate Mobile</label>
                                                <input

                                                    type="text"

                                                    name="alternate_mobile"

                                                    class="form-control"

                                                    value="<?= $row['alternate_mobile']; ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Email Address</label>
                                                <input

                                                    type="email"

                                                    name="email"

                                                    class="form-control"

                                                    value="<?= $row['email']; ?>">
                                            </div>

                                        </div>

                                        <!-- Row 5 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Aadhaar Number</label>
                                                <input

                                                    type="text"

                                                    name="aadhar_no"

                                                    class="form-control"

                                                    value="<?= $row['aadhar_no']; ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Date of Birth</label>
                                                <input

                                                    type="date"

                                                    name="date_of_birth"

                                                    class="form-control"

                                                    value="<?= $row['date_of_birth']; ?>">
                                            </div>

                                        </div>

                                        <!-- Row 6 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Anniversary Date</label>
                                                <input

                                                    type="date"

                                                    name="anniversary_date"

                                                    class="form-control"

                                                    value="<?= $row['anniversary_date']; ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Category <span class="text-danger">*</span></label>
                                                <select name="category_id" class="form-select" required">
                                                    <option value="">-- Select Category --</option>

                                                    <?php
                                                    $category = mysqli_query($conn, "
                                                    SELECT id,category_name
                                                    FROM tbl_doctor_categories
                                                    WHERE status=1
                                                    ORDER BY category_name
                                                    ");

                                                    while ($c = mysqli_fetch_assoc($category)) {
                                                    ?>


                                                        <option

                                                            value="<?= $c['id']; ?>"

                                                            <?= ($row['category_id'] == $c['id']) ? 'selected' : ''; ?>>

                                                            <?= $c['category_name']; ?>

                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>

                                        </div>

                                        <!-- Row 7 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Degree <span class="text-danger">*</span></label>
                                                <select name="degree_id" class="form-select" required">

                                                    <option value="">-- Select Degree --</option>

                                                    <?php
                                                    $degree = mysqli_query($conn, "
                                                    SELECT id,degree_name
                                                    FROM tbl_degrees
                                                    WHERE status=1
                                                    ORDER BY degree_name
                                                    ");

                                                    while ($d = mysqli_fetch_assoc($degree)) {
                                                    ?>


                                                        <option

                                                            value="<?= $d['id']; ?>"

                                                            <?= ($row['degree_id'] == $d['id']) ? 'selected' : ''; ?>>

                                                            <?= $d['degree_name']; ?>

                                                        </option>
                                                    <?php } ?>


                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Specialization <span class="text-danger">*</span></label>
                                                <select name="specialization_id" class="form-select" required">

                                                    <option value="">-- Select Specialization --</option>

                                                    <?php
                                                    $sp = mysqli_query($conn, "
                                                    SELECT id,specialization_name
                                                    FROM tbl_specializations
                                                    WHERE status=1
                                                    ORDER BY specialization_name
                                                    ");

                                                    while ($s = mysqli_fetch_assoc($sp)) {
                                                    ?>


                                                        <option

                                                            value="<?= $s['id']; ?>"

                                                            <?= ($row['specialization_id'] == $s['id']) ? 'selected' : ''; ?>>

                                                            <?= $s['specialization_name']; ?>

                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>

                                        </div>

                                        <!-- Row 8 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Business Potential</label>
                                                <select name="business_potential_id" class="form-select">

                                                    <option value="">-- Select Potential --</option>

                                                    <?php
                                                    $bp = mysqli_query($conn, "
                                                    SELECT id,potential_name
                                                    FROM tbl_business_potentials
                                                    WHERE status=1
                                                    ORDER BY potential_name
                                                    ");

                                                    while ($p = mysqli_fetch_assoc($bp)) {
                                                    ?>
                                                        <option

                                                            value="<?= $p['id']; ?>"

                                                            <?= ($row['business_potential_id'] == $p['id']) ? 'selected' : ''; ?>>

                                                            <?= $p['potential_name']; ?>

                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Hospital Name</label>
                                                <input

                                                    type="text"

                                                    name="hospital_name"

                                                    class="form-control"

                                                    value="<?= htmlspecialchars($row['hospital_name']); ?>">
                                            </div>

                                        </div>

                                        <!-- Row 9 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Clinic Name</label>
                                                <input

                                                    type="text"

                                                    name="clinic_name"

                                                    class="form-control"

                                                    value="<?= htmlspecialchars($row['clinic_name']); ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Address</label>
                                                <textarea

                                                    name="address"

                                                    class="form-control"

                                                    rows="3"><?= htmlspecialchars($row['address']); ?></textarea>
                                            </div>

                                        </div>

                                        <!-- Row 10 -->
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Latitude</label>
                                                <input

                                                    type="text"

                                                    name="latitude"

                                                    class="form-control"

                                                    value="<?= $row['latitude']; ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Longitude</label>
                                                <input

                                                    type="text"

                                                    name="longitude"

                                                    class="form-control"

                                                    value="<?= $row['longitude']; ?>">
                                            </div>

                                        </div>

                                        <!-- Address -->
                                        <div class="mb-3">
                                            <label>Address</label>
                                            <textarea

                                                name="remarks"

                                                class="form-control"

                                                rows="3"><?= htmlspecialchars($row['remarks']); ?></textarea>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-select">

                                                <option value="1"

                                                    <?= ($row['status'] == 1) ? 'selected' : ''; ?>>

                                                    Active

                                                </option>

                                                <option value="0"

                                                    <?= ($row['status'] == 0) ? 'selected' : ''; ?>>

                                                    Inactive

                                                </option>

                                            </select>
                                        </div>

                                        <button class="btn btn-primary">
                                            <i class="ri-save-line"></i> Update & Exit
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