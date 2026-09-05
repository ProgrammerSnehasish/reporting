<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Area.";

    header("Location: area-list.php");
    exit;
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "

SELECT

a.*,

hq.area_name AS hq_name,

CONCAT(cu.first_name,' ',cu.last_name) AS created_by_name,
cr.role_name AS created_role,

CONCAT(uu.first_name,' ',uu.last_name) AS updated_by_name,
ur.role_name AS updated_role

FROM tbl_areas a

LEFT JOIN tbl_areas hq
ON hq.id = a.hq_id

LEFT JOIN tbl_users cu_user
ON cu_user.id = a.created_by

LEFT JOIN tbl_employees cu
ON cu.id = cu_user.employee_id

LEFT JOIN tbl_roles cr
ON cr.id = cu_user.role_id

LEFT JOIN tbl_users uu_user
ON uu_user.id = a.updated_by

LEFT JOIN tbl_employees uu
ON uu.id = uu_user.employee_id

LEFT JOIN tbl_roles ur
ON ur.id = uu_user.role_id

WHERE a.id='$id'

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
                            View Area
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
                                <h4 class="card-title mb-0">Area Details</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="25%">Area Code</th>
                                        <td><?= $row['area_code']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area Name</th>
                                        <td><?= $row['area_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area Type</th>
                                        <td><?= $row['area_type']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Head Quarter</th>
                                        <td>
                                            <?= ($row['area_type'] == "HQ") ? "-" : (!empty($row['hq_name']) ? $row['hq_name'] : "-"); ?>
                                        </td>
                                    </tr>

                                    <!-- KM-->
                                    <tr>
                                        <th>KM from HQ</th>
                                        <td>
                                            <?php if ($row['area_type'] == "HQ") { ?>
                                                <span class="text-muted">-</span>
                                            <?php } else { ?>
                                                <strong><?php echo number_format($row['km_from_hq'], 1); ?> km</strong>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created By</th>
                                        <td>
                                            <?= !empty($row['created_by_name'])
                                                ? $row['created_by_name'] . " (" . strtoupper($row['created_role']) . ")"
                                                : "-"; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Updated By</th>
                                        <td>
                                            <?= !empty($row['updated_by_name'])
                                                ? $row['updated_by_name'] . " (" . strtoupper($row['updated_role']) . ")"
                                                : "-"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php if ($row['status'] == 1) { ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php } else { ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created On</th>
                                        <td><?= date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td><?= date("d M Y h:i A", strtotime($row['updated_at'])); ?></td>
                                    </tr>

                                </table>

                                <a href="area-list.php" class="btn btn-secondary">
                                    Back
                                </a>

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