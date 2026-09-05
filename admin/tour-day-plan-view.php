<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Day Plan.";

    header("Location: tour-day-plan-list.php");
    exit;
}

$id = (int)$_GET['id'];
//exit;

$id = (int)$_GET['id'];

$sql = "

SELECT

dp.*,

tpd.area_id,
tpd.plan_date,
tpd.night_halt,
tpd.objective,

a.area_name,

wt.working_type_name,

CONCAT(e.first_name,' ',e.last_name) AS working_with_name

FROM tbl_day_plans dp

INNER JOIN tbl_tour_plan_details tpd
ON tpd.id = dp.tour_plan_detail_id

LEFT JOIN tbl_areas a
ON a.id = tpd.area_id

LEFT JOIN tbl_working_types wt
ON wt.id = tpd.working_type_id

LEFT JOIN tbl_users u
ON u.id = tpd.working_with_user_id

LEFT JOIN tbl_employees e
ON e.id = u.employee_id

WHERE dp.id = '$id'

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    die("Day Plan Not Found");
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
                            View Day Plan
                        </h4>

                    </div>



                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title mb-0">Tour Day Plan Details</h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="30%">Plan Date</th>
                                        <td><?= date('d-m-Y', strtotime($row['plan_date'])); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Area</th>
                                        <td><?= $row['area_name']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Working Type</th>
                                        <td><?= !empty($row['working_type_name']) ? $row['working_type_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Working With</th>
                                        <td><?= !empty($row['working_with_name']) ? $row['working_with_name'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Town</th>
                                        <td><?= !empty($row['town']) ? $row['town'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Night Halt</th>
                                        <td><?= !empty($row['night_halt']) ? $row['night_halt'] : "-"; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Objective</th>
                                        <td><?= nl2br($row['objective']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Remarks</th>
                                        <td><?= nl2br($row['remarks']); ?></td>
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
                                                    echo '<span class="badge bg-success">ABM Approved</span>';
                                                    break;

                                                case 3:
                                                    echo '<span class="badge bg-success">RBM Approved</span>';
                                                    break;

                                                case 4:
                                                    echo '<span class="badge bg-danger">Rejected</span>';
                                                    break;

                                                default:
                                                    echo '<span class="badge bg-dark">Unknown</span>';
                                            }

                                            ?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created On</th>
                                        <td>
                                            <?= !empty($row['created_at']) ? date("d M Y h:i A", strtotime($row['created_at'])) : "-"; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Last Updated</th>
                                        <td>
                                            <?= !empty($row['updated_at']) ? date("d M Y h:i A", strtotime($row['updated_at'])) : "-"; ?>
                                        </td>
                                    </tr>

                                </table>
                                  <!--
                                <a href="tour-day-plan-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">
                                    Edit
                                </a>

                                <a href="tour-day-plan-list.php" class="btn btn-secondary">
                                    Back
                                </a> -->

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