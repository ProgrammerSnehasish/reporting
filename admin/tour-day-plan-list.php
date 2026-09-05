<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$where = "";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $tour_plan_id = (int)$_GET['id'];

    $where = " WHERE tpd.tour_plan_id='$tour_plan_id' ";
}

$sql = "

SELECT

tpd.id,
tpd.plan_date,
tpd.objective,
tpd.night_halt,
tpd.remarks,

tp.id AS tour_plan_id,
tp.month,
tp.year,
tp.status,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

a.area_name,

wt.working_type_name,

CONCAT(emp.first_name,' ',emp.last_name) working_with_name

FROM tbl_tour_plan_details tpd

INNER JOIN tbl_tour_plans tp
ON tp.id=tpd.tour_plan_id

INNER JOIN tbl_users u
ON u.id=tp.mr_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

LEFT JOIN tbl_areas a
ON a.id=tpd.area_id

LEFT JOIN tbl_working_types wt
ON wt.id=tpd.working_type_id

LEFT JOIN tbl_users wu
ON wu.id=tpd.working_with_user_id

LEFT JOIN tbl_employees emp
ON emp.id=wu.employee_id

$where

ORDER BY
tp.year DESC,
tp.month DESC,
tpd.plan_date ASC

";

$result = mysqli_query($conn, $sql);

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
                            View Tour Plan Day Wise
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="tour-plan-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View Tour Plans

                        </a>


                    </div>

                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title mb-0">View Tour Plan Day Wise</h4>
                            </div>

                            <div class="card-body">


                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>SL</th>

                                            <th>Employee</th>

                                            <th>Date</th>

                                            <th>Area</th>

                                            <th>Working Type</th>

                                            <th>Working With</th>

                                            <th>Night Halt</th>

                                            <th>Status</th>

                                            <!-- <th>Action</th> -->

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $sl = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td>

                                                    <strong><?= $row['employee_code']; ?></strong>

                                                    <br>

                                                    <?= $row['employee_name']; ?>

                                                </td>

                                                <td>

                                                    <?= date("d-m-Y", strtotime($row['plan_date'])); ?>

                                                </td>

                                                <td>

                                                    <?= $row['area_name']; ?>

                                                </td>

                                                <td>

                                                    <?= $row['working_type_name']; ?>

                                                </td>

                                                <td>

                                                    <?= !empty($row['working_with_name']) ? $row['working_with_name'] : "-"; ?>

                                                </td>

                                                <td>

                                                    <?= !empty($row['night_halt']) ? $row['night_halt'] : "-"; ?>

                                                </td>

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

                                                <!-- <td>

                                                    <a href="tour-day-plan-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>

                                                    <a href="tour-day-plan-edit.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-primary btn-sm">

                                                        Edit

                                                    </a>

                                                </td> -->

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