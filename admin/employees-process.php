<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: employees-add.php");
    exit;
}

$designation_id = (int)$_POST['designation_id'];
$reporting_to = !empty($_POST['reporting_to']) ? (int)$_POST['reporting_to'] : NULL;
$first_name     = mysqli_real_escape_string($conn, trim($_POST['first_name']));
$last_name      = mysqli_real_escape_string($conn, trim($_POST['last_name']));
$gender         = mysqli_real_escape_string($conn, trim($_POST['gender']));
$dob            = !empty($_POST['dob']) ? $_POST['dob'] : NULL;
$doa            = !empty($_POST['doa']) ? $_POST['doa'] : NULL;
$mobile         = mysqli_real_escape_string($conn, trim($_POST['mobile']));
$alternate_mobile = mysqli_real_escape_string($conn, trim($_POST['alternate_mobile']));
$personal_email = mysqli_real_escape_string($conn, trim($_POST['personal_email']));
$official_email = mysqli_real_escape_string($conn, trim($_POST['official_email']));
$joining_date   = !empty($_POST['joining_date']) ? $_POST['joining_date'] : NULL;
$date_of_leaving = !empty($_POST['date_of_leaving']) ? $_POST['date_of_leaving'] : NULL;
$employee_type  = mysqli_real_escape_string($conn, trim($_POST['employee_type']));
$zone           = mysqli_real_escape_string($conn, trim($_POST['zone']));
$region         = mysqli_real_escape_string($conn, trim($_POST['region']));
$hq_id = !empty($_POST['hq_id']) ? (int)$_POST['hq_id'] : NULL;
$area_id = !empty($_POST['area_id']) ? (int)$_POST['area_id'] : NULL;
$address        = mysqli_real_escape_string($conn, trim($_POST['address']));
$remarks        = mysqli_real_escape_string($conn, trim($_POST['remarks']));
$status         = (int)$_POST['status'];
// KYC
$aadhaar_no     = mysqli_real_escape_string($conn, trim($_POST['aadhaar_no']));
$pan_no         = mysqli_real_escape_string($conn, trim($_POST['pan_no']));
// Bank
$bank_name      = mysqli_real_escape_string($conn, trim($_POST['bank_name']));
$branch_name    = mysqli_real_escape_string($conn, trim($_POST['branch_name']));
$account_holder_name = mysqli_real_escape_string($conn, trim($_POST['account_holder_name']));
$account_no     = mysqli_real_escape_string($conn, trim($_POST['account_no']));
$ifsc_code      = mysqli_real_escape_string($conn, trim($_POST['ifsc_code']));
$upi_id         = mysqli_real_escape_string($conn, trim($_POST['upi_id']));
$pf_no          = !empty($_POST['pf_no']) ? $_POST['pf_no'] : NULL;
$uan_no         = mysqli_real_escape_string($conn, trim($_POST['uan_no']));
$created_by = $_SESSION['user_id'];



/*==========================
PHOTO UPLOAD
==========================*/

$photo = "";

if (!empty($_FILES['photo']['name'])) {

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    $allow = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allow)) {

        $_SESSION['error'] = "Only JPG, JPEG, PNG & WEBP allowed.";

        header("Location: employees-add.php");
        exit;
    }

    $photo = time() . rand(1000,9999) . "." . $ext;

    if (!move_uploaded_file(
        $_FILES['photo']['tmp_name'],
        "../uploads/employees/" . $photo
    )) {
        throw new Exception("Photo upload failed.");
    }

}



/*==========================
EMPLOYEE CODE
==========================*/

$get = mysqli_query($conn,"
SELECT MAX(id) last_id
FROM tbl_employees
");

$row = mysqli_fetch_assoc($get);

$next = $row['last_id'] + 1;

if($next==0){

    $next=1;

}

$employee_code="EMP".str_pad($next,5,"0",STR_PAD_LEFT);



mysqli_begin_transaction($conn);

try{

    /*==================================
DUPLICATE MOBILE
==================================*/

    if (!empty($mobile)) {

        $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_employees
    WHERE mobile='$mobile'
    LIMIT 1
    ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("Mobile Number already exists.");
        }
    }


    /*==================================
DUPLICATE OFFICIAL EMAIL
==================================*/

    if (!empty($official_email)) {

        $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_employees
    WHERE official_email='$official_email'
    LIMIT 1
    ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("Official Email already exists.");
        }
    }


    /*==================================
DUPLICATE AADHAAR
==================================*/

    if (!empty($aadhaar_no)) {

        $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_employees
    WHERE aadhaar_no='$aadhaar_no'
    LIMIT 1
    ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("Aadhaar Number already exists.");
        }
    }


    /*==================================
DUPLICATE PAN
==================================*/

    if (!empty($pan_no)) {

        $check = mysqli_query($conn, "
    SELECT id
    FROM tbl_employees
    WHERE pan_no='$pan_no'
    LIMIT 1
    ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("PAN Number already exists.");
        }
    }



    /*==================================
INSERT
==================================*/

    $sql = "

INSERT INTO tbl_employees(
employee_code,
designation_id,
reporting_to,
first_name,
last_name,
gender,
dob,
doa,
mobile,
alternate_mobile,
aadhaar_no,
pan_no,
personal_email,
official_email,
photo,
joining_date,
date_of_leaving,
employee_type,
zone,
region,
area_id,
hq_id,
address,
bank_name,
branch_name,
account_holder_name,
account_no,
ifsc_code,
upi_id,
uan_no,
remarks,
status,
created_by,
created_at

)

VALUES(

'$employee_code',
'$designation_id',
" . ($reporting_to == "NULL" ? "NULL" : "'$reporting_to'") . ",
'$first_name',
'$last_name',
'$gender',
" . ($dob ? "'$dob'" : "NULL") . ",
" . ($doa ? "'$doa'" : "NULL") . ",
'$mobile',
'$alternate_mobile',
'$aadhaar_no',
'$pan_no',
'$personal_email',
'$official_email',
'$photo',
" . ($joining_date ? "'$joining_date'" : "NULL") . ",
" . ($date_of_leaving ? "'$date_of_leaving'" : "NULL") . ",
'$employee_type',
'$zone',
'$region',
" . ($area_id ? "'$area_id'" : "NULL") . ",
" . ($hq_id ? "'$hq_id'" : "NULL") . ",
'$address',
'$bank_name',
'$branch_name',
'$account_holder_name',
'$account_no',
'$ifsc_code',
'$upi_id',
'$uan_no',
'$remarks',
'$status',
'$created_by',
NOW()

)

";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    /*==================================
COMMIT
==================================*/

    mysqli_commit($conn);

    $_SESSION['success'] = "Employee added successfully.";

    header("Location: employees-add.php");
    exit;
}

/*==================================
CATCH
==================================*/ catch (Exception $e) {

    mysqli_rollback($conn);

    // Delete uploaded photo if insert failed

    if (!empty($photo)) {

        $path = "../uploads/employees/" . $photo;

        if (file_exists($path)) {

            unlink($path);
        }
    }

    $_SESSION['error'] = $e->getMessage();

    header("Location: employees-add.php");
    exit;
}