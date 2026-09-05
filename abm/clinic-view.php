<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Clinic.";

    header("Location: clinic-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "SELECT

c.*,

a.area_name,
hq.area_name AS hq_name,

d.doctor_name

FROM tbl_clinics c

LEFT JOIN tbl_areas a
ON a.id = c.area_id

LEFT JOIN tbl_areas hq
ON hq.id=a.hq_id

LEFT JOIN tbl_doctors d
ON d.id = c.doctor_id

WHERE c.id = ?";

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
                            View Clinic
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
                                <h4 class="card-title mb-0">Clinic Details</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="25%">Clinic Code</th>
                                        <td><?php echo $row['clinic_code']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Clinic Name</th>
                                        <td><?php echo $row['clinic_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>HQ</th>
                                        <td><?= !empty($row['hq_name']) ? $row['hq_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area</th>
                                        <td><?php echo $row['area_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Doctor</th>
                                        <td><?php echo $row['doctor_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Contact Person</th>
                                        <td><?php echo $row['contact_person']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Mobile</th>
                                        <td><?php echo $row['mobile']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo $row['email']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <td><?php echo $row['address']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Latitude</th>
                                        <td><?php echo $row['latitude']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Longitude</th>
                                        <td><?php echo $row['longitude']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Remarks</th>
                                        <td><?php echo $row['remarks']; ?></td>
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
                                        <td><?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td><?php echo date("d M Y h:i A", strtotime($row['updated_at'])); ?></td>
                                    </tr>

                                </table>

                                <a href="clinic-list.php" class="btn btn-secondary">
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