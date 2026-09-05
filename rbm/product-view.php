<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query($conn, "

SELECT

p.*,

c.category_name,

d.division_name,

CONCAT(cb.first_name,' ',cb.last_name) AS created_by_name,

CONCAT(ub.first_name,' ',ub.last_name) AS updated_by_name

FROM tbl_products p

LEFT JOIN tbl_product_categories c
ON c.id = p.category_id

LEFT JOIN tbl_divisions d
ON d.id = p.division_id

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

    $_SESSION['error'] = "Product not found.";

    header("Location: product-list.php");

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

                        <h4 class="mb-0">

                            View Product

                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="product-list.php" class="btn btn-secondary btn-sm">

                            <i class="ri-arrow-left-line"></i>

                            Back

                        </a>

                    </div>

                </div>


                <div class="card shadow">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">

                            Product Details

                        </h5>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <tr>
                                <th width="30%">Product Code</th>
                                <td><?= $row['product_code']; ?></td>
                            </tr>

                            <tr>
                                <th>Product Name</th>
                                <td><?= $row['product_name']; ?></td>
                            </tr>

                            <tr>
                                <th>Brand Name</th>
                                <td><?= $row['brand_name']; ?></td>
                            </tr>

                            <tr>
                                <th>Category</th>
                                <td><?= $row['category_name']; ?></td>
                            </tr>

                            <tr>
                                <th>Division</th>
                                <td><?= $row['division_name']; ?></td>
                            </tr>

                            <tr>
                                <th>Dosage Form</th>
                                <td><?= !empty($row['dosage_form']) ? $row['dosage_form'] : "-"; ?></td>
                            </tr>

                            <tr>
                                <th>Strength</th>
                                <td><?= !empty($row['strength']) ? $row['strength'] : "-"; ?></td>
                            </tr>

                            <tr>
                                <th>Pack</th>
                                <td><?= !empty($row['pack']) ? $row['pack'] : "-"; ?></td>
                            </tr>

                            <tr>
                                <th>MRP</th>
                                <td>₹ <?= number_format($row['mrp'], 2); ?></td>
                            </tr>

                            <tr>
                                <th>PTR</th>
                                <td>₹ <?= number_format($row['ptr'], 2); ?></td>
                            </tr>

                            <tr>
                                <th>PTS</th>
                                <td>₹ <?= number_format($row['pts'], 2); ?></td>
                            </tr>

                            <tr>
                                <th>Stock Quantity</th>
                                <td><?= $row['stock_quantity']; ?></td>
                            </tr>

                            <tr>
                                <th>Remarks</th>
                                <td>

                                    <?= !empty($row['remarks']) ? nl2br($row['remarks']) : "-"; ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    <?php if ($row['status'] == 1) { ?>

                                        <span class="badge bg-success">

                                            Active

                                        </span>

                                    <?php } else { ?>

                                        <span class="badge bg-danger">

                                            Inactive

                                        </span>

                                    <?php } ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Created By</th>
                                <td>

                                    <?= !empty($row['created_by_name']) ? $row['created_by_name'] : "-"; ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Updated By</th>
                                <td>

                                    <?= !empty($row['updated_by_name']) ? $row['updated_by_name'] : "-"; ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Created On</th>
                                <td>

                                    <?= date("d M Y h:i A", strtotime($row['created_at'])); ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Last Updated</th>
                                <td>

                                    <?= !empty($row['updated_at']) ? date("d M Y h:i A", strtotime($row['updated_at'])) : "-"; ?>

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