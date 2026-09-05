<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['rbm']);

if (!isset($_GET['dcr_id']) || !is_numeric($_GET['dcr_id'])) {
    $_SESSION['error'] = "Invalid DCR.";
    header("Location:dcr-list.php");
    exit;
}

$dcr_id = (int)$_GET['dcr_id'];

$stmt = mysqli_prepare($conn, "
SELECT
d.*,
a.area_name
FROM tbl_dcr d
LEFT JOIN tbl_areas a ON a.id=d.area_id
WHERE d.id=?
");

mysqli_stmt_bind_param($stmt, "i", $dcr_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "DCR Not Found";
    header("Location:dcr-list.php");
    exit;
}

$dcr = mysqli_fetch_assoc($result);

/* Doctor Count */

$count = mysqli_query($conn, "
SELECT COUNT(*) total
FROM tbl_dcr_doctor_calls
WHERE dcr_id=" . $dcr_id);

$totalDoctor = mysqli_fetch_assoc($count)['total'];

?>

<div class="card">

    <div class="card-header bg-primary text-white">

        <div class="row">

            <div class="col-md-3">

                <b>DCR No</b><br>

                <?php echo $dcr['dcr_no']; ?>

            </div>

            <div class="col-md-3">

                <b>Date</b><br>

                <?php echo date('d-m-Y', strtotime($dcr['visit_date'])); ?>

            </div>

            <div class="col-md-3">

                <b>Working Area</b><br>

                <?php echo $dcr['area_name']; ?>

            </div>

            <div class="col-md-3 text-end">

                <h5>

                    Doctor Call

                    <span class="badge bg-warning">

                        <?php echo $totalDoctor; ?>/12

                    </span>

                </h5>

            </div>

        </div>

    </div>

    <div class="card-body">

        <form action="dcr-doctor-process.php" method="POST">

            <input type="hidden" name="dcr_id" value="<?php echo $dcr_id; ?>">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label>Doctor <span class="text-danger">*</span></label>

                    <select
                        name="doctor_id"
                        id="doctor_id"
                        class="form-select"
                        required>

                        <option value="">Select Doctor</option>

                        <?php

                        $doctor = mysqli_query($conn, "
                        SELECT
                        id,
                        doctor_name
                        FROM tbl_doctors
                        WHERE area_id=" . $dcr['area_id'] . "
                        AND status=1
                        ORDER BY doctor_name
                        ");

                        while ($row = mysqli_fetch_assoc($doctor)) {

                        ?>

                            <option value="<?php echo $row['id']; ?>">

                                <?php echo $row['doctor_name']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-4 mb-3">

                    <label>Clinic</label>

                    <select
                        name="clinic_id"
                        id="clinic_id"
                        class="form-select">

                        <option value="">Select Clinic</option>

                    </select>

                </div>

                <div class="col-md-4 mb-3">

                    <label>Visit Time</label>

                    <input
                        type="time"
                        name="visit_time"
                        class="form-control"
                        value="<?php echo date('H:i'); ?>"
                        required>

                </div>

                <div class="col-md-4 mb-3">

                    <label>Call Type</label>

                    <select
                        name="call_type_id"
                        class="form-select"
                        required>

                        <option value="">Select Call Type</option>

                        <?php

                        $call = mysqli_query($conn, "
                        SELECT
                        id,
                        call_type_name
                        FROM tbl_dcr_doctor_calls
                        WHERE status=1
                        ORDER BY call_type_name
                        ");

                        while ($c = mysqli_fetch_assoc($call)) {

                        ?>

                            <option value="<?php echo $c['id']; ?>">

                                <?php echo $c['call_type_name']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-8 mb-3">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="2"></textarea>

                </div>

                <div class="col-md-12">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fa fa-plus"></i>

                        Add Doctor Visit

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>