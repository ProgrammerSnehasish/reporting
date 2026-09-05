<?php
require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

$doctorList = [];
$res = mysqli_query($conn, "
    SELECT d.id, d.doctor_name, s.specialization_name
    FROM tbl_doctors d
    LEFT JOIN tbl_specializations s ON s.id = d.specialization_id
    WHERE d.status = 1
    ORDER BY d.doctor_name
");
while ($r = mysqli_fetch_assoc($res)) $doctorList[] = $r;

$callTypeList = [];
$res = mysqli_query($conn, "SELECT id, call_type_name FROM tbl_call_types WHERE status=1 ORDER BY call_type_name");
while ($r = mysqli_fetch_assoc($res)) $callTypeList[] = $r;

$productList = [];
$res = mysqli_query($conn, "SELECT * FROM tbl_products WHERE status=1 ORDER BY product_name");
while ($r = mysqli_fetch_assoc($res)) $productList[] = $r;

$painProductList = [];
$res = mysqli_query($conn, "SELECT id, pain_product_name FROM tbl_pain_management_products WHERE status=1 ORDER BY pain_product_name");
while ($r = mysqli_fetch_assoc($res)) $painProductList[] = $r;

$giftList = [];
$res = mysqli_query($conn, "SELECT * FROM tbl_gifts WHERE status=1 ORDER BY gift_name");
while ($r = mysqli_fetch_assoc($res)) $giftList[] = $r;

$chemistList = [];
$res = mysqli_query($conn, "
    SELECT c.id, c.chemist_name FROM tbl_chemists c
    INNER JOIN tbl_chemist_types t ON c.chemist_type_id = t.id
    WHERE c.status=1 AND t.type_name='Chemist' ORDER BY c.chemist_name
");
while ($r = mysqli_fetch_assoc($res)) $chemistList[] = $r;

$stockistList = [];
$res = mysqli_query($conn, "
    SELECT c.id, c.chemist_name FROM tbl_chemists c
    INNER JOIN tbl_chemist_types t ON c.chemist_type_id = t.id
    WHERE c.status=1 AND t.type_name='Stockist' ORDER BY c.chemist_name
");
while ($r = mysqli_fetch_assoc($res)) $stockistList[] = $r;

$hqList = [];
$res = mysqli_query($conn, "SELECT id, area_name FROM tbl_areas WHERE status=1 AND area_type='HQ' ORDER BY area_name");
while ($r = mysqli_fetch_assoc($res)) $hqList[] = $r;

$workingTypeList = [];
$res = mysqli_query($conn, "SELECT id, working_type_name FROM tbl_working_types WHERE status=1 ORDER BY working_type_name");
while ($r = mysqli_fetch_assoc($res)) $workingTypeList[] = $r;

$workingWithList = [];
$res = mysqli_query($conn, "
    SELECT u.id, e.employee_code,
           CONCAT(e.first_name,' ',e.last_name) AS employee_name,
           r.role_name
    FROM tbl_users u
    INNER JOIN tbl_employees e ON e.id = u.employee_id
    INNER JOIN tbl_roles r     ON r.id = u.role_id
    WHERE u.status=1 AND r.role_code IN ('abm','rbm','zsm','nsm')
    ORDER BY r.role_name, e.first_name
");
while ($r = mysqli_fetch_assoc($res)) $workingWithList[] = $r;
?>

<?php include('./includes/header.php'); ?>

<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
        animation: fadeIn .25s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .nav-pills .nav-link {
        pointer-events: none;
        border-radius: .5rem;
        font-weight: 500;
        color: #6c757d;
        background: #f1f3f9;
    }

    .nav-pills .nav-link.active {
        background: #0d6efd;
        color: #fff;
    }

    .nav-pills .nav-link.completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .step-card {
        border: 1px solid #e9ecef;
        border-radius: .75rem;
    }

    .step-card .card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    #doctorTable td,
    #doctorTable th,
    #chemistTable td,
    #chemistTable th,
    #stockistTable td,
    #stockistTable th {
        vertical-align: middle;
        font-size: .875rem;
    }

    .mini-table {
        font-size: .8rem;
    }

    .badge-soft {
        background: #eef2ff;
        color: #3b47ca;
        font-weight: 500;
    }

    /* ── Checkbox multi-select dropdown ── */
    .cb-dropdown {
        position: relative;
    }

    .cb-dropdown-btn {
        width: 100%;
        text-align: left;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: .375rem;
        padding: .375rem .75rem;
        font-size: .875rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cb-dropdown-btn:after {
        content: '▾';
        font-size: .75rem;
        color: #6c757d;
    }

    .cb-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 2px);
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: .375rem;
        max-height: 220px;
        overflow-y: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .cb-dropdown-menu.open {
        display: block;
    }

    .cb-dropdown-menu label {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .4rem .75rem;
        margin: 0;
        cursor: pointer;
        font-size: .85rem;
    }

    .cb-dropdown-menu label:hover {
        background: #f0f4ff;
    }

    .cb-dropdown-menu input[type=checkbox] {
        accent-color: #0d6efd;
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    /* Working-With checkbox list (inline, no dropdown) */
    .ww-checkbox-list {
        border: 1px solid #ced4da;
        border-radius: .375rem;
        max-height: 160px;
        overflow-y: auto;
        padding: .25rem .5rem;
        background: #fff;
    }

    .ww-checkbox-list label {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .3rem .25rem;
        cursor: pointer;
        font-size: .85rem;
    }

    .ww-checkbox-list label:hover {
        background: #f0f4ff;
        border-radius: .25rem;
    }

    .ww-checkbox-list input[type=checkbox] {
        accent-color: #0d6efd;
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }
</style>

<div id="layout-wrapper">
    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0">Add DCR</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="dcr-list.php" class="btn btn-primary btn-sm">
                            <i class="ri-list-check me-1"></i> View
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Add DCR</h4>
                            </div>
                            <div class="card-body">

                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo $_SESSION['success'];
                                        unset($_SESSION['success']); ?>
                                        <button class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?>
                                        <button class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <!-- Progress Bar -->
                                <div class="progress mb-4" style="height:8px;">
                                    <div class="progress-bar" id="progressBar" style="width:20%"></div>
                                </div>

                                <!-- Step Indicators -->
                                <ul class="nav nav-pills nav-justified mb-4 gap-2">
                                    <li class="nav-item"><button class="nav-link active"><i class="ri-file-info-line me-1"></i> General Info</button></li>
                                    <li class="nav-item"><button class="nav-link"><i class="ri-user-heart-line me-1"></i> Doctor Calls</button></li>
                                    <li class="nav-item"><button class="nav-link"><i class="ri-capsule-line me-1"></i> Chemist Calls</button></li>
                                    <li class="nav-item"><button class="nav-link"><i class="ri-store-2-line me-1"></i> Stockist Calls</button></li>
                                    <li class="nav-item"><button class="nav-link"><i class="ri-quill-pen-line me-1"></i> Remarks</button></li>
                                    <li class="nav-item"><button class="nav-link"><i class="ri-checkbox-circle-line me-1"></i> Review</button></li>
                                </ul>

                                <form action="dcr-process.php" method="POST" id="dcrForm">

                                    <!-- ======================================== -->
                                    <!-- STEP 1 : GENERAL INFORMATION             -->
                                    <!-- ======================================== -->
                                    <div class="step active">
                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-file-info-line me-1"></i> General Information</div>
                                            <div class="card-body">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">DCR No.</label>
                                                        <input type="text" name="dcr_no" class="form-control" placeholder="Auto Generated" readonly>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                                                        <input type="date" name="visit_date" value="<?php echo date('Y-m-d'); ?>" id="visitDate" class="form-control" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">HQ <span class="text-danger">*</span></label>
                                                        <select name="hq_id" id="hqSelect" class="form-select" required>
                                                            <option value="">Select HQ</option>
                                                            <?php foreach ($hqList as $h): ?>
                                                                <option value="<?php echo $h['id']; ?>"><?php echo htmlspecialchars($h['area_name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Working Area <span class="text-danger">*</span></label>
                                                        <select name="area_id" id="areaSelect" class="form-select" required>
                                                            <option value="">Select Area</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Working Type <span class="text-danger">*</span></label>
                                                        <select name="working_type_id" id="workingType" class="form-select" required>
                                                            <option value="">Select Working Type</option>
                                                            <?php foreach ($workingTypeList as $w): ?>
                                                                <option value="<?php echo $w['id']; ?>"><?php echo htmlspecialchars($w['working_type_name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- ── Working With — checkbox list ── -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Working With</label>
                                                        <div class="ww-checkbox-list" id="workingWithCheckboxList">
                                                            <label>
                                                                <input type="checkbox" name="working_with_user_id[]" value="0" id="wwIndividual">
                                                                Individual
                                                            </label>
                                                            <?php foreach ($workingWithList as $u): ?>
                                                                <label>
                                                                    <input type="checkbox" name="working_with_user_id[]" value="<?php echo $u['id']; ?>" class="ww-cb">
                                                                    <?php echo htmlspecialchars($u['employee_code']); ?> –
                                                                    <?php echo htmlspecialchars($u['employee_name']); ?>
                                                                    (<?php echo strtoupper($u['role_name']); ?>)
                                                                </label>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <small class="text-muted">Check one or more; tick "Individual" if working alone.</small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Total KM</label>
                                                        <div class="input-group">
                                                            <input type="number" name="total_km" id="total_km" class="form-control" readonly placeholder="Auto from area">
                                                            <span class="input-group-text">km</span>
                                                        </div>
                                                        <small class="text-danger">Auto filled from area selection</small>
                                                    </div>

                                                    <input type="hidden" name="start_latitude" id="start_latitude">
                                                    <input type="hidden" name="start_longitude" id="start_longitude">
                                                    <input type="hidden" name="end_latitude" id="end_latitude">
                                                    <input type="hidden" name="end_longitude" id="end_longitude">

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ======================================== -->
                                    <!-- STEP 2 : DOCTOR CALLS                   -->
                                    <!-- ======================================== -->
                                    <div class="step">
                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-user-heart-line me-1"></i> Add Doctor Call</div>
                                            <div class="card-body">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Doctor <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="doctorSelect">
                                                            <option value="">Select Doctor</option>
                                                            <?php foreach ($doctorList as $d): ?>
                                                                <option value="<?php echo $d['id']; ?>">
                                                                    <?php echo htmlspecialchars($d['doctor_name']); ?>
                                                                    <?php if (!empty($d['specialization_name'])): ?>
                                                                        (<?php echo htmlspecialchars($d['specialization_name']); ?>)
                                                                    <?php endif; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Visit Time</label>
                                                        <input type="time" class="form-control" id="doctorVisitTime">
                                                    </div>

                                                    <!-- Samples -->
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label fw-semibold">Samples</label>
                                                        <div class="row g-2 align-items-end mb-2">
                                                            <div class="col-md-5">
                                                                <select class="form-select form-select-sm" id="sampleProductSelect">
                                                                    <option value="">Select Product</option>
                                                                    <?php foreach ($productList as $p): ?>
                                                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['product_name']); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number" class="form-control form-control-sm" id="sampleQty" placeholder="Qty">
                                                            </div>
                                                            <div class="col-md-4 d-grid">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" id="addSampleBtn">
                                                                    <i class="ri-add-line"></i> Add Sample
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mini-table mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Product</th>
                                                                        <th style="width:20%;">Qty</th>
                                                                        <th style="width:15%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="sampleTableBody">
                                                                    <tr>
                                                                        <td colspan="3" class="text-center text-muted">No samples added.</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <!-- Pain Management -->
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label fw-semibold">Pain Management</label>
                                                        <div class="row g-2 align-items-end mb-2">
                                                            <div class="col-md-5">
                                                                <select class="form-select form-select-sm" id="painProductSelect">
                                                                    <option value="">Select Pain Product</option>
                                                                    <?php foreach ($painProductList as $pp): ?>
                                                                        <option value="<?php echo $pp['id']; ?>"><?php echo htmlspecialchars($pp['pain_product_name']); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number" class="form-control form-control-sm" id="painQty" placeholder="Qty">
                                                            </div>
                                                            <div class="col-md-4 d-grid">
                                                                <button type="button" class="btn btn-outline-warning btn-sm" id="addPainBtn">
                                                                    <i class="ri-add-line"></i> Add Pain Product
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mini-table mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Pain Product</th>
                                                                        <th style="width:20%;">Qty</th>
                                                                        <th style="width:15%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="painTableBody">
                                                                    <tr>
                                                                        <td colspan="3" class="text-center text-muted">No pain products added.</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <!-- Gifts -->
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label fw-semibold">Gift</label>
                                                        <div class="row g-2 align-items-end mb-2">
                                                            <div class="col-md-5">
                                                                <select class="form-select form-select-sm" id="giftSelect">
                                                                    <option value="">Select Gift</option>
                                                                    <?php foreach ($giftList as $g): ?>
                                                                        <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['gift_name']); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number" class="form-control form-control-sm" id="giftQty" placeholder="Qty">
                                                            </div>
                                                            <div class="col-md-4 d-grid">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" id="addGiftBtn">
                                                                    <i class="ri-add-line"></i> Add Gift
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mini-table mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Gift Name</th>
                                                                        <th style="width:20%;">Qty</th>
                                                                        <th style="width:15%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="giftTableBody">
                                                                    <tr>
                                                                        <td colspan="3" class="text-center text-muted">No gifts added.</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Remarks</label>
                                                        <textarea class="form-control" id="doctorRemarks" rows="2" placeholder="Enter remarks"></textarea>
                                                    </div>

                                                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                                                        <button type="button" class="btn btn-primary" id="addDoctorBtn">
                                                            <i class="ri-add-line"></i> Add Doctor
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card">
                                            <div class="card-header"><i class="ri-list-check-2 me-1"></i> Doctor Calls Added</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle" id="doctorTable">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:4%;">#</th>
                                                                <th>Doctor</th>
                                                                <th>Visit Time</th>
                                                                <th>Samples</th>
                                                                <th>Gift</th>
                                                                <th>Pain Management</th>
                                                                <th>Remarks</th>
                                                                <th style="width:8%;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="doctorTableBody">
                                                            <tr>
                                                                <td colspan="8" class="text-center text-muted">No doctors added yet.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ======================================== -->
                                    <!-- STEP 3 : CHEMIST CALLS                  -->
                                    <!-- ======================================== -->
                                    <div class="step">
                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-capsule-line me-1"></i> Add Chemist Call</div>
                                            <div class="card-body">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Chemist <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="chemistSelect">
                                                            <option value="">Select Chemist</option>
                                                            <?php foreach ($chemistList as $c): ?>
                                                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['chemist_name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Visit Time</label>
                                                        <input type="time" class="form-control" id="chemistVisitTime">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">POB</label>
                                                        <input type="number" step="0.01" class="form-control" id="chemistPob" placeholder="0.00">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Booking Value</label>
                                                        <input type="number" step="0.01" class="form-control" id="chemistBookingValue" placeholder="0.00">
                                                    </div>

                                                    <!-- ── Product Listing — checkbox dropdown ── -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Product Listing</label>
                                                        <div class="cb-dropdown" id="chemistProductDropdown">
                                                            <button type="button" class="cb-dropdown-btn" id="chemistProductBtn">
                                                                <span id="chemistProductLabel">Select Products</span>
                                                            </button>
                                                            <div class="cb-dropdown-menu" id="chemistProductMenu">
                                                                <?php foreach ($productList as $p): ?>
                                                                    <label>
                                                                        <input type="checkbox" class="chemist-product-cb" value="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['product_name']); ?>">
                                                                        <?php echo htmlspecialchars($p['product_name']); ?>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Remarks</label>
                                                        <textarea class="form-control" id="chemistRemarks" rows="2" placeholder="Enter remarks"></textarea>
                                                    </div>

                                                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                                                        <button type="button" class="btn btn-primary" id="addChemistBtn">
                                                            <i class="ri-add-line"></i> Add Chemist
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card">
                                            <div class="card-header"><i class="ri-list-check-2 me-1"></i> Chemist Calls Added</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle" id="chemistTable">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:4%;">#</th>
                                                                <th>Chemist</th>
                                                                <th>POB</th>
                                                                <th>Booking Value</th>
                                                                <th>Products</th>
                                                                <th>Remarks</th>
                                                                <th style="width:8%;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="chemistTableBody">
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted">No chemists added yet.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ======================================== -->
                                    <!-- STEP 4 : STOCKIST CALLS                 -->
                                    <!-- ======================================== -->
                                    <div class="step">
                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-store-2-line me-1"></i> Add Stockist Call</div>
                                            <div class="card-body">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Stockist <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="stockistSelect">
                                                            <option value="">Select Stockist</option>
                                                            <?php foreach ($stockistList as $s): ?>
                                                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['chemist_name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Visit Time</label>
                                                        <input type="time" class="form-control" id="stockistVisitTime">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Primary Order Value</label>
                                                        <input type="number" step="0.01" class="form-control" id="stockistPrimaryOrder" placeholder="0.00">
                                                    </div>

                                                    <!-- ── Product Listing — checkbox dropdown ── -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Product Listing</label>
                                                        <div class="cb-dropdown" id="stockistProductDropdown">
                                                            <button type="button" class="cb-dropdown-btn" id="stockistProductBtn">
                                                                <span id="stockistProductLabel">Select Products</span>
                                                            </button>
                                                            <div class="cb-dropdown-menu" id="stockistProductMenu">
                                                                <?php foreach ($productList as $p): ?>
                                                                    <label>
                                                                        <input type="checkbox" class="stockist-product-cb" value="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['product_name']); ?>">
                                                                        <?php echo htmlspecialchars($p['product_name']); ?>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Remarks</label>
                                                        <textarea class="form-control" id="stockistRemarks" rows="2" placeholder="Enter remarks"></textarea>
                                                    </div>

                                                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                                                        <button type="button" class="btn btn-primary" id="addStockistBtn">
                                                            <i class="ri-add-line"></i> Add Stockist
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card">
                                            <div class="card-header"><i class="ri-list-check-2 me-1"></i> Stockist Calls Added</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle" id="stockistTable">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:4%;">#</th>
                                                                <th>Stockist</th>
                                                                <th>Visit Time</th>
                                                                <th>Primary Order</th>
                                                                <th>Products</th>
                                                                <th>Remarks</th>
                                                                <th style="width:8%;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="stockistTableBody">
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted">No stockists added yet.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ======================================== -->
                                    <!-- STEP 5 : REMARKS & ACHIEVEMENT           -->
                                    <!-- ======================================== -->
                                    <div class="step">
                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-quill-pen-line me-1"></i> Remarks &amp; Achievement</div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Day Remarks</label>
                                                        <textarea name="remarks" rows="4" class="form-control" placeholder="Enter overall remarks for today's work"></textarea>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Achievement</label>
                                                        <textarea name="achievement" rows="4" class="form-control" placeholder="Enter Your Achievement"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ======================================== -->
                                    <!-- STEP 6 : REVIEW & SUBMIT                -->
                                    <!-- ======================================== -->
                                    <div class="step">
                                        <div class="alert alert-success">
                                            <i class="ri-information-line me-1"></i>
                                            Please review your entered details below and click <strong>Submit</strong>.
                                        </div>

                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-file-info-line me-1"></i> General Information</div>
                                            <div class="card-body">
                                                <div class="row" id="reviewGeneralInfo"></div>
                                            </div>
                                        </div>

                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-user-heart-line me-1"></i> Doctor Summary</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Doctor</th>
                                                                <th>Visit Time</th>
                                                                <th>Samples</th>
                                                                <th>Gift</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="reviewDoctorList"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-capsule-line me-1"></i> Chemist Summary</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Chemist</th>
                                                                <th>POB</th>
                                                                <th>Booking Value</th>
                                                                <th>Products</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="reviewChemistList"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card mb-3">
                                            <div class="card-header"><i class="ri-store-2-line me-1"></i> Stockist Summary</div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Stockist</th>
                                                                <th>Visit Time</th>
                                                                <th>Primary Order</th>
                                                                <th>Products</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="reviewStockistList"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card step-card">
                                            <div class="card-header"><i class="ri-checkbox-circle-line me-1"></i> Achievement &amp; Remarks</div>
                                            <div class="card-body">
                                                <div class="row" id="reviewAchievementRemarks"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="prevBtn" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Previous</button>
                                        <button type="button" id="nextBtn" class="btn btn-primary">Next <i class="ri-arrow-right-line"></i></button>
                                        <button type="submit" id="submitBtn" class="btn btn-success d-none"><i class="ri-send-plane-line"></i> Submit</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include('./includes/footer.php'); ?>
</div>

<?php include('./includes/scripts.php'); ?>

<script>
    /* ============================================================
   UTILITY
============================================================ */
    function emptyRow(cols, msg) {
        return `<tr><td colspan="${cols}" class="text-center text-muted">${msg}</td></tr>`;
    }

    function delBtn(onclick) {
        return `<button type="button" class="btn btn-danger btn-sm" onclick="${onclick}"><i class="ri-delete-bin-line"></i></button>`;
    }

    /* ============================================================
       CHECKBOX DROPDOWN (Chemist & Stockist Product Listing)
    ============================================================ */
    function initCbDropdown(btnId, menuId, labelId, cbClass) {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        const lbl = document.getElementById(labelId);

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('open');
        });

        menu.addEventListener('change', function() {
            const checked = Array.from(menu.querySelectorAll('.' + cbClass + ':checked'))
                .map(c => c.dataset.name);
            lbl.textContent = checked.length ? checked.join(', ') : 'Select Products';
        });

        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && e.target !== btn) {
                menu.classList.remove('open');
            }
        });
    }

    initCbDropdown('chemistProductBtn', 'chemistProductMenu', 'chemistProductLabel', 'chemist-product-cb');
    initCbDropdown('stockistProductBtn', 'stockistProductMenu', 'stockistProductLabel', 'stockist-product-cb');

    function getCheckedProducts(cbClass) {
        return Array.from(document.querySelectorAll('.' + cbClass + ':checked'))
            .map(c => ({
                id: c.value,
                name: c.dataset.name
            }));
    }

    function clearChecked(cbClass) {
        document.querySelectorAll('.' + cbClass).forEach(c => c.checked = false);
    }

    /* ============================================================
       WORKING WITH — "Individual" toggles off others
    ============================================================ */
    document.getElementById('wwIndividual').addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.ww-cb').forEach(c => c.checked = false);
        }
    });
    document.querySelectorAll('.ww-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            if (this.checked) document.getElementById('wwIndividual').checked = false;
        });
    });

    /* ============================================================
       STEP NAVIGATION
    ============================================================ */
    let currentStep = 0;
    const steps = document.querySelectorAll('.step');
    const navLinks = document.querySelectorAll('.nav-link');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');

    function showStep(n) {
        steps.forEach((s, i) => {
            s.classList.toggle('active', i === n);
            navLinks[i].classList.toggle('active', i === n);
            navLinks[i].classList.toggle('completed', i < n);
        });
        prevBtn.style.display = n === 0 ? 'none' : 'inline-block';
        const isLast = n === steps.length - 1;
        nextBtn.classList.toggle('d-none', isLast);
        submitBtn.classList.toggle('d-none', !isLast);
        if (isLast) renderReview();
        progressBar.style.width = ((n + 1) / steps.length * 100) + '%';
    }

    nextBtn.onclick = function() {
        let inputs = steps[currentStep].querySelectorAll("input,select,textarea");
        for (let inp of inputs) {
            if (!inp.checkValidity()) {
                inp.reportValidity();
                return;
            }
        }
        if (currentStep === 0) {
            getStartLocation(function() {
                showStep(++currentStep);
            });
            return;
        }
        if (currentStep < steps.length - 1) showStep(++currentStep);
    };

    prevBtn.onclick = () => {
        if (currentStep > 0) showStep(--currentStep);
    };
    showStep(0);

    /* ============================================================
       HQ → AREA CASCADE  (HQ itself also shown in area list)
    ============================================================ */
    document.getElementById('hqSelect').addEventListener('change', function() {
        const areaSelect = document.getElementById('areaSelect');
        document.getElementById('total_km').value = '';
        areaSelect.innerHTML = '<option value="">Loading...</option>';

        if (!this.value) {
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            return;
        }

        const hqId = this.value;
        const hqName = this.options[this.selectedIndex].text;

        fetch('get-area.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'hq_id=' + encodeURIComponent(hqId)
            })
            .then(r => r.text())
            .then(function(html) {
                // Prepend the HQ itself as the first area option (km = 0)
                const hqOption = `<option value="${hqId}" data-km="0">${hqName} (HQ)</option>`;
                areaSelect.innerHTML = '<option value="">Select Area</option>' + hqOption + html;
            })
            .catch(function() {
                areaSelect.innerHTML = '<option value="">Select Area</option>';
            });
    });

    /* ── Area → KM auto-fill ── */
    document.getElementById('areaSelect').addEventListener('change', function() {
        const km = parseFloat(this.options[this.selectedIndex].getAttribute('data-km')) || 0;
        document.getElementById('total_km').value = km;
    });

    /* ============================================================
       DOCTOR CALLS
    ============================================================ */
    let tempSamples = [],
        tempGifts = [],
        tempPainProducts = [],
        doctorRows = [];

    function renderSampleTable() {
        document.getElementById('sampleTableBody').innerHTML = tempSamples.length ?
            tempSamples.map((s, i) => `<tr>
            <td>${s.product_name}</td><td>${s.qty}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="tempSamples.splice(${i},1);renderSampleTable()"><i class="ri-close-line"></i></button></td>
          </tr>`).join('') :
            emptyRow(3, 'No samples added.');
    }

    function renderPainTable() {
        document.getElementById('painTableBody').innerHTML = tempPainProducts.length ?
            tempPainProducts.map((p, i) => `<tr>
            <td>${p.pain_product_name}</td><td>${p.qty}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="tempPainProducts.splice(${i},1);renderPainTable()"><i class="ri-close-line"></i></button></td>
          </tr>`).join('') :
            emptyRow(3, 'No pain products added.');
    }

    function renderGiftTable() {
        document.getElementById('giftTableBody').innerHTML = tempGifts.length ?
            tempGifts.map((g, i) => `<tr>
            <td>${g.gift_name}</td><td>${g.qty}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="tempGifts.splice(${i},1);renderGiftTable()"><i class="ri-close-line"></i></button></td>
          </tr>`).join('') :
            emptyRow(3, 'No gifts added.');
    }

    function checkStock(type, id, totalQty, onSuccess) {
        const fd = new FormData();
        fd.append('type', type);
        fd.append('id', id);
        fd.append('qty', totalQty);
        fetch('check-stock.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                data.success ? onSuccess() : alert(data.message);
            });
    }

    document.getElementById('addSampleBtn').onclick = function() {
        const prod = document.getElementById('sampleProductSelect');
        const qty = document.getElementById('sampleQty');
        if (!prod.value) {
            alert('Select a product');
            return;
        }
        if (!qty.value || +qty.value <= 0) {
            alert('Enter valid quantity');
            return;
        }

        const alreadyUsed = [...tempSamples, ...doctorRows.flatMap(r => r.samples)]
            .filter(s => s.product_id == prod.value)
            .reduce((sum, s) => sum + parseInt(s.qty), 0);

        checkStock('product', prod.value, parseInt(qty.value) + alreadyUsed, () => {
            tempSamples.push({
                product_id: prod.value,
                product_name: prod.options[prod.selectedIndex].text,
                qty: qty.value
            });
            renderSampleTable();
            prod.value = '';
            qty.value = '';
        });
    };

    document.getElementById('addPainBtn').onclick = function() {
        const prod = document.getElementById('painProductSelect');
        const qty = document.getElementById('painQty');
        if (!prod.value) {
            alert('Select a pain product');
            return;
        }
        if (!qty.value || +qty.value <= 0) {
            alert('Enter valid quantity');
            return;
        }
        if (tempPainProducts.find(p => p.pain_product_id == prod.value)) {
            alert('This pain product is already added. Remove it first to change qty.');
            return;
        }
        tempPainProducts.push({
            pain_product_id: prod.value,
            pain_product_name: prod.options[prod.selectedIndex].text,
            qty: qty.value
        });
        renderPainTable();
        prod.value = '';
        qty.value = '';
    };

    document.getElementById('addGiftBtn').onclick = function() {
        const gift = document.getElementById('giftSelect');
        const qty = document.getElementById('giftQty');
        if (!gift.value) {
            alert('Select a gift');
            return;
        }
        if (!qty.value || +qty.value <= 0) {
            alert('Enter valid quantity');
            return;
        }

        const alreadyUsed = [...tempGifts, ...doctorRows.flatMap(r => r.gifts)]
            .filter(g => g.gift_id == gift.value)
            .reduce((sum, g) => sum + parseInt(g.qty), 0);

        checkStock('gift', gift.value, parseInt(qty.value) + alreadyUsed, () => {
            tempGifts.push({
                gift_id: gift.value,
                gift_name: gift.options[gift.selectedIndex].text,
                qty: qty.value
            });
            renderGiftTable();
            gift.value = '';
            qty.value = '';
        });
    };

    function renderDoctorTable() {
        const tbody = document.getElementById('doctorTableBody');
        if (!doctorRows.length) {
            tbody.innerHTML = emptyRow(8, 'No doctors added yet.');
            return;
        }
        tbody.innerHTML = doctorRows.map((row, i) => {
            const samplesHidden = row.samples.map(s =>
                `<input type="hidden" name="sample_product_ids[${i}][]" value="${s.product_id}">
             <input type="hidden" name="sample_qty[${i}][]" value="${s.qty}">`).join('');
            const giftsHidden = row.gifts.map(g =>
                `<input type="hidden" name="gift_ids[${i}][]" value="${g.gift_id}">
             <input type="hidden" name="gift_qty[${i}][]" value="${g.qty}">`).join('');
            const painHidden = row.pain_products.map(p =>
                `<input type="hidden" name="pain_product_ids[${i}][]" value="${p.pain_product_id}">
             <input type="hidden" name="pain_qty[${i}][]" value="${p.qty}">`).join('');
            return `<tr>
            <td>${i+1}</td>
            <td>${row.doctor_name}<input type="hidden" name="doctor_id[]" value="${row.doctor_id}"></td>
            <td>${row.visit_time||'-'}<input type="hidden" name="doctor_visit_time[]" value="${row.visit_time}"></td>
            <td>${row.samples.map(s=>`${s.product_name}(${s.qty})`).join(', ')||'-'}${samplesHidden}</td>
            <td>${row.gifts.map(g=>`${g.gift_name}(${g.qty})`).join(', ')||'-'}${giftsHidden}</td>
            <td>${row.pain_products.map(p=>`${p.pain_product_name}(${p.qty})`).join(', ')||'-'}${painHidden}</td>
            <td>${row.remarks||'-'}<input type="hidden" name="doctor_remarks[]" value="${row.remarks}"></td>
            <td>${delBtn(`doctorRows.splice(${i},1);renderDoctorTable()`)}</td>
        </tr>`;
        }).join('');
    }

    document.getElementById('addDoctorBtn').onclick = function() {
        const sel = document.getElementById('doctorSelect');
        if (!sel.value) {
            alert('Select Doctor');
            return;
        }
        doctorRows.push({
            doctor_id: sel.value,
            doctor_name: sel.options[sel.selectedIndex].text,
            visit_time: document.getElementById('doctorVisitTime').value,
            samples: [...tempSamples],
            gifts: [...tempGifts],
            pain_products: [...tempPainProducts],
            remarks: document.getElementById('doctorRemarks').value
        });
        renderDoctorTable();
        sel.value = '';
        document.getElementById('doctorVisitTime').value = '';
        document.getElementById('doctorRemarks').value = '';
        tempSamples = [];
        tempGifts = [];
        tempPainProducts = [];
        renderSampleTable();
        renderGiftTable();
        renderPainTable();
    };

    renderSampleTable();
    renderGiftTable();
    renderPainTable();
    renderDoctorTable();

    /* ============================================================
       CHEMIST CALLS
    ============================================================ */
    let chemistRows = [];

    function renderChemistTable() {
        const tbody = document.getElementById('chemistTableBody');
        if (!chemistRows.length) {
            tbody.innerHTML = emptyRow(7, 'No chemists added yet.');
            return;
        }
        tbody.innerHTML = chemistRows.map((row, i) => {
            const productsHidden = row.products.map(p =>
                `<input type="hidden" name="chemist_products[${i}][]" value="${p.id}">`).join('');
            return `<tr>
            <td>${i+1}</td>
            <td>${row.chemist_name}
                <input type="hidden" name="chemist_id[]" value="${row.chemist_id}">
                <input type="hidden" name="chemist_visit_time[]" value="${row.visit_time}">
            </td>
            <td>${row.pob||0}<input type="hidden" name="chemist_pob[]" value="${row.pob}"></td>
            <td>${row.booking_value||0}<input type="hidden" name="chemist_booking_value[]" value="${row.booking_value}"></td>
            <td><span class="badge badge-soft">${row.products.map(p=>p.name).join(', ')||'-'}</span>${productsHidden}</td>
            <td>${row.remarks||'-'}<input type="hidden" name="chemist_remarks[]" value="${row.remarks}"></td>
            <td>${delBtn(`chemistRows.splice(${i},1);renderChemistTable()`)}</td>
        </tr>`;
        }).join('');
    }

    document.getElementById('addChemistBtn').onclick = function() {
        const sel = document.getElementById('chemistSelect');
        if (!sel.value) {
            alert('Select Chemist');
            return;
        }
        const products = getCheckedProducts('chemist-product-cb');
        chemistRows.push({
            chemist_id: sel.value,
            chemist_name: sel.options[sel.selectedIndex].text,
            visit_time: document.getElementById('chemistVisitTime').value,
            pob: document.getElementById('chemistPob').value,
            booking_value: document.getElementById('chemistBookingValue').value,
            products: products,
            remarks: document.getElementById('chemistRemarks').value
        });
        renderChemistTable();
        sel.value = '';
        ['chemistVisitTime', 'chemistPob', 'chemistBookingValue', 'chemistRemarks'].forEach(id => document.getElementById(id).value = '');
        clearChecked('chemist-product-cb');
        document.getElementById('chemistProductLabel').textContent = 'Select Products';
    };

    renderChemistTable();

    /* ============================================================
       STOCKIST CALLS
    ============================================================ */
    let stockistRows = [];

    function renderStockistTable() {
        const tbody = document.getElementById('stockistTableBody');
        if (!stockistRows.length) {
            tbody.innerHTML = emptyRow(7, 'No stockists added yet.');
            return;
        }
        tbody.innerHTML = stockistRows.map((row, i) => {
            const productsHidden = row.products.map(p =>
                `<input type="hidden" name="stockist_products[${i}][]" value="${p.id}">`).join('');
            return `<tr>
            <td>${i+1}</td>
            <td>${row.stockist_name}<input type="hidden" name="stockist_id[]" value="${row.stockist_id}"></td>
            <td>${row.visit_time||'-'}<input type="hidden" name="stockist_visit_time[]" value="${row.visit_time}"></td>
            <td>${row.primary_order||0}<input type="hidden" name="primary_order[]" value="${row.primary_order}"></td>
            <td><span class="badge badge-soft">${row.products.map(p=>p.name).join(', ')||'-'}</span>${productsHidden}</td>
            <td>${row.remarks||'-'}<input type="hidden" name="stockist_remarks[]" value="${row.remarks}"></td>
            <td>${delBtn(`stockistRows.splice(${i},1);renderStockistTable()`)}</td>
        </tr>`;
        }).join('');
    }

    document.getElementById('addStockistBtn').onclick = function() {
        const sel = document.getElementById('stockistSelect');
        if (!sel.value) {
            alert('Select Stockist');
            return;
        }
        const products = getCheckedProducts('stockist-product-cb');
        stockistRows.push({
            stockist_id: sel.value,
            stockist_name: sel.options[sel.selectedIndex].text,
            visit_time: document.getElementById('stockistVisitTime').value,
            primary_order: document.getElementById('stockistPrimaryOrder').value,
            products: products,
            remarks: document.getElementById('stockistRemarks').value
        });
        renderStockistTable();
        sel.value = '';
        ['stockistVisitTime', 'stockistPrimaryOrder', 'stockistRemarks'].forEach(id => document.getElementById(id).value = '');
        clearChecked('stockist-product-cb');
        document.getElementById('stockistProductLabel').textContent = 'Select Products';
    };

    renderStockistTable();

    /* ============================================================
       REVIEW
    ============================================================ */
    function renderReview() {
        const form = document.getElementById('dcrForm');
        const selText = id => {
            const el = document.getElementById(id);
            return el.options[el.selectedIndex]?.text || '-';
        };
        const wwChecked = Array.from(document.querySelectorAll('#workingWithCheckboxList input:checked'))
            .map(c => c.closest('label').textContent.trim()).join(', ') || '-';

        document.getElementById('reviewGeneralInfo').innerHTML = `
        <div class="col-md-3 mb-2"><strong>Visit Date:</strong><br>${form.visit_date.value||'-'}</div>
        <div class="col-md-3 mb-2"><strong>HQ:</strong><br>${selText('hqSelect')}</div>
        <div class="col-md-3 mb-2"><strong>Area:</strong><br>${selText('areaSelect')}</div>
        <div class="col-md-3 mb-2"><strong>Working Type:</strong><br>${selText('workingType')}</div>
        <div class="col-md-3 mb-2"><strong>Working With:</strong><br>${wwChecked}</div>`;

        document.getElementById('reviewAchievementRemarks').innerHTML = `
        <div class="col-md-6 mb-2"><strong>Day Remarks:</strong><br>${form.remarks.value||'-'}</div>
        <div class="col-md-6 mb-2"><strong>Achievement:</strong><br>${form.achievement.value||'-'}</div>`;

        document.getElementById('reviewDoctorList').innerHTML = doctorRows.length ?
            doctorRows.map((r, i) => `<tr>
            <td>${i+1}</td><td>${r.doctor_name}</td><td>${r.visit_time||'-'}</td>
            <td>${r.samples.map(s=>s.product_name+'('+s.qty+')').join(', ')||'-'}</td>
            <td>${r.gifts.map(g=>g.gift_name+'('+g.qty+')').join(', ')||'-'}</td>
            <td>${r.remarks||'-'}</td></tr>`).join('') :
            emptyRow(6, 'No doctor calls added.');

        document.getElementById('reviewChemistList').innerHTML = chemistRows.length ?
            chemistRows.map((r, i) => `<tr>
            <td>${i+1}</td><td>${r.chemist_name}</td><td>${r.pob||0}</td>
            <td>${r.booking_value||0}</td>
            <td>${r.products.map(p=>p.name).join(', ')||'-'}</td>
            <td>${r.remarks||'-'}</td></tr>`).join('') :
            emptyRow(6, 'No chemist calls added.');

        document.getElementById('reviewStockistList').innerHTML = stockistRows.length ?
            stockistRows.map((r, i) => `<tr>
            <td>${i+1}</td><td>${r.stockist_name}</td><td>${r.visit_time||'-'}</td>
            <td>${r.primary_order||0}</td>
            <td>${r.products.map(p=>p.name).join(', ')||'-'}</td>
            <td>${r.remarks||'-'}</td></tr>`).join('') :
            emptyRow(6, 'No stockist calls added.');
    }

    /* ============================================================
       GPS — Start on Next (step 1), End on Submit
    ============================================================ */
    function getStartLocation(callback) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                document.querySelector("[name=start_latitude]").value = pos.coords.latitude;
                document.querySelector("[name=start_longitude]").value = pos.coords.longitude;
                callback();
            },
            function() {
                alert("Location access is required. Please allow location.");
            }
        );
    }

    document.getElementById("dcrForm").addEventListener("submit", function(e) {
        e.preventDefault();
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById("end_latitude").value = pos.coords.latitude;
            document.getElementById("end_longitude").value = pos.coords.longitude;
            e.target.submit();
        });
    });
</script>