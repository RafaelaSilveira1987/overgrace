<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OverGrace</title>
    <link rel="stylesheet" href="frontend/pages/login/login.css">
    <link rel="stylesheet" href="frontend/css/styles.css">


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
                <h2>Entrar</h2>
                <p>Acesse sua conta para continuar</p>

                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" id="email" placeholder="Digite seu e-mail">
                </div>

                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" id="password" placeholder="Digite sua senha">
                </div>

                <button class="login-btn">Entrar</button>

                <div class="divider">ou</div>

                <button class="google-btn">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg"
                        alt="Google">
                    Entrar com Google
                </button>

                <div class="extra-links">
                    <p><a href="#">Esqueci minha senha</a></p>
                </div>

                <!-- Adicionando link para cadastro -->
                <p class="cadastro-text">
                    Não tem uma conta?
                    <a class="btn-cadastro" href="cadastro.html">Cadastre-se aqui</a>
                </p>
            </div>
        </form>
    </div>

    <script type="module" src="frontend/js/modules/auth/login.js"></script>

</body>

</html>