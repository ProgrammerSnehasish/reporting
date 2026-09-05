<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$id = $_GET['id'];

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "
SELECT *
FROM tbl_areas
WHERE id='$id'
");

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Area not found.";

    header("Location: area-list.php");
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

                                    <form action="area-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                        <div class="mb-3">
                                            <label>Area Name <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                name="area_name"
                                                class="form-control"
                                                value="<?= $row['area_name']; ?>"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Area Code</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= $row['area_code']; ?>"
                                                readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Area Type <span class="text-danger">*</span></label>

                                            <select
                                                name="area_type"
                                                id="area_type"
                                                class="form-select"
                                                required>

                                                <option value="">-- Select Area Type --</option>

                                                <option value="HQ" <?= ($row['area_type'] == "HQ") ? 'selected' : ''; ?>>
                                                    HQ
                                                </option>

                                                <option value="Urban" <?= ($row['area_type'] == "Urban") ? 'selected' : ''; ?>>
                                                    Urban
                                                </option>

                                                <option value="Rural" <?= ($row['area_type'] == "Rural") ? 'selected' : ''; ?>>
                                                    Rural
                                                </option>

                                                <option value="Mixed" <?= ($row['area_type'] == "Mixed") ? 'selected' : ''; ?>>
                                                    Mixed
                                                </option>

                                                <option value="Ex-station" <?= ($row['area_type'] == "Ex-station") ? 'selected' : ''; ?>>Ex-station</option>
                                                <option value="Outstation" <?= ($row['area_type'] == "Outstation") ? 'selected' : ''; ?>>Outstation</option>


                                            </select>

                                        </div>

                                        <div class="mb-3" id="hqDiv">

                                            <label class="form-label">
                                                Head Quarter
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                name="hq_id"
                                                class="form-select">

                                                <option value="">-- Select HQ --</option>

                                                <?php

                                                $hq = mysqli_query($conn, "
                                                SELECT id,area_name
                                                FROM tbl_areas
                                                WHERE area_type='HQ'
                                                AND status=1
                                                AND id!='{$row['id']}'
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

                                        <!-- KM from HQ -->
                                        <div class="mb-3" id="kmDiv">
                                            <label class="form-label">KM from HQ</label>
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="km_from_hq"
                                                    id="km_from_hq"
                                                    class="form-control"
                                                    step="0.1"
                                                    min="0"
                                                    value="<?php echo $row['km_from_hq'] ?? 0; ?>"
                                                    placeholder="0.0">
                                                <span class="input-group-text">km</span>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary">
                                            <i class="ri-save-line"></i>
                                            Update
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
    function toggleHQ() {

        var type = document.getElementById('area_type').value;
        var hqDiv = document.getElementById('hqDiv');
        var kmDiv = document.getElementById('kmDiv');
        var kmInput = document.getElementById('km_from_hq');

        if (type == "HQ") {
            hqDiv.style.display = "none";
            kmDiv.style.display = "none";
            kmInput.value = 0;
        } else {
            hqDiv.style.display = "block";
            kmDiv.style.display = "block";
        }
    }

    toggleHQ();

    document.getElementById('area_type').addEventListener('change', toggleHQ);
</script>