<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: employees-list.php");
    exit;
}

$id = (int)$_POST['id'];

/*==========================
GET DATA
==========================*/

$designation_id = (int)$_POST['designation_id'];
$reporting_to   = !empty($_POST['reporting_to']) ? (int)$_POST['reporting_to'] : "NULL";

$first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
$last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
$gender     = mysqli_real_escape_string($conn, trim($_POST['gender']));

$dob = !empty($_POST['dob']) ? $_POST['dob'] : NULL;
$doa = !empty($_POST['doa']) ? $_POST['doa'] : NULL;
$mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
$alternate_mobile = mysqli_real_escape_string($conn, trim($_POST['alternate_mobile']));

$personal_email = mysqli_real_escape_string($conn, trim($_POST['personal_email']));
$official_email = mysqli_real_escape_string($conn, trim($_POST['official_email']));

$joining_date = !empty($_POST['joining_date']) ? $_POST['joining_date'] : NULL;
$date_of_leaving = !empty($_POST['date_of_leaving']) ? $_POST['date_of_leaving'] : NULL;

$employee_type = mysqli_real_escape_string($conn, trim($_POST['employee_type']));

$zone = mysqli_real_escape_string($conn, trim($_POST['zone']));
$region = mysqli_real_escape_string($conn, trim($_POST['region']));

$hq_id = !empty($_POST['hq_id']) ? (int)$_POST['hq_id'] : NULL;
$area_id = !empty($_POST['area_id']) ? (int)$_POST['area_id'] : NULL;

$address = mysqli_real_escape_string($conn, trim($_POST['address']));
$remarks = mysqli_real_escape_string($conn, trim($_POST['remarks']));

$status = (int)$_POST['status'];

/*==========================
KYC
==========================*/

$aadhaar_no = mysqli_real_escape_string($conn, trim($_POST['aadhaar_no']));
$pan_no     = mysqli_real_escape_string($conn, trim($_POST['pan_no']));

/*==========================
BANK
==========================*/

$bank_name = mysqli_real_escape_string($conn, trim($_POST['bank_name']));
$branch_name = mysqli_real_escape_string($conn, trim($_POST['branch_name']));
$account_holder_name = mysqli_real_escape_string($conn, trim($_POST['account_holder_name']));
$account_no = mysqli_real_escape_string($conn, trim($_POST['account_no']));
$ifsc_code = mysqli_real_escape_string($conn, trim($_POST['ifsc_code']));
$upi_id = mysqli_real_escape_string($conn, trim($_POST['upi_id']));
$uan_no = mysqli_real_escape_string($conn, trim($_POST['uan_no']));

$updated_by = $_SESSION['user_id'];

/*==========================
OLD PHOTO
==========================*/

$get = mysqli_query($conn,"
SELECT photo
FROM tbl_employees
WHERE id='$id'
LIMIT 1
");

if(mysqli_num_rows($get)==0){

    $_SESSION['error']="Employee not found.";

    header("Location: employees-list.php");
    exit;
}

$old = mysqli_fetch_assoc($get);

$photo = $old['photo'];

/*==========================
PHOTO UPLOAD
==========================*/

if(!empty($_FILES['photo']['name'])){

    $ext = strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));

    $allow = ['jpg','jpeg','png','webp'];

    if(!in_array($ext,$allow)){

        $_SESSION['error']="Only JPG, JPEG, PNG & WEBP allowed.";

        header("Location: employee-edit.php?id=".$id);
        exit;
    }

    $photo = time().rand(1000,9999).".".$ext;

    move_uploaded_file(

        $_FILES['photo']['tmp_name'],

        "../uploads/employees/".$photo

    );

}

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

        AND id<>'$id'

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

        AND id<>'$id'

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

        AND id<>'$id'

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

        AND id<>'$id'

        LIMIT 1

    ");

        if (mysqli_num_rows($check) > 0) {

            throw new Exception("PAN Number already exists.");
        }
    }

    /*==================================
    UPDATE
    ==================================*/

    $sql = "

    UPDATE tbl_employees SET

    designation_id='$designation_id',

    reporting_to=" . ($reporting_to == "NULL" ? "NULL" : "'$reporting_to'") . ",

    first_name='$first_name',

    last_name='$last_name',

    gender='$gender',

    dob=" . ($dob ? "'$dob'" : "NULL") . ",
    doa=" . ($doa ? "'$doa'" : "NULL") . ",

    mobile='$mobile',

    alternate_mobile='$alternate_mobile',

    aadhaar_no='$aadhaar_no',

    pan_no='$pan_no',

    personal_email='$personal_email',

    official_email='$official_email',

    photo='$photo',

    joining_date=" . ($joining_date ? "'$joining_date'" : "NULL") . ",

    date_of_leaving=" . ($date_of_leaving ? "'$date_of_leaving'" : "NULL") . ",

    employee_type='$employee_type',

    zone='$zone',

    region='$region',

    hq_id=" . ($hq_id ? "'$hq_id'" : "NULL") . ",

    area_id=" . ($area_id ? "'$area_id'" : "NULL") . ",

    address='$address',

    bank_name='$bank_name',

    branch_name='$branch_name',

    account_holder_name='$account_holder_name',

    account_no='$account_no',

    ifsc_code='$ifsc_code',

    upi_id='$upi_id',

    uan_no='$uan_no',

    remarks='$remarks',

    status='$status',

    updated_by='$updated_by',

    updated_at=NOW()

    WHERE id='$id'

    ";

    mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {

        throw new Exception(mysqli_error($conn));
    }

    /*==================================
    COMMIT
    ==================================*/

    mysqli_commit($conn);

    /*==================================
    DELETE OLD PHOTO
    ==================================*/

    if (!empty($_FILES['photo']['name'])) {

        if (!empty($old['photo'])) {

            $old_path = "../uploads/employees/" . $old['photo'];

            if (file_exists($old_path)) {

                unlink($old_path);
            }
        }
    }

    $_SESSION['success'] = "Employee updated successfully.";

    header("Location: employee-edit.php?id=" . $id);
    exit;
  } catch (Exception $e) {

    mysqli_rollback($conn);

    /*==========================
    DELETE NEW PHOTO
    ==========================*/

    if (!empty($_FILES['photo']['name'])) {

        $new_path = "../uploads/employees/" . $photo;

        if (file_exists($new_path)) {

            unlink($new_path);
        }
    }

    $_SESSION['error'] = $e->getMessage();

    header("Location: employee-edit.php?id=" . $id);
    exit;
}