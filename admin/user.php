<?php

require_once __DIR__ . '/includes/header.php';

$pageTitle = __('user');


$id =
    (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    'id' => $id
]);

$user =
    $stmt->fetch();

if (!$user) {

    admin_redirect(
        'users.php'
    );
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    admin_check_csrf();

    try {

        $password =
            $_POST['password'] ?? '';

        $update =
            $pdo->prepare("
                UPDATE users

                SET
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone,
                    status = :status

                WHERE id = :id
            ");

        $update->execute([
            'first_name' =>
                trim(
                    $_POST['first_name'] ?? ''
                ),

            'last_name' =>
                trim(
                    $_POST['last_name'] ?? ''
                ),

            'email' =>
                trim(
                    $_POST['email'] ?? ''
                ),

            'phone' =>
                trim(
                    $_POST['phone'] ?? ''
                ),

            'status' =>
                $_POST['status'] === 'blocked'
                    ? 'blocked'
                    : 'active',

            'id' =>
                $id
        ]);


        if (
            $password !== ''
        ) {

            $passwordStmt =
                $pdo->prepare("
                    UPDATE users

                    SET password_hash = :password_hash

                    WHERE id = :id
                ");

            $passwordStmt->execute([
                'password_hash' =>
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),

                'id' =>
                    $id
            ]);
        }


        admin_flash(
            'success',
            __('user_updated')
        );

        admin_redirect(
            'user.php?id=' .
            $id
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
            class="admin-form"
        >

            <?= admin_csrf_field() ?>


            <div class="admin-form-toolbar">

                <a
                    href="users.php"
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


            <section class="admin-panel">

                <div class="admin-form-grid">

                    <div class="admin-input-group">

                        <label>
                            <?= __('first_name') ?>
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="<?= e(
                                $user['first_name']
                            ) ?>"
                        >

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('last_name') ?>
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="<?= e(
                                $user['last_name']
                            ) ?>"
                        >

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('email') ?>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= e(
                                $user['email']
                            ) ?>"
                        >

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('phone') ?>
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?= e(
                                $user['phone'] ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('status') ?>
                        </label>

                        <select name="status">

                            <option
                                value="active"
                                <?= $user['status'] === 'active'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= __('active') ?>
                            </option>

                            <option
                                value="blocked"
                                <?= $user['status'] === 'blocked'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= __('blocked') ?>
                            </option>

                        </select>

                    </div>


                    <div class="admin-input-group">

                        <label>
                            <?= __('new_password') ?>
                        </label>

                        <input
                            type="password"
                            name="password"
                            minlength="8"
                        >

                    </div>

                </div>

            </section>

        </form>

    </div>


<?php

require_once __DIR__ . '/includes/footer.php';
?>