<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin', 'superadmin']);

$id = (int)$_GET['id'];

$message = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT

m.*,
u.username

FROM tbl_messages m

LEFT JOIN tbl_users u
ON u.id=m.sender_user_id

WHERE m.id='$id'

LIMIT 1
"));

if (!$message) {
    $_SESSION['error'] = "Message not found.";
    header("Location:message-list.php");
    exit;
}

$total = mysqli_num_rows(mysqli_query($conn, "
SELECT id
FROM tbl_message_receivers
WHERE message_id='$id'
"));

$read = mysqli_num_rows(mysqli_query($conn, "
SELECT id
FROM tbl_message_receivers
WHERE message_id='$id'
AND is_read='1'
"));

$unread = $total - $read;

$receivers = mysqli_query($conn, "
SELECT

mr.*,

u.username,

e.employee_code,

CONCAT(e.first_name,' ',e.last_name) employee_name

FROM tbl_message_receivers mr

INNER JOIN tbl_users u
ON u.id=mr.receiver_user_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

WHERE mr.message_id='$id'

ORDER BY e.first_name
");

?>

<?php include("includes/header.php"); ?>

<div id="layout-wrapper">

    <?php include("includes/navbar.php"); ?>
    <?php include("includes/sidebar.php"); ?>

    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-12">

                        <div class="card">

                            <div class="card-header">

                                <h4>

                                    Message Details

                                </h4>

                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>

                                        <th width="180">

                                            Subject

                                        </th>

                                        <td>

                                            <?php echo $message['subject']; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>

                                            Sender

                                        </th>

                                        <td>

                                            <?php echo $message['username']; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>

                                            Created

                                        </th>

                                        <td>

                                            <?php echo date("d M Y h:i A", strtotime($message['created_at'])); ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>

                                            Message

                                        </th>

                                        <td>

                                            <?php echo nl2br($message['message']); ?>

                                        </td>

                                    </tr>

                                    <?php if ($message['attachment'] != "") { ?>

                                        <tr>

                                            <th>

                                                Attachment

                                            </th>

                                            <td>

                                                <a
                                                    href="../uploads/messages/<?php echo $message['attachment']; ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-primary">

                                                    Download

                                                </a>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-4">

                        <div class="card bg-primary text-white">

                            <div class="card-body">

                                <h5>Total Receiver</h5>

                                <h2><?php echo $total; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card bg-success text-white">

                            <div class="card-body">

                                <h5>Read</h5>

                                <h2><?php echo $read; ?></h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card bg-danger text-white">

                            <div class="card-body">

                                <h5>Unread</h5>

                                <h2><?php echo $unread; ?></h2>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="card">

                    <div class="card-header">

                        <h4>

                            Receiver List

                        </h4>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Employee</th>

                                        <th>Code</th>

                                        <th>Username</th>

                                        <th>Status</th>

                                        <th>Read Time</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $i = 1;

                                    while ($row = mysqli_fetch_assoc($receivers)) {

                                    ?>

                                        <tr>

                                            <td>

                                                <?php echo $i++; ?>

                                            </td>

                                            <td>

                                                <?php echo $row['employee_name']; ?>

                                            </td>

                                            <td>

                                                <?php echo $row['employee_code']; ?>

                                            </td>

                                            <td>

                                                <?php echo $row['username']; ?>

                                            </td>

                                            <td>

                                                <?php

                                                if ($row['is_read'] == 1) {

                                                    echo '<span class="badge bg-success">Read</span>';
                                                } else {

                                                    echo '<span class="badge bg-danger">Unread</span>';
                                                }

                                                ?>

                                            </td>

                                            <td>

                                                <?php

                                                if ($row['read_at'] != "0000-00-00 00:00:00" && !empty($row['read_at'])) {

                                                    echo date("d M Y h:i A", strtotime($row['read_at']));
                                                } else {

                                                    echo "-";
                                                }

                                                ?>

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

    </div>

    <?php include("includes/footer.php"); ?>

</div>

</div>

<?php include("includes/scripts.php"); ?>