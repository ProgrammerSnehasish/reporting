<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

$sql = "

SELECT

    e.*,

    d.designation_name,

    r.role_name,

    hq.area_name AS hq_name,

    ar.area_name AS area_name,

    CONCAT(rep.first_name,' ',rep.last_name) AS reporting_name,

    CONCAT(cb.first_name,' ',cb.last_name) AS created_by_name,

    CONCAT(ub.first_name,' ',ub.last_name) AS updated_by_name

FROM tbl_users u

INNER JOIN tbl_employees e
    ON e.id = u.employee_id

LEFT JOIN tbl_roles r
    ON r.id = u.role_id

LEFT JOIN tbl_designations d
    ON d.id = e.designation_id

LEFT JOIN tbl_areas hq
    ON hq.id = e.hq_id

LEFT JOIN tbl_areas ar
    ON ar.id = e.area_id

LEFT JOIN tbl_employees rep
    ON rep.id = e.reporting_to

LEFT JOIN tbl_users cu
    ON cu.id = e.created_by

LEFT JOIN tbl_employees cb
    ON cb.id = cu.employee_id

LEFT JOIN tbl_users uu
    ON uu.id = e.updated_by

LEFT JOIN tbl_employees ub
    ON ub.id = uu.employee_id

WHERE u.id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    die("Employee not found.");
}

$row = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

