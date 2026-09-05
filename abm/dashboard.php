<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/role-auth.php";
require_once "../config/dashboard-functions.php";

checkRole(['abm']);

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

                        <?php include('./includes/notice-bar.php'); ?>

                    </div>
                </div>

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0"><?php echo $_SESSION['role']; ?> Dashboard</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo $_SESSION['role']; ?></a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="row mb-4">



                    <div class="col-md-4">
                        <a href="dcr-list.php" class="text-decoration-none">
                            <div class="card border-0 shadow glow-card dcr-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-md bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-file-list-3-line fs-2"></i>
                                    </div>

                                    <div>
                                        <h5 class="mb-1">View All DCR</h5>
                                        <small class="text-muted">
                                            View All Dcr List
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="tour-plan-list.php" class="text-decoration-none">
                            <div class="card border-0 shadow glow-card tour-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-md bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-route-line fs-2"></i>
                                    </div>

                                    <div>
                                        <h5 class="mb-1">View All Tour Plan</h5>
                                        <small class="text-muted">
                                            View All Tour Plan List.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <?php

                    $unread_messages = mysqli_num_rows(mysqli_query($conn, "
					SELECT id
					FROM tbl_message_receivers
					WHERE
					receiver_user_id='{$_SESSION['user_id']}'
					AND is_read='0'
					"));

                    ?>

                    <div class="col-md-4">

                        <a href="message-list.php" class="text-decoration-none">

                            <div class="card border-0 shadow glow-card message-card">

                                <div class="card-body d-flex align-items-center">

                                    <div class="avatar-md bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3">

                                        <i class="ri-message-2-line fs-2"></i>

                                    </div>

                                    <div>

                                        <h5 class="mb-1">

                                            Messages

                                            <?php if ($unread_messages > 0) { ?>

                                                <span class="badge bg-danger ms-2">

                                                    <?php echo $unread_messages; ?>

                                                </span>

                                            <?php } ?>

                                        </h5>

                                        <small class="text-muted">

                                            <?php

                                            if ($unread_messages > 0) {

                                                echo $unread_messages . " Unread Message(s)";
                                            } else {

                                                echo "No unread messages";
                                            }

                                            ?>

                                        </small>

                                    </div>

                                </div>

                            </div>

                        </a>

                    </div>

                </div>

                <?php
                $totalMR = totalMRs($conn, $_SESSION['user_id']);
                $pendingTour = pendingTourPlans($conn, 'ABM', $_SESSION['user_id']);
                $pendingDCR = pendingDCR($conn, 'ABM', $_SESSION['user_id']);
                $todayDoctor = todayDoctorCalls($conn, $_SESSION['user_id'], 'ABM');
                $todayChemist = todayChemistCalls($conn, $_SESSION['user_id'], 'ABM');
                $todayStockist = todayStockistCalls($conn, $_SESSION['user_id'], 'ABM');
                $todayWorking = todayWorkingMRs($conn, $_SESSION['user_id'], 'ABM');
                $monthlyDoctor = monthlyDoctorCalls($conn, $_SESSION['user_id'], 'ABM');
                $monthlyDCR = monthlyDCR($conn, $_SESSION['user_id'], 'ABM');
                $monthlyTour = monthlyTourPlans($conn, $_SESSION['user_id'], 'ABM');
                ?>
                <div class="row mb-4">

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow rounded-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted">Team MRs</small>
                                        <h2 class="fw-bold mt-2"><?= $totalMR ?></h2>
                                    </div>

                                    <div class="avatar-md bg-primary rounded-circle">
                                        <div class="avatar-title">
                                            <i class="ri-team-line text-white fs-2"></i>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow rounded-4 h-100">
                            <div class="card-body">

                                <div class="d-flex justify-content-between">

                                    <div>
                                        <small class="text-muted">Pending Tour Plans</small>
                                        <h2 class="fw-bold text-warning mt-2">
                                            <?= $pendingTour ?>
                                        </h2>
                                    </div>

                                    <div class="avatar-md bg-warning rounded-circle">
                                        <div class="avatar-title">
                                            <i class="ri-route-line text-white fs-2"></i>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card border-0 shadow rounded-4 h-100">

                            <div class="card-body">

                                <div class="d-flex justify-content-between">

                                    <div>
                                        <small class="text-muted">Pending DCR</small>

                                        <h2 class="fw-bold text-danger mt-2">
                                            <?= $pendingDCR ?>
                                        </h2>
                                    </div>

                                    <div class="avatar-md bg-danger rounded-circle">
                                        <div class="avatar-title">
                                            <i class="ri-file-list-3-line text-white fs-2"></i>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card border-0 shadow rounded-4 h-100">

                            <div class="card-body">

                                <div class="d-flex justify-content-between">

                                    <div>

                                        <small class="text-muted">
                                            Today's Working MRs
                                        </small>

                                        <h2 class="fw-bold text-success mt-2">
                                            <?= $todayWorking ?>
                                        </h2>

                                    </div>

                                    <div class="avatar-md bg-success rounded-circle">

                                        <div class="avatar-title">

                                            <i class="ri-user-star-line text-white fs-2"></i>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="row mb-4">

                    <div class="col-xl-4">

                        <div class="card border-0 shadow rounded-4">

                            <div class="card-header bg-white">

                                <h5 class="mb-0">
                                    Today's Activities
                                </h5>

                            </div>

                            <div class="card-body">

                                <table class="table table-borderless mb-0">

                                    <tr>

                                        <td>
                                            Doctor Calls
                                        </td>

                                        <td class="text-end fw-bold">
                                            <?= $todayDoctor ?>
                                        </td>

                                    </tr>

                                    <tr>

                                        <td>
                                            Chemist Calls
                                        </td>

                                        <td class="text-end fw-bold">
                                            <?= $todayChemist ?>
                                        </td>

                                    </tr>

                                    <tr>

                                        <td>
                                            Stockist Calls
                                        </td>

                                        <td class="text-end fw-bold">
                                            <?= $todayStockist ?>
                                        </td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-8">

                        <div class="card border-0 shadow rounded-4">

                            <div class="card-header bg-white">

                                <h5 class="mb-0">
                                    Monthly Performance
                                </h5>

                            </div>

                            <div class="card-body">

                                <div class="row text-center">

                                    <div class="col-md-4">

                                        <h3 class="text-primary">
                                            <?= $monthlyTour ?>
                                        </h3>

                                        <p>Tour Plans</p>

                                    </div>

                                    <div class="col-md-4">

                                        <h3 class="text-success">
                                            <?= $monthlyDCR ?>
                                        </h3>

                                        <p>DCR Submitted</p>

                                    </div>

                                    <div class="col-md-4">

                                        <h3 class="text-info">
                                            <?= $monthlyDoctor ?>
                                        </h3>

                                        <p>Doctor Calls</p>

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