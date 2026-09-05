<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

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

                    <div class="col-md-3">
                        <a href="dcr-add.php" class="text-decoration-none">
                            <div class="card border-0 shadow glow-card dcr-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-md bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-file-list-3-line fs-2"></i>
                                    </div>

                                    <div>
                                        <h5 class="mb-1">Submit Today's DCR</h5>
                                        <small class="text-muted">
                                            Complete your Daily Call Report before leaving.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <a href="tour-plan-list.php" class="text-decoration-none">
                            <div class="card border-0 shadow glow-card tour-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-md bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-route-line fs-2"></i>
                                    </div>

                                    <div>
                                        <h5 class="mb-1">Create Tour Plan</h5>
                                        <small class="text-muted">
                                            Plan your upcoming field visits.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
					
					<?php

					$unread_messages = mysqli_num_rows(mysqli_query($conn,"
					SELECT id
					FROM tbl_message_receivers
					WHERE
					receiver_user_id='{$_SESSION['user_id']}'
					AND is_read='0'
					"));

					?>

					<div class="col-md-3">

						<a href="message-list.php" class="text-decoration-none">

							<div class="card border-0 shadow glow-card message-card">

								<div class="card-body d-flex align-items-center">

									<div class="avatar-md bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3">

										<i class="ri-message-2-line fs-2"></i>

									</div>

									<div>

										<h5 class="mb-1">

											Messages

											<?php if($unread_messages>0){ ?>

												<span class="badge bg-danger ms-2">

													<?php echo $unread_messages; ?>

												</span>

											<?php } ?>

										</h5>

										<small class="text-muted">

											<?php

											if($unread_messages>0){

												echo $unread_messages." Unread Message(s)";

											}else{

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

                <!-- Row 1 -->
                <div class="row">

                    <!-- <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">

                            <h4 class="fw-bold text-dark mb-2">
                                Welcome,
                                <span class="text-primary">
                                    <?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?>
                                </span>
                            </h4>

                            <div class="mb-2">
                                <span class="badge bg-info fs-6 px-3 py-2">
                                    <?php echo htmlspecialchars($row['designation_name']); ?>
                                </span>
                            </div>

                            <p class="text-muted mb-0">
                                <i class="ri-id-card-line"></i>
                                Employee Code :
                                <strong><?php echo htmlspecialchars($row['employee_code']); ?></strong>
                            </p>

                        </div>
                    </div> -->

                    <!-- Today's DCR -->
                    <?php

                    $today = date("Y-m-d");

                    $sql = "

                    SELECT

                    tpd.*,

                    a.area_name,

                    wt.working_type_name

                    FROM tbl_tour_plan_details tpd

                    INNER JOIN tbl_tour_plans tp
                    ON tp.id = tpd.tour_plan_id

                    LEFT JOIN tbl_areas a
                    ON a.id = tpd.area_id

                    LEFT JOIN tbl_working_types wt
                    ON wt.id = tpd.working_type_id

                    WHERE

                    tp.mr_id = ?

                    AND tpd.plan_date = ?

                    LIMIT 1

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $today);

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $todayTour = mysqli_fetch_assoc($result);

                    ?>
                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">
                                            Today's Tour
                                        </p>

                                        <?php if ($todayTour) { ?>

                                            <h5 class="mb-1">
                                                <?= $todayTour['area_name']; ?>
                                            </h5>

                                            <p class="text-muted mb-1">
                                                <?= $todayTour['working_type_name']; ?>
                                            </p>

                                            <small class="text-primary">
                                                <?= date("d M Y", strtotime($todayTour['plan_date'])); ?>
                                            </small>

                                        <?php } else { ?>

                                            <h6 class="text-danger mb-0">
                                                No Tour Planned
                                            </h6>

                                        <?php } ?>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-primary rounded">

                                            <i class="ri-route-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <?php

                    $today = date("Y-m-d");

                    $sql = "

                    SELECT

                    dp.*,

                    tpd.objective,

                    tpd.area_id,

                    a.area_name

                    FROM tbl_day_plans dp

                    INNER JOIN tbl_tour_plan_details tpd
                    ON tpd.id = dp.tour_plan_detail_id

                    LEFT JOIN tbl_areas a
                    ON a.id = tpd.area_id

                    WHERE

                    dp.mr_id = ?

                    AND dp.plan_date = ?

                    LIMIT 1

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $today);

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $todayPlan = mysqli_fetch_assoc($result);

                    ?>

                    <!-- Monthly Revenue -->
                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">
                                            Today's Day Plan
                                        </p>

                                        <?php if ($todayPlan) { ?>

                                            <h5 class="mb-1">
                                                <?= $todayPlan['area_name']; ?>
                                            </h5>

                                            <p class="text-muted mb-1">
                                                <?= $todayPlan['objective']; ?>
                                            </p>

                                            <small class="text-primary">

                                                Doctor :
                                                <?= $todayPlan['doctor_target']; ?>

                                                |

                                                Chemist :
                                                <?= $todayPlan['chemist_target']; ?>

                                                |

                                                Stockist :
                                                <?= $todayPlan['stockist_target']; ?>

                                            </small>

                                        <?php } else { ?>

                                            <h6 class="text-danger mb-0">

                                                No Day Plan

                                            </h6>

                                        <?php } ?>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-success rounded">

                                            <i class="ri-calendar-check-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <?php

                    $today = date("Y-m-d");

                    $sql = "

                    SELECT

                    d.id,
                    d.dcr_no,
                    d.visit_date,
                    d.status,

                    a.area_name

                    FROM tbl_dcr d

                    LEFT JOIN tbl_areas a
                    ON a.id = d.area_id

                    WHERE

                    d.mr_id = ?

                    AND d.visit_date = ?

                    LIMIT 1

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $today);

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $todayDCR = mysqli_fetch_assoc($result);

                    ?>

                    <!-- Total Patients -->
                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">
                                            Today's DCR
                                        </p>

                                        <?php if ($todayDCR) { ?>

                                            <h5 class="mb-1">
                                                <?= $todayDCR['dcr_no']; ?>
                                            </h5>

                                            <p class="text-muted mb-1">
                                                <?= $todayDCR['area_name']; ?>
                                            </p>

                                            <?php

                                            switch ($todayDCR['status']) {

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
                                            }

                                            ?>

                                        <?php } else { ?>

                                            <h6 class="text-danger mb-0">
                                                Not Submitted
                                            </h6>

                                        <?php } ?>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-warning rounded">

                                            <i class="ri-file-list-3-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>



                </div>
                <!-- Row 1 -->

                <!-- Row 2 -->
                <div class="row">

                    <?php

                    $currentMonth = date("m");
                    $currentYear  = date("Y");

                    $sql = "

                    SELECT

                    COALESCE(SUM(doctor_target),0) AS doctor_target

                    FROM tbl_day_plans

                    WHERE

                    mr_id = ?

                    AND MONTH(plan_date) = ?

                    AND YEAR(plan_date) = ?

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iii",
                        $_SESSION['user_id'],
                        $currentMonth,
                        $currentYear
                    );

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $doctorTarget = mysqli_fetch_assoc($result);

                    ?>

                    <!-- Pending -->
                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <p class="text-truncate font-size-14 mb-2">
                                            Current Month Doctor Target
                                        </p>

                                        <h3 class="mb-0">
                                            <?= $doctorTarget['doctor_target']; ?>
                                        </h3>

                                        <small class="text-muted">
                                            Total Planned Doctor Visits
                                        </small>

                                    </div>



                                </div>

                            </div>

                        </div>

                    </div>

                    <?php

                    $currentMonth = date("m");
                    $currentYear  = date("Y");

                    $sql = "

                    SELECT

                    COALESCE(SUM(chemist_target),0) AS chemist_target

                    FROM tbl_day_plans

                    WHERE

                    mr_id = ?

                    AND MONTH(plan_date)=?

                    AND YEAR(plan_date)=?

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iii",
                        $_SESSION['user_id'],
                        $currentMonth,
                        $currentYear
                    );

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $chemistTarget = mysqli_fetch_assoc($result);

                    ?>

                    <!-- New Leads -->

                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <p class="text-truncate font-size-14 mb-2">
                                            Current Month Chemist Target
                                        </p>

                                        <h3 class="mb-0">
                                            <?= $chemistTarget['chemist_target']; ?>
                                        </h3>

                                        <small class="text-muted">
                                            Planned Chemist Visits
                                        </small>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-success rounded">

                                            <i class="ri-store-2-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <?php

                    $currentMonth = date("m");
                    $currentYear  = date("Y");

                    $sql = "

                    SELECT

                    COALESCE(SUM(stockist_target),0) AS stockist_target

                    FROM tbl_day_plans

                    WHERE

                    mr_id = ?

                    AND MONTH(plan_date)=?

                    AND YEAR(plan_date)=?

                    ";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iii",
                        $_SESSION['user_id'],
                        $currentMonth,
                        $currentYear
                    );

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $stockistTarget = mysqli_fetch_assoc($result);

                    ?>

                    <!-- Lead Conversion -->
                    <div class="col-xl-4 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <p class="text-truncate font-size-14 mb-2">
                                            Current Month Stockist Target
                                        </p>

                                        <h3 class="mb-0">
                                            <?= $stockistTarget['stockist_target']; ?>
                                        </h3>

                                        <small class="text-muted">
                                            Planned Stockist Visits
                                        </small>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-warning rounded">

                                            <i class="ri-building-4-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>




                </div>
                <!-- Row 2 -->




                <!-- end row -->
            </div>

        </div>
        <!-- End Page-content -->

        <?php include('./includes/footer.php'); ?>

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->


<?php include('./includes/scripts.php'); ?>