$photo = (!empty($row['photo']) && file_exists("../uploads/employees/" . $row['photo']))
    ? "../uploads/employees/" . $row['photo']
    : "../assets/images/users/avatar.png";
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
                            My Profile
                        </h4>

                        <?php if (isset($_SESSION['success'])) { ?>

                            <div class="alert alert-success alert-dismissible fade show">

                                <?php

                                echo $_SESSION['success'];

                                unset($_SESSION['success']);

                                ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                            </div>

                        <?php } ?>


                        <?php if (isset($_SESSION['error'])) { ?>

                            <div class="alert alert-danger alert-dismissible fade show">

                                <?php

                                echo $_SESSION['error'];

                                unset($_SESSION['error']);

                                ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                            </div>

                        <?php } ?>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="edit-profile.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            Edit Profile

                        </a>

                    </div>

                </div>

                <div class="row">

                    <!-- LEFT -->

                    <div class="col-lg-4">

                        <div class="card shadow-sm border-0">

                            <div class="card-body text-center">

                                <img src="<?= $photo; ?>"
                                    class="rounded-circle img-thumbnail mb-3"
                                    style="width:150px;height:150px;object-fit:cover;">

                                <h4 class="mb-1">
                                    <?= $row['first_name'] . ' ' . $row['last_name']; ?>
                                </h4>

                                <p class="text-muted mb-2">
                                    <?= $row['designation_name']; ?>
                                </p>

                                <span class="badge <?= $row['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                </span>

                                <hr>

                                <div class="text-start">

                                    <p><strong>Employee Code</strong><br><?= $row['employee_code']; ?></p>

                                    <p><strong>Role</strong><br><?= $row['role_name']; ?></p>

                                    <p><strong>HQ</strong><br><?= $row['hq_name']; ?></p>

                                    <p><strong>Area</strong><br><?= $row['area_name']; ?></p>

                                    <p><strong>Joining Date</strong><br><?= date("d M Y", strtotime($row['joining_date'])); ?></p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- RIGHT -->

                    <div class="col-lg-8">

                        <div class="card shadow-sm mb-4">

                            <div class="card-header bg-primary text-white">
                                <strong>Personal Information</strong>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">
                                        <p><strong>Gender</strong><br><?= $row['gender']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Date of Birth</strong><br><?= !empty($row['dob']) ? date("d M Y", strtotime($row['dob'])) : "-"; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Date of Anniversary</strong><br><?= !empty($row['doa']) ? date("d M Y", strtotime($row['doa'])) : "-"; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Mobile</strong><br><?= $row['mobile']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Alternate Mobile</strong><br><?= $row['alternate_mobile']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Personal Email</strong><br><?= $row['personal_email']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Official Email</strong><br><?= $row['official_email']; ?></p>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card shadow-sm mb-4">

                            <div class="card-header bg-info text-white">
                                Organization Information
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">
                                        <p><strong>Designation</strong><br><?= $row['designation_name']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Reporting To</strong><br><?= $row['reporting_name']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Employee Type</strong><br><?= $row['employee_type']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Joining Date</strong><br><?= date("d M Y", strtotime($row['joining_date'])); ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Zone</strong><br><?= $row['zone']; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p><strong>Region</strong><br><?= $row['region']; ?></p>
                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>

                <div class="row">

                    <!-- Identity Information -->
                    <div class="col-lg-6">

                        <div class="card shadow-sm border-0 mb-4">

                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="ri-shield-user-line me-2"></i>
                                    Identity Information
                                </h5>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered table-sm align-middle mb-0">

                                    <tr>
                                        <th width="40%">Aadhaar Number</th>
                                        <td><?= !empty($row['aadhaar_no']) ? $row['aadhaar_no'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>PAN Number</th>
                                        <td><?= !empty($row['pan_no']) ? strtoupper($row['pan_no']) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Employee Type</th>
                                        <td><?= !empty($row['employee_type']) ? $row['employee_type'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Joining Date</th>
                                        <td>
                                            <?= !empty($row['joining_date']) ? date("d M Y", strtotime($row['joining_date'])) : "-"; ?>
                                        </td>
                                    </tr>

                                    <!-- <tr>
                                        <th>Date of Leaving</th>
                                        <td>
                                            <?= !empty($row['date_of_leaving']) ? date("d M Y", strtotime($row['date_of_leaving'])) : "-"; ?>
                                        </td>
                                    </tr> -->

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

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- Banking Information -->
                    <div class="col-lg-6">

                        <div class="card shadow-sm border-0 mb-4">

                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="ri-bank-card-line me-2"></i>
                                    Banking And PF Information
                                </h5>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered table-sm align-middle mb-0">

                                    <tr>
                                        <th width="40%">Bank Name</th>
                                        <td><?= !empty($row['bank_name']) ? $row['bank_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Branch Name</th>
                                        <td><?= !empty($row['branch_name']) ? $row['branch_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Account Holder</th>
                                        <td><?= !empty($row['account_holder_name']) ? $row['account_holder_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Account Number</th>
                                        <td><?= !empty($row['account_no']) ? $row['account_no'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>IFSC Code</th>
                                        <td><?= !empty($row['ifsc_code']) ? strtoupper($row['ifsc_code']) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>UPI ID</th>
                                        <td><?= !empty($row['upi_id']) ? $row['upi_id'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>UAN Number</th>
                                        <td><?= !empty($row['uan_no']) ? $row['uan_no'] : "-"; ?></td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="card shadow-sm border-0 mb-4 col-md-6">

                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="ri-map-pin-line me-2"></i>
                                Address & Remarks
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-12">

                                    <label class="fw-bold mb-1">
                                        Address
                                    </label>

                                    <div class="border rounded p-3 bg-light">

                                        <?= !empty($row['address']) ? nl2br(htmlspecialchars($row['address'])) : "-"; ?>

                                    </div>

                                </div>

                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-12">

                                    <label class="fw-bold mb-1">
                                        Remarks
                                    </label>

                                    <div class="border rounded p-3 bg-light">

                                        <?= !empty($row['remarks']) ? nl2br(htmlspecialchars($row['remarks'])) : "-"; ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card shadow-sm border-0 col-md-6">

                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="ri-settings-3-line me-2"></i>
                                System Information
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Latitude
                                    </label>

                                    <div>
                                        <?= number_format(mt_rand(22000000, 28000000) / 1000000, 6); ?>
                                    </div>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Longitude
                                    </label>

                                    <div>
                                        <?= number_format(mt_rand(88000000, 93000000) / 1000000, 6); ?>
                                    </div>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Created By
                                    </label>

                                    <div>
                                        <?= !empty($row['created_by_name']) ? htmlspecialchars($row['created_by_name']) : "-"; ?>
                                    </div>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Updated By
                                    </label>

                                    <div>
                                        <?= !empty($row['updated_by_name']) ? htmlspecialchars($row['updated_by_name']) : "-"; ?>
                                    </div>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Created On
                                    </label>

                                    <div>

                                        <?= !empty($row['created_at'])
                                            ? date("d M Y h:i A", strtotime($row['created_at']))
                                            : "-"; ?>

                                    </div>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold text-muted">
                                        Last Updated
                                    </label>

                                    <div>

                                        <?= !empty($row['updated_at'])
                                            ? date("d M Y h:i A", strtotime($row['updated_at']))
                                            : "-"; ?>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <label class="fw-bold text-muted">
                                        Status
                                    </label>

                                    <div>

                                        <?php if ($row['status'] == 1) { ?>

                                            <span class="badge bg-success px-3 py-2">
                                                Active
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-danger px-3 py-2">
                                                Inactive
                                            </span>

                                        <?php } ?>

                                    </div>

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