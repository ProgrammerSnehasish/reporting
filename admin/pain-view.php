<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query($conn, "

SELECT

p.*,

CONCAT(cb.first_name,' ',cb.last_name) AS created_by_name,

CONCAT(ub.first_name,' ',ub.last_name) AS updated_by_name

FROM tbl_pain_management_products p

LEFT JOIN tbl_users cu
ON cu.id = p.created_by

LEFT JOIN tbl_employees cb
ON cb.id = cu.employee_id

LEFT JOIN tbl_users uu
ON uu.id = p.updated_by

LEFT JOIN tbl_employees ub
ON ub.id = uu.employee_id

WHERE p.id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Pain Product not found.";

    header("Location: pain-list.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

?>

<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <div class="row mb-3">

                    <div class="col-md-6">
                        <h4 class="mb-0">View Pain Product</h4>
                    </div>

                    <div class="col-md-6 text-end">

                        <a href="pain-list.php" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line"></i>
                            Back
                        </a>

                        <a href="pain-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="ri-edit-line"></i>
                            Edit
                        </a>

                    </div>

                </div>

                <div class="card shadow">

                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Pain Product Details</h5>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <tr>
                                <th width="30%">Product Code</th>
                                <td><?php echo $row['product_code']; ?></td>
                            </tr>

                            <tr>
                                <th>Pain Product Name</th>
                                <td><?php echo $row['pain_product_name']; ?></td>
                            </tr>

                            <tr>
                                <th>Description</th>
                                <td>
                                    <?php
                                    echo !empty($row['description'])
                                        ? nl2br(htmlspecialchars($row['description']))
                                        : "-";
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Remarks</th>
                                <td>
                                    <?php
                                    echo !empty($row['remarks'])
                                        ? nl2br(htmlspecialchars($row['remarks']))
                                        : "-";
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    <?php if ($row['status'] == 1) { ?>

                                        <span class="badge bg-success">Active</span>

                                    <?php } else { ?>

                                        <span class="badge bg-danger">Inactive</span>

                                    <?php } ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Created By</th>
                                <td>
                                    <?php echo !empty($row['created_by_name']) ? $row['created_by_name'] : "-"; ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Updated By</th>
                                <td>
                                    <?php echo !empty($row['updated_by_name']) ? $row['updated_by_name'] : "-"; ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Created On</th>
                                <td>
                                    <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Last Updated</th>
                                <td>
                                    <?php
                                    echo !empty($row['updated_at'])
                                        ? date("d M Y h:i A", strtotime($row['updated_at']))
                                        : "-";
                                    ?>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <?php include('./includes/footer.php'); ?>

    </div>

</div>

<?php include('./includes/scripts.php'); ?>