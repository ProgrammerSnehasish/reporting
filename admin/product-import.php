<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

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

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Product Import</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Utility</a></li>
                                    <li class="breadcrumb-item active">Product Import</li>
                                </ol>
                            </div>

                        </div>
                    </div>

                    <div class="card">

                        <div class="card-header">

                            <h4 class="card-title">
                                Product Import
                            </h4>

                        </div>

                        <div class="card-body">

                            <?php
                            if (isset($_SESSION['success'])) {
                            ?>
                                <div class="alert alert-success">
                                    <?= $_SESSION['success']; ?>
                                </div>
                            <?php
                                unset($_SESSION['success']);
                            }

                            if (isset($_SESSION['error'])) {
                            ?>
                                <div class="alert alert-danger">
                                    <?= $_SESSION['error']; ?>
                                </div>
                            <?php
                                unset($_SESSION['error']);
                            }
                            ?>

                            <div class="alert alert-info">

                                <strong>Required Columns</strong>

                                <ul class="mb-0 mt-2">

                                    <li>Product Name</li>
                                    <li>Brand Name</li>
                                    <li>Formulation</li>
                                    <li>Packing</li>
                                    <li>MRP</li>
                                    <li>PTR</li>
                                    <li>PTS</li>

                                </ul>

                            </div>

                            <form
                                action="product-import-process.php"
                                method="POST"
                                enctype="multipart/form-data">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Select Excel File

                                    </label>

                                    <input
                                        type="file"
                                        name="excel_file"
                                        class="form-control"
                                        accept=".xlsx,.xls,.csv"
                                        required>

                                </div>

                                <button class="btn btn-success">

                                    <i class="ri-upload-line"></i>

                                    Import Products

                                </button>

                                <a
                                    href="product-sample-download.php"
                                    class="btn btn-primary">

                                    <i class="ri-download-line"></i>

                                    Download Sample

                                </a>

                            </form>

                        </div>

                    </div>


       </div>
      <!-- end page title -->

</div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php include('./includes/footer.php'); ?>

</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->

<?php include('./includes/scripts.php'); ?>