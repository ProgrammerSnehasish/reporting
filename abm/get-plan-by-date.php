<?php
require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);
header('Content-Type: application/json');

$date = mysqli_real_escape_string($conn, $_GET['date'] ?? '');
if (!$date) {
    echo json_encode(['success' => false, 'msg' => 'No date provided']);
    exit;
}

// --- Try every common session key ---
$mr_id = 0;
foreach (['employee_id', 'emp_id', 'user_id', 'id', 'userid'] as $key) {
    if (!empty($_SESSION[$key])) {
        $mr_id = (int)$_SESSION[$key];
        break;
    }
}

if (!$mr_id) {
    echo json_encode(['success' => false, 'msg' => 'MR not identified', 'session_keys' => array_keys($_SESSION)]);
    exit;
}

// --- Try matching mr_id as employee_id first, then as user_id ---
$sql = "
    SELECT
        tpd.area_id,
        tpd.working_type_id,
        tpd.working_with_user_id,
        a.hq_id,
        a.area_name,
        tp.status AS plan_status,
        tp.mr_id  AS plan_mr_id
    FROM tbl_tour_plans tp
    INNER JOIN tbl_tour_plan_details tpd
        ON tpd.tour_plan_id = tp.id
    LEFT JOIN tbl_areas a
        ON a.id = tpd.area_id
    WHERE tp.mr_id    = $mr_id
      AND tpd.plan_date = '$date'
    ORDER BY tp.id DESC
    LIMIT 1
";

$res = mysqli_query($conn, $sql);

if (!$res) {
    echo json_encode(['success' => false, 'msg' => mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode([
        'success'    => false,
        'msg'        => 'No plan found',
        'mr_id_used' => $mr_id,
        'date_used'  => $date
    ]);
}
exit;
