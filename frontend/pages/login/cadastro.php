<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro - OverGrace</title>
    <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />
    <link rel="stylesheet" href="/overgrace/frontend/pages/login/cadastro.css" />
</head>

<body>
    <header>
        <div class="header-inner">
            <a href="/overgrace/login" class="header-back">&larr; Voltar</a>
            <a href="/overgrace/" class="logo">OverGrace</a>
        </div>
    </header>

    <main class="checkout-layout">
        <form class="checkout-form-area" id="formCadastro" method="post" action="/overgrace/api/register">
            <h2 class="panel-title">Cadastro de Cliente</h2>

            <div class="form-row">
                <div class="field">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Nome" required />
                </div>
                <div class="field">
                    <label for="sobrenome">Sobrenome</label>
                    <input type="text" id="sobrenome" name="sobrenome" placeholder="Sobrenome" required />
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" required />
                </div>
                <div class="field">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Senha" minlength="6" required />
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required />
                </div>
                <div class="field">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15"
                        required />
                </div>
            </div>

            <button class="submit-btn" type="submit">Cadastrar</button>
        </form>
    </main>

    <footer>
        <div class="footer-top">
            <div>
                <div class="footer-logo">OverGrace</div>
                <p class="footer-tagline">
                    Camisas e bones para quem importa com o que veste - sem abrir mao do conforto e estilo.
                </p>
            </div>
            <div class="footer-col">
                <h4>Loja</h4>
                <a href="/overgrace/lista">Camisas</a>
                <a href="/overgrace/lista">Bones</a>
                <a href="/overgrace/lista">Cropped</a>
                <a href="/overgrace/lista">Kits</a>
            </div>
            <div class="footer-col">
                <h4>Empresa</h4>
                <a href="/overgrace/sobre">Sobre nos</a>
                <a href="#">Contato</a>
                <a href="#" target="_blank">Instagram</a>
                <a href="#">Seja Parceiro</a>
            </div>
            <div class="footer-col">
                <h4>Ajuda</h4>
                <a href="#">Trocas e Devolucoes</a>
                <a href="#">Rastrear Pedido</a>
                <a href="#">Tamanhos</a>
                <a href="#">FAQ</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2025 OverGrace. Todos os direitos reservados.</span>
            <div class="footer-socials">
                <a href="#" target="_blank">Instagram</a>
                <a href="#" target="_blank">Whatsapp</a>
            </div>
        </div>
    </footer>

    <script type="module" src="/overgrace/frontend/js/modules/auth/cadastro.js?v=3"></script>
</body>

</html>
