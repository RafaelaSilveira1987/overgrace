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

  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/colecoes.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />
</head>

<body>
  <!-- Topbar   -->
  <div class="topbar">Frete grátis acima de R$ 299 - Parcele em até 6x</div>

  <header>
    <div class="header-inner">
      <nav class="header-left">
        <a href="loja">Loja</a>
        <a href="colecoes">Coleções</a>
        <a href="sobre">Sobre</a>
      </nav>

      <a href="loja" class="logo">OverGrace</a>

      <div class="header-right">
        <a href="login">Entrar</a>
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
    <div class="hero-content">
      <p class="eyebrow">Nova coleção 2025</p>
      <h1>Vestidos pela graça.<br />Chamados para anunciar.</h1>
      <p>
        Cada peça da OverGrace carrega propósito, identidade e fé para uma
        geração que vive para anunciar.
      </p>
      <a href="lista" class="btn-outline">Comprar agora</a>
    </div>
  </section>

  <section class="manifesto">
    <img
      src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=1000&q=80"
      alt="Sobre a marca" />
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

  <section>
    <h2 class="section-title">Coleções em destaque</h2>
    <div class="collections-grid">
      <a href="lista?cat=camisas" class="card">
        <img
          src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=900&q=80" />
        <div class="card-content">
          <h3>Camisas</h3>
        </div>
      </a>
      <a href="lista?cat=bones" class="card">
        <img
          src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&q=80" />
        <div class="card-content">
          <h3>Bonés</h3>
        </div>
      </a>
      <a href="lista?cat=acessorios" class="card">
        <img
          src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&q=80" />
        <div class="card-content">
          <h3>Acessórios</h3>
        </div>
      </a>
    </div>
  </section>

  <section class="featured-products">
    <h2 class="section-title">Mais vendidos</h2>
    <div class="products-grid">
      <div class="product-card">
        <img
          src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?w=900&q=80" />
        <div class="product-info">
          <h4>Camiseta Grace</h4>
          <p class="price">R$ 89,90</p>
        </div>
      </div>
      <div class="product-card">
        <img
          src="https://images.unsplash.com/photo-1503342394128-c104d54dba01?w=900&q=80" />
        <div class="product-info">
          <h4>Boné Faith</h4>
          <p class="price">R$ 69,90</p>
        </div>
      </div>
      <div class="product-card">
        <img
          src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&q=80" />
        <div class="product-info">
          <h4>Moletom OverGrace</h4>
          <p class="price">R$ 149,90</p>
        </div>
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

</body>

</html>