<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";

mysqli_begin_transaction($conn);

try {

    $mr_id      = $_SESSION['user_id'];
    $created_by = $_SESSION['user_id'];
    $hq_id = (int)$_POST['hq_id'];

    $start_latitude  = mysqli_real_escape_string($conn, $_POST['start_latitude']  ?? '');
    $start_longitude = mysqli_real_escape_string($conn, $_POST['start_longitude'] ?? '');
    $end_latitude    = mysqli_real_escape_string($conn, $_POST['end_latitude']    ?? '');
    $end_longitude   = mysqli_real_escape_string($conn, $_POST['end_longitude']   ?? '');

    $area_id = mysqli_real_escape_string($conn, $_POST['area_id']);

    $area_res = mysqli_query($conn, "
        SELECT area_type, km_from_hq
        FROM tbl_areas
        WHERE id = '$area_id'
        LIMIT 1
    ");
    $area_row       = mysqli_fetch_assoc($area_res);
    $total_km       = (float)($area_row['km_from_hq'] ?? 0);
    $per_km_rate    = PER_KM_RATE;
    $travel_expense = 2 * $total_km * $per_km_rate;

    $visit_date      = mysqli_real_escape_string($conn, $_POST['visit_date']);
    $dcr_no          = "DCR" . date('dmY', strtotime($visit_date)) . rand(1000, 9999);
    $working_type_id = mysqli_real_escape_string($conn, $_POST['working_type_id']);
    $remarks         = mysqli_real_escape_string($conn, $_POST['remarks']     ?? '');
    $achievement     = mysqli_real_escape_string($conn, $_POST['achievement'] ?? '');

    $working_with_user_id = "";
    if (!empty($_POST['working_with_user_id'])) {
        $raw = $_POST['working_with_user_id'];
        if (count($raw) == 1 && $raw[0] == 0) {
            $working_with_user_id = "0";
        } else {
            $working_with_user_id = implode(",", array_map('intval', $raw));
        }
    }

    $doctor_count   = isset($_POST['doctor_id'])   ? count($_POST['doctor_id'])   : 0;
    $chemist_count  = isset($_POST['chemist_id'])  ? count($_POST['chemist_id'])  : 0;
    $stockist_count = isset($_POST['stockist_id']) ? count($_POST['stockist_id']) : 0;

    $ip     = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
    $device = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);


    //================ DUPLICATE DCR CHECK =================//

    $check = mysqli_query($conn, "
        SELECT id FROM tbl_dcr
        WHERE mr_id = '$mr_id'
        AND visit_date = '$visit_date'
        LIMIT 1
    ");
    if (!$check) throw new Exception(mysqli_error($conn));

    if (mysqli_num_rows($check) > 0) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "DCR already submitted for this date.";
        header("Location: dcr-add.php");
        exit;
    }


    //================ COLLECT TOTALS (for stock ADD only, no check) =================//

    $sample_product_ids = $_POST['sample_product_ids'] ?? [];
    $sample_qtys        = $_POST['sample_qty']         ?? [];

    $product_totals = [];
    foreach ($sample_product_ids as $doctor_index => $products) {
        foreach ($products as $k => $product_id) {
            $pid = (int)$product_id;
            $product_totals[$pid] = ($product_totals[$pid] ?? 0) + (int)($sample_qtys[$doctor_index][$k] ?? 0);
        }
    }

    $gift_ids  = $_POST['gift_ids']  ?? [];
    $gift_qtys = $_POST['gift_qty']  ?? [];

    $gift_totals = [];
    foreach ($gift_ids as $doctor_index => $gifts) {
        foreach ($gifts as $k => $gift_id) {
            $gid = (int)$gift_id;
            $gift_totals[$gid] = ($gift_totals[$gid] ?? 0) + (int)($gift_qtys[$doctor_index][$k] ?? 0);
        }
    }


    //================ INSERT MASTER DCR =================//

    $sql = "
        INSERT INTO tbl_dcr
        (
            dcr_no, mr_id, area_id,
            hq_id, visit_date,
            working_type_id, working_with_user_id,
            total_km, travel_expense,
            start_latitude, start_longitude, end_latitude, end_longitude,
            doctor_call_count, chemist_call_count, stockist_call_count,
            remarks, achievement, submitted_at, submission_ip,
            submission_device, created_by
        )
        VALUES
        (
            '$dcr_no', '$mr_id', '$area_id', '$hq_id', '$visit_date',
            '$working_type_id', '$working_with_user_id',
            '$total_km', '$travel_expense', '$start_latitude', '$start_longitude',
            '$end_latitude', '$end_longitude', '$doctor_count', '$chemist_count', '$stockist_count',
            '$remarks', '$achievement', NOW(), '$ip', '$device', '$created_by'
        )
    ";

    if (!mysqli_query($conn, $sql)) {
        if (mysqli_errno($conn) == 1062) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "DCR already submitted for this date.";
            header("Location: dcr-add.php");
            exit;
        }
        throw new Exception(mysqli_error($conn));
    }

    $dcr_id = mysqli_insert_id($conn);

    // Send DCR to ABM
    mysqli_query($conn, "
    UPDATE tbl_dcr
    SET
    status='Submitted',
    current_level='ABM'
    WHERE id='$dcr_id'
    ");

    if (mysqli_error($conn)) {
        throw new Exception(mysqli_error($conn));
    }


    //================ INSERT EXPENSE =================//

    $da_expense    = (float)($_POST['da_expense']    ?? 0);
    $hotel_expense = (float)($_POST['hotel_expense'] ?? 0);
    $food_expense  = (float)($_POST['food_expense']  ?? 0);
    $other_expense = (float)($_POST['other_expense'] ?? 0);

    $total_expense = $travel_expense + $da_expense + $hotel_expense + $food_expense + $other_expense;

    $expense_remark = mysqli_real_escape_string($conn, $_POST['expense_remarks'] ?? '');

    $sql = "
        INSERT INTO tbl_dcr_expenses
        (
            dcr_id, mr_id, area_id, hq_id,
            total_km, per_km_rate, travel_expense,
            da_expense, hotel_expense, food_expense, other_expense,
            total_expense, remarks, status
        )
        VALUES
        (
            '$dcr_id', '$mr_id', '$area_id', '$hq_id',
            '$total_km', '$per_km_rate', '$travel_expense',
            '$da_expense', '$hotel_expense', '$food_expense', '$other_expense',
            '$total_expense', '$expense_remark', 'Submitted'
        )
    ";
    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));


    //================ INSERT DOCTOR CALLS =================//

    if (!empty($_POST['doctor_id'])) {

        foreach ($_POST['doctor_id'] as $key => $doctor_id) {

            $doctor_id  = mysqli_real_escape_string($conn, $doctor_id);
            $visit_time = mysqli_real_escape_string($conn, $_POST['doctor_visit_time'][$key] ?? '');
            $remark     = mysqli_real_escape_string($conn, $_POST['doctor_remarks'][$key]    ?? '');
            $vt_val     = !empty($visit_time) ? "'$visit_time'" : "NULL";

            $sql = "
                INSERT INTO tbl_dcr_doctor_calls
                (dcr_id, doctor_id, visit_time, remarks, created_by)
                VALUES ('$dcr_id', '$doctor_id', $vt_val, '$remark', '$created_by')
            ";
            if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));

            $doctor_call_id = mysqli_insert_id($conn);

            // Samples
            if (!empty($_POST['sample_product_ids'][$key])) {
                foreach ($_POST['sample_product_ids'][$key] as $k => $product_id) {
                    $product_id = mysqli_real_escape_string($conn, $product_id);
                    $qty        = (int)($_POST['sample_qty'][$key][$k] ?? 0);
                    $sql = "
                        INSERT INTO tbl_dcr_doctor_products
                        (doctor_call_id, product_id, quantity, created_by)
                        VALUES ('$doctor_call_id', '$product_id', '$qty', '$created_by')
                    ";
                    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
                }
            }

            // Gifts
            if (!empty($_POST['gift_ids'][$key])) {
                foreach ($_POST['gift_ids'][$key] as $k => $gift_id) {
                    $gift_id = mysqli_real_escape_string($conn, $gift_id);
                    $qty     = (int)($_POST['gift_qty'][$key][$k] ?? 0);
                    $sql = "
                        INSERT INTO tbl_dcr_gifts
                        (dcr_doctor_call_id, gift_id, quantity, created_by)
                        VALUES ('$doctor_call_id', '$gift_id', '$qty', '$created_by')
                    ";
                    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
                }
            }

            // Pain Products
            if (!empty($_POST['pain_product_ids'][$key])) {
                foreach ($_POST['pain_product_ids'][$key] as $k => $pain_id) {
                    $pain_id = (int)$pain_id;
                    $qty     = (int)($_POST['pain_qty'][$key][$k] ?? 0);
                    $sql = "
                        INSERT INTO tbl_dcr_doctor_call_pain_management
                        (doctor_call_id, pain_product_id, quantity, created_by, created_at)
                        VALUES ('$doctor_call_id', '$pain_id', '$qty', '$created_by', NOW())
                    ";
                    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
                }
            }
        }
    }


    //================ INSERT CHEMIST CALLS =================//

    if (!empty($_POST['chemist_id'])) {

        foreach ($_POST['chemist_id'] as $key => $chemist_id) {

            $chemist_id    = mysqli_real_escape_string($conn, $chemist_id);
            $visit_time    = mysqli_real_escape_string($conn, $_POST['chemist_visit_time'][$key]    ?? '');
            $pob           = (float)($_POST['chemist_pob'][$key]           ?? 0);
            $booking_value = (float)($_POST['chemist_booking_value'][$key] ?? 0);
            $remark        = mysqli_real_escape_string($conn, $_POST['chemist_remarks'][$key]       ?? '');
            $vt_val        = !empty($visit_time) ? "'$visit_time'" : "NULL";

            $sql = "
                INSERT INTO tbl_dcr_chemist_calls
                (dcr_id, chemist_id, visit_time, pob, booking_value, remarks, created_by)
                VALUES ('$dcr_id', '$chemist_id', $vt_val, '$pob', '$booking_value', '$remark', '$created_by')
            ";
            if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));

            $chemist_call_id = mysqli_insert_id($conn);

            if (!empty($_POST['chemist_products'][$key])) {
                foreach ($_POST['chemist_products'][$key] as $product_id) {
                    $product_id = mysqli_real_escape_string($conn, $product_id);
                    $sql = "
                        INSERT INTO tbl_dcr_chemist_products
                        (chemist_call_id, product_id, created_by)
                        VALUES ('$chemist_call_id', '$product_id', '$created_by')
                    ";
                    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
                }
            }
        }
    }


    //================ INSERT STOCKIST CALLS =================//

    if (!empty($_POST['stockist_id'])) {

        foreach ($_POST['stockist_id'] as $key => $stockist_id) {

            $stockist_id   = mysqli_real_escape_string($conn, $stockist_id);
            $visit_time    = mysqli_real_escape_string($conn, $_POST['stockist_visit_time'][$key] ?? '');
            $primary_order = (float)($_POST['primary_order'][$key] ?? 0);
            $remark        = mysqli_real_escape_string($conn, $_POST['stockist_remarks'][$key]    ?? '');
            $vt_val        = !empty($visit_time) ? "'$visit_time'" : "NULL";

            $sql = "
                INSERT INTO tbl_dcr_stockist_calls
                (dcr_id, stockist_id, visit_time, primary_order, remarks, created_by)
                VALUES ('$dcr_id', '$stockist_id', $vt_val, '$primary_order', '$remark', '$created_by')
            ";
            if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));

            $stockist_call_id = mysqli_insert_id($conn);

            if (!empty($_POST['stockist_products'][$key])) {
                foreach ($_POST['stockist_products'][$key] as $product_id) {
                    $product_id = mysqli_real_escape_string($conn, $product_id);
                    $sql = "
                        INSERT INTO tbl_dcr_stockist_products
                        (stockist_call_id, product_id, created_by)
                        VALUES ('$stockist_call_id', '$product_id', '$created_by')
                    ";
                    if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
                }
            }
        }
    }


    //================ ADD STOCK (no check, no deduction — ADD quantity to DB) =================//

    // foreach ($product_totals as $product_id => $total_qty) {
    //     if (!mysqli_query($conn, "UPDATE tbl_products SET stock_quantity = stock_quantity + $total_qty WHERE id = $product_id"))
    //         throw new Exception(mysqli_error($conn));
    // }

    // foreach ($gift_totals as $gift_id => $total_qty) {
    //     if (!mysqli_query($conn, "UPDATE tbl_gifts SET stock_quantity = stock_quantity + $total_qty WHERE id = $gift_id"))
    //         throw new Exception(mysqli_error($conn));
    // }


    //================ AUTO ATTENDANCE =================//

    $attendance_date = $visit_date;

    $emp_res     = mysqli_query($conn, "SELECT employee_id FROM tbl_users WHERE id = '$mr_id'");
    $emp_row     = mysqli_fetch_assoc($emp_res);
    $employee_id = $emp_row['employee_id'];

    if (!$employee_id) throw new Exception("Employee not found for this user.");

    $checkAttendance = mysqli_query($conn, "
        SELECT id, check_in_time FROM tbl_attendance
        WHERE employee_id = '$employee_id' AND attendance_date = '$attendance_date'
        LIMIT 1
    ");
    if (!$checkAttendance) throw new Exception(mysqli_error($conn));

    if (mysqli_num_rows($checkAttendance) > 0) {

        $att_row       = mysqli_fetch_assoc($checkAttendance);
        $attendance_id = $att_row['id'];

        $sql = "
            UPDATE tbl_attendance SET
                dcr_id            = '$dcr_id',
                attendance_status = 'Present',
                source            = 'DCR',
                check_in_time     = IFNULL(check_in_time, CURTIME()),
                remarks           = 'Auto updated from DCR'
            WHERE id = '$attendance_id'
        ";
        if (!mysqli_query($conn, $sql)) throw new Exception("Attendance update error: " . mysqli_error($conn));
    } else {

        $lat = !empty($start_latitude)  ? "'$start_latitude'"  : "NULL";
        $lng = !empty($start_longitude) ? "'$start_longitude'" : "NULL";

        $sql = "
            INSERT INTO tbl_attendance
            (employee_id, dcr_id, attendance_date, check_in_time, latitude, longitude,
             attendance_status, source, remarks, created_at)
            VALUES
            ('$employee_id', '$dcr_id', '$attendance_date', CURTIME(), $lat, $lng,
             'Present', 'DCR', 'Auto generated from DCR', NOW())
        ";
        if (!mysqli_query($conn, $sql)) throw new Exception("Attendance insert error: " . mysqli_error($conn));
    }


    //================ COMMIT =================//

    mysqli_commit($conn);

    $_SESSION['success'] = "DCR Submitted Successfully.";
    header("Location: dcr-add.php");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
    header("Location: dcr-add.php");
    exit;
}
