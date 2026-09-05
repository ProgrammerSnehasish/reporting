<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "

SELECT

td.target_master_id,
td.mr_user_id,

tm.target_month,
tm.target_year,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

SUM(td.target_qty) total_qty,
SUM(td.target_value) total_value,

COUNT(td.product_id) total_products

FROM tbl_target_details td

INNER JOIN tbl_target_master tm
ON tm.id=td.target_master_id

INNER JOIN tbl_users u
ON u.id=td.mr_user_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

GROUP BY

td.target_master_id,
td.mr_user_id

ORDER BY

tm.target_year DESC,
tm.target_month DESC,
employee_name

";

$result = mysqli_query($conn, $sql);

?>

<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <div class="row">

                    <div class="col-12">

                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                            <h4 class="mb-sm-0">
                                Target Month List
                            </h4>

                            <div>

                                <a href="target-month.php"
                                    class="btn btn-primary">

                                    <i class="ri-add-line"></i>

                                    Add Target Month

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <?php if (isset($_SESSION['success'])) { ?>

                    <div class="alert alert-success alert-dismissible fade show">

                        <?= $_SESSION['success']; ?>

                        <?php unset($_SESSION['success']); ?>

                        <button class="btn-close" data-bs-dismiss="alert"></button>

                    </div>

                <?php } ?>

                <?php if (isset($_SESSION['error'])) { ?>

                    <div class="alert alert-danger alert-dismissible fade show">

                        <?= $_SESSION['error']; ?>

                        <?php unset($_SESSION['error']); ?>

                        <button class="btn-close" data-bs-dismiss="alert"></button>

                    </div>

                <?php } ?>

                <div class="row">

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                        <div class="col-lg-6 mb-4">

                            <div class="card shadow border-0">

                                <div class="card-header bg-primary text-white">

                                    <div class="d-flex justify-content-between">

                                        <div>

                                            <h5 class="mb-1">

                                                <?= $row['employee_name']; ?>

                                            </h5>

                                            <small>

                                                <?= $row['employee_code']; ?>

                                            </small>

                                        </div>

                                        <div class="text-end">

                                            <strong>

                                                <?= date("F", mktime(0, 0, 0, $row['target_month'], 1)); ?>

                                                <?= $row['target_year']; ?>

                                            </strong>

                                        </div>

                                    </div>

                                </div>

                                <div class="card-body">

                                    <?php

                                    $detail = mysqli_query($conn, "

SELECT

p.product_name,
p.product_code,

td.target_qty,
td.target_value

FROM tbl_target_details td

INNER JOIN tbl_products p
ON p.id=td.product_id

WHERE

td.target_master_id='" . $row['target_master_id'] . "'

AND td.mr_user_id='" . $row['mr_user_id'] . "'

ORDER BY

p.display_order,
p.product_name

");

                                    ?>

                                    <table class="table table-sm">

                                        <thead>

                                            <tr>

                                                <th>Product</th>

                                                <th class="text-end">Qty</th>

                                                <th class="text-end">Value</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php while ($pro = mysqli_fetch_assoc($detail)) { ?>

                                                <tr>

                                                    <td>

                                                        <strong>

                                                            <?= $pro['product_name']; ?>

                                                        </strong>

                                                        <br>

                                                        <small class="text-muted">

                                                            <?= $pro['product_code']; ?>

                                                        </small>

                                                    </td>

                                                    <td class="text-end">

                                                        <?= number_format($pro['target_qty']); ?>

                                                    </td>

                                                    <td class="text-end">

                                                        ₹ <?= number_format($pro['target_value'], 2); ?>

                                                    </td>

                                                </tr>

                                            <?php } ?>

                                        </tbody>

                                        <tfoot>

                                            <tr class="table-light">

                                                <th>Total</th>

                                                <th class="text-end">

                                                    <?= number_format($row['total_qty']); ?>

                                                </th>

                                                <th class="text-end">

                                                    ₹ <?= number_format($row['total_value'], 2); ?>

                                                </th>

                                            </tr>

                                        </tfoot>

                                    </table>

                                    <!-- <div class="text-end">

                                        <a href="target-edit.php?target_master_id=<?= $row['target_master_id']; ?>&mr_user_id=<?= $row['mr_user_id']; ?>"
                                            class="btn btn-primary btn-sm">

                                            <i class="ri-edit-line"></i>

                                            Edit

                                        </a>

                                        <a href="target-delete.php?target_master_id=<?= $row['target_master_id']; ?>&mr_user_id=<?= $row['mr_user_id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this target?')">

                                            <i class="ri-delete-bin-line"></i>

                                            Delete

                                        </a>

                                    </div> -->

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>




            </div>

        </div>

        <?php include('./includes/footer.php'); ?>

    </div>

</div>

<?php include('./includes/scripts.php'); ?>