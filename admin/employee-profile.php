<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "SELECT

e.*,

d.designation_name,

r.role_name,

CONCAT(manager.first_name,' ',manager.last_name) AS reporting_manager

FROM tbl_users u

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_designations d
ON d.id = e.designation_id

LEFT JOIN tbl_roles r
ON r.id = u.role_id

LEFT JOIN tbl_employees manager
ON manager.id = e.reporting_to

WHERE u.id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$photo = (!empty($row['photo']))
    ? "../uploads/profile/" . $row['photo']
    : "../assets/img/user.png";

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

                        <a href="change-password.php" class="btn btn-warning btn-sm">

                            <i class="ri-lock-password-line"></i>

                            Change Password

                        </a>

                    </div>

                </div>

                <div class="row">

                    <!-- LEFT -->

                    <div class="col-lg-4">

                        <div class="card">

                            <div class="card-body text-center">

                                <img src="<?php echo $photo; ?>"

                                    class="rounded-circle img-thumbnail"

                                    width="150">

                                <h4 class="mt-3">

                                    <?php

                                    echo $row['first_name'] . " " . $row['last_name'];

                                    ?>

                                </h4>

                                <p class="text-muted">

                                    <?php echo $row['designation_name']; ?>

                                </p>

                                <?php

                                if ($row['status'] == 1) {

                                    echo '<span class="badge bg-success">Active</span>';
                                } else {

                                    echo '<span class="badge bg-danger">Inactive</span>';
                                }

                                ?>

                                <hr>

                                <table class="table table-borderless table-sm">

                                    <tr>

                                        <th>Employee Code</th>

                                        <td>

                                            <?php echo $row['employee_code']; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Role</th>

                                        <td>

                                            <?php echo $row['role_name']; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Joining</th>

                                        <td>

                                            <?php

                                            echo date("d M Y", strtotime($row['joining_date']));

                                            ?>

                                        </td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- RIGHT -->

                    <div class="col-lg-8">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Employee Information

                                </h4>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Employee Code

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['employee_code']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Designation

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['designation_name']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            First Name

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['first_name']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Last Name

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['last_name']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Mobile

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['mobile']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Official Email

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['official_email']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Personal Email

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['personal_email']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Reporting Manager

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['reporting_manager']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Employee Type

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['employee_type']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Zone

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['zone']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Region

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['region']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Area

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['area']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Headquarter

                                        </label>

                                        <input

                                            type="text"

                                            class="form-control"

                                            value="<?php echo $row['headquarter']; ?>"

                                            readonly>

                                    </div>

                                    <div class="col-12 mb-3">

                                        <label class="form-label">

                                            Address

                                        </label>

                                        <textarea

                                            class="form-control"

                                            rows="4"

                                            readonly><?php echo $row['address']; ?></textarea>

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