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

                <!-- Page Title -->

                <div class="row mb-3">

                    <div class="col-md-6">

                        <h4 class="mb-0">
                            Add Product For Pain
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

                                    Add Product For Pain

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

                                    <form action="pain-process.php" method="POST">

                                        <div class="row">

                                            <!-- Pain Product Code -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Pain Product Code <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="pain_product_code"
                                                    class="form-control"
                                                    placeholder="Auto Generated"
                                                    readonly>
                                            </div>

                                            <!-- Pain Product Name -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Pain Product Name <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="pain_product_name"
                                                    class="form-control"
                                                    placeholder="Enter Pain Product Name"
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
                                                    placeholder="Enter Description"></textarea>
                                            </div>

                                            <!-- Category -->
                                            <!-- <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Category
                                                </label>

                                                <select name="category" class="form-select">

                                                    <option value="">Select Category</option>

                                                    <option value="Needle">Needle</option>

                                                    <option value="Catheter">Catheter</option>

                                                    <option value="Cannula">Cannula</option>

                                                    <option value="Injection">Injection</option>

                                                    <option value="Consumable">Consumable</option>

                                                    <option value="Equipment">Equipment</option>

                                                    <option value="Others">Others</option>

                                                </select>
                                            </div> -->

                                            <!-- UOM -->
                                            <!-- <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Unit
                                                </label>

                                                <select name="unit" class="form-select">

                                                    <option value="Pcs">Pcs</option>

                                                    <option value="Box">Box</option>

                                                    <option value="Pack">Pack</option>

                                                    <option value="Bottle">Bottle</option>

                                                    <option value="Kit">Kit</option>

                                                </select>
                                            </div> -->

                                            <!-- Manufacturer -->
                                            <!-- <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Manufacturer
                                                </label>

                                                <input type="text"
                                                    name="manufacturer"
                                                    class="form-control"
                                                    placeholder="Manufacturer Name">
                                            </div> -->

                                            <!-- Remarks -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Remarks
                                                </label>

                                                <input type="text"
                                                    name="remarks"
                                                    class="form-control"
                                                    placeholder="Remarks">
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Status
                                                </label>

                                                <select name="status" class="form-select">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>

                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line"></i>
                                            Save Pain Product
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