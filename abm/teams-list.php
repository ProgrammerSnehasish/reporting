<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

$abm_id = $_SESSION['user_id'];

$sql = "

SELECT

u.id,
u.mobile,
u.email,
u.status,

e.employee_code,
e.first_name,
e.last_name,
e.photo,

d.designation_name,

a.area_name

FROM tbl_user_mapping m

INNER JOIN tbl_users u
ON u.id = m.employee_user_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_designations d
ON d.id = e.designation_id

LEFT JOIN tbl_areas a
ON a.id = e.area_id

WHERE m.manager_user_id = ?

ORDER BY e.first_name

";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $abm_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$totalMR = mysqli_num_rows($result);

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

                    <?php while ($mr = mysqli_fetch_assoc($result)) { ?>

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">

                            <div class="card shadow border-0 rounded-4 h-100">

                                <div class="card-body text-center">

                                    <img src="../uploads/employees/<?= !empty($mr['photo']) ? $mr['photo'] : 'default.png'; ?>"
                                        class="rounded-circle border border-3 border-primary mb-3"
                                        width="85"
                                        height="85">

                                    <h5 class="mb-1">
                                        <?= $mr['first_name'] . " " . $mr['last_name']; ?>
                                    </h5>

                                    <span class="badge bg-light text-dark mb-3">
                                        <?= $mr['designation_name']; ?>
                                    </span>

                                    <table class="table table-sm table-borderless text-start">

                                        <tr>
                                            <td width="35%">
                                                <i class="ri-id-card-line"></i>
                                                Code
                                            </td>

                                            <td><?= $mr['employee_code']; ?></td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <i class="ri-phone-line"></i>
                                                Mobile
                                            </td>

                                            <td><?= $mr['mobile']; ?></td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <i class="ri-mail-line"></i>
                                                Email
                                            </td>

                                            <td class="small"><?= $mr['email']; ?></td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <i class="ri-map-pin-line"></i>
                                                Area
                                            </td>

                                            <td><?= $mr['area_name']; ?></td>
                                        </tr>

                                    </table>

                                    <?php if ($mr['status']) { ?>

                                        <span class="badge bg-success mb-3">
                                            Active
                                        </span>

                                    <?php } else { ?>

                                        <span class="badge bg-danger mb-3">
                                            Inactive
                                        </span>

                                    <?php } ?>

                                    <!-- <div class="d-grid">

                                        <a href="mr-profile.php?id=<?= $mr['id']; ?>"
                                            class="btn btn-primary btn-sm rounded-pill">

                                            <i class="ri-user-line"></i>
                                            View Profile

                                        </a>

                                    </div> -->

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