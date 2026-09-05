<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$rbm = $_SESSION['user_id'];

$sql = "

SELECT

u.id AS abm_user_id,

e.employee_code,
e.first_name,
e.last_name,
e.photo,

u.mobile,
u.email,

d.designation_name

FROM tbl_user_mapping m

INNER JOIN tbl_users u
ON u.id=m.employee_user_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

LEFT JOIN tbl_designations d
ON d.id=e.designation_id

WHERE

m.manager_user_id=?

ORDER BY e.first_name

";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $rbm);

mysqli_stmt_execute($stmt);

$abmResult = mysqli_stmt_get_result($stmt);


?>

<!-- DataTables -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Teams</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Teams</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>



                <div class="row">

                    <?php while ($abm = mysqli_fetch_assoc($abmResult)) {

                        $abmId = $abm['abm_user_id'];

                        $sqlMR = "
                        SELECT
                            u.id,
                            u.mobile,
                            u.email,
                            u.status,
                            e.employee_code,
                            e.first_name,
                            e.last_name,
                            e.photo,
                            a.area_name

                        FROM tbl_user_mapping m

                        INNER JOIN tbl_users u
                            ON u.id=m.employee_user_id

                        INNER JOIN tbl_employees e
                            ON e.id=u.employee_id

                        LEFT JOIN tbl_areas a
                            ON a.id=e.area_id

                        WHERE m.manager_user_id=?

                        ORDER BY e.first_name
                        ";

                        $stmtMR = mysqli_prepare($conn, $sqlMR);
                        mysqli_stmt_bind_param($stmtMR, "i", $abmId);
                        mysqli_stmt_execute($stmtMR);

                        $mrResult = mysqli_stmt_get_result($stmtMR);

                        $totalMR = mysqli_num_rows($mrResult);

                    ?>

                        <div class="col-12 mb-4">

                            <div class="card shadow rounded-4">

                                <div class="card-header bg-primary text-white">

                                    <div class="d-flex align-items-center">

                                        <img src="../uploads/employees/<?= $abm['photo']; ?>"
                                            class="rounded-circle me-3"
                                            width="60"
                                            height="60">

                                        <div class="flex-grow-1">

                                            <h5 class="mb-0">
                                                <?= $abm['first_name'] . " " . $abm['last_name']; ?>
                                            </h5>

                                            <small>
                                                <?= $abm['employee_code']; ?>
                                            </small><br>

                                            <small><?= $abm['mobile']; ?></small><br>

                                            <small><?= $abm['email']; ?></small>

                                        </div>

                                        <span class="badge bg-light text-dark fs-6">

                                            Total MR : <?= $totalMR; ?>

                                        </span>

                                    </div>

                                </div>

                                <div class="card-body">

                                    <div class="row">

                                        <?php while ($mr = mysqli_fetch_assoc($mrResult)) { ?>

                                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">

                                                <div class="card border h-100 shadow-sm rounded-4">

                                                    <div class="card-body text-center">

                                                        <img src="../uploads/employees/<?= $mr['photo']; ?>"
                                                            class="rounded-circle mb-3"
                                                            width="70"
                                                            height="70">

                                                        <h6 class="mb-1">
                                                            <?= $mr['first_name'] . " " . $mr['last_name']; ?>
                                                        </h6>

                                                        <small class="text-muted">
                                                            <?= $mr['employee_code']; ?>
                                                        </small>

                                                        <hr>

                                                        <p class="mb-1">
                                                            <i class="ri-phone-line"></i>
                                                            <?= $mr['mobile']; ?>
                                                        </p>

                                                        <p class="mb-1">
                                                            <i class="ri-mail-line"></i>
                                                            <?= $mr['email']; ?>
                                                        </p>

                                                        <p class="mb-2">
                                                            <i class="ri-map-pin-line"></i>
                                                            <?= $mr['area_name']; ?>
                                                        </p>

                                                        <?php if ($mr['status']) { ?>

                                                            <span class="badge bg-success">
                                                                Active
                                                            </span>

                                                        <?php } else { ?>

                                                            <span class="badge bg-danger">
                                                                Inactive
                                                            </span>

                                                        <?php } ?>

                                                        <hr>

                                                        <!-- <a href="mr-profile.php?id=<?= $mr['id']; ?>"
                                                            class="btn btn-primary btn-sm w-100">

                                                            View Profile

                                                        </a> -->

                                                    </div>

                                                </div>

                                            </div>

                                        <?php } ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

                <!-- /.page-content -->

                <?php include('./includes/footer.php'); ?>

            </div><!-- /.main-content -->
        </div><!-- /#layout-wrapper -->

        <?php include('./includes/scripts.php'); ?>

        <!-- DataTables JS -->
        <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
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
        <script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
        <script src="../assets/js/pages/datatables.init.js"></script>