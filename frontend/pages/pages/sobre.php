<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sobre | OverGrace</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/sobre.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />



</head>

<body>
  <!-- Topbar   -->
  <div class="topbar">Frete grátis acima de R$ 299 - Parcele em até 6x</div>

  <!-- Header -->
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

  <section class="about-hero">
    <h1>Não vendemos apenas roupas.<br />Vestimos propósito.</h1>
  </section>

  <section class="story">
    <img
      src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=1200&q=80"
      alt="Nossa história" />
    <div>
      <h2>Nossa história</h2>
      <p>
        A OverGrace nasceu do desejo de unir fé, identidade e estilo em cada
        detalhe. Criamos peças modernas e minimalistas para uma geração que
        carrega uma mensagem.
      </p>
      <br />
      <p>
        Mais do que moda, queremos inspirar vidas a viverem e anunciarem o
        Evangelho com autenticidade.
      </p>
    </div>
  </section>

  <section class="values">
    <h2 class="section-title" style="text-align: center">
      Missão, Visão & Valores
    </h2>
    <div class="values-grid">
      <div class="value-card">
        <h3>Missão</h3>
        <p>Vestir pessoas com propósito e anunciar Cristo através da moda.</p>
      </div>
      <div class="value-card">
        <h3>Visão</h3>
        <p>Alcançar uma geração com estilo, identidade e fé.</p>
      </div>
      <div class="value-card">
        <h3>Valores</h3>
        <p>Excelência, autenticidade, propósito e compromisso com o Reino.</p>
      </div>
    </div>
  </section>

  <section class="message">
    <h2>Cobertos por Ele,<br />vivemos para anunciar.</h2>
    <p>Uma marca que carrega propósito em cada peça.</p>
  </section>

  <section class="impact">
    <h2 class="section-title">Nosso impacto</h2>
    <div class="impact-grid">
      <div class="impact-item">
        <h3>+500</h3>
        <p>Clientes atendidos</p>
      </div>
      <div class="impact-item">
        <h3>+50</h3>
        <p>Envios por mês</p>
      </div>
      <div class="impact-item">
        <h3>100%</h3>
        <p>Propósito</p>
      </div>
    </div>
  </section>

  <section class="cta">
    <h2 class="section-title">Vista essa mensagem</h2>
    <p>Conheça nossas coleções e faça parte desse movimento.</p>
    <a href="../pages/loja.html" class="btn-outline">Comprar agora</a>
  </section>

  <footer>
    <div class="footer-top">
      <div>
        <div class="footer-logo">OverGrace</div>
        <p class="footer-tagline">
          Camisas e bonés para quem importa com o que veste - sem abrir mão do conforto e estilo.
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
        <a href="bonés">Rastrear Pedido
        </a>
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