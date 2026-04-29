<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Produto | FORMA</title>

  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/produto.css" />
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
        <button class="cart-btn" onclick="toggleCart()">
          Carrinho
          <span class="cart-count" id="cartCount">0</span>
        </button>
      </div>
    </div>
  </header>

  <div class="product-page">
    <div class="product-container">
      <!-- GALERIA -->
      <div class="product-gallery">
        <img
          src="https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=900"
          class="main-image"
          id="mainProductImage" />

        <div class="thumb-list">
          <img
            src="https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=300"
            onclick="trocarImagem(this)" />
          <img
            src="https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=300"
            onclick="trocarImagem(this)" />
          <img
            src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=300"
            onclick="trocarImagem(this)" />
        </div>
      </div>

      <!-- INFO -->
      <div class="product-info">
        <h1>Camisa Linho Off-White</h1>

        <p class="price">
          <span class="old-price">R$219,00</span>
          R$189,00
        </p>

        <p class="short-description">
          Modelagem premium em linho leve, ideal para ocasiões casuais e
          elegantes.
        </p>

        <h4>Tamanho</h4>
        <div class="size-options">
          <button class="size-btn">P</button>
          <button class="size-btn">M</button>
          <button class="size-btn">G</button>
          <button class="size-btn">GG</button>
        </div>

        <div class="qty-cart">
          <input type="number" value="1" min="1" />
          <button class="buy-btn">Adicionar ao carrinho</button>
        </div>

        <div class="description-full">
          <h4>Descrição</h4>
          <p>
            Produzida com tecido respirável de alta qualidade, acabamento
            premium e costura reforçada.
          </p>
        </div>
      </div>
    </div>

    <!-- RELACIONADOS -->
    <section class="section-block">
      <h2>Produtos relacionados</h2>

      <div class="related-grid">
        <div class="card">
          <img
            src="https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=600" />
          <div class="card-content">
            <p>Camisa Oversized Cáqui</p>
            <strong>R$175,00</strong>
          </div>
        </div>

        <div class="card">
          <img
            src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600" />
          <div class="card-content">
            <p>Camiseta Essential</p>
            <strong>R$119,00</strong>
          </div>
        </div>
      </div>
    </section>

    <!-- NOVIDADES -->
    <section class="section-block">
      <h2>Novidades em breve</h2>

      <div class="coming-grid">
        <div class="card">
          <img
            src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=600" />
          <div class="card-content">
            <p>Nova coleção inverno</p>
          </div>
        </div>

        <div class="card">
          <img
            src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600" />
          <div class="card-content">
            <p>Novos acessórios</p>
          </div>
        </div>
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

  <script>
    function trocarImagem(el) {
      document.getElementById("mainProductImage").src = el.src;
    }
  </script>
</body>

</html>