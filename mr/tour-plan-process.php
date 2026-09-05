<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tour-plan-add.php");
    exit;
}

$mr_id      = (int)$_SESSION['user_id'];
$tour_month = trim($_POST['tour_month'] ?? '');

if (empty($tour_month) || !preg_match('/^\d{4}-\d{2}$/', $tour_month)) {
    $_SESSION['error'] = "Please select a valid Tour Month.";
    header("Location: tour-plan-add.php");
    exit;
}

$month = (int)date('m', strtotime($tour_month . '-01'));
$year  = (int)date('Y', strtotime($tour_month . '-01'));
$totalDays = (int)date('t', mktime(0, 0, 0, $month, 1, $year));

// ── All arrays are keyed by 1-based day number $d ────────────────────────────
// Form sends: tour_date[1], area_id[1], hq_id[1], working_type_id[1], etc.
$tour_dates      = $_POST['tour_date']            ?? [];  // [d => 'Y-m-d']
$hq_ids          = $_POST['hq_id']                ?? [];  // [d => id]
$area_ids        = $_POST['area_id']              ?? [];  // [d => id]
$type_ids        = $_POST['working_type_id']      ?? [];  // [d => id]
$night_halts     = $_POST['night_halt']           ?? [];  // [d => 'Yes'|'No']
$objectives      = $_POST['objective']            ?? [];  // [d => string]
$remarks_arr     = $_POST['remarks']              ?? [];  // [d => string]
$working_with_map = $_POST['working_with_user_id'] ?? []; // [d => [id,...]]

mysqli_begin_transaction($conn);

try {

    // ── Duplicate check ───────────────────────────────────────────────────────
    $stmtCheck = mysqli_prepare($conn, "
        SELECT id FROM tbl_tour_plans
        WHERE mr_id = ? AND month = ? AND year = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmtCheck, 'iii', $mr_id, $month, $year);
    mysqli_stmt_execute($stmtCheck);
    mysqli_stmt_store_result($stmtCheck);

    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        throw new Exception("Tour Plan already exists for this month.");
    }
    mysqli_stmt_close($stmtCheck);

    // ── Validate: HQ + Area required for every day ───────────────────────────
    // Loop by $d (1-based) to match the form's array keys exactly.
    for ($d = 1; $d <= $totalDays; $d++) {
        $plan_date = trim($tour_dates[$d] ?? '');
        if (empty($plan_date)) continue;

        if ((int)($hq_ids[$d] ?? 0) === 0) {
            throw new Exception("Please select HQ for: " . date('d-m-Y', strtotime($plan_date)));
        }
        if ((int)($area_ids[$d] ?? 0) === 0) {
            throw new Exception("Please select Area for: " . date('d-m-Y', strtotime($plan_date)));
        }
    }

    // ── Insert master record ─────────────────────────────────────────────────
    $stmtMaster = mysqli_prepare($conn, "
        INSERT INTO tbl_tour_plans (mr_id, month, year, status, current_level, created_at)
        VALUES (?, ?, ?, 'Submitted', 'ABM', NOW())
    ");
    mysqli_stmt_bind_param($stmtMaster, 'iii', $mr_id, $month, $year);
    mysqli_stmt_execute($stmtMaster);

    if (mysqli_stmt_error($stmtMaster)) {
        throw new Exception("Master insert failed: " . mysqli_stmt_error($stmtMaster));
    }

    $tour_plan_id = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmtMaster);

    // ── Insert detail rows, one per day, keyed by $d ─────────────────────────
    for ($d = 1; $d <= $totalDays; $d++) {

        $plan_date_raw = trim($tour_dates[$d] ?? '');
        if (empty($plan_date_raw)) continue;

        $hq_id           = (int)($hq_ids[$d]    ?? 0);
        $area_id         = (int)($area_ids[$d]  ?? 0);
        $working_type_id = (int)($type_ids[$d]  ?? 0);
        $objective       = trim($objectives[$d]  ?? '');
        $halt            = in_array(trim($night_halts[$d] ?? ''), ['Yes', 'No'])
            ? trim($night_halts[$d]) : 'No';
        $remark          = trim($remarks_arr[$d] ?? '');

        // working_with: array of user IDs for this day, keyed by $d (1-based)
        $rawUsers     = isset($working_with_map[$d]) ? (array)$working_with_map[$d] : [];
        $cleanUsers   = array_values(array_filter(array_map('intval', $rawUsers)));
        $working_with = !empty($cleanUsers) ? implode(',', $cleanUsers) : '';

        // Escape strings
        $plan_date_esc    = mysqli_real_escape_string($conn, $plan_date_raw);
        $objective_esc    = mysqli_real_escape_string($conn, $objective);
        $halt_esc         = mysqli_real_escape_string($conn, $halt);
        $remark_esc       = mysqli_real_escape_string($conn, $remark);
        $working_with_esc = mysqli_real_escape_string($conn, $working_with);

        $working_type_sql = $working_type_id > 0 ? $working_type_id : 'NULL';
        $working_with_sql = $working_with_esc !== '' ? "'$working_with_esc'" : 'NULL';

        $sql = "
            INSERT INTO tbl_tour_plan_details
                (tour_plan_id, plan_date, hq_id, area_id, working_type_id,
                 working_with_user_id, objective, night_halt, remarks, created_at)
            VALUES
                ($tour_plan_id, '$plan_date_esc', $hq_id, $area_id, $working_type_sql,
                 $working_with_sql, '$objective_esc', '$halt_esc', '$remark_esc', NOW())
        ";

        mysqli_query($conn, $sql);

        if (mysqli_error($conn)) {
            throw new Exception(
                "Detail insert failed for $plan_date_raw: " . mysqli_error($conn)
            );
        }
    }

    mysqli_commit($conn);
    unset($_SESSION['tour_plan_draft']);

    $_SESSION['success'] = "Monthly Tour Plan saved successfully.";
    header("Location: tour-plan-add.php");
    exit;
} catch (Exception $e) {

    mysqli_rollback($conn);

    // ── Save draft so the form repopulates exactly as the user left it ────────
    // Store $_POST directly — it already has all the 1-based [$d] keys the
    // add form expects, including hq_id[$d] and area_id[$d].
    $_SESSION['tour_plan_draft'] = $_POST;
    $_SESSION['error']           = $e->getMessage();

    header("Location: tour-plan-add.php?tour_month=" . urlencode($tour_month));
    exit;
}
