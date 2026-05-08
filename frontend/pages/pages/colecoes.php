<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coleções | OverGrace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="frontend/pages/pages/pages-css/colecoes.css" />
    <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />
</head>

<body>
    <!-- Topbar   -->
    <div class="topbar">Frete grátis acima de R$ 299 - Parcele em até 6x</div>

    <header>
        <div class="header-inner">
            <nav class="header-left">
                <a href="loja">Home</a>
                <a href="lista">Loja</a>
                <a href="colecoes">Coleções</a>
                <a href="sobre">Sobre</a>
            </nav>

            <a href="loja" class="logo">OverGrace</a>

            <div class="header-right">
                <div class="customer-summary" id="customerSummary" hidden>
                    <span id="customerName">Ola</span>
                    <!-- <small id="customerEmail"></small> -->
                </div>
                <a href="login" id="loginLink">Entrar</a>
                <a href="minha-conta" id="accountLink" hidden>Minha Conta</a>
                <button class="header-link-button" id="logoutButton" type="button" hidden>Sair</button>
                <a href="carrinho">
                    <button class="cart-btn">
                        Carrinho
                        <span class="cart-count" id="cartCount">0</span>
                    </button>
                </a>
            </div>
        </div>
    </header>


    <section class="brand-hero">
        <!-- IMAGEM -->
        <img src="frontend/pages/assets/img5.png" alt="Coleção OverGrace" class="hero-bg" />
        <!-- OVERLAY -->
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="eyebrow">
                DROP 02 • WINTER COLLECTION
            </div>
            <h1>
                Estilo que permanece.<br>
                Presença que marca.
            </h1>
            <p>
                Peças minimalistas desenvolvidas para quem
                entende que vestir também comunica identidade.
            </p>
            <a href="#lista" class="btn-outline">
                Explorar coleção
                <span class="arrow">→</span>
            </a>
        </div>
    </section>

    <section class="manifesto">
        <img src="frontend/pages/assets/img1.png" alt="Sobre a marca" />
        <div>
            <h2>Mais que roupas.</h2>
            <p>
                A OverGrace nasceu para vestir quem carrega uma mensagem. Unimos moda,
                propósito e excelência para criar peças minimalistas, modernas e
                cheias de significado.
            </p>
            <br />
            <p>
                Nosso desejo é inspirar uma geração a viver o Evangelho em cada
                detalhe.
            </p>
        </div>
    </section>

    <section class="drops-section">
        <div class="drops-heading">
            <span class="drops-kicker">OVERGRACE DROPS</span>

            <h2>
                Mais que catálogo.
                <span>Conceito.</span>
            </h2>

            <p>
                Coleções desenvolvidas para transmitir identidade,
                estética e propósito.
            </p>
        </div>
        <div class="drops-top">

            <a href="#" class="drop-card">
                <img src="frontend/pages/assets/img2.png">
                <div class="drop-overlay"></div>

                <div class="drop-content">
                    <span>DROP 01</span>
                    <h3>Essential Lines</h3>
                </div>
            </a>

            <a href="#" class="drop-card">
                <img src="frontend/pages/assets/img5.png">
                <div class="drop-overlay"></div>

                <div class="drop-content">
                    <span>DROP 02</span>
                    <h3>Winter Layers</h3>
                </div>
            </a>

        </div>

        <div class="drops-bottom">

            <a href="#" class="drop-card featured">
                <img src="frontend/pages/assets/img6.png">
                <div class="drop-overlay"></div>

                <div class="drop-content">
                    <span>DROP 03</span>
                    <h2>Street Uniform</h2>

                    <p>
                        Modelagens amplas, tons neutros e estética minimalista.
                    </p>
                </div>
            </a>

        </div>

    </section>

    <section class="best-sellers">
        <div class="section-head">
            <div>
                <span class="section-eyebrow">
                    OVERGRACE SELECTION
                </span>
                <h2>
                    Mais vendidos
                </h2>
            </div>
            <a href="lista" class="section-link">
                Ver catálogo →
            </a>
        </div>
        <div class="best-wrapper">
            <div class="best-track" id="bestTrack"></div>
            <!-- Cards de produtos mais vendidos serão inseridos aqui via JavaScript -->
        </div>
        </div>
    </section>

    <footer>
        <div class="footer-top">
            <div>
                <div class="footer-logo">OverGrace</div>
                <p class="footer-tagline">
                    Camisas e bonés para quem importa com o que veste - sem abrir mão do
                    conforto e estilo.
                </p>
            </div>
            <div class="footer-col">
                <h4>Loja</h4>
                <a href="camisas">Camisas</a>
                <a href="bonés">Bonés</a>
                <a href="cropped">Cropped</a>
                <a href="camisas">Kits</a>
            </div>
            <div class="footer-col">
                <h4>Empresa</h4>
                <a href="camisas">Sobre nós</a>
                <a href="bonés">Contato</a>
                <a href="cropped">Instagram</a>
                <a href="camisas">Seja Parceiro</a>
            </div>
            <div class="footer-col">
                <h4>Ajuda</h4>
                <a href="camisas">trocas e Devoluções</a>
                <a href="bonés">Rastrear Pedido </a>
                <a href="cropped">Tamanhos</a>
                <a href="camisas">FAQ</a>
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
    <script type="module" src="frontend/js/modules/cart/qtyCart.js"></script>
    <script type="module" src="frontend/js/modules/auth/sessionHeader.js?v=1"></script>
    <script type="module" src="frontend/js/modules/product/bestsellers.js"></script>

</body>

</html>