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

a.area_name,

hq.area_name AS hq_name,

dc.category_name,

dg.degree_name,

sp.specialization_name,

bp.potential_name,

CONCAT(cu.first_name,' ',cu.last_name) AS created_by_name,
cr.role_name AS created_role,

CONCAT(uu.first_name,' ',uu.last_name) AS updated_by_name,
ur.role_name AS updated_role

FROM tbl_doctors d

LEFT JOIN tbl_areas a
ON a.id=d.area_id

LEFT JOIN tbl_areas hq
ON hq.id=a.hq_id

LEFT JOIN tbl_doctor_categories dc
ON dc.id=d.category_id

LEFT JOIN tbl_degrees dg
ON dg.id=d.degree_id

LEFT JOIN tbl_specializations sp
ON sp.id=d.specialization_id

LEFT JOIN tbl_business_potentials bp
ON bp.id=d.business_potential_id

LEFT JOIN tbl_users cu_user
ON cu_user.id=d.created_by

LEFT JOIN tbl_employees cu
ON cu.id=cu_user.employee_id

LEFT JOIN tbl_roles cr
ON cr.id=cu_user.role_id

LEFT JOIN tbl_users uu_user
ON uu_user.id=d.updated_by

LEFT JOIN tbl_employees uu
ON uu.id=uu_user.employee_id

LEFT JOIN tbl_roles ur
ON ur.id=uu_user.role_id

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
                            View Doctor
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
                                <h4 class="card-title mb-0">Doctor Details</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="25%">Doctor Code</th>
                                        <td><?= $row['doctor_code']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Doctor Name</th>
                                        <td><?= $row['doctor_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>HQ</th>
                                        <td><?= !empty($row['hq_name']) ? $row['hq_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area</th>
                                        <td><?= $row['area_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Gender</th>
                                        <td><?= !empty($row['gender']) ? $row['gender'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Mobile Number</th>
                                        <td>
                                            <?php

                                            if (!empty($row['mobile']) && !empty($row['alternate_mobile'])) {

                                                echo $row['mobile'] . "<br><small class='text-muted'>Alt : " . $row['alternate_mobile'] . "</small>";
                                            } elseif (!empty($row['mobile'])) {

                                                echo $row['mobile'];
                                            } elseif (!empty($row['alternate_mobile'])) {

                                                echo $row['alternate_mobile'];
                                            } else {

                                                echo "-";
                                            }

                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td><?= !empty($row['email']) ? $row['email'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Aadhaar No.</th>
                                        <td><?= !empty($row['aadhar_no']) ? $row['aadhar_no'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Date of Birth</th>
                                        <td><?= !empty($row['date_of_birth']) ? date("d M Y", strtotime($row['date_of_birth'])) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Anniversary</th>
                                        <td><?= !empty($row['anniversary_date']) ? date("d M Y", strtotime($row['anniversary_date'])) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Category</th>
                                        <td><?= $row['category_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Degree</th>
                                        <td><?= $row['degree_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Specialization</th>
                                        <td><?= $row['specialization_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Business Potential</th>
                                        <td><?= !empty($row['potential_name']) ? $row['potential_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Hospital</th>
                                        <td><?= !empty($row['hospital_name']) ? $row['hospital_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Clinic</th>
                                        <td><?= !empty($row['clinic_name']) ? $row['clinic_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <td><?= !empty($row['address']) ? nl2br($row['address']) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Latitude</th>
                                        <td><?= !empty($row['latitude']) ? $row['latitude'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Longitude</th>
                                        <td><?= !empty($row['longitude']) ? $row['longitude'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Remarks</th>
                                        <td><?= !empty($row['remarks']) ? nl2br($row['remarks']) : "-"; ?></td>
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
                                        <th>Created On</th>
                                        <td><?= date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td>

                                            <?= !empty($row['updated_at'])

                                                ? date("d M Y h:i A", strtotime($row['updated_at']))

                                                : "-"; ?>

                                        </td>
                                    </tr>

                                </table>


                                <a href="doctor-list.php" class="btn btn-secondary">
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