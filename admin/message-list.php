<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin', 'superadmin']);

$sql = "
SELECT

m.*,

u.username,

(
SELECT COUNT(*)
FROM tbl_message_receivers r
WHERE r.message_id=m.id
) total_receiver,

(
SELECT COUNT(*)
FROM tbl_message_receivers r
WHERE r.message_id=m.id
AND r.is_read='1'
) total_read,

(
SELECT COUNT(*)
FROM tbl_message_receivers r
WHERE r.message_id=m.id
AND r.is_read='0'
) total_unread

FROM tbl_messages m

LEFT JOIN tbl_users u
ON u.id=m.sender_user_id

ORDER BY m.id DESC
";

$result = mysqli_query($conn, $sql);

?>

<?php include("includes/header.php"); ?>

<div id="layout-wrapper">

    <?php include("includes/navbar.php"); ?>
    <?php include("includes/sidebar.php"); ?>

    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title">
                                    All Messages
                                </h4>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover align-middle">

                                        <thead>

                                            <tr>

                                                <th width="60">#</th>

                                                <th>Subject</th>

                                                <th>Message</th>

                                                <th width="120">Sender</th>

                                                <th width="90">Receivers</th>

                                                <th width="90">Read</th>

                                                <th width="90">Unread</th>

                                                <th width="160">Created</th>

                                                <th width="90">Action</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php

                                            $i = 1;

                                            while ($row = mysqli_fetch_assoc($result)) {

                                            ?>

                                                <tr>

                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>

                                                    <td>
                                                        <strong><?php echo htmlspecialchars($row['subject']); ?></strong>
                                                    </td>

                                                    <td>

                                                        <?php

                                                        echo strlen($row['message']) > 70
                                                            ? substr(strip_tags($row['message']), 0, 70) . "..."
                                                            : htmlspecialchars($row['message']);

                                                        ?>

                                                    </td>

                                                    <td>

                                                        <?php echo $row['username']; ?>

                                                    </td>

                                                    <td>

                                                        <span class="badge bg-primary">

                                                            <?php echo $row['total_receiver']; ?>

                                                        </span>

                                                    </td>

                                                    <td>

                                                        <span class="badge bg-success">

                                                            <?php echo $row['total_read']; ?>

                                                        </span>

                                                    </td>

                                                    <td>

                                                        <span class="badge bg-danger">

                                                            <?php echo $row['total_unread']; ?>

                                                        </span>

                                                    </td>

                                                    <td>

                                                        <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>

                                                    </td>

                                                    <td>

                                                        <a
                                                            href="message-view.php?id=<?php echo $row['id']; ?>"
                                                            class="btn btn-sm btn-primary">

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

            </div>

        </div>

        <?php include("includes/footer.php"); ?>

    </div>

</div>

<?php include("includes/scripts.php"); ?>