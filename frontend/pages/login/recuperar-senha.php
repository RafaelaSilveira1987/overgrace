<?php
$appBasePath = $appBasePath ?? '';
$isAdminRecovery = (($_GET['mode'] ?? '') === 'admin');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $isAdminRecovery ? 'Recuperar senha administrativa' : 'Recuperar senha' ?></title>

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
                <?= $isAdminRecovery ? 'Recuperar acesso administrativo' : 'Recuperar senha' ?>
            </h1>

            <p class="password-description">
                <?= $isAdminRecovery
                    ? 'Informe o e-mail do usuário administrador para criar uma nova senha.'
                    : 'Informe o e-mail cadastrado na sua conta. Enviaremos as instruções para você criar uma nova senha.' ?>
            </p>

            <form id="formForgotPassword" class="password-form">

                <div class="form-group">

                    <label for="email">
                        E-mail
                    </label>

                    <input type="email" id="email" name="email" autocomplete="email" placeholder="seuemail@exemplo.com"
                        required>

                </div>

                <button type="submit" class="password-btn">
                    Enviar instruções
                </button>

            </form>

            <div id="developmentResetLink"></div>

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
        '/frontend/js/modules/auth/forgot-password.js?v=5',
        ENT_QUOTES,
        'UTF-8'
    ) ?>"></script>

</body>

</html>