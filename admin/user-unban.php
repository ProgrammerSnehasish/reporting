<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location:user-list.php");
    exit;
}

$id = (int)$_GET['id'];

mysqli_query($conn, "

UPDATE tbl_users

SET

status=1,
updated_at=NOW()

WHERE id='$id'

");

$_SESSION['success'] = "User has been activated.";

header("Location:user-list.php");
exit;
