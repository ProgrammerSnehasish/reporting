<?php

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

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Dashboard</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Upcube</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Row 1 -->
                <div class="row">

                    <!-- Today's Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Today's Revenue</p>
                                        <h4 class="mb-2">₹1,25,500</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>12.5%
                                            </span>from yesterday
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-primary rounded-3">
                                            <i class="ri-money-rupee-circle-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Monthly Revenue</p>
                                        <h4 class="mb-2">₹32,45,000</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>8.7%
                                            </span>from last month
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-line-chart-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Patients -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Total Patients</p>
                                        <h4 class="mb-2">8,246</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>5.2%
                                            </span>new registrations
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-info rounded-3">
                                            <i class="ri-heart-pulse-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Treatment -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Active Treatment</p>
                                        <h4 class="mb-2">1,278</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>3.8%
                                            </span>currently ongoing
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-danger rounded-3">
                                            <i class="ri-hospital-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row 1 -->

                <!-- Row 2 -->
                <div class="row">

                    <!-- New Leads -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">New Leads</p>
                                        <h4 class="mb-2">328</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>15.6%
                                            </span>from last week
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-primary rounded-3">
                                            <i class="ri-user-add-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Conversion -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Lead Conversion</p>
                                        <h4 class="mb-2">72%</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>4.8%
                                            </span>conversion rate
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-pie-chart-2-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Follow Up -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Pending Follow-up</p>
                                        <h4 class="mb-2">94</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-warning fw-bold font-size-12 me-2">
                                                <i class="ri-time-line me-1 align-middle"></i>Need Attention
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-warning rounded-3">
                                            <i class="ri-calendar-check-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Pending Payments</p>
                                        <h4 class="mb-2">₹8,45,300</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-danger fw-bold font-size-12 me-2">
                                                <i class="ri-error-warning-line me-1 align-middle"></i>18 Invoices Due
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-danger rounded-3">
                                            <i class="ri-bank-card-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row 2 -->

                <!-- Row 3 -->
                <div class="row">

                    <!-- Inventory Alerts -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Inventory Alerts</p>
                                        <h4 class="mb-2">42</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-warning fw-bold font-size-12 me-2">
                                                <i class="ri-alert-line me-1 align-middle"></i>Needs Attention
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-warning rounded-3">
                                            <i class="ri-alarm-warning-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Medicines -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Low Stock Medicines</p>
                                        <h4 class="mb-2">18</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-danger fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-down-line me-1 align-middle"></i>Restock Required
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-danger rounded-3">
                                            <i class="ri-capsule-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expiring Medicines -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Expiring Medicines</p>
                                        <h4 class="mb-2">11</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-warning fw-bold font-size-12 me-2">
                                                <i class="ri-time-line me-1 align-middle"></i>Expire in 30 Days
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-info rounded-3">
                                            <i class="ri-medicine-bottle-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Medical Representatives -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Active MR</p>
                                        <h4 class="mb-2">156</h4>
                                        <p class="text-muted mb-0">
                                            <span class="text-success fw-bold font-size-12 me-2">
                                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>24 On Field Today
                                            </span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-user-star-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row 3 -->

                <!-- Row 4 -->
                <div class="row">

                    <!-- Top Franchise -->
                    <div class="col-lg-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-building-4-line me-3"></i>
                                    Top Franchise
                                </h5>

                                <h3 class="text-white mb-2">Kolkata Central</h3>

                                <p class="card-text mb-2">
                                    Monthly Revenue : <strong>₹12,45,600</strong>
                                </p>

                                <p class="card-text mb-0">
                                    426 Patients • 94% Performance
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Medical Representative -->
                    <div class="col-lg-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-user-star-line me-3"></i>
                                    Top Medical Representative
                                </h5>

                                <h3 class="text-white mb-2">Rahul Sharma</h3>

                                <p class="card-text mb-2">
                                    Doctor Visits : <strong>186</strong>
                                </p>

                                <p class="card-text mb-0">
                                    58 Leads • 46 Conversions
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Medicine -->
                    <div class="col-lg-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-capsule-line me-3"></i>
                                    Top Selling Medicine
                                </h5>

                                <h3 class="text-white mb-2">Pain Relief Kit</h3>

                                <p class="card-text mb-2">
                                    Units Sold : <strong>2,450</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Revenue : ₹8,75,000
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row 4 -->

                <!-- Row 5 -->
                <div class="row">

                    <!-- Active Medical Representatives -->
                    <div class="col-lg-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-user-star-line me-3"></i>
                                    Active Medical Representatives
                                </h5>

                                <h3 class="text-white mb-2">156 Active MR</h3>

                                <p class="card-text mb-2">
                                    Today's Field Visit : <strong>128</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Present : 148 &nbsp; | &nbsp; Leave : 8
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Zone Wise Dashboard -->
                    <div class="col-lg-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-map-pin-line me-3"></i>
                                    Zone Wise Dashboard
                                </h5>

                                <h3 class="text-white mb-2">12 Active Zones</h3>

                                <p class="card-text mb-2">
                                    Best Zone : <strong>North Zone</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Revenue : ₹18.45 Lakh
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Wise Dashboard -->
                    <div class="col-lg-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="mb-4 text-white">
                                    <i class="ri-hospital-line me-3"></i>
                                    Branch Wise Dashboard
                                </h5>

                                <h3 class="text-white mb-2">25 Active Branches</h3>

                                <p class="card-text mb-2">
                                    Best Branch : <strong>Kolkata Central</strong>
                                </p>

                                <p class="card-text mb-0">
                                    Monthly Revenue : ₹12.45 Lakh
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row 5 -->



                <div class="row">
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                            <a class="dropdown-item" href="#">Download Report</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Revenue Analytics</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">25,117</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>2.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Marketplace</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$34,856</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Week</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$18,225</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.7 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Month</p>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="area_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown">
                                        <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">This Years<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Today</a>
                                            <a class="dropdown-item" href="#">Last Week</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">This Year</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Patient Growth</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>17,493</h5>
                                                <p class="text-muted text-truncate mb-0">Marketplace</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>$44,960</h5>
                                                <p class="text-muted text-truncate mb-0">Last Week</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div>
                                                <h5>$29,142</h5>
                                                <p class="text-muted text-truncate mb-0">Last Month</p>
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="column_line_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                            <a class="dropdown-item" href="#">Download Report</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Lead Conversion</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">25,117</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>2.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Marketplace</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$34,856</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Week</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$18,225</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.7 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Month</p>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="area_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown">
                                        <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">This Years<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Today</a>
                                            <a class="dropdown-item" href="#">Last Week</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">This Year</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Franchise Performance</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>17,493</h5>
                                                <p class="text-muted text-truncate mb-0">Marketplace</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>$44,960</h5>
                                                <p class="text-muted text-truncate mb-0">Last Week</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div>
                                                <h5>$29,142</h5>
                                                <p class="text-muted text-truncate mb-0">Last Month</p>
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="column_line_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>

                <div class="row">
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                            <a class="dropdown-item" href="#">Download Report</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Mr Performance</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">25,117</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>2.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Marketplace</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$34,856</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Week</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$18,225</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.7 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Month</p>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="area_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown">
                                        <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">This Years<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Today</a>
                                            <a class="dropdown-item" href="#">Last Week</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">This Year</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Sales by Product</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>17,493</h5>
                                                <p class="text-muted text-truncate mb-0">Marketplace</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div>
                                                <h5>$44,960</h5>
                                                <p class="text-muted text-truncate mb-0">Last Week</p>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div>
                                                <h5>$29,142</h5>
                                                <p class="text-muted text-truncate mb-0">Last Month</p>
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="column_line_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>

                <div class="row">
                    <div class="col-xl-12">

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="float-end d-none d-md-inline-block">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                            <a class="dropdown-item" href="#">Download Report</a>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">Top Selling Medicine</h4>

                                <div class="text-center pt-3">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">25,117</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>2.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Marketplace</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4 mb-3 mb-sm-0">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$34,856</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.2 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Week</p>
                                        </div><!-- end col -->
                                        <div class="col-sm-4">
                                            <div class="d-inline-flex">
                                                <h5 class="me-2">$18,225</h5>
                                                <div class="text-success font-size-12">
                                                    <i class="mdi mdi-menu-up font-size-14"> </i>1.7 %
                                                </div>
                                            </div>
                                            <p class="text-muted text-truncate mb-0">Last Month</p>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                            </div>
                            <div class="card-body py-0 px-2">
                                <div id="area_chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card -->
                    </div>

                </div>

                <!-- end row -->
            </div>

        </div>
        <!-- End Page-content -->

        <?php include('./includes/footer.php'); ?>

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->
<?php include('./includes/scripts.php'); ?>