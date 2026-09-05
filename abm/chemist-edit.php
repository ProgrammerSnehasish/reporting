<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Chemist.";

    header("Location: chemist-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "
SELECT
    c.*,
    a.hq_id

FROM tbl_chemists c

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

    $_SESSION['error'] = "Chemist not found.";

    header("Location: chemist-list.php");
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
                            Edit Chemist Details
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="chemist-list.php" class="btn btn-primary btn-sm">

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

                                    Chemist Details

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

                                    <form action="chemist-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">


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



                                        <!-- Chemist Type -->

                                        <div class="mb-3">

                                            <label>Chemist / Stockist Type <span class="text-danger">*</span></label>

                                            <select name="chemist_type_id" class="form-select" required>

                                                <option value="">-- Select Type --</option>

                                                <?php

                                                $type = mysqli_query($conn, "
                                                    SELECT id,type_name
                                                    FROM tbl_chemist_types
                                                    WHERE status=1
                                                    ORDER BY type_name
                                                ");

                                                while ($t = mysqli_fetch_assoc($type)) {
                                                ?>

                                                    <option value="<?php echo $t['id']; ?>"
                                                        <?php if ($row['chemist_type_id'] == $t['id']) echo "selected"; ?>>

                                                        <?php echo $t['type_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Code -->

                                        <div class="mb-3">

                                            <label>Chemist Code</label>

                                            <input type="text"
                                                name="chemist_code"
                                                class="form-control"
                                                value="<?php echo $row['chemist_code']; ?>"
                                                readonly>

                                        </div>

                                        <!-- Name -->

                                        <div class="mb-3">

                                            <label>Chemist Name <span class="text-danger">*</span></label>

                                            <input type="text"
                                                name="chemist_name"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['chemist_name']); ?>"
                                                required>

                                        </div>

                                        <!-- Mobile -->

                                        <div class="mb-3">

                                            <label>Mobile</label>

                                            <input type="text"
                                                name="mobile"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['mobile']); ?>">

                                        </div>

                                        <!-- Aadhaar -->

                                        <div class="mb-3">

                                            <label>Aadhaar Number</label>

                                            <input type="text"
                                                name="aadhar_no"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['aadhar_no']); ?>">

                                        </div>

                                        <!-- Address -->

                                        <div class="mb-3">

                                            <label>Address</label>

                                            <textarea
                                                name="address"
                                                class="form-control"
                                                rows="3"><?php echo htmlspecialchars($row['address']); ?></textarea>

                                        </div>

                                        <!-- Remarks -->

                                        <div class="mb-3">

                                            <label>Remarks</label>

                                            <textarea
                                                name="remarks"
                                                class="form-control"
                                                rows="3"><?php echo htmlspecialchars($row['remarks']); ?></textarea>

                                        </div>

                                        <button class="btn btn-primary">

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