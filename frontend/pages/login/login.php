<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isAdminLogin = $path === '/overgrace/admin-login' || ($_GET['mode'] ?? '') === 'admin';
$loginMode = $isAdminLogin ? 'admin' : 'client';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdminLogin ? 'Login Admin' : 'Login' ?> | OverGrace</title>
    <link rel="stylesheet" href="/overgrace/frontend/pages/login/login.css">
</head>

<body>

    <div class="login-container">
        <div class="login-banner">
            <div class="login-banner-content">
                <h1>OverGrace</h1>
                <p>Cobertos por Ele, vivemos para anunciar!</p>
            </div>
        </div>

        <form id="formLogin">
            <div class="login-form">
                <div class="logo">OverGrace</div>
                <h2><?= $isAdminLogin ? 'Entrar no painel' : 'Entrar' ?></h2>
                <p><?= $isAdminLogin ? 'Acesse o painel administrativo' : 'Acesse sua conta para continuar' ?></p>

                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" id="email" placeholder="Digite seu e-mail">
                </div>

                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" id="password" placeholder="Digite sua senha">
                </div>

                <button class="login-btn">Entrar</button>

                <?php if (!$isAdminLogin): ?>
                    <div class="divider">ou</div>

                    <button class="google-btn" type="button">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg"
                            alt="Google">
                        Entrar com Google
                    </button>

                    <div class="extra-links">
                        <p><a href="#">Esqueci minha senha</a></p>
                    </div>

                    <p class="cadastro-text">
                        Nao tem uma conta?
                        <a class="btn-cadastro" href="/overgrace/cadastro">Cadastre-se aqui</a>
                    </p>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        window.LOGIN_MODE = '<?= $loginMode ?>';
    </script>
    <script type="module" src="/overgrace/frontend/js/modules/auth/login.js?v=6"></script>

</body>

</html>
