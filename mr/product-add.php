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
                            Add Product
                        </h4>

                    </div>

                    <div class="col-md-6 text-end">

                        <a href="product-list.php" class="btn btn-primary btn-sm">

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

                                    Add Product

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

                                    <form action="product-process.php" method="POST">

                                        <div class="row">

                                            <!-- Product Code -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Product Code <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="product_code"
                                                    class="form-control"
                                                    placeholder="Auto Generated"
                                                    readonly>
                                            </div>

                                            <!-- Product Name -->
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">
                                                    Product Name <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="product_name"
                                                    class="form-control"
                                                    required>
                                            </div>

                                            <!-- Brand Name -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Brand Name <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="brand_name"
                                                    class="form-control"
                                                    required>
                                            </div>

                                            <!-- Generic Name -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Generic / Formulation
                                                </label>

                                                <input type="text"
                                                    name="generic_name"
                                                    class="form-control"
                                                    placeholder="Aceclofenac + Paracetamol">
                                            </div>

                                            <!-- Category -->
                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">
                                                    Category
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select name="category_id" class="form-select" required>

                                                    <option value="">Select Category</option>

                                                    <?php

                                                    $category = mysqli_query($conn, "
                                                    SELECT id, category_name
                                                    FROM tbl_product_categories
                                                    WHERE status = 1
                                                    ORDER BY category_name ASC
                                                   ");

                                                    while ($cat = mysqli_fetch_assoc($category)) {
                                                    ?>

                                                        <option value="<?= $cat['id']; ?>">

                                                            <?= htmlspecialchars($cat['category_name']); ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>

                                            <!-- Division -->
                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">
                                                    Division
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select name="division_id" class="form-select" required>

                                                    <option value="">Select Division</option>

                                                    <?php

                                                    $division = mysqli_query($conn, "
                                                    SELECT id, division_name
                                                    FROM tbl_divisions
                                                    WHERE status = 1
                                                    ORDER BY division_name ASC
                                                    ");

                                                    while ($div = mysqli_fetch_assoc($division)) {
                                                    ?>

                                                        <option value="<?= $div['id']; ?>">

                                                            <?= htmlspecialchars($div['division_name']); ?>

                                                        </option>

                                                    <?php } ?>

                                                </select>

                                            </div>

                                            <!-- Dosage Form -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Dosage Form
                                                </label>

                                                <select name="dosage_form" class="form-select">

                                                    <option value="">Select</option>

                                                    <option>Tablet</option>

                                                    <option>Capsule</option>

                                                    <option>Syrup</option>

                                                    <option>Injection</option>

                                                    <option>Gel</option>

                                                    <option>Cream</option>

                                                    <option>Ointment</option>

                                                    <option>Powder</option>

                                                    <option>Drops</option>

                                                </select>
                                            </div>

                                            <!-- Strength -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Strength
                                                </label>

                                                <input type="text"
                                                    name="strength"
                                                    class="form-control"
                                                    placeholder="500 mg">
                                            </div>

                                            <!-- Pack -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Pack
                                                </label>

                                                <input type="text"
                                                    name="pack"
                                                    class="form-control"
                                                    placeholder="10x10">
                                            </div>

                                            <!-- MRP -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    MRP (₹)
                                                </label>

                                                <input type="number"
                                                    name="mrp"
                                                    step="0.01"
                                                    class="form-control">
                                            </div>

                                            <!-- PTR -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    PTR (₹)
                                                </label>

                                                <input type="number"
                                                    name="ptr"
                                                    step="0.01"
                                                    class="form-control">
                                            </div>

                                            <!-- PTS -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    PTS (₹)
                                                </label>

                                                <input type="number"
                                                    name="pts"
                                                    step="0.01"
                                                    class="form-control">
                                            </div>

                                            <!-- GST -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    GST %
                                                </label>

                                                <input type="number"
                                                    name="gst"
                                                    step="0.01"
                                                    class="form-control">
                                            </div>

                                            <!-- HSN -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    HSN Code
                                                </label>

                                                <input type="text"
                                                    name="hsn_code"
                                                    class="form-control">
                                            </div>

                                            <!-- Bonus -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    Bonus Offer
                                                </label>

                                                <input type="text"
                                                    name="bonus_offer"
                                                    class="form-control"
                                                    placeholder="10+1">
                                            </div>

                                            <!-- Manufacturer -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Manufacturer
                                                </label>

                                                <input type="text"
                                                    name="manufacturer"
                                                    class="form-control">
                                            </div>

                                            <!-- Stock -->
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">
                                                    Stock Quantity
                                                </label>

                                                <input type="number"
                                                    name="stock_quantity"
                                                    class="form-control"
                                                    value="0">
                                            </div>

                                            <!-- Focus Product -->
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">
                                                    Focus Product
                                                </label>

                                                <select name="is_focus_product" class="form-select">

                                                    <option value="0">No</option>

                                                    <option value="1">Yes</option>

                                                </select>
                                            </div>

                                            <!-- Remarks -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">
                                                    Remarks
                                                </label>

                                                <textarea
                                                    name="remarks"
                                                    rows="3"
                                                    class="form-control"></textarea>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mb-3">
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

                                            Save Product

                                        </button>

                                        <a href="product-list.php" class="btn btn-secondary">

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