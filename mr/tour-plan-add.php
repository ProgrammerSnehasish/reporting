<?php

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../config/role-auth.php";

checkRole(['mr']);

// ── Month selection ──────────────────────────────────────────────────────────
$selectedMonth = isset($_GET['tour_month']) && $_GET['tour_month'] !== ''
    ? $_GET['tour_month']
    : date('Y-m');

if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
    $selectedMonth = date('Y-m');
}

// ── Restore draft from session (saved by process.php on validation/DB error) ─
$draft = $_SESSION['tour_plan_draft'] ?? [];
if (!empty($draft) && ($draft['tour_month'] ?? '') !== $selectedMonth) {
    $draft = [];
}
unset($_SESSION['tour_plan_draft']);

// ── Draft helpers — all keyed by 1-based day $d ──────────────────────────────
function draftInt(array $draft, string $key, int $d): int
{
    return (int)($draft[$key][$d] ?? 0);
}

function draftStr(array $draft, string $key, int $d): string
{
    return htmlspecialchars($draft[$key][$d] ?? '');
}

function draftWorkingWith(array $draft, int $d): array
{
    $raw = $draft['working_with_user_id'][$d] ?? [];
    return array_map('intval', (array)$raw);
}

// ── Calculate days ───────────────────────────────────────────────────────────
[$year, $mon] = explode('-', $selectedMonth);
$year      = (int)$year;
$mon       = (int)$mon;
$totalDays = (int)date('t', mktime(0, 0, 0, $mon, 1, $year));
$weekdays  = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// ── DB queries (once, outside loop) ─────────────────────────────────────────
$hqResult = mysqli_query($conn, "
    SELECT id, area_name FROM tbl_areas
    WHERE status = 1 AND area_type = 'HQ'
    ORDER BY area_name
");
$hqList = [];
while ($row = mysqli_fetch_assoc($hqResult)) $hqList[] = $row;

$typeResult = mysqli_query($conn, "
    SELECT id, working_type_name FROM tbl_working_types
    WHERE status = 1 ORDER BY working_type_name
");
$workingTypes = [];
while ($row = mysqli_fetch_assoc($typeResult)) $workingTypes[] = $row;

$userResult = mysqli_query($conn, "
    SELECT u.id, e.employee_code,
           CONCAT(e.first_name,' ',e.last_name) AS employee_name,
           r.role_name
    FROM tbl_users u
    INNER JOIN tbl_employees e ON e.id = u.employee_id
    INNER JOIN tbl_roles r     ON r.id = u.role_id
    WHERE u.status = 1
      AND r.role_code IN ('abm','rbm','zsm','nsm')
    ORDER BY r.role_name, e.first_name
");
$users = [];
while ($row = mysqli_fetch_assoc($userResult)) $users[] = $row;

// ── Pre-fetch areas for every HQ that appears in the draft ───────────────────
// KEY FIX: When restoring a draft, we load area options from the DB in PHP
// so the Area dropdown is fully populated with the saved value already selected.
// This avoids AJAX timing issues entirely on page load.
$draftAreaOptions = []; // [$d => '<option>...</option>']
for ($d = 1; $d <= $totalDays; $d++) {
    $hqId     = draftInt($draft, 'hq_id',   $d);
    $areaId   = draftInt($draft, 'area_id', $d);
    if ($hqId <= 0) continue;

    $hqIdSafe = (int)$hqId;
    $aRes = mysqli_query($conn, "
        SELECT id, area_name FROM tbl_areas
        WHERE hq_id = $hqIdSafe AND status = 1
        ORDER BY area_name
    ");
    $opts = '<option value="">-- Select Area --</option>';
    while ($aRow = mysqli_fetch_assoc($aRes)) {
        $sel   = ((int)$aRow['id'] === $areaId) ? 'selected' : '';
        $opts .= "<option value=\"{$aRow['id']}\" {$sel}>"
            . htmlspecialchars($aRow['area_name'])
            . "</option>";
    }
    $draftAreaOptions[$d] = $opts;
}

?>
<?php include('./includes/header.php'); ?>

<style>
    body {
        background: #f4f6f9;
    }

    .month-bar {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
    }

    .month-bar label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 5px;
        display: block;
    }

    .day-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .day-card-header {
        background: #0d6efd;
        color: #fff;
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .92rem;
    }

    .day-card-header.sunday {
        background: #dc3545;
    }

    .day-card-header.saturday {
        background: #6f42c1;
    }

    .day-card-body {
        background: #fff;
        padding: 14px 16px;
    }

    .form-label {
        font-size: .82rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px;
    }
</style>

<div id="layout-wrapper">

    <?php include('./includes/navbar.php'); ?>
    <?php include('./includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0">Add Tour Plan</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="tour-plan-list.php" class="btn btn-primary btn-sm">
                            <i class="ri-list-check"></i> View Plans
                        </a>
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

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Tour Plan</h5>
                    </div>
                    <div class="card-body">

                        <!-- ── Month Selector (GET) ── -->
                        <form method="GET" action="" class="month-bar">
                            <div>
                                <label for="tour_month">Select Month</label>
                                <input
                                    type="month"
                                    id="tour_month"
                                    name="tour_month"
                                    class="form-control"
                                    value="<?= htmlspecialchars($selectedMonth) ?>"
                                    min="<?= date('Y-m') ?>"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-calendar-line"></i> Load Days
                            </button>
                        </form>

                        <!-- ── Tour Entry Form (POST) ── -->
                        <form action="tour-plan-process.php" method="POST">

                            <input type="hidden" name="tour_month" value="<?= htmlspecialchars($selectedMonth) ?>">

                            <?php for ($d = 1; $d <= $totalDays; $d++):

                                $dateObj     = mktime(0, 0, 0, $mon, $d, $year);
                                $dateValue   = date('Y-m-d', $dateObj);
                                $dateDisplay = date('d-m-Y', $dateObj);
                                $dayName     = $weekdays[(int)date('w', $dateObj)];
                                $headerClass = $dayName === 'Sunday'    ? 'sunday'
                                    : ($dayName === 'Saturday' ? 'saturday' : '');

                                $draftHq    = draftInt($draft, 'hq_id',          $d);
                                $draftArea  = draftInt($draft, 'area_id',         $d);
                                $draftType  = draftInt($draft, 'working_type_id', $d);
                                $draftHalt  = $draft['night_halt'][$d] ?? 'No';
                                $draftObj   = draftStr($draft, 'objective', $d);
                                $draftRmk   = draftStr($draft, 'remarks',   $d);
                                $draftUsers = draftWorkingWith($draft, $d);

                                // PHP-rendered area options (populated if draft exists for this day)
                                $areaOptions = $draftAreaOptions[$d]
                                    ?? '<option value="">Select HQ first</option>';

                            ?>

                                <div class="day-card">

                                    <div class="day-card-header <?= $headerClass ?>">
                                        <span><b><?= $dateDisplay ?></b> &nbsp;(<?= $dayName ?>)</span>
                                        <span class="badge bg-light text-dark">Planning</span>
                                    </div>

                                    <div class="day-card-body">

                                        <input type="hidden" name="tour_date[<?= $d ?>]" value="<?= $dateValue ?>">

                                        <div class="row">

                                            <!-- HQ -->
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">
                                                    HQ <span class="text-danger">*</span>
                                                </label>
                                                <select
                                                    name="hq_id[<?= $d ?>]"
                                                    class="form-select form-select-sm hq-select"
                                                    data-day="<?= $d ?>">
                                                    <option value="">Select HQ</option>
                                                    <?php foreach ($hqList as $hq): ?>
                                                        <option value="<?= $hq['id'] ?>"
                                                            <?= $draftHq === (int)$hq['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($hq['area_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Area -->
                                            <!-- On fresh load: shows "Select HQ first", JS fills via AJAX on HQ change -->
                                            <!-- On draft restore: PHP has already rendered the correct options      -->
                                            <!-- with the saved area pre-selected — no AJAX needed on page load      -->
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">
                                                    Working Area <span class="text-danger">*</span>
                                                </label>
                                                <select
                                                    name="area_id[<?= $d ?>]"
                                                    class="form-select form-select-sm area-select">
                                                    <?= $areaOptions ?>
                                                </select>
                                            </div>

                                            <!-- Working Type -->
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Working Type</label>
                                                <select name="working_type_id[<?= $d ?>]" class="form-select form-select-sm">
                                                    <option value="">Select Working Type</option>
                                                    <?php foreach ($workingTypes as $t): ?>
                                                        <option value="<?= $t['id'] ?>"
                                                            <?= $draftType === (int)$t['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($t['working_type_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Working With -->
                                            <div class="col-md-8 mb-2">
                                                <label class="form-label">Working With</label>
                                                <select
                                                    name="working_with_user_id[<?= $d ?>][]"
                                                    class="form-select form-select-sm"
                                                    multiple>
                                                    <?php foreach ($users as $u): ?>
                                                        <option value="<?= $u['id'] ?>"
                                                            <?= in_array((int)$u['id'], $draftUsers) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($u['employee_code']) ?> -
                                                            <?= htmlspecialchars($u['employee_name']) ?>
                                                            (<?= strtoupper($u['role_name']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Night Halt -->
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Night Halt</label>
                                                <select name="night_halt[<?= $d ?>]" class="form-select form-select-sm">
                                                    <option value="No" <?= $draftHalt === 'No'  ? 'selected' : '' ?>>No</option>
                                                    <option value="Yes" <?= $draftHalt === 'Yes' ? 'selected' : '' ?>>Yes</option>
                                                </select>
                                            </div>


                                        </div><!-- /.row -->
                                    </div><!-- /.day-card-body -->
                                </div><!-- /.day-card -->

                            <?php endfor; ?>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Tour Plan
                                </button>
                            </div>

                        </form>

                    </div><!-- /.card-body -->
                </div><!-- /.card -->

            </div>
        </div>
    </div>

    <?php include('./includes/footer.php'); ?>

</div>

<?php include('./includes/scripts.php'); ?>

<script>
    $(function() {

        /**
         * loadAreas()
         * Called only when the user manually changes HQ.
         * On draft restore, PHP has already rendered the correct options — no AJAX needed.
         */
        function loadAreas($hqSelect) {
            var hq_id = $hqSelect.val();
            var $body = $hqSelect.closest('.day-card-body');
            var $area = $body.find('.area-select');

            if (!hq_id) {
                $area.html('<option value="">Select HQ first</option>');
                return;
            }

            $area.html('<option value="">Loading...</option>').prop('disabled', true);

            $.ajax({
                url: 'get-area.php',
                type: 'POST',
                data: {
                    hq_id: hq_id
                },
                success: function(data) {
                    $area.prop('disabled', false).html(data);
                },
                error: function() {
                    $area.prop('disabled', false)
                        .html('<option value="">Error — please retry</option>');
                }
            });
        }

        // User manually picks a new HQ — reload areas via AJAX
        $(document).on('change', '.hq-select', function() {
            loadAreas($(this));
        });

    });
</script>