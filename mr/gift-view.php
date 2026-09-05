<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query($conn, "

SELECT *

FROM tbl_gifts

WHERE id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Gift not found.";

    header("Location: gift-list.php");

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

                            View Gift

                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="gift-list.php" class="btn btn-secondary btn-sm">

                            <i class="ri-arrow-left-line"></i>

                            Back

                        </a>


                    </div>

                </div>


                <div class="card">

                    <div class="card-header">

                        <h5 class="mb-0">

                            Gift Details

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Gift Code

                                </label>

                                <p>

                                    <?php echo $row['gift_code']; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Gift Name

                                </label>

                                <p>

                                    <?php echo $row['gift_name']; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Brand Name

                                </label>

                                <p>

                                    <?php echo $row['brand_name']; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Gift Category

                                </label>

                                <p>

                                    <?php echo $row['gift_category']; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Gift Value

                                </label>

                                <p>

                                    <?php echo $row['gift_value']; ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Stock Quantity

                                </label>

                                <p>

                                    <?php echo $row['stock_quantity']; ?>

                                </p>

                            </div>


                            <div class="col-md-12 mb-3">

                                <label class="fw-bold">

                                    Remarks

                                </label>

                                <p>

                                    <?php

                                    echo !empty($row['remarks'])

                                        ? nl2br($row['remarks'])

                                        : "-";

                                    ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Status

                                </label>

                                <p>

                                    <?php

                                    if ($row['status'] == 1) {

                                    ?>

                                        <span class="badge bg-success">

                                            Active

                                        </span>

                                    <?php

                                    } else {

                                    ?>

                                        <span class="badge bg-danger">

                                            Inactive

                                        </span>

                                    <?php } ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Created At

                                </label>

                                <p>

                                    <?php

                                    echo date("d-m-Y h:i A", strtotime($row['created_at']));

                                    ?>

                                </p>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">

                                    Updated At

                                </label>

                                <p>

                                    <?php

                                    echo !empty($row['updated_at'])

                                        ? date("d-m-Y h:i A", strtotime($row['updated_at']))

                                        : "-";

                                    ?>

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php include('./includes/footer.php'); ?>

    </div>

</div>

<?php include('./includes/scripts.php'); ?>