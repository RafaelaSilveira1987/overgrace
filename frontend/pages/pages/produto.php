<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Produto | OverGrace</title>
    <link rel="icon" href="/overgrace/frontend/assets/favicon.ico" />

    <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/produto.css" />
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

    <div class="product-page">
        <div class="product-container">
            <!-- GALERIA -->
            <div class="product-gallery">
                <div class="main-image-wrap">
                    <img class="main-image" id="mainProductImage" />
                </div>

                <div class="thumb-list" id="thumbList">

                </div>
            </div>

            <!-- INFO -->
            <form id="formProd">
                <div class="product-info">
                    <h1 id="f-name"></h1>

                    <p class="product-price">
                        <span class="old-price" id="f-price-old">R$0,00</span>
                        R$<span id="f-price">0,00</span>
                    <p class="installments">ou 3x de R$ 26,63 sem juros</p>
                    </p>

                    <p class="short-description">
                    </p>

                    <h4>Tamanho</h4>
                    <div class="size-options" id="sizes">
                    </div>

                    <div class="qty-cart">
                        <input type="hidden" id="f-id">
                        <input type="number" value="1" min="1" id="f-qtd" />
                        <button class="buy-btn">Adicionar ao carrinho</button>
                    </div>

                    <div class="description-full">
                        <h4>Descrição</h4>
                        <p id="f-desc">
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- RELACIONADOS -->
        <section class="section-block">
            <h2>Produtos relacionados</h2>

            <div class="related-wrapper">
                <div class="related-track" id="relatedTrack">
                    <!-- CARROSSEL -->
                </div>
            </div>
        </section>

        <section class="coming-section">
            <div class="coming-header">
                <h2>Novidades em breve</h2>
                <p class="coming-subtitle">
                    Estamos preparando peças exclusivas para a próxima coleção.
                </p>
            </div>

            <div class="coming-grid">
                <div class="coming-card">
                    <img src="frontend/pages/assets/img5.png" alt="">
                    <div class="coming-overlay">
                        <p>Nova coleção inverno</p>
                    </div>
                </div>

                <div class="coming-card">
                    <img src="frontend/pages/assets/img6.png" alt="">
                    <div class="coming-overlay">
                        <p>Novos acessórios</p>
                    </div>
                </div>
            </div>

            <div class="coming-cta">
                <p>Quer ser avisado primeiro?</p>
                <button>Receber novidades</button>
            </div>
        </section>
    </div>

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
                <a href="camisas">Trocas e Devoluções</a>
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
    <script type="module" src="frontend/js/modules/cart/formVitrine.js"></script>
    <script src="frontend/js/modules/cart/utils.js"></script>
    <script type="module" src="frontend/js/modules/auth/sessionHeader.js?v=1"></script>
    <script type="module" src="frontend/js/modules/product/relacionedtrack.js"></script>

    <script>
    document.querySelectorAll('.thumb-list img').forEach(img => {

        img.addEventListener('click', () => {

            document.getElementById('mainProductImage').src = img.src;

            document
                .querySelectorAll('.thumb-list img')
                .forEach(i => i.classList.remove('active'));

            img.classList.add('active');

        });

    });
    </script>

</body>

</html>