<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('edit_category');


$id =
    (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$category =
    $stmt->fetch();

if (!$category) {
    admin_redirect(
        'categories.php'
    );
}


$translations = [];

$stmt = $pdo->prepare("
    SELECT *
    FROM category_translations
    WHERE category_id = :id
");

$stmt->execute([
    'id' => $id
]);

foreach (
    $stmt->fetchAll()
    as $translation
) {

    $translations[
    $translation['language']
    ] = $translation;
}


$parents = $pdo
    ->query("
        SELECT
            c.id,
            ct.name
        FROM categories c
        INNER JOIN category_translations ct
            ON ct.category_id = c.id
            AND ct.language = 'fa'
        WHERE c.id != {$id}
        ORDER BY ct.name ASC
    ")
    ->fetchAll();


if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    admin_check_csrf();

    try {

        $image =
            admin_upload_image(
                $_FILES['image'] ?? [],
                'categories'
            );


        $sql = "
            UPDATE categories

            SET
                parent_id = :parent_id,
                sort_order = :sort_order,
                status = :status
        ";

        $params = [
            'parent_id' =>
                ((int) $_POST['parent_id'] > 0)
                    ? (int) $_POST['parent_id']
                    : null,

            'sort_order' =>
                (int) ($_POST['sort_order'] ?? 0),

            'status' =>
                $_POST['status'] === 'inactive'
                    ? 'inactive'
                    : 'active',

            'id' =>
                $id
        ];

        if ($image) {

            $sql .= ",
                image = :image
            ";

            $params['image'] =
                $image;

            if (!empty($category['image'])) {

                admin_delete_image(
                    'categories',
                    $category['image']
                );
            }
        }

        $sql .= "
            WHERE id = :id
        ";

        $stmt =
            $pdo->prepare($sql);

        $stmt->execute($params);


        foreach (
            ['fa', 'de']
            as $language
        ) {

            $stmt =
                $pdo->prepare("
                    INSERT INTO category_translations
                    (
                        category_id,
                        language,
                        name,
                        slug
                    )
                    VALUES
                    (
                        :category_id,
                        :language,
                        :name,
                        :slug
                    )

                    ON DUPLICATE KEY UPDATE
                        name = VALUES(name),
                        slug = VALUES(slug)
                ");

            $stmt->execute([
                'category_id' => $id,
                'language' => $language,
                'name' => trim(
                    $_POST[
                    $language . '_name'
                    ] ?? ''
                ),
                'slug' => trim(
                    $_POST[
                    $language . '_slug'
                    ] ?? ''
                )
            ]);
        }


        admin_flash(
            'success',
            __('category_updated')
        );

        admin_redirect(
            'categories.php'
        );

    } catch (Throwable $e) {

        $error =
            $e->getMessage();
    }
}

?>

    <div class="admin-form-page">

        <?php if (!empty($error)): ?>

            <div class="admin-alert error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
            class="admin-form"
        >

            <?= admin_csrf_field() ?>


            <div class="admin-form-toolbar">

                <a
                    href="categories.php"
                    class="admin-button"
                >
                    ← <?= __('back') ?>
                </a>

                <button
                    type="submit"
                    class="admin-button primary"
                >
                    <?= __('save') ?>
                </button>

            </div>


            <div class="admin-form-grid">

                <section class="admin-panel">

                    <div class="admin-form-fields">

                        <div class="admin-input-group">

                            <label>
                                <?= __('parent_category') ?>
                            </label>

                            <select name="parent_id">

                                <option value="0">
                                    <?= __('none') ?>
                                </option>

                                <?php foreach (
                                    $parents
                                    as $parent
                                ): ?>

                                    <option
                                        value="<?= (int) $parent['id'] ?>"
                                        <?= (int) $category['parent_id']
                                        === (int) $parent['id']
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= e(
                                            $parent['name']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('sort_order') ?>
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                value="<?= (int) $category['sort_order'] ?>"
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('status') ?>
                            </label>

                            <select name="status">

                                <option
                                    value="active"
                                    <?= $category['status'] === 'active'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= __('active') ?>
                                </option>

                                <option
                                    value="inactive"
                                    <?= $category['status'] === 'inactive'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= __('inactive') ?>
                                </option>

                            </select>

                        </div>


                        <?php if (
                            !empty($category['image'])
                        ): ?>

                            <img
                                src="<?= e(
                                    '../storage/categories/' .
                                    $category['image']
                                ) ?>"
                                class="admin-preview-image"
                                alt=""
                            >

                        <?php endif; ?>


                        <div class="admin-input-group">

                            <label>
                                <?= __('replace_image') ?>
                            </label>

                            <input
                                type="file"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                            >

                        </div>

                    </div>

                </section>


                <?php foreach (
                    ['fa', 'de']
                    as $language
                ): ?>

                    <?php

                    $translation =
                        $translations[
                        $language
                        ] ?? [
                        'name' => '',
                        'slug' => ''
                    ];

                    ?>

                    <section class="admin-panel">

                        <div class="admin-panel-heading">

                            <h2>
                                <?= $language === 'fa'
                                    ? 'فارسی'
                                    : 'Deutsch'
                                ?>
                            </h2>

                        </div>


                        <div class="admin-form-fields">

                            <div class="admin-input-group">

                                <label>
                                    <?= __('name') ?>
                                </label>

                                <input
                                    type="text"
                                    name="<?= $language ?>_name"
                                    value="<?= e(
                                        $translation['name']
                                    ) ?>"
                                    required
                                >

                            </div>


                            <div class="admin-input-group">

                                <label>
                                    Slug
                                </label>

                                <input
                                    type="text"
                                    name="<?= $language ?>_slug"
                                    value="<?= e(
                                        $translation['slug']
                                    ) ?>"
                                    required
                                >

                            </div>

                        </div>

                    </section>

                <?php endforeach; ?>

            </div>

        </form>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>