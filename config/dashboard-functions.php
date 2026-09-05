<?php

// dashboard-functions.php

function getSingleValue($conn, $sql)
{
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($res);
    return $row[0] ?? 0;
}

/*==============================
    TOTAL MRs UNDER MANAGER
================================*/
function totalMRs($conn, $managerId)
{
    $sql = "
    SELECT COUNT(*)
    FROM tbl_user_mapping
    WHERE manager_user_id='$managerId'
    ";

    return getSingleValue($conn,$sql);
}

/*==============================
    PENDING TOUR PLAN
================================*/
function pendingTourPlans($conn,$level,$managerId)
{

    if($level=='ABM')
    {
        $sql="

        SELECT COUNT(*)

        FROM tbl_tour_plans tp

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=tp.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND tp.current_level='ABM'

        ";
    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_tour_plans tp

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=tp.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND tp.current_level='RBM'

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
    PENDING DCR
================================*/
function pendingDCR($conn,$level,$managerId)
{

    if($level=='ABM')
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND d.current_level='ABM'

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND d.current_level='RBM'

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
    TODAY DOCTOR CALL
================================*/
function todayDoctorCalls($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_doctor_calls dc

        INNER JOIN tbl_dcr d
        ON d.id=dc.dcr_id

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_doctor_calls dc

        INNER JOIN tbl_dcr d
        ON d.id=dc.dcr_id

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
TODAY CHEMIST
================================*/
function todayChemistCalls($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_chemist_calls cc

        INNER JOIN tbl_dcr d
        ON d.id=cc.dcr_id

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_chemist_calls cc

        INNER JOIN tbl_dcr d
        ON d.id=cc.dcr_id

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
TODAY STOCKIST
================================*/
function todayStockistCalls($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_stockist_calls sc

        INNER JOIN tbl_dcr d
        ON d.id=sc.dcr_id

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr_stockist_calls sc

        INNER JOIN tbl_dcr d
        ON d.id=sc.dcr_id

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
TODAY WORKING MRs
================================*/
function todayWorkingMRs($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(DISTINCT d.mr_id)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(DISTINCT d.mr_id)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'
        AND d.visit_date=CURDATE()

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
MONTHLY DOCTOR CALLS
================================*/
function monthlyDoctorCalls($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $join="INNER JOIN tbl_user_mapping um ON um.employee_user_id=d.mr_id";
        $where="um.manager_user_id='$managerId'";

    }
    else
    {

        $join="INNER JOIN tbl_user_mapping a ON a.employee_user_id=d.mr_id
               INNER JOIN tbl_user_mapping b ON b.employee_user_id=a.manager_user_id";

        $where="b.manager_user_id='$managerId'";

    }

    $sql="

    SELECT COUNT(*)

    FROM tbl_dcr_doctor_calls dc

    INNER JOIN tbl_dcr d
    ON d.id=dc.dcr_id

    $join

    WHERE
    $where

    AND MONTH(d.visit_date)=MONTH(CURDATE())
    AND YEAR(d.visit_date)=YEAR(CURDATE())

    ";

    return getSingleValue($conn,$sql);

}

/*==============================
MONTHLY DCR
================================*/
function monthlyDCR($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=d.mr_id

        WHERE
        um.manager_user_id='$managerId'

        AND MONTH(d.visit_date)=MONTH(CURDATE())
        AND YEAR(d.visit_date)=YEAR(CURDATE())

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_dcr d

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=d.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'

        AND MONTH(d.visit_date)=MONTH(CURDATE())
        AND YEAR(d.visit_date)=YEAR(CURDATE())

        ";

    }

    return getSingleValue($conn,$sql);

}

/*==============================
MONTHLY TOUR PLAN
================================*/
function monthlyTourPlans($conn,$managerId,$level)
{

    if($level=="ABM")
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_tour_plans tp

        INNER JOIN tbl_user_mapping um
        ON um.employee_user_id=tp.mr_id

        WHERE
        um.manager_user_id='$managerId'

        AND tp.month=MONTH(CURDATE())
        AND tp.year=YEAR(CURDATE())

        ";

    }
    else
    {

        $sql="

        SELECT COUNT(*)

        FROM tbl_tour_plans tp

        INNER JOIN tbl_user_mapping a
        ON a.employee_user_id=tp.mr_id

        INNER JOIN tbl_user_mapping b
        ON b.employee_user_id=a.manager_user_id

        WHERE
        b.manager_user_id='$managerId'

        AND tp.month=MONTH(CURDATE())
        AND tp.year=YEAR(CURDATE())

        ";

    }

    return getSingleValue($conn,$sql);

}


function monthlyChemistCalls($conn, $managerId, $level)
{

    if ($level == "ABM") {

        $join = "INNER JOIN tbl_user_mapping um
               ON um.employee_user_id=d.mr_id";

        $where = "um.manager_user_id='$managerId'";
    } else {

        $join = "INNER JOIN tbl_user_mapping a
               ON a.employee_user_id=d.mr_id

               INNER JOIN tbl_user_mapping b
               ON b.employee_user_id=a.manager_user_id";

        $where = "b.manager_user_id='$managerId'";
    }

    $sql = "

    SELECT COUNT(*)

    FROM tbl_dcr_chemist_calls cc

    INNER JOIN tbl_dcr d
    ON d.id=cc.dcr_id

    $join

    WHERE

    $where

    AND MONTH(d.visit_date)=MONTH(CURDATE())
    AND YEAR(d.visit_date)=YEAR(CURDATE())

    ";

    return getSingleValue($conn, $sql);
}