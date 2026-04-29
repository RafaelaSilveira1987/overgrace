<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Produtos | OverGrace</title>

  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/loja.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />
</head>

<body>
  <!-- Topbar   -->
  <div class="topbar">Frete grátis acima de R$ 299 - Parcele em até 6x</div>

  <header>
    <div class="header-inner">
      <nav class="header-left">
        <a href="lista">Loja</a>
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

  <div class="products-page">
    <div class="page-hero">
      <h1>Nossos Produtos</h1>
      <p>Explore nossa coleção completa</p>
    </div>

    <!-- FILTROS -->
    <div class="filters">
      <button class="filter-btn active" onclick="filtrar('todos', this)">
        Todos
      </button>
      <button class="filter-btn" onclick="filtrar('camisas', this)">
        Camisas
      </button>
      <button class="filter-btn" onclick="filtrar('bones', this)">
        Bonés
      </button>
      <button class="filter-btn" onclick="filtrar('acessorios', this)">
        Acessórios
      </button>
      <button class="filter-btn" onclick="filtrar('kits', this)">Kits</button>
    </div>  
  
    <!-- GRID -->
    <div class="products-grid" id="lista-produtos">
    </div>
  </div>

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

  <script type="module" src="frontend/js/modules/product/listaVitrine.js"></script>
  <script>
    function filtrar(cat, btn) {
      const cards = document.querySelectorAll(".product-card");
      const botoes = document.querySelectorAll(".filter-btn");

      botoes.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      cards.forEach((card) => {
        if (cat === "todos" || card.dataset.category === cat) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }
  </script>
</body>

</html>