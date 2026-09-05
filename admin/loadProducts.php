<?php

require_once "../config/config.php";
require_once "../config/database.php";

$result = mysqli_query($conn, "
SELECT
id,
product_code,
product_name,
brand_name,
strength,
pack
FROM tbl_products
WHERE status='1'
ORDER BY display_order,product_name
");

?>

<div class="row">

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">

            <div class="card border shadow-sm h-100">

                <div class="card-body">

                    <input type="hidden"
                        name="product_id[]"
                        value="<?= $row['id']; ?>">

                    <h6 class="fw-bold mb-1">

                        <?= $row['product_name']; ?>

                    </h6>

                    <div class="text-muted small mb-2">

                        <?= $row['product_code']; ?>

                        <?php if ($row['strength']) { ?>

                            |

                            <?= $row['strength']; ?>

                        <?php } ?>

                        <?php if ($row['pack']) { ?>

                            |

                            <?= $row['pack']; ?>

                        <?php } ?>

                    </div>

                    <div class="row">

                        <div class="col-6">

                            <label class="small text-muted">

                                Target Qty

                            </label>

                            <input
                                type="number"
                                name="target_qty[]"
                                class="form-control"
                                min="0"
                                value="0">

                        </div>

                        <div class="col-6">

                            <label class="small text-muted">

                                Target Value

                            </label>

                            <input
                                type="number"
                                name="target_value[]"
                                class="form-control"
                                min="0"
                                step="1"
                                value="0">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    <?php } ?>

</div>