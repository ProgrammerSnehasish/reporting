<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Expense.";

    header("Location: expense-list.php");

    exit;
}

$id = (int)$_GET['id'];

$sql = "

SELECT

ex.*,

d.dcr_no,
d.visit_date,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) employee_name,

a.area_name

FROM tbl_dcr_expenses ex

INNER JOIN tbl_dcr d
ON d.id=ex.dcr_id

INNER JOIN tbl_users u
ON u.id=ex.mr_id

INNER JOIN tbl_employees e
ON e.id=u.employee_id

LEFT JOIN tbl_areas a
ON a.id=ex.area_id

WHERE ex.id='$id'

LIMIT 1

";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {

    $_SESSION['error'] = "Expense not found.";

    header("Location: expense-list.php");

    exit;
}

?>
<!-- DataTables -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<!-- Responsive datatable examples -->
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

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
                            View Expense
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="expense-list.php" class="btn btn-primary btn-sm">

                            <i class="ri-edit-line"></i>

                            View Expense Lists

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

                                <table class="table table-bordered">

                                    <tr>

                                        <th width="30%">Employee</th>

                                        <td>

                                            <?= $row['employee_code']; ?>

                                            -

                                            <?= $row['employee_name']; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>DCR No</th>

                                        <td><?= $row['dcr_no']; ?></td>

                                    </tr>

                                    <tr>

                                        <th>Visit Date</th>

                                        <td><?= date("d-m-Y", strtotime($row['visit_date'])); ?></td>

                                    </tr>

                                    <tr>

                                        <th>Area</th>

                                        <td><?= $row['area_name']; ?></td>

                                    </tr>

                                    <tr>

                                        <th>Travel Expense</th>

                                        <td>

                                            ₹<?= number_format($row['travel_expense'], 2); ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>DA Expense</th>

                                        <td>

                                            ₹<?= number_format($row['da_expense'], 2); ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Hotel Expense</th>

                                        <td>

                                            ₹<?= number_format($row['hotel_expense'], 2); ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Food Expense</th>

                                        <td>

                                            ₹<?= number_format($row['food_expense'], 2); ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Other Expense</th>

                                        <td>

                                            ₹<?= number_format($row['other_expense'], 2); ?>

                                        </td>

                                    </tr>

                                    <tr class="table-success">

                                        <th>Total Expense</th>

                                        <td>

                                            <strong>

                                                ₹<?= number_format($row['total_expense'], 2); ?>

                                            </strong>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Remarks</th>

                                        <td>

                                            <?= !empty($row['remarks'])

                                                ? nl2br(htmlspecialchars($row['remarks']))

                                                : "-"; ?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Status</th>

                                        <td>

                                            <?php

                                            switch ($row['status']) {

                                                case 0:

                                                    echo '<span class="badge bg-warning">Pending</span>';

                                                    break;

                                                case 1:

                                                    echo '<span class="badge bg-success">Approved</span>';

                                                    break;

                                                case 2:

                                                    echo '<span class="badge bg-danger">Rejected</span>';

                                                    break;

                                                default:

                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                            }

                                            ?>

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

                                            <?= date("d M Y h:i A", strtotime($row['updated_at'])); ?>

                                        </td>

                                    </tr>

                                </table>

                                <!-- <a href="expense-edit.php?id=<?= $row['id']; ?>" class="btn btn-primary">

                                    Edit

                                </a> -->

                                <a href="expense-list.php" class="btn btn-secondary">

                                    Back

                                </a>

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