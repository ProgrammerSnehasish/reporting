<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$rbm_id = $_SESSION['user_id'];

$sql = "

SELECT

tp.id,
tp.month,
tp.year,
tp.status,
tp.current_level,
tp.created_at,
tp.submitted_at,

e.employee_code,
CONCAT(e.first_name,' ',e.last_name) AS employee_name,

COUNT(tpd.id) AS total_days

FROM tbl_tour_plans tp

INNER JOIN tbl_user_mapping map_abm
ON map_abm.employee_user_id = tp.mr_id

INNER JOIN tbl_user_mapping map_rbm
ON map_rbm.employee_user_id = map_abm.manager_user_id

INNER JOIN tbl_users u
ON u.id = tp.mr_id

INNER JOIN tbl_employees e
ON e.id = u.employee_id

LEFT JOIN tbl_tour_plan_details tpd
ON tpd.tour_plan_id = tp.id

WHERE
map_rbm.manager_user_id = ?
AND
(
    tp.current_level='RBM'
    OR tp.rbm_user_id = ?
)

GROUP BY

tp.id,
tp.month,
tp.year,
tp.status,
tp.current_level,
tp.created_at,
tp.submitted_at,
e.employee_code,
e.first_name,
e.last_name

ORDER BY
tp.year DESC,
tp.month DESC,
tp.id DESC

";

$stmt=mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt, "ii", $rbm_id, $rbm_id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);

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
                            <h4 class="mb-sm-0">Tour Plan History</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Tour Plan</li>
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
                                <h5 class="card-title mb-0">Tour Plan History</h5>
                                <a href="tour-plan-add.php" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line"></i> Add Tour Plan
                                </a>
                            </div>
                            <div class="card-body">

                                <table
                                    id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>
                                        <tr>
                                            <th>Sl. No</th>
                                            <th>Month &amp; Year</th>
                                            <th>MR</th>
                                            <th>Total Days</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <!-- <th>Submitted At</th> -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php
                                        $sl = 1;
                                        while ($row = mysqli_fetch_assoc($result)):
                                        ?>
                                            <tr>

                                                <td><?= $sl++ ?></td>

                                                <!-- FIX: merged Month + Year into one column for readability -->
                                                <td>
                                                    <?= date('F Y', mktime(0, 0, 0, $row['month'], 1, $row['year'])) ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['employee_code']) ?>
                                                    -
                                                    <?= htmlspecialchars($row['employee_name']) ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= (int)$row['total_days'] ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    switch($row['current_level']){

														case 'ABM':
															echo '<span class="badge bg-warning">Pending ABM</span>';
														break;

														case 'RBM':
															echo '<span class="badge bg-info">Pending RBM</span>';
														break;

														case 'ADMIN':
															echo '<span class="badge bg-primary">Pending Admin</span>';
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

                                                <!-- FIX: was showing created_at in both columns.
                                                     Now Created At and Submitted At are separate and correct. -->
                                                <td>
                                                    <?= !empty($row['created_at'])
                                                        ? date('d-m-Y H:i', strtotime($row['created_at']))
                                                        : '-' ?>
                                                </td>

                                                <!-- <td>
                                                    <?= !empty($row['submitted_at'])
                                                        ? date('d-m-Y H:i', strtotime($row['submitted_at']))
                                                        : '-' ?>
                                                </td> -->

                                                <td>
                                                    <a href="tour-plan-view.php?id=<?= (int)$row['id'] ?>"
                                                        class="btn btn-info btn-sm">
                                                        <i class="ri-eye-line"></i> View
                                                    </a>

                                                    
													
													<?php
													if(
														$_SESSION['role']=='rbm'
														&& $row['current_level']=='RBM'
													){
													?>

													<a href="tour-plan-approve.php?id=<?= $row['id'];?>"
													class="btn btn-success btn-sm"
													onclick="return confirm('Approve this Tour Plan?')">

													<i class="ri-check-line"></i>
													Approve

													</a>

													<?php } ?>
                                                </td>

                                            </tr>
                                        <?php endwhile; ?>

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