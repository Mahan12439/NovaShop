<?php

require_once __DIR__ . '/includes/auth.php';

$pageTitle = __('categories');
$error = '';


// =====================================================
// Create Category — process before HTML
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['action'] ?? '') === 'create_category'
) {
    admin_check_csrf();

    $uploadedImage = null;

    try {
        $faName = trim($_POST['fa_name'] ?? '');
        $faSlug = trim($_POST['fa_slug'] ?? '');

        $deName = trim($_POST['de_name'] ?? '');
        $deSlug = trim($_POST['de_slug'] ?? '');

        $parentId = (int) ($_POST['parent_id'] ?? 0);
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        $status = $_POST['status'] ?? 'active';

        if (
            $faName === '' ||
            $faSlug === '' ||
            $deName === '' ||
            $deSlug === ''
        ) {
            throw new RuntimeException(__('required_field'));
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            throw new RuntimeException(__('invalid_data'));
        }

        $uploadedImage = admin_upload_image(
            $_FILES['image'] ?? [],
            'categories'
        );

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO categories
                (parent_id, image, sort_order, status)
            VALUES
                (:parent_id, :image, :sort_order, :status)
        ");

        $stmt->execute([
            'parent_id' => $parentId > 0 ? $parentId : null,
            'image' => $uploadedImage,
            'sort_order' => $sortOrder,
            'status' => $status
        ]);

        $categoryId = (int) $pdo->lastInsertId();

        $translationStmt = $pdo->prepare("
            INSERT INTO category_translations
                (category_id, language, name, slug)
            VALUES
                (:category_id, :language, :name, :slug)
        ");

        $translationStmt->execute([
            'category_id' => $categoryId,
            'language' => 'fa',
            'name' => $faName,
            'slug' => $faSlug
        ]);

        $translationStmt->execute([
            'category_id' => $categoryId,
            'language' => 'de',
            'name' => $deName,
            'slug' => $deSlug
        ]);

        $pdo->commit();

        admin_flash('success', __('category_created'));

        admin_redirect('categories.php');

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($uploadedImage) {
            admin_delete_image('categories', $uploadedImage);
        }

        $error = $e->getMessage();
    }
}


// =====================================================
// Categories
// =====================================================

$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.parent_id,
        c.image,
        c.sort_order,
        c.status,
        ct.name,
        ct.slug

    FROM categories c

    INNER JOIN category_translations ct
        ON ct.category_id = c.id
        AND ct.language = :language

    ORDER BY c.sort_order ASC, c.id DESC
");

$stmt->execute([
    'language' => $currentLanguage
]);

$categories = $stmt->fetchAll();


// Parent options for modal

$stmt = $pdo->prepare("
    SELECT
        c.id,
        ct.name
    FROM categories c
    INNER JOIN category_translations ct
        ON ct.category_id = c.id
        AND ct.language = :language
    WHERE c.status = 'active'
    ORDER BY ct.name ASC
");

$stmt->execute([
    'language' => $currentLanguage
]);

$parentCategories = $stmt->fetchAll();


require_once __DIR__ . '/includes/header.php';

?>


    <div class="admin-page-toolbar">

        <div>
            <p class="admin-page-description">
                <?= e(__('category_management')) ?>
            </p>
        </div>

        <button
                type="button"
                class="admin-button primary"
                id="openCategoryModal"
        >
            + <?= e(__('add_category')) ?>
        </button>

    </div>


<?php if ($error !== ''): ?>
    <div class="admin-alert error">
        <?= e($error) ?>
    </div>
<?php endif; ?>


    <div class="admin-panel">

        <div class="admin-panel-heading">

            <div>
                <span>NovaShop</span>
                <h2><?= e(__('categories')) ?></h2>
            </div>

            <span class="admin-result-count">
            <?= count($categories) ?>
        </span>

        </div>


        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>
                <tr>
                    <th><?= e(__('name')) ?></th>
                    <th><?= e(__('sort_order')) ?></th>
                    <th><?= e(__('status')) ?></th>
                    <th><?= e(__('actions')) ?></th>
                </tr>
                </thead>

                <tbody>

                <?php if (!empty($categories)): ?>

                    <?php foreach ($categories as $category): ?>

                        <tr>

                            <td>
                                <strong><?= e($category['name']) ?></strong>
                            </td>

                            <td><?= (int)$category['sort_order'] ?></td>

                            <td>
                            <span class="admin-status status-<?= e($category['status']) ?>">
                                <?= e(__($category['status'])) ?>
                            </span>
                            </td>

                            <td>
                                <div class="admin-actions">

                                    <a
                                            href="category-edit.php?id=<?= (int)$category['id'] ?>"
                                            class="admin-table-action"
                                    >
                                        <?= e(__('edit')) ?>
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
                                                value="<?= (int)$category['id'] ?>"
                                        >

                                        <button
                                                type="submit"
                                                class="admin-table-action danger"
                                        >
                                            <?= e(__('delete')) ?>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="admin-empty-table">
                            <?= e(__('no_categories')) ?>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =====================================================
         ADD CATEGORY MODAL
         ===================================================== -->

    <div
            class="admin-modal"
            id="categoryModal"
            aria-hidden="true"
    >

        <div
                class="admin-modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="categoryModalTitle"
        >

            <div class="admin-modal-header">

                <h2 id="categoryModalTitle">
                    <?= e(__('add_category')) ?>
                </h2>

                <button
                        type="button"
                        class="admin-modal-close"
                        id="closeCategoryModal"
                        aria-label="<?= e(__('close')) ?>"
                >
                    ×
                </button>

            </div>


            <form
                    method="POST"
                    enctype="multipart/form-data"
            >

                <?= admin_csrf_field() ?>

                <input
                        type="hidden"
                        name="action"
                        value="create_category"
                >


                <div class="admin-form-grid">

                    <section class="admin-panel">

                        <div class="admin-form-fields">

                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('parent_category')) ?>
                                </label>

                                <select name="parent_id">

                                    <option value="0">
                                        <?= e(__('none')) ?>
                                    </option>

                                    <?php foreach ($parentCategories as $parent): ?>

                                        <option value="<?= (int)$parent['id'] ?>">
                                            <?= e($parent['name']) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('sort_order')) ?>
                                </label>

                                <input
                                        type="number"
                                        name="sort_order"
                                        value="0"
                                >

                            </div>


                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('status')) ?>
                                </label>

                                <select name="status">

                                    <option value="active">
                                        <?= e(__('active')) ?>
                                    </option>

                                    <option value="inactive">
                                        <?= e(__('inactive')) ?>
                                    </option>

                                </select>

                            </div>


                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('image')) ?>
                                </label>

                                <input
                                        type="file"
                                        name="image"
                                        accept="image/jpeg,image/png,image/webp"
                                >

                            </div>

                        </div>

                    </section>


                    <section class="admin-panel">

                        <div class="admin-panel-heading">
                            <h2>فارسی</h2>
                        </div>

                        <div class="admin-form-fields">

                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('name')) ?>
                                </label>

                                <input
                                        type="text"
                                        name="fa_name"
                                        required
                                >

                            </div>

                            <div class="admin-input-group">

                                <label>Slug</label>

                                <input
                                        type="text"
                                        name="fa_slug"
                                        required
                                >

                            </div>

                        </div>

                    </section>


                    <section class="admin-panel full">

                        <div class="admin-panel-heading">
                            <h2>Deutsch</h2>
                        </div>

                        <div class="admin-form-fields">

                            <div class="admin-input-group">

                                <label>
                                    <?= e(__('name')) ?>
                                </label>

                                <input
                                        type="text"
                                        name="de_name"
                                        required
                                >

                            </div>

                            <div class="admin-input-group">

                                <label>Slug</label>

                                <input
                                        type="text"
                                        name="de_slug"
                                        required
                                >

                            </div>

                        </div>

                    </section>

                </div>


                <div class="admin-modal-footer">

                    <button
                            type="button"
                            class="admin-button"
                            id="cancelCategoryModal"
                    >
                        <?= e(__('cancel')) ?>
                    </button>

                    <button
                            type="submit"
                            class="admin-button primary"
                    >
                        <?= e(__('save')) ?>
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modal = document.getElementById('categoryModal');
            const openButton = document.getElementById('openCategoryModal');
            const closeButton = document.getElementById('closeCategoryModal');
            const cancelButton = document.getElementById('cancelCategoryModal');

            if (!modal || !openButton) return;

            function openModal() {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                const firstInput = modal.querySelector(
                    'input[name="fa_name"]'
                );

                if (firstInput) firstInput.focus();
            }

            function closeModal() {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            openButton.addEventListener('click', openModal);

            if (closeButton) {
                closeButton.addEventListener('click', closeModal);
            }

            if (cancelButton) {
                cancelButton.addEventListener('click', closeModal);
            }

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

        });
    </script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>