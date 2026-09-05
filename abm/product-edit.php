<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query($conn, "

SELECT

p.*

FROM tbl_products p

WHERE p.id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Product not found.";

    header("Location: product-list.php");

    exit;
}

$row = mysqli_fetch_assoc($result);

/*==========================
CATEGORY
==========================*/

$category = mysqli_query($conn, "

SELECT

id,
category_name

FROM tbl_product_categories

WHERE status=1

ORDER BY category_name

");

/*==========================
DIVISION
==========================*/

$division = mysqli_query($conn, "

SELECT

id,
division_name

FROM tbl_divisions

WHERE status=1

ORDER BY division_name

");


?>

<!-- ========== Header Start ========== -->
<?php include('./includes/header.php'); ?>
<!-- ========== Header End ========== -->

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ========== Topnavbar Start ========== -->
    <?php include('./includes/navbar.php'); ?>
    <!-- ========== Topnavbar End ========== -->

    <!-- ========== Left Sidebar Start ========== -->
    <?php include('./includes/sidebar.php'); ?>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">

            <div class="container-fluid">

                <!-- Page Title -->

                <div class="row mb-3">

                    <div class="col-md-6">

                        <h4 class="mb-0">
                            Edit Product Detail
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="product-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View

                        </a>


                    </div>

                </div>

                <div class="row">

                    <!-- LEFT -->



                    <!-- RIGHT -->

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Product Detail

                                </h4>

                            </div>

                            <div class="card-body">

                                <?php if (isset($_SESSION['success'])) { ?>

                                    <div class="alert alert-success alert-dismissible fade show">

                                        <?php
                                        echo $_SESSION['success'];
                                        unset($_SESSION['success']);
                                        ?>

                                        <button class="btn-close" data-bs-dismiss="alert"></button>

                                    </div>

                                <?php } ?>


                                <?php if (isset($_SESSION['error'])) { ?>

                                    <div class="alert alert-danger alert-dismissible fade show">

                                        <?php
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?>

                                        <button class="btn-close" data-bs-dismiss="alert"></button>

                                    </div>

                                <?php } ?>

                                <div class="row">

                                    <form action="product-update.php" method="POST">

                                        <input type="hidden"
                                            name="id"
                                            value="<?= $row['id']; ?>">

                                        <!-- Product Code -->
                                        <div class="mb-3">
                                            <label>
                                                Product Code
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= $row['product_code']; ?>"
                                                readonly>
                                        </div>

                                        <!-- Product Name -->
                                        <div class="mb-3">

                                            <label>

                                                Product Name

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="text"
                                                name="product_name"
                                                class="form-control"
                                                required
                                                value="<?= htmlspecialchars($row['product_name']); ?>">

                                        </div>

                                        <!-- Brand -->
                                        <div class="mb-3">

                                            <label>

                                                Brand Name

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input
                                                type="text"
                                                name="brand_name"
                                                class="form-control"
                                                required
                                                value="<?= htmlspecialchars($row['brand_name']); ?>">

                                        </div>

                                        <!-- Category -->
                                        <div class="mb-3">

                                            <label>

                                                Category

                                                <span class="text-danger">*</span>

                                            </label>

                                            <select
                                                name="category_id"
                                                class="form-select"
                                                required>

                                                <option value="">Select Category</option>

                                                <?php while ($cat = mysqli_fetch_assoc($category)) { ?>

                                                    <option
                                                        value="<?= $cat['id']; ?>"
                                                        <?= ($row['category_id'] == $cat['id']) ? 'selected' : ''; ?>>

                                                        <?= $cat['category_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Division -->
                                        <div class="mb-3">

                                            <label>

                                                Division

                                                <span class="text-danger">*</span>

                                            </label>

                                            <select
                                                name="division_id"
                                                class="form-select"
                                                required>

                                                <option value="">Select Division</option>

                                                <?php while ($div = mysqli_fetch_assoc($division)) { ?>

                                                    <option
                                                        value="<?= $div['id']; ?>"
                                                        <?= ($row['division_id'] == $div['id']) ? 'selected' : ''; ?>>

                                                        <?= $div['division_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Dosage Form -->
                                        <div class="mb-3">

                                            <label>

                                                Dosage Form

                                            </label>

                                            <input
                                                type="text"
                                                name="dosage_form"
                                                class="form-control"
                                                value="<?= htmlspecialchars($row['dosage_form']); ?>"
                                                placeholder="Tablet / Capsule / Syrup">

                                        </div>

                                        <!-- Strength -->
                                        <div class="mb-3">

                                            <label>

                                                Strength

                                            </label>

                                            <input
                                                type="text"
                                                name="strength"
                                                class="form-control"
                                                value="<?= htmlspecialchars($row['strength']); ?>">

                                        </div>

                                        <!-- Pack -->
                                        <div class="mb-3">

                                            <label>

                                                Pack

                                            </label>

                                            <input
                                                type="text"
                                                name="pack"
                                                class="form-control"
                                                value="<?= htmlspecialchars($row['pack']); ?>">

                                        </div>

                                        <!-- MRP -->
                                        <div class="mb-3">

                                            <label>

                                                MRP

                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="mrp"
                                                class="form-control"
                                                value="<?= $row['mrp']; ?>">

                                        </div>

                                        <!-- PTR -->
                                        <div class="mb-3">

                                            <label>

                                                PTR

                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="ptr"
                                                class="form-control"
                                                value="<?= $row['ptr']; ?>">

                                        </div>

                                        <!-- PTS -->
                                        <div class="mb-3">

                                            <label>

                                                PTS

                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="pts"
                                                class="form-control"
                                                value="<?= $row['pts']; ?>">

                                        </div>

                                        <!-- Stock -->
                                        <div class="mb-3">

                                            <label>

                                                Stock Quantity

                                            </label>

                                            <input
                                                type="number"
                                                name="stock_quantity"
                                                class="form-control"
                                                value="<?= $row['stock_quantity']; ?>">

                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">

                                            <label>

                                                Remarks

                                            </label>

                                            <textarea
                                                name="remarks"
                                                rows="4"
                                                class="form-control"><?= htmlspecialchars($row['remarks']); ?></textarea>

                                        </div>

                                        <!-- Status -->
                                        <div class="mb-3">

                                            <label>

                                                Status

                                            </label>

                                            <select
                                                name="status"
                                                class="form-select">

                                                <option
                                                    value="1"
                                                    <?= ($row['status'] == 1) ? 'selected' : ''; ?>>

                                                    Active

                                                </option>

                                                <option
                                                    value="0"
                                                    <?= ($row['status'] == 0) ? 'selected' : ''; ?>>

                                                    Inactive

                                                </option>

                                            </select>

                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-primary">

                                            <i class="ri-save-line"></i>

                                            Update Product

                                        </button>

                                        <a
                                            href="product-list.php"
                                            class="btn btn-secondary">

                                            Cancel

                                        </a>

                                    </form>




                                </div>



                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- End Page-content -->

    <?php include('./includes/footer.php'); ?>

</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<?php include('./includes/scripts.php'); ?>


<script>
    const fields = [
        'travel_expense',
        'da_expense',
        'hotel_expense',
        'food_expense',
        'other_expense'
    ];

    function calculateTotal() {

        let total = 0;

        fields.forEach(function(name) {

            let value = parseFloat(document.getElementsByName(name)[0].value) || 0;

            total += value;

        });

        document.getElementById('total_expense').value = total.toFixed(2);

    }

    fields.forEach(function(name) {

        document.getElementsByName(name)[0].addEventListener('input', calculateTotal);

    });

    calculateTotal();
</script>