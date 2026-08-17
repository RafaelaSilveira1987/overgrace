<?php
$appBasePath = $appBasePath ?? '';
$isAdminRecovery = (($_GET['mode'] ?? '') === 'admin');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $isAdminRecovery ? 'Redefinir senha administrativa' : 'Redefinir senha' ?></title>

    <link rel="stylesheet" href="<?= htmlspecialchars(
            $appBasePath .
            '/frontend/css/password-recovery.css',
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

</head>

<body>

    <main class="password-page">

        <section class="password-card">

            <h1>
                Nova senha
            </h1>

            <p class="password-description">
                Crie uma nova senha para acessar
                sua conta.
            </p>

            <form id="formResetPassword" class="password-form">

                <div class="form-group">

                    <label for="password">
                        Nova senha
                    </label>

                    <input type="password" id="password" name="password" minlength="6" autocomplete="new-password"
                        required>

                </div>

                <div class="form-group">

                    <label for="passwordConfirmation">
                        Confirmar nova senha
                    </label>

                    <input type="password" id="passwordConfirmation" name="passwordConfirmation" minlength="6"
                        autocomplete="new-password" required>

                </div>

                <p class="password-requirements">
                    Utilize pelo menos 6 caracteres.
                </p>

                <button type="submit" class="password-btn">
                    Alterar senha
                </button>

            </form>

            <div class="password-footer">

                <a href="<?= htmlspecialchars(
                    $appBasePath . ($isAdminRecovery ? '/admin-login' : '/login'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Voltar para o login
                </a>

            </div>

        </section>

    </main>

    <script>
    window.APP_BASE_PATH = <?= json_encode(
    $appBasePath,
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE
) ?>;
    </script>

    <script type="module" src="<?= htmlspecialchars(
        $appBasePath .
        '/frontend/js/modules/auth/reset-password.js?v=3',
        ENT_QUOTES,
        'UTF-8'
    ) ?>"></script>

</body>

</html>