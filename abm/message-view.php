<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid Message.";
    header("Location: message-list.php");
    exit;
}

$message_id = (int)$_GET['id'];


//=======================
// Mark as Read
//=======================

mysqli_query($conn,"
UPDATE tbl_message_receivers
SET
is_read='1',
read_at=NOW()
WHERE
message_id='$message_id'
AND receiver_user_id='$user_id'
");


//=======================
// Fetch Message
//=======================

$sql = "
SELECT

m.*,
mr.read_at

FROM tbl_messages m

INNER JOIN tbl_message_receivers mr
ON mr.message_id = m.id

WHERE

m.id='$message_id'
AND mr.receiver_user_id='$user_id'

LIMIT 1
";

$result = mysqli_query($conn,$sql);

$row = mysqli_fetch_assoc($result);

if(!$row)
{
    $_SESSION['error']="Message not found.";
    header("Location:message-list.php");
    exit;
}

?>

<?php include('includes/header.php'); ?>

<div id="layout-wrapper">

<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="main-content">

<div class="page-content">

<div class="container-fluid">

<div class="row">

<div class="col-md-12">

<div class="card">

<div class="card-header d-flex justify-content-between">

<h4 class="mb-0">

Message Details

</h4>

<a href="message-list.php" class="btn btn-secondary btn-sm">

<i class="ri-arrow-left-line"></i>

Back

</a>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th width="180">

Subject

</th>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

</tr>

<tr>

<th>

Message

</th>

<td>

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</td>

</tr>

<?php if(!empty($row['attachment'])){ ?>

<tr>

<th>

Attachment

</th>

<td>

<a
href="../uploads/messages/<?php echo $row['attachment'];?>"
target="_blank"
class="btn btn-primary btn-sm">

<i class="ri-download-line"></i>

Download Attachment

</a>

</td>

</tr>

<?php } ?>

<tr>

<th>

Sent On

</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

</tr>

<tr>

<th>

Read Time

</th>

<td>

<?php

if(!empty($row['read_at']))
{
    echo date("d M Y h:i A",strtotime($row['read_at']));
}
else
{
    echo "-";
}

?>

</td>

</tr>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

<?php include('includes/footer.php'); ?>

</div>

</div>

<?php include('includes/scripts.php'); ?>