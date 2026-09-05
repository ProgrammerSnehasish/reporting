<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$sql = "SELECT
e.*,
d.designation_name

FROM tbl_users u

INNER JOIN tbl_employees e
ON e.id=u.employee_id

LEFT JOIN tbl_designations d
ON d.id=e.designation_id

WHERE u.id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

$photo = (!empty($row['photo']))
    ? "../uploads/employees/" . $row['photo']
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

                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Edit Profile

                                </h4>

                            </div>

                            <div class="card-body">

                                <form action="update-profile.php"
                                    method="POST"
                                    enctype="multipart/form-data">

                                    <input type="hidden"
                                        name="employee_id"
                                        value="<?php echo $row['id']; ?>">

                                    <div class="text-center mb-4">

                                        <img src="<?php echo $photo; ?>"
                                            class="rounded-circle img-thumbnail"
                                            width="140">

                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 mb-3">

                                            <label>Employee Code</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['employee_code']; ?>"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Designation</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['designation_name']; ?>"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>First Name</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['first_name']; ?>"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Last Name</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['last_name']; ?>"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Mobile</label>

                                            <input
                                                type="text"
                                                name="mobile"
                                                class="form-control"
                                                value="<?php echo $row['mobile']; ?>">

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Alternate Mobile</label>

                                            <input
                                                type="text"
                                                name="alternate_mobile"
                                                class="form-control"
                                                value="<?php echo $row['alternate_mobile']; ?>">

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Personal Email</label>

                                            <input
                                                type="email"
                                                name="personal_email"
                                                class="form-control"
                                                value="<?php echo $row['personal_email']; ?>">

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Official Email</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php echo $row['official_email']; ?>"
                                                readonly>

                                        </div>

                                        <div class="col-12 mb-3">

                                            <label>Address</label>

                                            <textarea
                                                name="address"
                                                class="form-control"
                                                rows="4"><?php echo $row['address']; ?></textarea>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Profile Photo</label>

                                            <input
                                                type="file"
                                                name="photo"
                                                class="form-control">

                                        </div>

                                    </div>

                                    <hr>

                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i class="ri-save-line"></i>

                                        Update Profile

                                    </button>

                                    <a
                                        href="employee-profile.php"
                                        class="btn btn-secondary">

                                        Cancel

                                    </a>

                                </form>

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