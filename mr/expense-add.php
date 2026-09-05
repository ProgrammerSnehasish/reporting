<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Id.";

    header("Location: dcr-list.php");
    exit;
}

$dcr_id = (int)$_GET['id'];

$sql = mysqli_query($conn, "
SELECT
    d.id,
    d.dcr_no,
    d.visit_date,
    d.area_id,
    a.area_name
FROM tbl_dcr d
INNER JOIN tbl_areas a
ON a.id=d.area_id
WHERE d.id='$dcr_id'
LIMIT 1
");

$dcr = mysqli_fetch_assoc($sql);

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
                            Add Expense
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="expense-list.php" class="btn btn-primary btn-sm">

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

                                    Add Expense

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

                                    <form action="expense-process.php" method="POST">

                                        <input type="hidden" name="dcr_id" value="<?= $dcr['id']; ?>">
                                    <input type="hidden" name="area_id" value="<?= $dcr['area_id']; ?>">

                                        <div class="mb-3">
                                            <label class="form-label">DCR No.</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= $dcr['dcr_no']; ?>"
                                                readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Visit Date</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= date('d-m-Y', strtotime($dcr['visit_date'])); ?>"
                                                readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Area</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= $dcr['area_name']; ?>"
                                                readonly>
                                        </div>



                                        <!-- Travel Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Travel Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="travel_expense"
                                                value="0"
                                                class="form-control">

                                        </div>

                                        <!-- DA Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                DA Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="da_expense"
                                                value="0"
                                                class="form-control">

                                        </div>

                                        <!-- Hotel Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Hotel Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="hotel_expense"
                                                value="0"
                                                class="form-control">

                                        </div>

                                        <!-- Food Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Food Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="food_expense"
                                                value="0"
                                                class="form-control">

                                        </div>

                                        <!-- Other Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Other Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="other_expense"
                                                value="0"
                                                class="form-control">

                                        </div>

                                        <!-- Total Expense -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Total Expense
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="total_expense"
                                                id="total_expense"
                                                class="form-control"
                                                readonly>

                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Remarks
                                            </label>

                                            <textarea
                                                name="remarks"
                                                rows="4"
                                                class="form-control"></textarea>

                                        </div>

                                        <button class="btn btn-primary">

                                            <i class="ri-save-line"></i>

                                            Save & Exit

                                        </button>

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