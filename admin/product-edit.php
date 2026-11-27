<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('add_product');

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    admin_check_csrf();

    $sku =
        trim($_POST['sku'] ?? '');

    $categoryId =
        (int) ($_POST['category_id'] ?? 0);

    $price =
        (float) ($_POST['price'] ?? 0);

    $comparePrice =
        ($_POST['compare_price'] ?? '') !== ''
            ? (float) $_POST['compare_price']
            : null;

    $stock =
        max(
            0,
            (int) ($_POST['stock'] ?? 0)
        );

    $status =
        $_POST['status'] ?? 'active';

    $featured =
        isset($_POST['featured'])
            ? 1
            : 0;

    $faName =
        trim($_POST['fa_name'] ?? '');

    $faSlug =
        trim($_POST['fa_slug'] ?? '');

    $faShort =
        trim($_POST['fa_short'] ?? '');

    $faDescription =
        trim($_POST['fa_description'] ?? '');

    $deName =
        trim($_POST['de_name'] ?? '');

    $deSlug =
        trim($_POST['de_slug'] ?? '');

    $deShort =
        trim($_POST['de_short'] ?? '');

    $deDescription =
        trim($_POST['de_description'] ?? '');

    try {

        if (
            $sku === '' ||
            $faName === '' ||
            $faSlug === '' ||
            $deName === '' ||
            $deSlug === ''
        ) {
            throw new RuntimeException(
                __('required_field')
            );
        }

        if (
            !in_array(
                $status,
                admin_product_statuses(),
                true
            )
        ) {
            throw new RuntimeException(
                __('invalid_data')
            );
        }

        $pdo->beginTransaction();


        $stmt = $pdo->prepare("
            INSERT INTO products
            (
                category_id,
                sku,
                price,
                compare_price,
                stock,
                status,
                featured
            )
            VALUES
            (
                :category_id,
                :sku,
                :price,
                :compare_price,
                :stock,
                :status,
                :featured
            )
        ");

        $stmt->execute([
            'category_id' =>
                $categoryId > 0
                    ? $categoryId
                    : null,

            'sku' => $sku,

            'price' => $price,

            'compare_price' =>
                $comparePrice,

            'stock' => $stock,

            'status' => $status,

            'featured' => $featured
        ]);

        $productId =
            (int) $pdo->lastInsertId();


        $translationStmt = $pdo->prepare("
            INSERT INTO product_translations
            (
                product_id,
                language,
                name,
                slug,
                short_description,
                description
            )
            VALUES
            (
                :product_id,
                :language,
                :name,
                :slug,
                :short_description,
                :description
            )
        ");

        $translationStmt->execute([
            'product_id' => $productId,
            'language' => 'fa',
            'name' => $faName,
            'slug' => $faSlug,
            'short_description' => $faShort,
            'description' => $faDescription
        ]);

        $translationStmt->execute([
            'product_id' => $productId,
            'language' => 'de',
            'name' => $deName,
            'slug' => $deSlug,
            'short_description' => $deShort,
            'description' => $deDescription
        ]);


        $mainImage =
            admin_upload_image(
                $_FILES['main_image'] ?? [],
                'products'
            );


        if ($mainImage) {

            $update = $pdo->prepare("
                UPDATE products
                SET image = :image
                WHERE id = :id
            ");

            $update->execute([
                'image' => $mainImage,
                'id' => $productId
            ]);


            $imageStmt = $pdo->prepare("
                INSERT INTO product_images
                (
                    product_id,
                    image,
                    sort_order,
                    is_main
                )
                VALUES
                (
                    :product_id,
                    :image,
                    0,
                    1
                )
            ");

            $imageStmt->execute([
                'product_id' => $productId,
                'image' => $mainImage
            ]);
        }


        if (
            isset($_FILES['gallery']['name']) &&
            is_array(
                $_FILES['gallery']['name']
            )
        ) {

            $galleryStmt = $pdo->prepare("
                INSERT INTO product_images
                (
                    product_id,
                    image,
                    sort_order,
                    is_main
                )
                VALUES
                (
                    :product_id,
                    :image,
                    :sort_order,
                    0
                )
            ");

            $sortOrder = 1;

            foreach (
                $_FILES['gallery']['name']
                as $index => $name
            ) {

                if (
                    $_FILES['gallery']['error'][$index]
                    === UPLOAD_ERR_NO_FILE
                ) {
                    continue;
                }

                $file = [
                    'name' =>
                        $_FILES['gallery']['name'][$index],

                    'type' =>
                        $_FILES['gallery']['type'][$index],

                    'tmp_name' =>
                        $_FILES['gallery']['tmp_name'][$index],

                    'error' =>
                        $_FILES['gallery']['error'][$index],

                    'size' =>
                        $_FILES['gallery']['size'][$index]
                ];

                $galleryImage =
                    admin_upload_image(
                        $file,
                        'products'
                    );

                if ($galleryImage) {

                    $galleryStmt->execute([
                        'product_id' =>
                            $productId,

                        'image' =>
                            $galleryImage,

                        'sort_order' =>
                            $sortOrder++
                    ]);
                }
            }
        }


        $pdo->commit();

        admin_flash(
            'success',
            __('product_created')
        );

        admin_redirect(
            'products.php'
        );

    } catch (Throwable $e) {

        if (
            $pdo->inTransaction()
        ) {
            $pdo->rollBack();
        }

        $error =
            $e->getMessage();
    }
}


$categories =
    $pdo
        ->query("
            SELECT
                c.id,
                ct.name
            FROM categories c
            INNER JOIN category_translations ct
                ON ct.category_id = c.id
                AND ct.language = 'fa'
            WHERE c.status = 'active'
            ORDER BY ct.name ASC
        ")
        ->fetchAll();

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
                    href="products.php"
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

                    <div class="admin-panel-heading">
                        <h2>
                            <?= __('general_information') ?>
                        </h2>
                    </div>


                    <div class="admin-form-fields">

                        <div class="admin-input-group">

                            <label>
                                <?= __('sku') ?>
                            </label>

                            <input
                                type="text"
                                name="sku"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('category') ?>
                            </label>

                            <select name="category_id">

                                <option value="0">
                                    -
                                </option>

                                <?php foreach (
                                    $categories as $category
                                ): ?>

                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                    >
                                        <?= e(
                                            $category['name']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('price') ?> (€)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="price"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('old_price') ?> (€)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="compare_price"
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('stock') ?>
                            </label>

                            <input
                                type="number"
                                min="0"
                                name="stock"
                                value="0"
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('status') ?>
                            </label>

                            <select name="status">

                                <?php foreach (
                                    admin_product_statuses()
                                    as $item
                                ): ?>

                                    <option
                                        value="<?= e($item) ?>"
                                    >
                                        <?= __($item) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <label class="admin-checkbox">

                            <input
                                type="checkbox"
                                name="featured"
                            >

                            <span>
                            <?= __('featured_product') ?>
                        </span>

                        </label>

                    </div>

                </section>


                <section class="admin-panel">

                    <div class="admin-panel-heading">

                        <h2>
                            <?= __('images') ?>
                        </h2>

                    </div>


                    <div class="admin-form-fields">

                        <div class="admin-input-group">

                            <label>
                                <?= __('main_image') ?>
                            </label>

                            <input
                                type="file"
                                name="main_image"
                                accept="image/jpeg,image/png,image/webp"
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('gallery_images') ?>
                            </label>

                            <input
                                type="file"
                                name="gallery[]"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                            >

                        </div>

                    </div>

                </section>


                <section class="admin-panel full">

                    <div class="admin-panel-heading">

                        <h2>
                            فارسی
                        </h2>

                    </div>


                    <div class="admin-form-fields">

                        <div class="admin-input-group">

                            <label>
                                <?= __('name') ?>
                            </label>

                            <input
                                type="text"
                                name="fa_name"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                Slug
                            </label>

                            <input
                                type="text"
                                name="fa_slug"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('short_description') ?>
                            </label>

                            <textarea
                                name="fa_short"
                                rows="3"
                            ></textarea>

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('description') ?>
                            </label>

                            <textarea
                                name="fa_description"
                                rows="8"
                            ></textarea>

                        </div>

                    </div>

                </section>


                <section class="admin-panel full">

                    <div class="admin-panel-heading">

                        <h2>
                            Deutsch
                        </h2>

                    </div>


                    <div class="admin-form-fields">

                        <div class="admin-input-group">

                            <label>
                                <?= __('name') ?>
                            </label>

                            <input
                                type="text"
                                name="de_name"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                Slug
                            </label>

                            <input
                                type="text"
                                name="de_slug"
                                required
                            >

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('short_description') ?>
                            </label>

                            <textarea
                                name="de_short"
                                rows="3"
                            ></textarea>

                        </div>


                        <div class="admin-input-group">

                            <label>
                                <?= __('description') ?>
                            </label>

                            <textarea
                                name="de_description"
                                rows="8"
                            ></textarea>

                        </div>

                    </div>

                </section>

            </div>

        </form>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>