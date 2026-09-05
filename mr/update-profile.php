<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
header("Location: employee-profile.php");
exit;
}

$employee_id = intval($_POST['employee_id']);
$mobile = trim($_POST['mobile']);
$alternate_mobile = trim($_POST['alternate_mobile']);
$personal_email = trim($_POST['personal_email']);
$address = trim($_POST['address']);

/* Get Old Photo */

$sql = "SELECT photo FROM tbl_employees WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $employee_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

$photo = $row['photo'];

/* Upload New Photo */

if (!empty($_FILES['photo']['name'])) {

$extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

$allowed = array("jpg", "jpeg", "png", "webp");

if (in_array($extension, $allowed)) {

$new_photo = time() . "_" . rand(1000,9999) . "." . $extension;

$upload_path = "../uploads/employees/" . $new_photo;

if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {

if (!empty($photo) && file_exists("../uploads/employees/".$photo)) {
unlink("../uploads/profile/".$photo);
}

$photo = $new_photo;
}
}
}

/* Update Profile */

$sql = "UPDATE tbl_employees
SET

mobile=?,
alternate_mobile=?,
personal_email=?,
address=?,
photo=?,
updated_at=NOW()

WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
$stmt,
"sssssi",
$mobile,
$alternate_mobile,
$personal_email,
$address,
$photo,
$employee_id
);

if (mysqli_stmt_execute($stmt)) {

$_SESSION['success'] = "Profile Updated Successfully.";

} else {

$_SESSION['error'] = "Something went wrong.";

}

header("Location: employee-profile.php");

exit;