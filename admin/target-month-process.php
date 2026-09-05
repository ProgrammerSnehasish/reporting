<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$month=$_POST['target_month'];
$year=$_POST['target_year'];
$title=mysqli_real_escape_string($conn,$_POST['title']);
$status=$_POST['status'];

$created_by=$_SESSION['user_id'];

$check=mysqli_query($conn,"
SELECT id
FROM tbl_target_master
WHERE
target_month='$month'
AND
target_year='$year'
");

if(mysqli_num_rows($check)>0){

$_SESSION['error']="Target month already exists.";

header("Location:target-month.php");
exit;

}

$sql="

INSERT INTO tbl_target_master(

target_month,
target_year,
title,
status,
created_by

)

VALUES(

'$month',
'$year',
'$title',
'$status',
'$created_by'

)

";

if(mysqli_query($conn,$sql)){

$_SESSION['success']="Target Month Added Successfully.";

}else{

$_SESSION['error']="Something went wrong.";

}

header("Location:target-month.php");