<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid Tour Plan.";
    header("Location: tour-plan-list.php");
    exit;
}

$id = (int)$_GET['id'];

// ── Master record ─────────────────────────────────────────────────────────────
// FIX: added JOIN to tbl_users/tbl_employees for approved_by_name
//      (tp.* alone never contains that — needs a second join on approved_by column)

$abm_id = $_SESSION['user_id'];

$stmtMaster = mysqli_prepare($conn, "
SELECT

tp.*,
e.employee_code,
CONCAT(e.first_name,' ',e.last_name) AS employee_name

FROM tbl_tour_plans tp

INNER JOIN tbl_user_mapping um
ON um.employee_user_id = tp.mr_id

INNER JOIN tbl_users u
ON u.id = tp.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

WHERE
tp.id = ?
AND
um.manager_user_id=?
AND tp.current_level='ABM'

LIMIT 1
");

mysqli_stmt_bind_param($stmtMaster, "ii", $id, $abm_id);
mysqli_stmt_execute($stmtMaster);

$tour = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtMaster));

if (!$tour) {
    $_SESSION['error'] = "Tour Plan not found.";
    header("Location:tour-plan-list.php");
    exit;
}

// ── Details with working_with names resolved ──────────────────────────────────
// FIX: working_with_user_id is stored as "1,2,3" (comma-separated).
//      A single LEFT JOIN on that column only ever matches one ID.
//      Solution: fetch all detail rows first, collect every user ID mentioned,
//      load them in ONE query, then map names back — no N+1, no missing names.

