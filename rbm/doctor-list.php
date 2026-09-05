<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

$result = mysqli_query($conn, "

SELECT

d.*,

a.area_name,

hq.area_name AS hq_name,

dc.category_name,

dg.degree_name,

sp.specialization_name,

bp.potential_name

FROM tbl_doctors d

LEFT JOIN tbl_areas a
ON a.id=d.area_id

LEFT JOIN tbl_areas hq
ON hq.id=a.hq_id

LEFT JOIN tbl_doctor_categories dc
ON dc.id=d.category_id

LEFT JOIN tbl_degrees dg
ON dg.id=d.degree_id

LEFT JOIN tbl_specializations sp
ON sp.id=d.specialization_id

LEFT JOIN tbl_business_potentials bp
ON bp.id=d.business_potential_id

ORDER BY d.id DESC

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
                            <h4 class="mb-sm-0">Doctor List</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Doctor List</li>
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

                                <h4 class="card-title">Doctor List</h4>


                                <table id="datatable-buttons"
                                    class="table table-striped table-bordered dt-responsive nowrap"
                                    style="width:100%;">

                                    <thead>

                                        <tr>

                                            <th>SL</th>
                                            <th>Doctor Code</th>
                                            <th>Doctor Name</th>
                                            <th>HQ</th>
                                            <th>Area</th>
                                            <th>Mobile</th>
                                            <th>Category</th>
                                            <th>Degree</th>
                                            <th>Specialization</th>
                                            <!-- <th>Business Potential</th> -->
                                            <th>Status</th>
                                            <th width="150">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        $sl = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {

                                        ?>

                                            <tr>

                                                <td><?= $sl++; ?></td>

                                                <td><?= $row['doctor_code']; ?></td>

                                                <td><?= $row['doctor_name']; ?></td>

                                                <td><?= !empty($row['hq_name']) ? $row['hq_name'] : "-"; ?></td>

                                                <td><?= $row['area_name']; ?></td>

                                                <td>

                                                    <?php

                                                    if (!empty($row['mobile']) && !empty($row['alternate_mobile'])) {

                                                        echo $row['mobile'] . "<br><small class='text-muted'>Alt: " . $row['alternate_mobile'] . "</small>";
                                                    } elseif (!empty($row['mobile'])) {

                                                        echo $row['mobile'];
                                                    } elseif (!empty($row['alternate_mobile'])) {

                                                        echo $row['alternate_mobile'];
                                                    } else {

                                                        echo "-";
                                                    }

                                                    ?>

                                                </td>

                                               <td><?= $row['category_name']; ?></td>

                                                <td><?= $row['degree_name']; ?></td>

                                                <td><?= $row['specialization_name']; ?></td>

                                                <!-- <td><?= !empty($row['potential_name']) ? $row['potential_name'] : "-"; ?></td> -->

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

                                                    <a href="doctor-view.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-sm">

                                                        View

                                                    </a>

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