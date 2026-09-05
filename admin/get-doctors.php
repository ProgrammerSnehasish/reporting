<?php

require_once "../config/database.php";

if (isset($_POST['area_id'])) {

    $area_id = $_POST['area_id'];

    $sql = "SELECT id, doctor_name
            FROM tbl_doctors
            WHERE area_id = ?
            AND status = 1
            ORDER BY doctor_name";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $area_id);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        echo '<option value="">-- Select Doctor --</option>';

        while ($row = mysqli_fetch_assoc($result)) {

            echo '<option value="' . $row['id'] . '">'
                . htmlspecialchars($row['doctor_name']) .
                '</option>';
        }
    } else {

        echo '<option value="">No Doctor Available</option>';
    }
}
