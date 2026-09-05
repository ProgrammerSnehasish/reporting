<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

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
                            Add Employee
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="employees-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View All Employees

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

                                    Add Employee

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

                                    <form action="employees-process.php" method="POST" enctype="multipart/form-data">

                                        <!-- ============================= -->
                                        <!-- Office Information -->
                                        <!-- ============================= -->

                                        <div class="col-12">
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0">Office Information</h5>
                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- Employee Code -->
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Employee Code</label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                value="Auto Generated"
                                                                readonly>
                                                        </div>

                                                        <!-- Designation -->
                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Designation <span class="text-danger">*</span>
                                                            </label>

                                                            <select
                                                                name="designation_id"
                                                                class="form-select"
                                                                required>

                                                                <option value="">-- Select Designation --</option>

                                                                <?php

                                                                $designation = mysqli_query($conn, "
                                                                SELECT id,designation_name
                                                                FROM tbl_designations
                                                                WHERE status=1
                                                                ORDER BY designation_name
                                                                ");

                                                                while ($d = mysqli_fetch_assoc($designation)) {
                                                                ?>

                                                                    <option value="<?= $d['id']; ?>">
                                                                        <?= $d['designation_name']; ?>
                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </div>

                                                        <!-- Reporting To -->
                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Reporting To
                                                            </label>

                                                            <select name="reporting_to" class="form-select">

                                                                <option value="">
                                                                    -- Select Reporting Person --
                                                                </option>

                                                                <?php

                                                                $employee = mysqli_query($conn, "
            SELECT
                e.id,
                e.employee_code,
                e.first_name,
                e.last_name,
                d.designation_name
            FROM tbl_employees e
            LEFT JOIN tbl_designations d
                ON d.id = e.designation_id
            WHERE e.status = 1
            ORDER BY e.first_name
        ");

                                                                while ($e = mysqli_fetch_assoc($employee)) {
                                                                ?>

                                                                    <option value="<?= $e['id']; ?>">

                                                                        <?= htmlspecialchars($e['employee_code']); ?>

                                                                        -

                                                                        <?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?>

                                                                        (<?= htmlspecialchars($e['designation_name']); ?>)

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </div>

                                                        <!-- Employee Type -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Employee Type
                                                            </label>

                                                            <select name="employee_type" class="form-select" required>

                                                                <option value="">-- Select --</option>

                                                                <option value="Contract">Contract</option>

                                                                <option value="Trainee">Trainee</option>

                                                                <option value="Intern">Intern</option>

                                                                <option value="Consultant">Consultant</option>

                                                                <option value="Field Work">Field Work</option>

                                                                <option value="Office Work">Office Work</option>

                                                            </select>

                                                        </div>

                                                        <!-- Joining Date -->

                                                        <div class="col-md-12 mb-3">

                                                            <label class="form-label">

                                                                Joining Date

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="joining_date"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Leaving Date -->

                                                        <!-- <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Date Of Leaving

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="date_of_leaving"
                                                                class="form-control">

                                                        </div> -->

                                                    </div>

                                                </div>
                                            </div>
                                        </div>


                                        <!-- ============================= -->
                                        <!-- Personal Information -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card mb-4">

                                                <div class="card-header bg-info text-white">

                                                    <h5 class="mb-0">

                                                        Personal Information

                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- First Name -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                First Name
                                                                <span class="text-danger">*</span>

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="first_name"
                                                                class="form-control"
                                                                required>

                                                        </div>

                                                        <!-- Last Name -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Last Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="last_name"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Gender -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Gender

                                                            </label>

                                                            <select
                                                                name="gender"
                                                                class="form-select">

                                                                <option value="">

                                                                    -- Select --

                                                                </option>

                                                                <option value="Male">

                                                                    Male

                                                                </option>

                                                                <option value="Female">

                                                                    Female

                                                                </option>

                                                                <option value="Other">

                                                                    Other

                                                                </option>

                                                            </select>

                                                        </div>

                                                        <!-- DOB -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Date Of Birth

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="dob"
                                                                class="form-control">

                                                        </div>

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Date Of Anniversary

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="doa"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Photo -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Employee Photo

                                                            </label>

                                                            <input
                                                                type="file"
                                                                name="photo"
                                                                class="form-control">

                                                            <small class="text-muted">

                                                                JPG / PNG only

                                                            </small>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- ============================= -->
                                        <!-- Contact Information -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card mb-4">

                                                <div class="card-header bg-success text-white">

                                                    <h5 class="mb-0">
                                                        Contact Information
                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- Mobile -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Mobile Number
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="mobile"
                                                                class="form-control"
                                                                maxlength="10"
                                                                required>

                                                        </div>

                                                        <!-- Alternate Mobile -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Alternate Mobile

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="alternate_mobile"
                                                                class="form-control"
                                                                maxlength="10">

                                                        </div>

                                                        <!-- Personal Email -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Personal Email

                                                            </label>

                                                            <input
                                                                type="email"
                                                                name="personal_email"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Official Email -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Official Email

                                                            </label>

                                                            <input
                                                                type="email"
                                                                name="official_email"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Address -->

                                                        <div class="col-12 mb-3">

                                                            <label class="form-label">

                                                                Address

                                                            </label>

                                                            <textarea

                                                                name="address"

                                                                class="form-control"

                                                                rows="3"></textarea>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>






                                        <!-- ============================= -->
                                        <!-- Posting Information -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card mb-4">

                                                <div class="card-header bg-warning">

                                                    <h5 class="mb-0">

                                                        Posting Information

                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- Zone -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Zone

                                                            </label>

                                                            <input

                                                                type="text"

                                                                name="zone"

                                                                class="form-control"

                                                                placeholder="North Zone">

                                                        </div>

                                                        <!-- Region -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Region

                                                            </label>

                                                            <input

                                                                type="text"

                                                                name="region"

                                                                class="form-control"

                                                                placeholder="Murshidabad Region">

                                                        </div>

                                                        <!-- HQ -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Head Quarter
                                                                <span class="text-danger">*</span>

                                                            </label>

                                                            <select

                                                                name="hq_id"

                                                                id="hq_id"

                                                                class="form-select"

                                                                required>

                                                                <option value="">

                                                                    -- Select HQ --

                                                                </option>

                                                                <?php

                                                                $hq = mysqli_query($conn, "
                                                                SELECT id,area_name
                                                                FROM tbl_areas
                                                                WHERE area_type='HQ'
                                                                AND status=1
                                                                ORDER BY area_name
                                                                ");

                                                                while ($h = mysqli_fetch_assoc($hq)) {
                                                                ?>

                                                                    <option value="<?= $h['id']; ?>">

                                                                        <?= $h['area_name']; ?>

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </div>

                                                        <!-- Area -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Area
                                                                <span class="text-danger">*</span>

                                                            </label>

                                                            <select

                                                                name="area_id"

                                                                id="area_id"

                                                                class="form-select"

                                                                required>

                                                                <option value="">

                                                                    -- Select Area --

                                                                </option>

                                                            </select>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <!-- ============================= -->
                                        <!-- KYC & Bank Details -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card mb-4">

                                                <div class="card-header bg-secondary text-white">

                                                    <h5 class="mb-0">

                                                        KYC & Bank Details

                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- Aadhaar -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Aadhaar Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="aadhaar_no"
                                                                class="form-control"
                                                                maxlength="12"
                                                                placeholder="123412341234">

                                                        </div>

                                                        <!-- PAN -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                PAN Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="pan_no"
                                                                class="form-control"
                                                                maxlength="10"
                                                                style="text-transform:uppercase"
                                                                placeholder="ABCDE1234F">

                                                        </div>

                                                        <!-- Bank Name -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Bank Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="bank_name"
                                                                class="form-control"
                                                                placeholder="State Bank of India">

                                                        </div>

                                                        <!-- Branch -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Branch Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="branch_name"
                                                                class="form-control"
                                                                placeholder="Berhampore Branch">

                                                        </div>

                                                        <!-- Account Holder -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Account Holder Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="account_holder_name"
                                                                class="form-control">

                                                        </div>

                                                        <!-- Account Number -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Account Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="account_no"
                                                                class="form-control">

                                                        </div>

                                                        <!-- IFSC -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                IFSC Code

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="ifsc_code"
                                                                class="form-control"
                                                                style="text-transform:uppercase"
                                                                placeholder="SBIN0001234">

                                                        </div>

                                                        <!-- UPI -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                UPI ID

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="upi_id"
                                                                class="form-control"
                                                                placeholder="employee@upi">

                                                        </div>

                                                        <!-- PF -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                PF Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="pf_no"
                                                                class="form-control">

                                                        </div>

                                                        <!-- UAN -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                UAN Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="uan_no"
                                                                class="form-control">

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- ============================= -->
                                        <!-- Other Information -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card mb-4">

                                                <div class="card-header bg-dark text-white">

                                                    <h5 class="mb-0">

                                                        Other Information

                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <!-- Remarks -->

                                                        <div class="col-md-12 mb-3">

                                                            <label class="form-label">

                                                                Remarks

                                                            </label>

                                                            <textarea

                                                                name="remarks"

                                                                class="form-control"

                                                                rows="4"

                                                                placeholder="Any additional information..."></textarea>

                                                        </div>

                                                        <!-- Status -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Status

                                                            </label>

                                                            <select

                                                                name="status"

                                                                class="form-select">

                                                                <option value="1" selected>

                                                                    Active

                                                                </option>

                                                                <option value="0">

                                                                    Inactive

                                                                </option>

                                                            </select>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>





                                        <!-- ============================= -->
                                        <!-- Action Buttons -->
                                        <!-- ============================= -->

                                        <div class="col-12">

                                            <div class="card">

                                                <div class="card-body text-end">

                                                    <a

                                                        href="employee-list.php"

                                                        class="btn btn-secondary">

                                                        <i class="ri-arrow-left-line"></i>

                                                        Back

                                                    </a>

                                                    <button

                                                        type="reset"

                                                        class="btn btn-warning">

                                                        <i class="ri-refresh-line"></i>

                                                        Reset

                                                    </button>

                                                    <button

                                                        type="submit"

                                                        class="btn btn-primary">

                                                        <i class="ri-save-line"></i>

                                                        Save & Exit

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

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

<script>
    $("#hq_id").change(function() {

        var hq = $(this).val();

        $.ajax({

            url: "get-area.php",

            type: "POST",

            data: {
                hq_id: hq
            },

            success: function(res) {

                $("#area_id").html(res);

            }

        });

    });
</script>