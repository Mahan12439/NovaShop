<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('categories');



$categories = $pdo
    ->query("
        SELECT
            c.id,
            c.parent_id,
            c.image,
            c.sort_order,
            c.status,
            ct.name

        FROM categories c

        INNER JOIN category_translations ct
            ON ct.category_id = c.id
            AND ct.language = 'fa'

        ORDER BY
            c.sort_order ASC,
            c.id DESC
    ")
    ->fetchAll();

?>

    <div class="admin-page-toolbar">

        <a
            href="category-add.php"
            class="admin-button primary"
        >
            + <?= __('add_category') ?>
        </a>

    </div>


    <div class="admin-panel">

        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>

                <tr>

                    <th>
                        <?= __('name') ?>
                    </th>

                    <th>
                        <?= __('sort_order') ?>
                    </th>

                    <th>
                        <?= __('status') ?>
                    </th>

                    <th>
                        <?= __('actions') ?>
                    </th>

                </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $categories as $category
                ): ?>

                    <tr>

                        <td>
                            <?= e(
                                $category['name']
                            ) ?>
                        </td>

                        <td>
                            <?= (int) $category['sort_order'] ?>
                        </td>

                        <td>

                        <span
                            class="admin-status status-<?= e(
                                $category['status']
                            ) ?>"
                        >
                            <?= __(
                                $category['status']
                            ) ?>
                        </span>

                        </td>

                        <td>

                            <div class="admin-actions">

                                <a
                                    href="category-edit.php?id=<?= (int) $category['id'] ?>"
                                    class="admin-table-action"
                                >
                                    <?= __('edit') ?>
                                </a>


                                <form
                                    method="POST"
                                    action="category-delete.php"
                                    onsubmit="return confirm('<?= e(__('confirm_delete')) ?>')"
                                >

                                    <?= admin_csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $category['id'] ?>"
                                    >

                                    <button
                                        type="submit"
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