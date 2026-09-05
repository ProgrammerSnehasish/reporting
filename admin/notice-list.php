<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['admin']);

$result = mysqli_query($conn, "

SELECT

n.*,

CONCAT(c.first_name,' ',c.last_name) AS created_by_name,

CONCAT(u.first_name,' ',u.last_name) AS updated_by_name

FROM tbl_notices n

LEFT JOIN tbl_users cu
ON cu.id=n.created_by

LEFT JOIN tbl_employees c
ON c.id=cu.employee_id

LEFT JOIN tbl_users uu
ON uu.id=n.updated_by

LEFT JOIN tbl_employees u
ON u.id=uu.employee_id

ORDER BY n.id DESC

");

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
                            <h4 class="mb-sm-0">Notice List</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Notice List</li>
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

                                <h4 class="card-title">Notice List</h4>


                                <table class="table table-bordered table-hover align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th>#</th>

                                            <th>Title</th>

                                            <th>Message</th>

                                            <th>Status</th>

                                            <th width="160">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $i = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $i++; ?></td>

                                                <td><?= $row['title']; ?></td>

                                                <td><?= $row['message']; ?></td>

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



                                                    <a

                                                        href="notice-edit.php?id=<?= $row['id']; ?>"

                                                        class="btn btn-sm btn-warning">

                                                        Edit <i class="ri-pencil-line"></i>

                                                    </a>

                                                    <!-- <a

                                                        href="notice-delete.php?id=<?= $row['id']; ?>"

                                                        class="btn btn-sm btn-danger"

                                                        onclick="return confirm('Delete this notice?')">

                                                        <i class="ri-delete-bin-line"></i>

                                                    </a> -->

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