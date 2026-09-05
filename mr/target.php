<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$mr_user_id = $_SESSION['user_id'];

$sql = "

SELECT

tm.target_month,
tm.target_year,

p.product_code,
p.product_name,
p.brand_name,
p.pack,
p.strength,

td.target_qty,
td.target_value

FROM tbl_target_details td

INNER JOIN tbl_target_master tm
ON tm.id=td.target_master_id

INNER JOIN tbl_products p
ON p.id=td.product_id

WHERE

td.mr_user_id='$mr_user_id'

ORDER BY

tm.target_year DESC,
tm.target_month DESC,
p.display_order,
p.product_name

";

$result = mysqli_query($conn, $sql);

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

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">My Target</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">My Target</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->



                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header bg-primary text-white">

                                <h5 class="mb-0">

                                    <i class="ri-target-line me-2"></i>

                                    My Monthly Target

                                </h5>

                            </div>

                            <div class="card-body p-0">

                                <div class="table-responsive">

                                    <table class="table table-bordered align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>

                                                <th>Month</th>

                                                <th>Product</th>

                                                <th width="120">Target Qty</th>

                                                <th width="150">Target Value</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php

                                            $totalQty = 0;
                                            $totalValue = 0;

                                            while ($row = mysqli_fetch_assoc($result)) {

                                                $totalQty += $row['target_qty'];
                                                $totalValue += $row['target_value'];

                                            ?>

                                                <tr>

                                                    <td>

                                                        <strong>

                                                            <?= date("F", mktime(0, 0, 0, $row['target_month'], 1)); ?>

                                                            <?= $row['target_year']; ?>

                                                        </strong>

                                                    </td>

                                                    <td>

                                                        <strong>

                                                            <?= $row['product_name']; ?>

                                                        </strong>

                                                        <br>

                                                        <small class="text-muted">

                                                            <?= $row['product_code']; ?>

                                                            |

                                                            <?= $row['strength']; ?>

                                                            |

                                                            <?= $row['pack']; ?>

                                                        </small>

                                                    </td>

                                                    <td class="text-center">

                                                        <span class="badge bg-primary fs-6">

                                                            <?= number_format($row['target_qty']); ?>

                                                        </span>

                                                    </td>

                                                    <td class="text-end">

                                                        <strong>

                                                            ₹ <?= number_format($row['target_value'], 2); ?>

                                                        </strong>

                                                    </td>

                                                </tr>

                                            <?php } ?>

                                        </tbody>

                                        <tfoot class="table-light">

                                            <tr>

                                                <th colspan="2">

                                                    Overall Total

                                                </th>

                                                <th class="text-center">

                                                    <?= number_format($totalQty); ?>

                                                </th>

                                                <th class="text-end">

                                                    ₹ <?= number_format($totalValue, 2); ?>

                                                </th>

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>

                            </div>

                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php include('./includes/footer.php'); ?>

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->



<?php include('./includes/scripts.php'); ?>

<!-- Required datatable js -->
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/libs/jszip/jszip.min.js"></script>
<script src="../assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="../assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

<script src="../assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="../assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>

<!-- Responsive examples -->
<script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Datatable init js -->
<script src="../assets/js/pages/datatables.init.js"></script>