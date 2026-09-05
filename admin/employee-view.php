<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Doctor.";

    header("Location: employees-list.php");
    exit;
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "

SELECT

e.*,

d.designation_name,

hq.area_name AS hq_name,

ar.area_name AS area_name,

CONCAT(r.first_name,' ',r.last_name) AS reporting_name,

CONCAT(cb.first_name,' ',cb.last_name) AS created_by_name,

CONCAT(ub.first_name,' ',ub.last_name) AS updated_by_name

FROM tbl_employees e

LEFT JOIN tbl_designations d
ON d.id=e.designation_id

LEFT JOIN tbl_areas hq
ON hq.id=e.hq_id

LEFT JOIN tbl_areas ar
ON ar.id=e.area_id

LEFT JOIN tbl_employees r
ON r.id=e.reporting_to

LEFT JOIN tbl_users cu
ON cu.id=e.created_by

LEFT JOIN tbl_employees cb
ON cb.id=cu.employee_id

LEFT JOIN tbl_users uu
ON uu.id=e.updated_by

LEFT JOIN tbl_employees ub
ON ub.id=uu.employee_id

WHERE e.id='$id'

");

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Employee not found.";

    header("Location: employees-list.php");
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
                            View Employee Details
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="employees-list.php" class="btn btn-primary btn-sm">

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
                                <h4 class="card-title mb-0">Employee Details</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="30%">Employee Code</th>
                                        <td><?= $row['employee_code']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Employee Name</th>
                                        <td><?= $row['first_name'] . " " . $row['last_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Designation</th>
                                        <td><?= $row['designation_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Reporting To</th>
                                        <td><?= !empty($row['reporting_name']) ? $row['reporting_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Gender</th>
                                        <td><?= $row['gender']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Date of Birth</th>
                                        <td><?= !empty($row['dob']) ? date("d M Y", strtotime($row['dob'])) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Date of Anniversary</th>
                                        <td><?= !empty($row['doa']) ? date("d M Y", strtotime($row['doa'])) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Mobile</th>
                                        <td><?= $row['mobile']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Alternate Mobile</th>
                                        <td><?= !empty($row['alternate_mobile']) ? $row['alternate_mobile'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Personal Email</th>
                                        <td><?= !empty($row['personal_email']) ? $row['personal_email'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Official Email</th>
                                        <td><?= !empty($row['official_email']) ? $row['official_email'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Aadhaar Number</th>
                                        <td><?= !empty($row['aadhaar_no']) ? $row['aadhaar_no'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>PAN Number</th>
                                        <td><?= !empty($row['pan_no']) ? strtoupper($row['pan_no']) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Joining Date</th>
                                        <td><?= !empty($row['joining_date']) ? date("d M Y", strtotime($row['joining_date'])) : "-"; ?></td>
                                    </tr>

                                    <!-- <tr>
                                        <th>Date of Leaving</th>
                                        <td><?= !empty($row['date_of_leaving']) ? date("d M Y", strtotime($row['date_of_leaving'])) : "-"; ?></td>
                                    </tr> -->

                                    <tr>
                                        <th>Employee Type</th>
                                        <td><?= $row['employee_type']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Zone</th>
                                        <td><?= !empty($row['zone']) ? $row['zone'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Region</th>
                                        <td><?= !empty($row['region']) ? $row['region'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Head Quarter</th>
                                        <td><?= !empty($row['hq_name']) ? $row['hq_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area</th>
                                        <td><?= !empty($row['area_name']) ? $row['area_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <td><?= !empty($row['address']) ? nl2br($row['address']) : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Bank Name</th>
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

                                    <!-- <tr>
                                        <th>PF Number</th>
                                        <td><?= !empty($row['pf_no']) ? $row['pf_no'] : "-"; ?></td>
                                    </tr> -->

                                    <tr>
                                        <th>UAN Number</th>
                                        <td><?= !empty($row['uan_no']) ? $row['uan_no'] : "-"; ?></td>
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
                                        <td><?= !empty($row['created_by_name']) ? $row['created_by_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Updated By</th>
                                        <td><?= !empty($row['updated_by_name']) ? $row['updated_by_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Created On</th>
                                        <td><?= date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td>
                                            <?= !empty($row['updated_at']) ? date("d M Y h:i A", strtotime($row['updated_at'])) : "-"; ?>
                                        </td>
                                    </tr>

                                </table>

                                <a href="employee-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">
                                    Edit
                                </a>

                                <a href="employees-list.php" class="btn btn-secondary">
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