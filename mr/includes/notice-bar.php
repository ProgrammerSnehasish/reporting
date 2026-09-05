<!-- Welcome Card -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body py-3">

        <h4 class="text-primary mb-2">
            👋 Welcome to Luminia Lifecare CRM Portal
        </h4>

        <p class="text-muted mb-0">
            <strong>Luminia Lifecare Pvt. Ltd.</strong> —
            <em>India's Next-Generation Pain Relief & Rehabilitation Ecosystem.</em>

        </p>

    </div>
</div>

<!-- Notice Bar -->
<div class="alert mb-3 d-flex align-items-center overflow-hidden"
    style="background:#fff3cd;border:1px solid #ffe69c;">

    <strong class="me-3 text-danger">
        📢 Notice
    </strong>

    <div class="ticker-wrap flex-grow-1">

        <div class="ticker">

            <?php

            $today = date("Y-m-d");

            $notice = mysqli_query($conn, "

                SELECT *

                FROM tbl_notices

                WHERE status = 1

                ORDER BY id DESC

                ");

            if (mysqli_num_rows($notice) > 0) {

                while ($noticeRow = mysqli_fetch_assoc($notice)) {

            ?>

                    <span style="color:#212529;font-weight:600;">

                        📢 <strong><?= htmlspecialchars($noticeRow['title']); ?></strong>

                        <?php

                        if (!empty($noticeRow['message'])) {

                            echo " - " . $noticeRow['message'];
                        }

                        ?>

                    </span>

                <?php

                }
            } else {

                ?>

                <span style="color:#212529;font-weight:600;">

                    📢 Welcome to Luminia Lifecare CRM Portal.

                </span>

            <?php } ?>

        </div>

    </div>

</div>

<style>
    .ticker-wrap {
        overflow: hidden;
        width: 100%;
        position: relative;
    }

    .ticker {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        animation: ticker 25s linear infinite;
    }

    .ticker span {
        padding-right: 80px;
        font-size: 15px;
        font-weight: 500;
        color: #fff;
    }

    @keyframes ticker {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>