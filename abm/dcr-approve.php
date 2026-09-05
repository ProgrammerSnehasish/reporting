<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid DCR.";
    header("Location:dcr-list.php");
    exit;
}

$id = (int)$_GET['id'];
$abm_id = $_SESSION['user_id'];

/*
Check:
1. DCR exists
2. It belongs to an MR mapped under this ABM
3. Current level is ABM
*/

$sql = "
SELECT d.id

FROM tbl_dcr d

INNER JOIN tbl_user_mapping um
ON um.employee_user_id=d.mr_id

WHERE

d.id='$id'
AND um.manager_user_id='$abm_id'
AND d.current_level='ABM'

LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "You are not authorized to approve this DCR.";

    header("Location:dcr-list.php");
    exit;
}

mysqli_query($conn, "
UPDATE tbl_dcr
SET

status='ABM Approved',
current_level='RBM',

abm_user_id='$abm_id',
abm_approved_at=NOW(),

updated_by='$abm_id',
updated_at=NOW()

WHERE id='$id'
");

if (mysqli_error($conn)) {

    $_SESSION['error'] = mysqli_error($conn);
} else {

    $_SESSION['success'] = "DCR Approved Successfully.";
}

header("Location:dcr-list.php");
exit;
