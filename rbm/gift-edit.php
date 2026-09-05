<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

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
                            Edit Gift Detail
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="gift-list.php" class="btn btn-primary btn-sm">

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

                                    Gift Details

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

                                    <form action="gift-update.php" method="POST">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo $row['id']; ?>">

                                        <!-- Product Code -->

                                        <div class="mb-3">

                                            <label>

                                                Gift Code

                                            </label>

                                            <input

                                                type="text"

                                                class="form-control"

                                                value="<?php echo $row['gift_code']; ?>"

                                                readonly>

                                        </div>


                                        <!-- Product Name -->

                                        <div class="mb-3">

                                            <label>

                                                Gift Name

                                                <span class="text-danger">*</span>

                                            </label>

                                            <input

                                                type="text"

                                                name="gift_name"

                                                class="form-control"

                                                required

                                                value="<?php echo htmlspecialchars($row['gift_name']); ?>">

                                        </div>

                                        <!-- Product Name -->

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

                                                value="<?php echo htmlspecialchars($row['brand_name']); ?>">

                                        </div>


                                        <!-- Division -->

                                        <!-- Gift Category -->

                                        <div class="mb-3">
                                            <label>
                                                Gift Category
                                            </label>

                                            <select
                                                name="gift_category"
                                                class="form-select">

                                                <option value="">Select Category</option>

                                                <option value="Doctor"
                                                    <?php if ($row['gift_category'] == "Doctor") echo "selected"; ?>>
                                                    Doctor
                                                </option>

                                                <option value="Chemist"
                                                    <?php if ($row['gift_category'] == "Chemist") echo "selected"; ?>>
                                                    Chemist
                                                </option>

                                                <option value="Stockist"
                                                    <?php if ($row['gift_category'] == "Stockist") echo "selected"; ?>>
                                                    Stockist
                                                </option>

                                                <option value="General"
                                                    <?php if ($row['gift_category'] == "General") echo "selected"; ?>>
                                                    General
                                                </option>

                                            </select>
                                        </div>



                                        <!-- Unit -->
                                        <div class="mb-3">
                                            <label>
                                                Gift Value
                                            </label>

                                            <input
                                                type="text"
                                                name="gift_value"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['gift_value']); ?>">
                                        </div>


                                        <!-- Strength -->


                                        <!-- MRP -->

                                        <div class="mb-3">

                                            <label>

                                                Stock Quantity

                                            </label>

                                            <input
                                                type="number"
                                                step="1"
                                                min="0"
                                                name="qty"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['stock_quantity']); ?>">

                                        </div>


                                        <!-- Remarks -->

                                        <div class="mb-3">

                                            <label>

                                                Remarks

                                            </label>

                                            <textarea

                                                name="remarks"

                                                rows="4"

                                                class="form-control"><?php echo htmlspecialchars($row['remarks']); ?></textarea>

                                        </div>


                                        <!-- Status -->

                                        <div class="mb-3">

                                            <label>

                                                Status

                                            </label>

                                            <select

                                                name="status"

                                                class="form-select">

                                                <option value="1"

                                                    <?php if ($row['status'] == 1) echo "selected"; ?>>

                                                    Active

                                                </option>

                                                <option value="0"

                                                    <?php if ($row['status'] == 0) echo "selected"; ?>>

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

                                            href="gift-list.php"

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