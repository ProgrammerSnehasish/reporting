<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$id = (int)$_GET['id'];
$rbm_id = $_SESSION['user_id'];

$sql = "
UPDATE tbl_tour_plans
SET
    status = 'RBM Approved',
    current_level = 'ADMIN',
    rbm_user_id = ?,
    rbm_approved_at = NOW(),
    approved_by = ?,
    approved_at = NOW()
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $rbm_id, $rbm_id, $id);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = "Tour Plan Approved Successfully.";

header("Location: tour-plan-list.php");
exit;