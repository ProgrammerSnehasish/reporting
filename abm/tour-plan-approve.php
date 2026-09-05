<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

$id = (int)$_GET['id'];

mysqli_query($conn, "
UPDATE tbl_tour_plans
SET
status='ABM Approved',
current_level='RBM',
abm_user_id='{$_SESSION['user_id']}',
abm_approved_at=NOW()
WHERE id='$id'
");

$_SESSION['success'] = "Tour Plan Approved Successfully.";

header("Location:tour-plan-list.php");
exit;
