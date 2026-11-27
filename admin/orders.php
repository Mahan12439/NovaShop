<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('orders');



$status =
    $_GET['status'] ?? '';

$sql = "
    SELECT
        o.*,

        CONCAT(
            COALESCE(u.first_name, ''),
            ' ',
            COALESCE(u.last_name, '')
        ) AS customer_name

    FROM orders o

    LEFT JOIN users u
        ON u.id = o.user_id
";

$params = [];

if (
    in_array(
        $status,
        admin_order_statuses(),
        true
    )
) {

    $sql .= "
        WHERE o.status = :status
    ";

    $params['status'] =
        $status;
}

$sql .= "
    ORDER BY o.id DESC
    LIMIT 100
";

$stmt =
    $pdo->prepare($sql);

$stmt->execute($params);

$orders =
    $stmt->fetchAll();

?>

    <div class="admin-page-toolbar">

        <form
            method="GET"
            class="admin-search-form"
        >

            <select name="status">

                <option value="">
                    <?= __('all_statuses') ?>
                </option>

                <?php foreach (
                    admin_order_statuses()
                    as $item
                ): ?>

                    <option
                        value="<?= e($item) ?>"
                        <?= $status === $item
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= __($item) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button
                class="admin-button"
                type="submit"
            >
                <?= __('filter') ?>
            </button>

        </form>

    </div>


    <div class="admin-panel">

        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>

                <tr>

                    <th>
                        <?= __('order_number') ?>
                    </th>

                    <th>
                        <?= __('customer') ?>
                    </th>

                    <th>
                        <?= __('total') ?>
                    </th>

                    <th>
                        <?= __('order_status') ?>
                    </th>

                    <th>
                        <?= __('payment_status') ?>
                    </th>

                    <th>
                        <?= __('order_date') ?>
                    </th>

                    <th>
                        <?= __('actions') ?>
                    </th>

                </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $orders as $order
                ): ?>

                    <tr>

                        <td>
                            <?= e(
                                $order['order_number']
                            ) ?>
                        </td>

                        <td>
                            <?= e(
                                trim(
                                    $order['customer_name']
                                ) ?: '-'
                            ) ?>
                        </td>

                        <td>
                            <?= formatPrice(
                                $order['total']
                            ) ?>
                        </td>

                        <td>

                        <span
                            class="admin-status status-<?= e(
                                $order['status']
                            ) ?>"
                        >
                            <?= __(
                                $order['status']
                            ) ?>
                        </span>

                        </td>

                        <td>

                        <span
                            class="admin-status status-<?= e(
                                $order['payment_status']
                            ) ?>"
                        >
                            <?= __(
                                $order['payment_status']
                            ) ?>
                        </span>

                        </td>

                        <td>
                            <?= date(
                                'Y/m/d H:i',
                                strtotime(
                                    $order['created_at']
                                )
                            ) ?>
                        </td>

                        <td>

                            <a
                                href="order.php?id=<?= (int) $order['id'] ?>"
                                class="admin-table-action"
                            >
                                <?= __('details') ?>
                            </a>

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