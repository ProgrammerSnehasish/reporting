<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Tour Plan.";

    header("Location: tour-plan-list.php");
    exit;
}

$id = (int)$_GET['id'];

$sql = "

SELECT

tp.*,

e.employee_code,

CONCAT(e.first_name,' ',e.last_name) AS employee_name,

d.designation_name,
d.designation_code

FROM tbl_tour_plans tp

INNER JOIN tbl_users u
ON u.id = tp.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_designations d
ON d.id = e.designation_id

WHERE tp.id='$id'

LIMIT 1

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Tour Plan not found.";

    header("Location:tour-plan-list.php");
    exit;
}
?>


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
                            View Tour Plan
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="tour-plan-list.php" class="btn btn-primary btn-sm">

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
                                <h4 class="card-title mb-0">Tour Plan Detail</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="30%">Employee</th>
                                        <td>
                                            <?= $row['employee_code']; ?>
                                            -
                                            <?= $row['employee_name']; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Designation</th>
                                        <td><?= strtoupper($row['designation_code']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Month</th>
                                        <td><?= date("F", mktime(0, 0, 0, $row['month'], 1)); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Year</th>
                                        <td><?= $row['year']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            <?php

                                            switch ($row['status']) {

                                                case 0:
                                                    echo '<span class="badge bg-secondary">Draft</span>';
                                                    break;

                                                case 1:
                                                    echo '<span class="badge bg-primary">Submitted</span>';
                                                    break;

                                                case 2:
                                                    echo '<span class="badge bg-success">Approved</span>';
                                                    break;

                                                case 3:
                                                    echo '<span class="badge bg-danger">Rejected</span>';
                                                    break;

                                                default:
                                                    echo '<span class="badge bg-dark">Unknown</span>';
                                            }

                                            ?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Submitted On</th>
                                        <td>

                                            <?= !empty($row['submitted_at'])
                                                ? date("d M Y h:i A", strtotime($row['submitted_at']))
                                                : "-"; ?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created On</th>
                                        <td>

                                            <?= date("d M Y h:i A", strtotime($row['created_at'])); ?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td>

                                            <?= date("d M Y h:i A", strtotime($row['updated_at'])); ?>

                                        </td>
                                    </tr>



                                </table>


                                <!-- Tour Plan Details -->
                                <h5 class="mt-4 mb-3">Tour Plan Details</h5>

                                <table class="table table-bordered table-striped">

                                    <thead>

                                        <tr>

                                            <th>SL</th>

                                            <th>Date</th>

                                            <th>Area</th>

                                            <th>Working Type</th>

                                            <th>Working With</th>

                                            <th>Night Halt</th>

                                            <th>Objective</th>

                                            <th>Remarks</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        $details = mysqli_query($conn, "

                                        SELECT

                                        tpd.*,

                                        a.area_name,

                                        wt.working_type_name,

                                        CONCAT(emp.first_name,' ',emp.last_name) working_with_name

                                        FROM tbl_tour_plan_details tpd

                                        LEFT JOIN tbl_areas a
                                        ON a.id=tpd.area_id

                                        LEFT JOIN tbl_working_types wt
                                        ON wt.id=tpd.working_type_id

                                        LEFT JOIN tbl_users u
                                        ON u.id=tpd.working_with_user_id

                                        LEFT JOIN tbl_employees emp
                                        ON emp.id=u.employee_id

                                        WHERE tpd.tour_plan_id='$id'

                                        ORDER BY tpd.plan_date

                                        ");

                                        while ($d = mysqli_fetch_assoc($details)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= date("d-m-Y", strtotime($d['plan_date'])); ?></td>

                                                <td><?= $d['area_name']; ?></td>

                                                <td><?= $d['working_type_name']; ?></td>

                                                <td><?= !empty($d['working_with_name']) ? $d['working_with_name'] : "-"; ?></td>

                                                <td><?= !empty($d['night_halt']) ? $d['night_halt'] : "-"; ?></td>

                                                <td><?= nl2br($d['objective']); ?></td>

                                                <td><?= nl2br($d['remarks']); ?></td>

                                            </tr>

                                        <?php } ?>

                                        <?php if (mysqli_num_rows($details) == 0) { ?>

                                            <tr>

                                                <td colspan="8" class="text-center">

                                                    No Tour Plan Details Found.

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                                <a href="tour-plan-list.php" class="btn btn-secondary">
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