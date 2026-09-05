<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$sql = "

SELECT

p.*,

c.category_name,

d.division_name

FROM tbl_products p

LEFT JOIN tbl_product_categories c
ON c.id = p.category_id

LEFT JOIN tbl_divisions d
ON d.id = p.division_id

ORDER BY p.id DESC

";

$result = mysqli_query($conn, $sql);

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
                            <h4 class="mb-sm-0">Product History</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Product History</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-12">



                        <div class="mb-3">

                            <a href="product-add.php" class="btn btn-primary">

                                <i class="ri-add-line"></i>

                                Add Product

                            </a>

                            <a href="product-import.php" class="btn btn-success">

                                <i class="ri-upload-2-line"></i>

                                Import Excel

                            </a>

                            <a href="product-export.php" class="btn btn-info">

                                <i class="ri-download-2-line"></i>

                                Export Excel

                            </a>

                            <a href="product-sample-download.php" class="btn btn-success">
                                <i class="ri-download-2-line"></i>
                                Download Sample Format
                            </a>

                        </div>

                        <div class="card">

                            <div class="card-body">

                                <table id="datatable" class="table table-bordered table-hover align-middle">

                                    <thead class="table-dark">

                                        <tr>

                                            <th>#</th>

                                            <th>Product Code</th>

                                            <th>Product Name</th>

                                            <th>Brand</th>

                                            <th>Category</th>

                                            <th>Division</th>

                                            <th>Dosage</th>

                                            <th>Strength</th>

                                            <th>Pack</th>

                                            <th>MRP</th>

                                            <th>PTR</th>

                                            <th>PTS</th>

                                            <th>Stock</th>

                                            <th>Status</th>

                                            <th width="120">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $i = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $i++; ?></td>

                                                <td><?= $row['product_code']; ?></td>

                                                <td><?= htmlspecialchars($row['product_name']); ?></td>

                                                <td><?= htmlspecialchars($row['brand_name']); ?></td>

                                                <td><?= $row['category_name']; ?></td>

                                                <td><?= $row['division_name']; ?></td>

                                                <td><?= $row['dosage_form']; ?></td>

                                                <td><?= $row['strength']; ?></td>

                                                <td><?= $row['pack']; ?></td>

                                                <td>₹ <?= number_format($row['mrp'], 2); ?></td>

                                                <td>₹ <?= number_format($row['ptr'], 2); ?></td>

                                                <td>₹ <?= number_format($row['pts'], 2); ?></td>

                                                <td>

                                                    <?php

                                                    if ($row['stock_quantity'] <= 10) {

                                                        echo '<span class="badge bg-danger">' . $row['stock_quantity'] . '</span>';
                                                    } else {

                                                        echo '<span class="badge bg-success">' . $row['stock_quantity'] . '</span>';
                                                    }

                                                    ?>

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

                                                <td>

                                                    <a href="product-view.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">

                                                        <i class="ri-eye-line"></i>

                                                    </a>

                                                    <a href="product-edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">

                                                        <i class="ri-edit-line"></i>

                                                    </a>

                                                    <a href="product-delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm deleteBtn">

                                                        <i class="ri-delete-bin-line"></i>

                                                    </a>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>




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

<!-- <script>
    new DataTable('#datatable');
</script> -->