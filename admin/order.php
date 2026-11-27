<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('order');


$id =
    (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
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

    WHERE o.id = :id

    LIMIT 1
");

$stmt->execute([
    'id' => $id
]);

$order =
    $stmt->fetch();

if (!$order) {

    admin_redirect(
        'orders.php'
    );
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    admin_check_csrf();

    $orderStatus =
        $_POST['status'] ?? '';

    $paymentStatus =
        $_POST['payment_status'] ?? '';

    if (
        in_array(
            $orderStatus,
            admin_order_statuses(),
            true
        ) &&
        in_array(
            $paymentStatus,
            admin_payment_statuses(),
            true
        )
    ) {

        $update =
            $pdo->prepare("
                UPDATE orders

                SET
                    status = :status,
                    payment_status = :payment_status

                WHERE id = :id
            ");

        $update->execute([
            'status' =>
                $orderStatus,

            'payment_status' =>
                $paymentStatus,

            'id' =>
                $id
        ]);

        admin_flash(
            'success',
            __('order_updated')
        );

        admin_redirect(
            'order.php?id=' .
            $id
        );
    }
}


$itemsStmt =
    $pdo->prepare("
        SELECT *
        FROM order_items
        WHERE order_id = :order_id
        ORDER BY id ASC
    ");

$itemsStmt->execute([
    'order_id' => $id
]);

$items =
    $itemsStmt->fetchAll();

?>

    <div class="admin-form-page">


        <div class="admin-form-toolbar">

            <a
                href="orders.php"
                class="admin-button"
            >
                ← <?= __('back') ?>
            </a>

        </div>


        <div class="admin-form-grid">


            <section class="admin-panel">

                <div class="admin-panel-heading">

                    <div>

                    <span>
                        <?= __('order_number') ?>
                    </span>

                        <h2>
                            <?= e(
                                $order['order_number']
                            ) ?>
                        </h2>

                    </div>

                </div>


                <div class="admin-form-fields">

                    <div class="admin-order-detail">

                    <span>
                        <?= __('customer') ?>
                    </span>

                        <strong>
                            <?= e(
                                trim(
                                    $order['customer_name']
                                ) ?: '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="admin-order-detail">

                    <span>
                        <?= __('phone') ?>
                    </span>

                        <strong>
                            <?= e(
                                $order['phone']
                            ) ?>
                        </strong>

                    </div>


                    <div class="admin-order-detail">

                    <span>
                        <?= __('email') ?>
                    </span>

                        <strong>
                            <?= e(
                                $order['email'] ?? '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="admin-order-detail">

                    <span>
                        <?= __('address') ?>
                    </span>

                        <strong>
                            <?= e(
                                trim(
                                    implode(
                                        ' ',
                                        array_filter([
                                            $order['country'],
                                            $order['state'],
                                            $order['city'],
                                            $order['postal_code'],
                                            $order['address']
                                        ])
                                    )
                                )
                            ) ?>
                        </strong>

                    </div>

                </div>

            </section>


            <section class="admin-panel">

                <div class="admin-panel-heading">

                    <h2>
                        <?= __('status') ?>
                    </h2>

                </div>


                <form
                    method="POST"
                    class="admin-form-fields"
                >

                    <?= admin_csrf_field() ?>


                    <div class="admin-input-group">

                        <label>
                            <?= __('order_status') ?>
                        </label>

                        <select name="status">

                            <?php foreach (
                                admin_order_statuses()
                                as $status
                            ): ?>

                                <option
                                    value="<?= e($status) ?>"
                                    <?= $order['status'] === $status
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= __($status) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('payment_status') ?>
                        </label>

                        <select
                            name="payment_status"
                        >

                            <?php foreach (
                                admin_payment_statuses()
                                as $paymentStatus
                            ): ?>

                                <option
                                    value="<?= e($paymentStatus) ?>"
                                    <?= $order['payment_status']
                                    === $paymentStatus
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= __(
                                        $paymentStatus
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="admin-button primary"
                    >
                        <?= __('save') ?>
                    </button>

                </form>

            </section>


            <section class="admin-panel full">

                <div class="admin-panel-heading">

                    <h2>
                        <?= __('order_items') ?>
                    </h2>

                </div>


                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                        <tr>

                            <th>
                                <?= __('product') ?>
                            </th>

                            <th>
                                <?= __('sku') ?>
                            </th>

                            <th>
                                <?= __('price') ?>
                            </th>

                            <th>
                                <?= __('quantity') ?>
                            </th>

                            <th>
                                <?= __('total') ?>
                            </th>

                        </tr>

                        </thead>


                        <tbody>

                        <?php foreach (
                            $items as $item
                        ): ?>

                            <tr>

                                <td>
                                    <?= e(
                                        $item['product_name']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $item['product_sku']
                                        ?? '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= formatPrice(
                                        $item['price']
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int)
                                    $item['quantity']
                                    ?>
                                </td>

                                <td>
                                    <?= formatPrice(
                                        $item['total']
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>


        </div>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>