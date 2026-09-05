<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query($conn, "

SELECT

p.*

FROM tbl_pain_management_products p

WHERE p.id='$id'

LIMIT 1

");

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Product not found.";

    header("Location: pain-list.php");

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
                            Edit Pain Detail
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="pain-list.php" class="btn btn-primary btn-sm">

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

                                    Pain Detail

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

                                    <form action="pain-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <div class="row">

                                            <!-- Product Code -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Pain Product Code
                                                </label>

                                                <input type="text"
                                                    class="form-control"
                                                    value="<?php echo $row['product_code']; ?>"
                                                    readonly>
                                            </div>

                                            <!-- Product Name -->
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">
                                                    Pain Product Name <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="pain_product_name"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row['pain_product_name']); ?>"
                                                    required>
                                            </div>

                                            <!-- Description -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">
                                                    Description
                                                </label>

                                                <textarea
                                                    name="description"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Enter Description"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                            </div>

                                            <!-- Remarks -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Remarks
                                                </label>

                                                <input type="text"
                                                    name="remarks"
                                                    class="form-control"
                                                    placeholder="Remarks" value="<?php echo htmlspecialchars($row['remarks']); ?>">
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Status
                                                </label>

                                                <select name="status" class="form-select">

                                                    <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>
                                                        Active
                                                    </option>

                                                    <option value="0" <?php echo ($row['status'] == 0) ? 'selected' : ''; ?>>
                                                        Inactive
                                                    </option>

                                                </select>
                                            </div>

                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line"></i>
                                            Update Pain Product
                                        </button>

                                        <a href="pain-list.php" class="btn btn-secondary">
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