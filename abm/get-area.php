<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";

$hq_id = (int) $_POST['hq_id'];

$res = mysqli_query($conn, "
    SELECT id, area_name, area_type, km_from_hq
    FROM tbl_areas
    WHERE hq_id = '$hq_id'
    AND status = 1
    ORDER BY area_name
");

while ($row = mysqli_fetch_assoc($res)) {
    echo '<option value="' . $row['id'] . '" data-km="' . $row['km_from_hq'] . '">'
        . htmlspecialchars($row['area_name'])
        . ' (' . $row['area_type'] . ')'
        . '</option>';
}