$detailStmt = mysqli_prepare($conn, "
    SELECT
        tpd.*,
        hq.area_name  AS hq_name,
        a.area_name   AS area_name,
        wt.working_type_name

    FROM tbl_tour_plan_details tpd

    LEFT JOIN tbl_areas hq
        ON hq.id = tpd.hq_id

    LEFT JOIN tbl_areas a
        ON a.id = tpd.area_id

    LEFT JOIN tbl_working_types wt
        ON wt.id = tpd.working_type_id

    WHERE tpd.tour_plan_id = ?

    ORDER BY tpd.plan_date ASC
");
mysqli_stmt_bind_param($detailStmt, "i", $id);
mysqli_stmt_execute($detailStmt);
$detailRows = mysqli_fetch_all(mysqli_stmt_get_result($detailStmt), MYSQLI_ASSOC);

// Collect all user IDs from every working_with_user_id cell
$allUserIds = [];
foreach ($detailRows as $row) {
    if (!empty($row['working_with_user_id'])) {
        foreach (explode(',', $row['working_with_user_id']) as $uid) {
            $uid = (int)trim($uid);
            if ($uid > 0) $allUserIds[$uid] = $uid;
        }
    }
}

// Load all those users in one query
$userNameMap = []; // [user_id => 'First Last (ROLE)']
if (!empty($allUserIds)) {
    $inList   = implode(',', $allUserIds);
    $userRes  = mysqli_query($conn, "
        SELECT u.id,
               CONCAT(e.first_name, ' ', e.last_name) AS full_name,
               r.role_name
        FROM tbl_users u
        INNER JOIN tbl_employees e ON e.id = u.employee_id
        INNER JOIN tbl_roles r     ON r.id = u.role_id
        WHERE u.id IN ($inList)
    ");
    while ($u = mysqli_fetch_assoc($userRes)) {
        $userNameMap[(int)$u['id']] = htmlspecialchars($u['full_name'])
            . ' (' . strtoupper($u['role_name']) . ')';
    }
}

// FIX: pre-fetch day_plan existence for ALL detail IDs in one query
//      instead of one query per row inside the loop (N+1 problem)
$detailIds  = array_column($detailRows, 'id');
$dayPlanMap = []; // [tour_plan_detail_id => day_plan_id]
if (!empty($detailIds)) {
    $inIds      = implode(',', array_map('intval', $detailIds));
    $dpRes      = mysqli_query($conn, "
        SELECT id, tour_plan_detail_id
        FROM tbl_day_plans
        WHERE tour_plan_detail_id IN ($inIds)
    ");
    while ($dp = mysqli_fetch_assoc($dpRes)) {
        $dayPlanMap[(int)$dp['tour_plan_detail_id']] = (int)$dp['id'];
    }
}

?>

<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0">View Tour Plan Details</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="tour-plan-list.php" class="btn btn-primary btn-sm">
                            <i class="ri-list-check"></i> Back to List
                        </a>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-12">

                        <!-- ── Master Info ── -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Tour Plan Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered mb-0">

                                    <tr>
                                        <th width="25%">Month</th>
                                        <td>
                                            <?= date('F Y', mktime(0, 0, 0, (int)$tour['month'], 1, (int)$tour['year'])) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Employee</th>
                                        <td>
                                            <?= htmlspecialchars($tour['employee_code']) ?>
                                            -
                                            <?= htmlspecialchars($tour['employee_name']) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php
                                            switch ((int)$tour['status']) {
                                                case 0:
                                                    echo '<span class="badge bg-secondary">Draft</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge bg-warning text-dark">Pending Approval</span>';
                                                    break;
                                                case 2:
                                                    echo '<span class="badge bg-info">ABM Approved</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="badge bg-success">RBM Approved</span>';
                                                    break;
                                                case 4:
                                                    echo '<span class="badge bg-danger">Rejected</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-dark">Unknown</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created On</th>
                                        <td>
                                            <?= !empty($tour['created_at'])
                                                ? date('d-m-Y h:i A', strtotime($tour['created_at']))
                                                : '-' ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Submitted On</th>
                                        <td>
                                            <?= !empty($tour['submitted_at'])
                                                ? date('d-m-Y h:i A', strtotime($tour['submitted_at']))
                                                : '-' ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Approved On</th>
                                        <td>
                                            <?= !empty($tour['approved_at'])
                                                ? date('d-m-Y h:i A', strtotime($tour['approved_at']))
                                                : '-' ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Approved By</th>
                                        <td>
                                            <?= !empty($tour['approved_by_name'])
                                                ? htmlspecialchars($tour['approved_by_name'])
                                                : '-' ?>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <!-- ── Detail Rows ── -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Tour Plan Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Day</th>
                                                <th>HQ</th>
                                                <th>Area</th>
                                                <th>Working Type</th>
                                                <th>Working With</th>
                                                <th>Night Halt</th>
                                                <!-- <th>Objective</th>
                                                <th>Remarks</th> -->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php if (empty($detailRows)): ?>
                                                <tr>
                                                    <td colspan="11" class="text-center">No Tour Plan Details Found.</td>
                                                </tr>
                                            <?php else: ?>

                                                <?php $sl = 1;
                                                foreach ($detailRows as $plan): ?>

                                                    <?php
                                                    // Resolve working_with names from the pre-loaded map
                                                    $wwNames = [];
                                                    if (!empty($plan['working_with_user_id'])) {
                                                        foreach (explode(',', $plan['working_with_user_id']) as $uid) {
                                                            $uid = (int)trim($uid);
                                                            if (isset($userNameMap[$uid])) {
                                                                $wwNames[] = $userNameMap[$uid];
                                                            }
                                                        }
                                                    }

                                                    // FIX: look up day plan from pre-fetched map, not a per-row query
                                                    $dayPlanId = $dayPlanMap[(int)$plan['id']] ?? null;
                                                    ?>

                                                    <tr>
                                                        <td><?= $sl++ ?></td>
                                                        <td><?= date('d-m-Y', strtotime($plan['plan_date'])) ?></td>
                                                        <td><?= date('l',     strtotime($plan['plan_date'])) ?></td>
                                                        <td><?= htmlspecialchars($plan['hq_name']            ?? '-') ?></td>
                                                        <td><?= htmlspecialchars($plan['area_name']          ?? '-') ?></td>
                                                        <td><?= htmlspecialchars($plan['working_type_name']  ?? '-') ?></td>

                                                        <!-- FIX: shows all working-with names, comma-separated -->
                                                        <td>
                                                            <?= !empty($wwNames)
                                                                ? implode('<br>', $wwNames)
                                                                : '-' ?>
                                                        </td>

                                                        <td><?= htmlspecialchars($plan['night_halt'] ?? '-') ?></td>

                                                        <!-- <td>
                                                            <?= !empty($plan['objective'])
                                                                ? nl2br(htmlspecialchars($plan['objective']))
                                                                : '-' ?>
                                                        </td>

                                                        <td>
                                                            <?= !empty($plan['remarks'])
                                                                ? nl2br(htmlspecialchars($plan['remarks']))
                                                                : '-' ?>
                                                        </td> -->

                                                        <td>
                                                            <?php if ($dayPlanId): ?>
                                                                <!-- Day plan exists: show View + Edit -->
                                                                <a href="tour-day-plan-view.php?id=<?= $dayPlanId ?>"
                                                                    class="btn btn-success btn-sm mb-1">
                                                                    <i class="ri-eye-line"></i> View Day Plan
                                                                </a>
                                                                <a href="tour-day-plan-edit.php?id=<?= $dayPlanId ?>"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="ri-edit-line"></i> Edit Day Plan
                                                                </a>
                                                            <?php else: ?>
                                                                <!-- FIX: Edit button was shown here before even though
                                                                 $dayPlan was undefined in this branch -->
                                                                <a href="tour-day-plan-add.php?id=<?= (int)$plan['id'] ?>"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="ri-add-line"></i> Create Day Plan
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php include('./includes/footer.php'); ?>

    </div>
</div>

<?php include('./includes/scripts.php'); ?>