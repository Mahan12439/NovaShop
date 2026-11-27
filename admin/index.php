<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = __('dashboard');

require_once __DIR__ . '/includes/header.php';


// =====================================================
// Statistics
// =====================================================

$productCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM products
    ")
    ->fetchColumn();


$userCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM users
    ")
    ->fetchColumn();


$orderCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM orders
    ")
    ->fetchColumn();


$revenue = (float) $pdo
    ->query("
        SELECT
            COALESCE(
                SUM(total),
                0
            )
        FROM orders
        WHERE payment_status = 'paid'
    ")
    ->fetchColumn();


$recentOrders = $pdo
    ->query("
        SELECT
            o.id,
            o.order_number,
            o.total,
            o.status,
            o.payment_status,
            o.created_at,

            u.first_name,
            u.last_name

        FROM orders o

        LEFT JOIN users u
            ON u.id = o.user_id

        ORDER BY
            o.created_at DESC

        LIMIT 8
    ")
    ->fetchAll();

?>


    <div class="admin-stats-grid">


        <div class="admin-stat-card">

            <div class="admin-stat-icon purple">
                ▣
            </div>

            <div>

            <span>
                <?= __('products') ?>
            </span>

                <strong>
                    <?= number_format($productCount) ?>
                </strong>

            </div>

        </div>


        <div class="admin-stat-card">

            <div class="admin-stat-icon blue">
                ♙
            </div>

            <div>

            <span>
                <?= __('users') ?>
            </span>

                <strong>
                    <?= number_format($userCount) ?>
                </strong>

            </div>

        </div>


        <div class="admin-stat-card">

            <div class="admin-stat-icon orange">
                ◫
            </div>

            <div>

            <span>
                <?= __('orders') ?>
            </span>

                <strong>
                    <?= number_format($orderCount) ?>
                </strong>

            </div>

        </div>


        <div class="admin-stat-card">

            <div class="admin-stat-icon green">
                €
            </div>

            <div>

            <span>
                <?= __('revenue') ?>
            </span>

                <strong>
                    <?= formatPrice($revenue) ?>
                </strong>

            </div>

        </div>

    </div>


    <div class="admin-dashboard-grid">


        <section class="admin-panel">

            <div class="admin-panel-heading">

                <div>

                <span>
                    NovaShop
                </span>

                    <h2>
                        <?= __('recent_orders') ?>
                    </h2>

                </div>


                <a
                    href="orders.php"
                    class="admin-panel-link"
                >
                    <?= __('view_all') ?>
                </a>

            </div>


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

                    </tr>

                    </thead>


                    <tbody>

                    <?php if (!empty($recentOrders)): ?>

                        <?php foreach ($recentOrders as $order): ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?= e(
                                            $order['order_number']
                                        ) ?>
                                    </strong>

                                </td>


                                <td>

                                    <?= e(
                                        trim(
                                            ($order['first_name'] ?? '') .
                                            ' ' .
                                            ($order['last_name'] ?? '')
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

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                class="admin-empty-table"
                            >
                                <?= __('no_orders') ?>
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <aside class="admin-side-panels">


            <div class="admin-panel admin-quick-panel">

                <div class="admin-panel-heading">

                    <div>

                    <span>
                        NovaShop
                    </span>

                        <h2>
                            <?= __('quick_actions') ?>
                        </h2>

                    </div>

                </div>


                <div class="admin-quick-actions">

                    <a
                        href="product-add.php"
                        class="admin-quick-action"
                    >
                        <span>+</span>
                        <?= __('add_product') ?>
                    </a>


                    <a
                        href="category-add.php"
                        class="admin-quick-action"
                    >
                        <span>+</span>
                        <?= __('add_category') ?>
                    </a>


                    <a
                        href="orders.php"
                        class="admin-quick-action"
                    >
                        <span>◫</span>
                        <?= __('manage_orders') ?>
                    </a>


                    <a
                        href="reviews.php"
                        class="admin-quick-action"
                    >
                        <span>★</span>
                        <?= __('manage_reviews') ?>
                    </a>

                </div>

            </div>


            <div class="admin-panel admin-welcome-panel">

                <div class="admin-welcome-icon">
                    N
                </div>

                <h2>
                    <?= __('welcome_admin') ?>
                </h2>

                <p>
                    <?= __('welcome_admin_desc') ?>
                </p>

            </div>

        </aside>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>