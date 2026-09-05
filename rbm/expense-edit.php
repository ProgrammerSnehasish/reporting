<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Expense.";

    header("Location: expense-list.php");
    exit;
}

$id = (int)$_GET['id'];


$sql = "SELECT

e.*,

d.dcr_no,
d.visit_date,

a.area_name

FROM tbl_dcr_expenses e

INNER JOIN tbl_dcr d
ON d.id = e.dcr_id

INNER JOIN tbl_areas a
ON a.id = e.area_id

WHERE e.id=?
AND e.mr_id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $_SESSION['user_id']
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Expense not found.";

    header("Location: expense-list.php");

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
                            Edit Expense Details
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

                                    Expense Details

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

                                    <form action="expense-update.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <!-- DCR -->
                                        <div class="mb-3">
                                            <label>DCR <span class="text-danger">*</span></label>

                                            <select name="dcr_id" class="form-select" required>

                                                <option value="">-- Select DCR --</option>

                                                <?php

                                                $dcr = mysqli_query($conn, "
                                                SELECT
                                                    d.id,
                                                    d.dcr_no,
                                                    d.visit_date,
                                                    a.area_name
                                                FROM tbl_dcr d

                                                INNER JOIN tbl_areas a
                                                ON a.id=d.area_id

                                                WHERE d.status=1
                                                AND d.mr_id='" . $_SESSION['user_id'] . "'

                                                ORDER BY d.visit_date DESC
                                               ");

                                                while ($d = mysqli_fetch_assoc($dcr)) {
                                                ?>

                                                    <option
                                                        value="<?php echo $d['id']; ?>"
                                                        <?php if ($row['dcr_id'] == $d['id']) echo "selected"; ?>>

                                                        <?php echo $d['dcr_no']; ?>
                                                        -
                                                        <?php echo date('d-m-Y', strtotime($d['visit_date'])); ?>
                                                        -
                                                        <?php echo $d['area_name']; ?>

                                                    </option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                        <!-- Area -->
                                        <div class="mb-3">

                                            <label>Area</label>

                                            <input type="text"
                                                class="form-control"
                                                value="<?php echo $row['area_name']; ?>"
                                                readonly>

                                            <input type="hidden"
                                                name="area_id"
                                                value="<?php echo $row['area_id']; ?>">

                                        </div>

                                        <!-- Travel -->
                                        <div class="mb-3">

                                            <label>Travel Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="travel_expense"
                                                class="form-control"
                                                value="<?php echo $row['travel_expense']; ?>">

                                        </div>

                                        <!-- DA -->
                                        <div class="mb-3">

                                            <label>DA Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="da_expense"
                                                class="form-control"
                                                value="<?php echo $row['da_expense']; ?>">

                                        </div>

                                        <!-- Hotel -->
                                        <div class="mb-3">

                                            <label>Hotel Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="hotel_expense"
                                                class="form-control"
                                                value="<?php echo $row['hotel_expense']; ?>">

                                        </div>

                                        <!-- Food -->
                                        <div class="mb-3">

                                            <label>Food Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="food_expense"
                                                class="form-control"
                                                value="<?php echo $row['food_expense']; ?>">

                                        </div>

                                        <!-- Other -->
                                        <div class="mb-3">

                                            <label>Other Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="other_expense"
                                                class="form-control"
                                                value="<?php echo $row['other_expense']; ?>">

                                        </div>

                                        <!-- Total -->
                                        <div class="mb-3">
                                            <label>Total Expense</label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                id="total_expense"
                                                name="total_expense"
                                                class="form-control"
                                                value="<?php echo $row['total_expense']; ?>"
                                                readonly>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">

                                            <label>Remarks</label>

                                            <textarea
                                                name="remarks"
                                                rows="4"
                                                class="form-control"><?php echo $row['remarks']; ?></textarea>

                                        </div>

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

                                        <button type="submit" class="btn btn-primary">

                                            Update & Exit

                                        </button>

                                        <a href="expense-list.php" class="btn btn-secondary">

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