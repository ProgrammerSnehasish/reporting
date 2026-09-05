<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$id=(int)$_GET['id'];

mysqli_query($conn,"
UPDATE tbl_dcr
SET
status='RBM Approved',
current_level='ZSM',
rbm_user_id='{$_SESSION['user_id']}',
rbm_approved_at=NOW()
WHERE
id='$id'
AND current_level='RBM'
");

$_SESSION['success']="DCR Approved Successfully.";

header("Location:dcr-list.php");
exit;