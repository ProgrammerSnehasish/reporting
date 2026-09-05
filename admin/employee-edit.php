<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (empty($_GET['id'])) {

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

CONCAT(re.first_name,' ',re.last_name) AS reporting_to_name,

CONCAT(cb.first_name,' ',cb.last_name) AS created_by_name,

CONCAT(ub.first_name,' ',ub.last_name) AS updated_by_name

FROM tbl_employees e

LEFT JOIN tbl_designations d
ON d.id = e.designation_id

LEFT JOIN tbl_employees re
ON re.id = e.reporting_to

LEFT JOIN tbl_areas hq
ON hq.id = e.hq_id

LEFT JOIN tbl_areas ar
ON ar.id = e.area_id

LEFT JOIN tbl_users cu
ON cu.id = e.created_by

LEFT JOIN tbl_employees cb
ON cb.id = cu.employee_id

LEFT JOIN tbl_users uu
ON uu.id = e.updated_by

LEFT JOIN tbl_employees ub
ON ub.id = uu.employee_id

WHERE e.id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Employee not found.";

    header("Location: employees-list.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

//print_r($row);
//exit;

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
                            Update Employee
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

                                    Update Employee

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

                                    <form action="employees-update.php" method="POST" enctype="multipart/form-data">

                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

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

                                                                    <option value="<?= $d['id']; ?>"
                                                                        <?= ($row['designation_id'] == $d['id']) ? 'selected' : ''; ?>>
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

                                                            <select
                                                                name="reporting_to"
                                                                class="form-select">

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

                                                                    <option value="<?= $e['id']; ?>"
                                                                        <?= ($row['reporting_to'] == $e['id']) ? 'selected' : ''; ?>>

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

                                                            <select name="employee_type" class="form-select">

                                                                <option value=""
                                                                    <?= empty($row['employee_type']) ? 'selected' : ''; ?>>
                                                                    -- Select --
                                                                </option>

                                                                <option value="Contract"
                                                                    <?= ($row['employee_type'] == 'Contract') ? 'selected' : ''; ?>>
                                                                    Contract
                                                                </option>

                                                                <option value="Trainee"
                                                                    <?= ($row['employee_type'] == 'Trainee') ? 'selected' : ''; ?>>
                                                                    Trainee
                                                                </option>

                                                                <option value="Contract"
                                                                    <?= ($row['employee_type'] == 'Intern') ? 'selected' : ''; ?>>
                                                                    Intern
                                                                </option>

                                                                <option value="Temporary"
                                                                    <?= ($row['employee_type'] == 'Consultant') ? 'selected' : ''; ?>>
                                                                    Consultant
                                                                </option>

                                                                <option value="Intern"
                                                                    <?= ($row['employee_type'] == 'Field Work') ? 'selected' : ''; ?>>
                                                                    Field Work
                                                                </option>

                                                                <option value="Intern"
                                                                    <?= ($row['employee_type'] == 'Office Work') ? 'selected' : ''; ?>>
                                                                    Office Work
                                                                </option>

                                                            </select>

                                                        </div>

                                                        <!-- Joining Date -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Joining Date

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="joining_date"
                                                                class="form-control"
                                                                value="<?= $row['joining_date']; ?>">

                                                        </div>

                                                        <!-- Leaving Date -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Date Of Leaving

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="date_of_leaving"
                                                                class="form-control"
                                                                value="<?= $row['date_of_leaving']; ?>">

                                                        </div>

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
                                                                value="<?= $row['first_name']; ?>">

                                                        </div>

                                                        <!-- Last Name -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Last Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="last_name"
                                                                class="form-control"
                                                                value="<?= $row['last_name']; ?>">

                                                        </div>

                                                        <!-- Gender -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Gender
                                                            </label>

                                                            <select name="gender" class="form-select" required>

                                                                <option value=""
                                                                    <?= empty($row['gender']) ? 'selected' : ''; ?>>
                                                                    -- Select --
                                                                </option>

                                                                <option value="Male"
                                                                    <?= ($row['gender'] == 'Male') ? 'selected' : ''; ?>>
                                                                    Male
                                                                </option>

                                                                <option value="Female"
                                                                    <?= ($row['gender'] == 'Female') ? 'selected' : ''; ?>>
                                                                    Female
                                                                </option>

                                                                <option value="Other"
                                                                    <?= ($row['gender'] == 'Other') ? 'selected' : ''; ?>>
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
                                                                class="form-control"
                                                                value="<?= $row['dob']; ?>">

                                                        </div>

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Date Of Anniversary

                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="doa"
                                                                class="form-control"
                                                                value="<?= $row['doa']; ?>">

                                                        </div>

                                                        <!-- Photo -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Employee Photo

                                                            </label>

                                                            <?php if (!empty($row['photo'])) { ?>

                                                                <img src="../uploads/employees/<?= $row['photo']; ?>"
                                                                    class="img-thumbnail mb-2"
                                                                    width="120">

                                                            <?php } ?>

                                                            <input type="file" name="photo" class="form-control">

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
                                                                value="<?= $row['mobile']; ?>">

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
                                                                value="<?= $row['alternate_mobile']; ?>">

                                                        </div>

                                                        <!-- Personal Email -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Personal Email

                                                            </label>

                                                            <input
                                                                type="email"
                                                                name="personal_email"
                                                                class="form-control"
                                                                value="<?= $row['personal_email']; ?>">

                                                        </div>

                                                        <!-- Official Email -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Official Email

                                                            </label>

                                                            <input
                                                                type="email"
                                                                name="official_email"
                                                                class="form-control"
                                                                value="<?= $row['official_email']; ?>">

                                                        </div>

                                                        <!-- Address -->

                                                        <div class="col-12 mb-3">

                                                            <label class="form-label">

                                                                Address

                                                            </label>

                                                            <textarea
                                                                name="address"
                                                                class="form-control"
                                                                rows="3"><?= $row['address']; ?></textarea>

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
                                                                value="<?= $row['zone']; ?>">

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
                                                                value="<?= $row['region']; ?>">

                                                        </div>

                                                        <!-- HQ -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Head Quarter
                                                            </label>

                                                            <select
                                                                name="hq_id"
                                                                id="hq_id"
                                                                class="form-select">

                                                                <option value="">Select HQ</option>

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

                                                                    <option
                                                                        value="<?= $h['id']; ?>"
                                                                        <?= ($row['hq_id'] == $h['id']) ? 'selected' : ''; ?>>

                                                                        <?= $h['area_name']; ?>

                                                                    </option>

                                                                <?php } ?>

                                                            </select>

                                                        </div>

                                                        <!-- Area -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Area
                                                            </label>

                                                            <select
                                                                name="area_id"
                                                                id="area_id"
                                                                class="form-select">

                                                                <option value="">Select Area</option>

                                                                <?php

                                                                $area = mysqli_query($conn, "
                                                                SELECT id,area_name
                                                                FROM tbl_areas
                                                                WHERE hq_id='" . $row['hq_id'] . "'
                                                                AND status=1
                                                                ORDER BY area_name
                                                                ");

                                                                while ($a = mysqli_fetch_assoc($area)) {

                                                                ?>

                                                                    <option
                                                                        value="<?= $a['id']; ?>"
                                                                        <?= ($row['area_id'] == $a['id']) ? 'selected' : ''; ?>>

                                                                        <?= $a['area_name']; ?>

                                                                    </option>

                                                                <?php } ?>

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
                                                                value="<?= $row['aadhaar_no']; ?>">

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
                                                                value="<?= $row['pan_no']; ?>">

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
                                                                value="<?= $row['bank_name']; ?>">

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
                                                                value="<?= $row['branch_name']; ?>">

                                                        </div>

                                                        <!-- Account Holder -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Account Holder Name

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="account_holder_name"
                                                                class="form-control"
                                                                value="<?= $row['account_holder_name']; ?>">

                                                        </div>

                                                        <!-- Account Number -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                Account Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="account_no"
                                                                class="form-control"
                                                                value="<?= $row['account_no']; ?>">

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
                                                                value="<?= $row['ifsc_code']; ?>">
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
                                                                value="<?= $row['upi_id']; ?>">

                                                        </div>



                                                        <!-- UAN -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">

                                                                UAN Number

                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="uan_no"
                                                                class="form-control"
                                                                value="<?= $row['uan_no']; ?>">

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
                                                                rows="4"><?= $row['remarks']; ?></textarea>

                                                        </div>

                                                        <!-- Status -->

                                                        <div class="col-md-6 mb-3">

                                                            <label class="form-label">
                                                                Status
                                                            </label>

                                                            <select
                                                                name="status"
                                                                class="form-select">

                                                                <option value="1" <?= ($row['status'] == 1) ? 'selected' : ''; ?>>
                                                                    Active
                                                                </option>

                                                                <option value="0" <?= ($row['status'] == 0) ? 'selected' : ''; ?>>
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

                                                        href="employees-list.php"

                                                        class="btn btn-secondary">

                                                        <i class="ri-arrow-left-line"></i>

                                                        Back

                                                    </a>



                                                    <button

                                                        type="submit"

                                                        class="btn btn-primary">

                                                        <i class="ri-save-line"></i>

                                                        Update & Exit

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

        var hq_id = $(this).val();

        $.ajax({

            url: "get-area.php",

            type: "POST",

            data: {
                hq_id: hq_id
            },

            success: function(data) {

                $("#area_id").html(data);

            }

        });

    });
</script>