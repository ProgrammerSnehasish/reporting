<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$query = mysqli_query($conn, "SELECT *FROM tbl_gifts ORDER BY gift_name ASC");

//$result = mysqli_fetch_assoc($query);

?>

<!-- DataTables -->
<link href="../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include('./includes/header.php'); ?>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Gift History</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Gift History</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Gift History</h5>
                                <a href="gift-add.php" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line"></i> Add Gift
                                </a>
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

                                <table
                                    id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>Sl.</th>

                                            <th>Gift Code</th>

                                            <th>Gift Name</th>

                                            <th>Brand Name</th>

                                            <th>Gift Category</th>

                                            <th>Gift Value</th>

                                            <th>Stock Quantity</th>

                                            <!-- <th>MRP</th> -->

                                            <th>Status</th>

                                            <!-- <th>Created At</th> -->

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($query)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td>
                                                    <?= htmlspecialchars($row['gift_code']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['gift_name']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['brand_name']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['gift_category']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['gift_value']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['stock_quantity']); ?>
                                                </td>

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

                                                <!-- <td>

                                                    <?= date("d-m-Y H:i", strtotime($row['created_at'])); ?>

                                                </td> -->

                                                <td>

                                                    <a href="gift-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        <i class="ri-eye-line"></i>

                                                    </a>


                                                    <a href="gift-delete.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-danger btn-sm"

                                                        onclick="return confirm('Delete this Product?');">

                                                        <i class="ri-delete-bin-line"></i>

                                                    </a>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div><!-- /.card-body -->
                        </div><!-- /.card -->
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </div><!-- /.page-content -->

        <?php include('./includes/footer.php'); ?>

    </div><!-- /.main-content -->
</div><!-- /#layout-wrapper -->

<?php include('./includes/scripts.php'); ?>

<!-- DataTables JS -->
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
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
<script src="../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/js/pages/datatables.init.js"></script>