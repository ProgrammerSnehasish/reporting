<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

?>

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

                            <h4 class="mb-sm-0">
                                Pain Product Import
                            </h4>

                            <div class="page-title-right">

                                <ol class="breadcrumb m-0">

                                    <li class="breadcrumb-item">
                                        <a href="javascript:void(0);">Pain Management</a>
                                    </li>

                                    <li class="breadcrumb-item active">
                                        Import Pain Products
                                    </li>

                                </ol>

                            </div>

                        </div>

                    </div>

                    <div class="card">

                        <div class="card-header">

                            <h4 class="card-title">
                                Import Pain Products
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

                                <strong>Required Excel Columns</strong>

                                <ul class="mb-0 mt-2">

                                    <li>Pain Product Name</li>
                                    <li>Description</li>
                                    <li>Remarks</li>
                                    <li>Status (Active / Inactive)</li>

                                </ul>

                            </div>

                            <form
                                action="pain-import-process.php"
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

                                <button type="submit" class="btn btn-success">

                                    <i class="ri-upload-line"></i>

                                    Import Pain Products

                                </button>

                                <a
                                    href="pain-sample-download.php"
                                    class="btn btn-primary">

                                    <i class="ri-download-line"></i>

                                    Download Sample

                                </a>

                            </form>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        <?php include('./includes/footer.php'); ?>

    </div>

</div>

<?php include('./includes/scripts.php'); ?>