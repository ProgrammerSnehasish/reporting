<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

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
                            Add Gift
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="gift-list.php" class="btn btn-primary btn-sm">

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

                                    Add Gift

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

                                    <form action="gift-process.php" method="POST">

                                        <!-- Gift Code -->
                                        <div class="mb-3">
                                            <label>
                                                Gift Code
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                name="gift_code"
                                                class="form-control"
                                                placeholder="Auto Generated"
                                                readonly>
                                        </div>

                                        <!-- Gift Name -->
                                        <div class="mb-3">
                                            <label>
                                                Gift Name
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                name="gift_name"
                                                class="form-control"
                                                required
                                                placeholder="Enter Gift Name">
                                        </div>

                                        <!-- Brand -->
                                        <div class="mb-3">
                                            <label>
                                                Brand
                                            </label>

                                            <input
                                                type="text"
                                                name="brand_name"
                                                class="form-control"
                                                placeholder="Enter Brand Name">
                                        </div>

                                        <!-- Gift Category -->
                                        <div class="mb-3">
                                            <label>
                                                Gift Category
                                            </label>

                                            <select
                                                name="gift_category"
                                                class="form-select">

                                                <option value="">Select Category</option>

                                                <option value="Doctor">Doctor</option>

                                                <option value="Chemist">Chemist</option>

                                                <option value="Stockist">Stockist</option>

                                                <option value="General">General</option>

                                            </select>
                                        </div>

                                        <!-- Unit -->
                                        <div class="mb-3">
                                            <label>
                                                Gift Value
                                            </label>

                                            <input
                                                type="text"
                                                name="gift_value"
                                                class="form-control"
                                                placeholder="Example: 10">
                                        </div>

                                        <!-- Cost -->
                                        <div class="mb-3">
                                            <label>
                                                Stock Quantity
                                            </label>

                                            <input
                                                type="number"
                                                step="1"
                                                min="0"
                                                name="qty"
                                                class="form-control"
                                                placeholder="1">
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mb-3">
                                            <label>
                                                Remarks
                                            </label>

                                            <textarea
                                                name="remarks"
                                                rows="4"
                                                class="form-control"
                                                placeholder="Enter Remarks"></textarea>
                                        </div>

                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label>
                                                Status
                                            </label>

                                            <select
                                                name="status"
                                                class="form-select">

                                                <option value="1" selected>
                                                    Active
                                                </option>

                                                <option value="0">
                                                    Inactive
                                                </option>

                                            </select>
                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-primary">

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