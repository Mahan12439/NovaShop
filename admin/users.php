<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('users');


$q =
    trim($_GET['q'] ?? '');

$sql = "
    SELECT
        id,
        first_name,
        last_name,
        email,
        phone,
        status,
        created_at
    FROM users
";

$params = [];

if ($q !== '') {

    $sql .= "
        WHERE
            first_name LIKE :q
            OR last_name LIKE :q
            OR email LIKE :q
            OR phone LIKE :q
    ";

    $params['q'] =
        '%' . $q . '%';
}

$sql .= "
    ORDER BY id DESC
    LIMIT 100
";

$stmt =
    $pdo->prepare($sql);

$stmt->execute($params);

$users =
    $stmt->fetchAll();

?>

    <div class="admin-page-toolbar">

        <form
            method="GET"
            class="admin-search-form"
        >

            <input
                type="search"
                name="q"
                value="<?= e($q) ?>"
                placeholder="<?= e(__('search')) ?>"
            >

            <button
                type="submit"
                class="admin-button"
            >
                <?= __('search') ?>
            </button>

        </form>

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
                        <?= __('email') ?>
                    </th>

                    <th>
                        <?= __('phone') ?>
                    </th>

                    <th>
                        <?= __('status') ?>
                    </th>

                    <th>
                        <?= __('created_at') ?>
                    </th>

                    <th>
                        <?= __('actions') ?>
                    </th>

                </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $users as $user
                ): ?>

                    <tr>

                        <td>

                            <?= e(
                                trim(
                                    $user['first_name'] .
                                    ' ' .
                                    $user['last_name']
                                )
                            ) ?>

                        </td>


                        <td>
                            <?= e(
                                $user['email']
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                $user['phone'] ?? '-'
                            ) ?>
                        </td>


                        <td>

                        <span
                            class="admin-status status-<?= e(
                                $user['status']
                            ) ?>"
                        >
                            <?= __(
                                $user['status']
                            ) ?>
                        </span>

                        </td>


                        <td>
                            <?= date(
                                'Y/m/d',
                                strtotime(
                                    $user['created_at']
                                )
                            ) ?>
                        </td>


                        <td>

                            <a
                                href="user.php?id=<?= (int) $user['id'] ?>"
                                class="admin-table-action"
                            >
                                <?= __('edit') ?>
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