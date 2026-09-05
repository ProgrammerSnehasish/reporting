<?php

require_once "../config/auth.php";
require_once "../config/database.php";
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
                            <h4 class="mb-sm-0">Dashboard</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row mb-4">

                    <div class="col-lg-3 col-md-3">
                        <a href="dcr-list.php" class="text-decoration-none">
                            <div class="card action-card border-0 shadow">
                                <div class="card-body text-center">
                                    <i class="ri-file-list-3-line display-5 text-primary"></i>
                                    <h5 class="mt-3 mb-1">DCR</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <a href="tour-plan-list.php" class="text-decoration-none">
                            <div class="card action-card border-0 shadow">
                                <div class="card-body text-center">
                                    <i class="ri-route-line display-5 text-success"></i>
                                    <h5 class="mt-3 mb-1">Tour Plans</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-2 col-md-2">
                        <a href="attendance-list.php" class="text-decoration-none">
                            <div class="card action-card border-0 shadow">
                                <div class="card-body text-center">
                                    <i class="ri-calendar-check-line display-5 text-warning"></i>
                                    <h5 class="mt-3 mb-1">Attendance</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-2 col-md-2">
                        <a href="expense-list.php" class="text-decoration-none">
                            <div class="card action-card border-0 shadow">
                                <div class="card-body text-center">
                                    <i class="ri-money-dollar-circle-line display-5 text-danger"></i>
                                    <h5 class="mt-3 mb-1">Expenses</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-2 col-md-2">
                        <a href="message-list.php" class="text-decoration-none">
                            <div class="card action-card border-0 shadow">
                                <div class="card-body text-center">
                                    <i class="ri-money-dollar-circle-line display-5 text-danger"></i>
                                    <h5 class="mt-3 mb-1">All Messages</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <!-- Row 1 -->
                <div class="row">

                    <?php

                    // Total DCR

                    $totalDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    "));

                    // Today's DCR

                    $todayDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE visit_date=CURDATE()
                    "));

                    // Current Month DCR

                    $currentMonthDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE MONTH(visit_date)=MONTH(CURDATE())
                    AND YEAR(visit_date)=YEAR(CURDATE())
                    "));

                    // Approved DCR

                    $approvedDCR = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT COUNT(*) total
                    FROM tbl_dcr
                    WHERE status=1
                    "));

                    ?>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Total DCR
                                </p>

                                <h3><?= $totalDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Today's DCR
                                </p>

                                <h3><?= $todayDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Current Month
                                </p>

                                <h3><?= $currentMonthDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <p class="text-truncate font-size-14 mb-2">
                                    Approved DCR
                                </p>

                                <h3><?= $approvedDCR['total']; ?></h3>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Row 1 -->

                <!-- Row 2 -->

                <?php

                // Total Tour Plans
                $totalPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                "));

                // Pending Approval
                $pendingPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=1
                "));

                // Approved Tour Plans
                $approvedPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=3
                "));

                // Rejected Tour Plans
                $rejectedPlan = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) total
                FROM tbl_tour_plans
                WHERE status=4
                "));

                ?>

                <div class="row">

                    <!-- Total Tour Plans -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Total Tour Plans

                                        </p>

                                        <h4 class="mb-2">

                                            <?= $totalPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-primary rounded">

                                            <i class="ri-road-map-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Pending -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Pending Approval

                                        </p>

                                        <h4 class="mb-2 text-warning">

                                            <?= $pendingPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-warning rounded">

                                            <i class="ri-time-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Approved -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Approved Plans

                                        </p>

                                        <h4 class="mb-2 text-success">

                                            <?= $approvedPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-success rounded">

                                            <i class="ri-checkbox-circle-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Rejected -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Rejected Plans

                                        </p>

                                        <h4 class="mb-2 text-danger">

                                            <?= $rejectedPlan['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-danger rounded">

                                            <i class="ri-close-circle-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Row 2 -->

                <!-- Row 3 -->

                <div class="row">

                    <?php

                    $totalDoctorVisit = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls

                "));

                    $todayDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls dc

                INNER JOIN tbl_dcr d
                ON d.id = dc.dcr_id

                WHERE d.visit_date = CURDATE()

                "));

                    $currentMonthDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_doctor_calls dc

                INNER JOIN tbl_dcr d
                ON d.id = dc.dcr_id

                WHERE

                MONTH(d.visit_date)=MONTH(CURDATE())

                AND YEAR(d.visit_date)=YEAR(CURDATE())

                "));

                    $uniqueDoctor = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(DISTINCT doctor_id) total

                FROM tbl_dcr_doctor_calls

                "));

                    ?>


                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Total Doctor Visits</h6>

                                <h2><?= $totalDoctorVisit['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Today's Calls</h6>

                                <h2><?= $todayDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Current Month Calls</h6>

                                <h2><?= $currentMonthDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Unique Doctors</h6>

                                <h2><?= $uniqueDoctor['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Row 3 -->

                <?php

                $totalChemistVisit = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_chemist_calls

                "));

                $todayChemist = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_chemist_calls cc

                INNER JOIN tbl_dcr d
                ON d.id = cc.dcr_id

                WHERE d.visit_date = CURDATE()

                "));

                $currentMonthChemist = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_dcr_chemist_calls cc

                INNER JOIN tbl_dcr d
                ON d.id = cc.dcr_id

                WHERE

                MONTH(d.visit_date)=MONTH(CURDATE())

                AND YEAR(d.visit_date)=YEAR(CURDATE())

                "));

                $uniqueChemist = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(DISTINCT chemist_id) total

                FROM tbl_dcr_chemist_calls

                "));
                ?>

                <div class="row">

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Total Chemist Visits</h6>

                                <h2><?= $totalChemistVisit['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Today's Calls</h6>

                                <h2><?= $todayChemist['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Current Month Calls</h6>

                                <h2><?= $currentMonthChemist['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body text-center">

                                <h6>Unique Chemists</h6>

                                <h2><?= $uniqueChemist['total']; ?></h2>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <?php

                    $totalStockistVisit = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_chemists c
                    ON c.id = sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id = c.chemist_type_id

                    WHERE LOWER(ct.type_name)='stockist'

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Total Stockist Visit</p>
                                        <h4 class="mb-2"><?php echo $totalStockistVisit['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $todayStockist = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_dcr d
                    ON d.id=sc.dcr_id

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE

                    LOWER(ct.type_name)='stockist'

                    AND d.visit_date=CURDATE()

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Today's Stockist Calls</p>
                                        <h4 class="mb-2"><?php echo $todayStockist['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                    $currentMonth = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(*) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_dcr d
                    ON d.id=sc.dcr_id

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE

                    LOWER(ct.type_name)='stockist'

                    AND MONTH(d.visit_date)=MONTH(CURDATE())

                    AND YEAR(d.visit_date)=YEAR(CURDATE())

                    "));

                    ?>

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Current Month Stockist Calls</p>
                                        <h4 class="mb-2"><?php echo $currentMonth['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                    $uniqueStockist = mysqli_fetch_assoc(mysqli_query($conn, "

                    SELECT COUNT(DISTINCT sc.stockist_id) total

                    FROM tbl_dcr_stockist_calls sc

                    INNER JOIN tbl_chemists c
                    ON c.id=sc.stockist_id

                    INNER JOIN tbl_chemist_types ct
                    ON ct.id=c.chemist_type_id

                    WHERE LOWER(ct.type_name)='stockist'

                    "));


                    ?>

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Unique Stockists</p>
                                        <h4 class="mb-2"><?php echo $uniqueStockist['total']; ?></h4>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <?php

                $totalEmployees = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_employees

                "));


                $activeEmployees = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_employees

                WHERE status=1

                "));

                $totalMR = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_users u

                INNER JOIN tbl_roles r
                ON r.id=u.role_id

                WHERE LOWER(r.role_code)='mr'

                "));

                $totalManagers = mysqli_fetch_assoc(mysqli_query($conn, "

                SELECT COUNT(*) total

                FROM tbl_users u

                INNER JOIN tbl_roles r
                ON r.id=u.role_id

                WHERE LOWER(r.role_code) IN

                ('abm','rbm','zbm','sm','nsm')

                "));

                ?>


                <div class="row">

                    <!-- Total Employees -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Total Employees

                                        </p>

                                        <h4 class="mb-2">

                                            <?= $totalEmployees['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-primary rounded">

                                            <i class="ri-team-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Active Employees -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Active Employees

                                        </p>

                                        <h4 class="mb-2 text-success">

                                            <?= $activeEmployees['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-success rounded">

                                            <i class="ri-user-follow-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Total MR -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Total MR

                                        </p>

                                        <h4 class="mb-2 text-info">

                                            <?= $totalMR['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-info rounded">

                                            <i class="ri-user-star-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Total Managers -->

                    <div class="col-xl-3 col-md-6">

                        <div class="card">

                            <div class="card-body">

                                <div class="d-flex">

                                    <div class="flex-grow-1">

                                        <p class="text-truncate font-size-14 mb-2">

                                            Total Managers

                                        </p>

                                        <h4 class="mb-2 text-warning">

                                            <?= $totalManagers['total']; ?>

                                        </h4>

                                    </div>

                                    <div class="avatar-sm">

                                        <span class="avatar-title bg-warning rounded">

                                            <i class="ri-user-settings-line font-size-24"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Row 4 -->
                <!-- <div class="row"> -->

                <!-- Top Franchise -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-building-4-line me-3"></i>
                                    Top Franchise
                                </h5>

                                <h3 class="text-white mb-2">Kolkata Central</h3>

                                <p class="card-text mb-2">
                                    Monthly Revenue : <strong>₹12,45,600</strong>
                                </p>

                                <p class="card-text mb-0">
                                    426 Patients • 94% Performance
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- Top Medical Representative -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-user-star-line me-3"></i>
                                    Top Medical Representative
                                </h5>

                                <h3 class="text-white mb-2">Rahul Sharma</h3>

                                <p class="card-text mb-2">
                                    Doctor Visits : <strong>186</strong>
                                </p>

                                <p class="card-text mb-0">
                                    58 Leads • 46 Conversions
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- Top Selling Medicine -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-capsule-line me-3"></i>
                                    Top Selling Medicine
                                </h5>

                                <h3 class="text-white mb-2">Pain Relief Kit</h3>

                                <p class="card-text mb-2">
                                    Units Sold : <strong>2,450</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Revenue : ₹8,75,000
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- </div> -->
                <!-- Row 4 -->

                <!-- Row 5 -->
                <!-- <div class="row"> -->

                <!-- Active Medical Representatives -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-user-star-line me-3"></i>
                                    Active Medical Representatives
                                </h5>

                                <h3 class="text-white mb-2">156 Active MR</h3>

                                <p class="card-text mb-2">
                                    Today's Field Visit : <strong>128</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Present : 148 &nbsp; | &nbsp; Leave : 8
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- Zone Wise Dashboard -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-map-pin-line me-3"></i>
                                    Zone Wise Dashboard
                                </h5>

                                <h3 class="text-white mb-2">12 Active Zones</h3>

                                <p class="card-text mb-2">
                                    Best Zone : <strong>North Zone</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Revenue : ₹18.45 Lakh
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- Branch Wise Dashboard -->
                <!-- <div class="col-lg-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-hospital-line me-3"></i>
                                    Branch Wise Dashboard
                                </h5>

                                <h3 class="text-white mb-2">25 Active Branches</h3>

                                <p class="card-text mb-2">
                                    Best Branch : <strong>Kolkata Central</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Monthly Revenue : ₹12.45 Lakh
                                </p>
                            </div>
                        </div>
                    </div> -->

                <!-- </div> -->
                <!-- Row 5 -->



                <div class="row">
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                            <a class="dropdown-item" href="#">Download Report</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Revenue Analytics</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">25,117</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>2.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Marketplace</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$34,856</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Week</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$18,225</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.7 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Month</p>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="area_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown">
                                        <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">This Years<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Today</a>
                                            <a class="dropdown-item" href="#">Last Week</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">This Year</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Patient Growth</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>17,493</h5>
                                                <p class="text-muted text-truncate mb-0">Marketplace</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>$44,960</h5>
                                                <p class="text-muted text-truncate mb-0">Last Week</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div>
                                                <h5>$29,142</h5>
                                                <p class="text-muted text-truncate mb-0">Last Month</p>
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="column_line_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->


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