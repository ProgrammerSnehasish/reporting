<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$rbm_id = $_SESSION['user_id'];

$sql = "

SELECT

d.*,
a.area_name,
wt.working_type_name,

emp.first_name,
emp.last_name,
emp.employee_code,

COUNT(DISTINCT dc.id) doctor_count,
COUNT(DISTINCT cc.id) chemist_count,
COUNT(DISTINCT sc.id) stockist_count

FROM tbl_dcr d

INNER JOIN tbl_user_mapping map_abm
ON map_abm.employee_user_id = d.mr_id

INNER JOIN tbl_user_mapping map_rbm
ON map_rbm.employee_user_id = map_abm.manager_user_id

LEFT JOIN tbl_users u
ON u.id = d.mr_id

LEFT JOIN tbl_employees emp
ON emp.id = u.employee_id

LEFT JOIN tbl_areas a
ON a.id = d.area_id

LEFT JOIN tbl_working_types wt
ON wt.id = d.working_type_id

LEFT JOIN tbl_dcr_doctor_calls dc
ON dc.dcr_id = d.id

LEFT JOIN tbl_dcr_chemist_calls cc
ON cc.dcr_id = d.id

LEFT JOIN tbl_dcr_stockist_calls sc
ON sc.dcr_id = d.id

WHERE
map_rbm.manager_user_id = ?

GROUP BY d.id

ORDER BY d.visit_date DESC,d.id DESC

";

$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"i",$rbm_id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);

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
                            <h4 class="mb-sm-0">Dcr List</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Dcr List</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->



                <div class="row">
                    <div class="col-12">
                        <div class="card">
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

                                <h4 class="card-title">Dcr List</h4>


                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>Sl.</th>
                                            <th>DCR No</th>
                                            <th>MR</th>
                                            <th>Employee Code</th>
                                            <th>Visit Date</th>
                                            <th>Working Area</th>
                                            <th>Working Type</th>
                                            <!-- <th>Doctor,Chemist and Stockist</th> -->
                                            <th>Status</th>
                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $sl = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= htmlspecialchars($row['dcr_no']); ?></td>

                                                <td>

                                                    <b>

                                                        <?php echo $row['first_name'] . " " . $row['last_name']; ?>

                                                    </b>

                                                </td>

                                                <td>

                                                    <?php echo $row['employee_code']; ?>

                                                </td>

                                                <td><?= date('d-m-Y', strtotime($row['visit_date'])); ?></td>

                                                <td><?= htmlspecialchars($row['area_name']); ?></td>

                                                <td><?= htmlspecialchars($row['working_type_name']); ?></td>

                                                <!-- <td>
                                                    <span class="badge bg-primary">
                                                        D: <?= $row['doctor_count']; ?>
                                                    </span>
                                                    <span class="badge bg-info">
                                                        C: <?= $row['chemist_count']; ?>
                                                    </span>
                                                    <span class="badge bg-warning text-dark">
                                                        S: <?= $row['stockist_count']; ?>
                                                    </span>
                                                </td> -->

                                                <td>

                                                    <?php

                                                    switch ($row['current_level']) {

                                                        case 'ABM':
                                                            echo '<span class="badge bg-warning">Pending ABM</span>';
                                                            break;

                                                        case 'RBM':
                                                            echo '<span class="badge bg-info">Pending RBM</span>';
                                                            break;

                                                        case 'ZSM':
                                                            echo '<span class="badge bg-primary">Pending ZSM</span>';
                                                            break;

                                                        case 'NSM':
                                                            echo '<span class="badge bg-secondary">Pending NSM</span>';
                                                            break;

                                                        case 'Completed':
                                                            echo '<span class="badge bg-success">Approved</span>';
                                                            break;

                                                        case 'Rejected':
                                                            echo '<span class="badge bg-danger">Rejected</span>';
                                                            break;
                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <a href="dcr-view.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>


                                                    <?php if(
														$_SESSION['role']=='rbm'
														&& $row['current_level']=='RBM'
													){ ?>

													<a href="dcr-approve.php?id=<?= $row['id']; ?>"
													   class="btn btn-success btn-sm"
													   onclick="return confirm('Approve this DCR?')">

														<i class="ri-check-line"></i>
														Approve

													</a>

													<?php } ?>



                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

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