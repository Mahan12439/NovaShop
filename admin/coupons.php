<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('coupons');


$coupons =
    $pdo->query("
        SELECT *
        FROM coupons
        ORDER BY id DESC
    ")->fetchAll();

?>

    <div class="admin-page-toolbar">

        <a
            href="coupon-add.php"
            class="admin-button primary"
        >
            + <?= __('add_coupon') ?>
        </a>

    </div>


    <div class="admin-panel">

        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>

                <tr>

                    <th>Code</th>
                    <th><?= __('type') ?></th>
                    <th><?= __('value') ?></th>
                    <th><?= __('used_count') ?></th>
                    <th><?= __('status') ?></th>
                    <th><?= __('expires_at') ?></th>
                    <th><?= __('actions') ?></th>

                </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $coupons as $coupon
                ): ?>

                    <tr>

                        <td>
                            <strong>
                                <?= e(
                                    $coupon['code']
                                ) ?>
                            </strong>
                        </td>

                        <td>
                            <?= __(
                                $coupon['type']
                            ) ?>
                        </td>

                        <td>

                            <?php if (
                                $coupon['type']
                                === 'percentage'
                            ): ?>

                                <?= e(
                                    $coupon['value']
                                ) ?>%

                            <?php else: ?>

                                <?= formatPrice(
                                    $coupon['value']
                                ) ?>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= (int)
                            $coupon['used_count']
                            ?>
                        </td>

                        <td>

                        <span
                            class="admin-status status-<?= e(
                                $coupon['status']
                            ) ?>"
                        >
                            <?= __(
                                $coupon['status']
                            ) ?>
                        </span>

                        </td>

                        <td>
                            <?= $coupon['expires_at']
                                ? e(
                                    $coupon['expires_at']
                                )
                                : '-'
                            ?>
                        </td>

                        <td>

                            <div class="admin-actions">

                                <a
                                    href="coupon-edit.php?id=<?= (int) $coupon['id'] ?>"
                                    class="admin-table-action"
                                >
                                    <?= __('edit') ?>
                                </a>


                                <form
                                    method="POST"
                                    action="coupon-delete.php"
                                    onsubmit="return confirm('<?= e(__('confirm_delete')) ?>')"
                                >

                                    <?= admin_csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $coupon['id'] ?>"
                                    >

                                    <button
                                        class="admin-table-action danger"
                                    >
                                        <?= __('delete') ?>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>