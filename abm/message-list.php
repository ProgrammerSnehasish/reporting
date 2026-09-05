<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['abm']);

$user_id = $_SESSION['user_id'];

$sql = "
SELECT

mr.id,
mr.message_id,
mr.is_read,
mr.read_at,

m.subject,
m.message,
m.attachment,
m.created_at

FROM tbl_message_receivers mr

INNER JOIN tbl_messages m
ON m.id = mr.message_id

WHERE mr.receiver_user_id = '$user_id'

ORDER BY m.created_at DESC
";

$result = mysqli_query($conn,$sql);

?>

<?php include('includes/header.php'); ?>

<div id="layout-wrapper">

<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="main-content">

<div class="page-content">

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between">

<h4 class="mb-0">
Inbox
</h4>

<?php

$unread=mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM tbl_message_receivers
WHERE receiver_user_id='$user_id'
AND is_read='0'
"));

?>

<span class="badge bg-danger">
Unread :
<?php echo $unread; ?>
</span>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Subject</th>

<th>Message</th>

<th>Date</th>

<th>Status</th>

<th width="80">Action</th>

</tr>

</thead>

<tbody>

<?php

$i=1;

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>
<?php echo $i++; ?>
</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

<td>

<?php

echo strlen(strip_tags($row['message']))>80

? substr(strip_tags($row['message']),0,80)." ..."

: htmlspecialchars($row['message']);

?>

</td>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

<td>

<?php

if($row['is_read']==1)
{
    echo '<span class="badge bg-success">Read</span>';
}
else
{
    echo '<span class="badge bg-danger">Unread</span>';
}

?>

</td>

<td>

<a
href="message-view.php?id=<?php echo $row['message_id'];?>"
class="btn btn-primary btn-sm">

<i class="ri-eye-line"></i>

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include('includes/footer.php'); ?>

</div>

</div>

<?php include('includes/scripts.php'); ?>