<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Carrinho — OverGrace</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/carrinho.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />


</head>

<body>
  <div class="topbar">
    Frete grátis acima de R$ 299 &nbsp;·&nbsp; Parcele em até 6x sem juros
  </div>

  <header>
    <div class="header-inner">
      <div class="header-left">
        <a href="lista">← Continuar comprando</a>
      </div>
      <a href="loja" class="logo">Overgrace</a>
      <div></div>
    </div>
  </header>

  <!-- Título da página -->
  <div class="page-hero">
    <h1 class="page-title">Seu <em>carrinho</em></h1>
    <p class="page-subtitle" id="itemCount">0 itens selecionados</p>
  </div>

  <!-- Layout principal -->
  <form id="formCart">
    <div class="cart-layout" id="cartLayout">
      <!-- Coluna de itens -->
      <div class="cart-items">
        <div class="cart-header-row">
          <span>Produto</span>
          <span style="text-align: center">Quantidade</span>
          <span style="text-align: right">Subtotal</span>
          <span></span>
        </div>

        <!-- Item 1 -->
        <div id="list-items">
          <div class="cart-item">
            <h4>Nenhum item adicionado ao carrinho</h4>
          </div>  
        </div>

        <!-- Cupom -->
        <div>
          <div class="coupon-row">
            <input
              type="text"
              id="couponInput"
              placeholder="Código de cupom"
              maxlength="20" />
            <button onclick="applyCoupon()">Aplicar</button>
          </div>
          <p class="coupon-msg" id="couponMsg"></p>
        </div>
      </div>

      <!-- Resumo lateral -->
      <div class="cart-summary">
        <h2 class="summary-title">Resumo</h2>

        <div class="summary-row">
          <span class="label">Subtotal</span>
          <span class="value" id="sub-total-items">R$ 0,00</span>
        </div>
        <div class="summary-row" id="discountRow" style="display: none">
          <span class="label">Desconto</span>
          <span class="value" id="summaryDiscount" style="color: #3a6248">— R$ 0,00</span>
        </div>
        <div class="summary-row">
          <span class="label">Frete</span>
          <span class="value" id="summaryShipping">Calcular no checkout</span>
        </div>

        <div class="shipping-badge" id="freeShippingBadge">
          ✓ &nbsp;Pedido acima de R$ 299 — frete grátis!
        </div>

        <div class="summary-row total">
          <span class="label">Total</span>
          <span class="value" id="total-items">R$ 407,00</span>
        </div>

        <a href="checkout" class="checkout-btn">
          Fechar pedido <span class="arrow">→</span>
        </a>

        <a href="lista" class="continue-btn">← Continuar comprando</a>

        <div class="payment-icons">
          <span class="pay-badge">PIX</span>
          <span class="pay-badge">Boleto</span>
          <span class="pay-badge">Cartão</span>
          <span class="pay-badge">6x sem juros</span>
        </div>
      </div>
    </div>
  </form>

  <!-- Produtos sugeridos -->
  <div class="suggested">
    <h2 class="suggested-title">Você também pode gostar</h2>
    <div class="suggested-grid">
      <div class="sug-card">
        <div class="sug-img">
          <img
            src="https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=400&q=80"
            alt="Camisa Oversized" />
        </div>
        <p class="sug-name">Camisa Oversized Cáqui</p>
        <p class="sug-price">R$ 175,00</p>
      </div>
      <div class="sug-card">
        <div class="sug-img">
          <img
            src="https://images.unsplash.com/photo-1622445275463-afa2ab738c34?w=400&q=80"
            alt="Camisa Estampada" />
        </div>
        <p class="sug-name">Camisa Listrada Navy</p>
        <p class="sug-price">R$ 209,00</p>
      </div>
      <div class="sug-card">
        <div class="sug-img">
          <img
            src="https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400&q=80"
            alt="Boné Bucket" />
        </div>
        <p class="sug-name">Bucket Hat Natural</p>
        <p class="sug-price">R$ 129,00</p>
      </div>
      <div class="sug-card">
        <div class="sug-img">
          <img
            src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=400&q=80"
            alt="Kit Camisa + Boné" />
        </div>
        <p class="sug-name">Kit Camisa + Boné</p>
        <p class="sug-price">R$ 269,00</p>
      </div>
    </div>
  </div>

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

  <script type="module" src="frontend/js/modules/cart/cart.js"></script>

  <script>
    const prices = {
      item1: 189,
      item2: 99,
      item3: 119
    };
    const qtys = {
      item1: 1,
      item2: 1,
      item3: 1
    };
    let discount = 0;

    function fmt(v) {
      return "R$ " + v.toFixed(2).replace(".", ",");
    }

    function recalc() {
      let sub = 0;
      let active = 0;
      Object.keys(qtys).forEach((id) => {
        if (document.getElementById(id)) {
          sub += prices[id] * qtys[id];
          active++;
        }
      });

      document.getElementById("itemCount").textContent =
        active + (active === 1 ? " item selecionado" : " itens selecionados");

      document.getElementById("summarySubtotal").textContent = fmt(sub);

      if (discount > 0) {
        document.getElementById("discountRow").style.display = "flex";
        document.getElementById("summaryDiscount").textContent =
          "— " + fmt(discount);
      }

      const total = Math.max(0, sub - discount);
      document.getElementById("summaryTotal").textContent = fmt(total);

      const badge = document.getElementById("freeShippingBadge");
      const shipRow = document.getElementById("summaryShipping");
      if (total >= 299) {
        badge.style.display = "flex";
        shipRow.textContent = "Grátis ✓";
        shipRow.style.color = "#3a6248";
      } else {
        badge.style.display = "none";
        shipRow.textContent = "Calcular no checkout";
        shipRow.style.color = "";
      }
    }

    function changeQty(id, delta, price) {
      qtys[id] = Math.max(1, (qtys[id] || 1) + delta);
      document.getElementById("qty-" + id).textContent = qtys[id];
      document.getElementById("sub-" + id).textContent = fmt(
        price * qtys[id],
      );
      recalc();
    }

    function removeItem(id, price) {
      const el = document.getElementById(id);
      if (!el) return;
      el.style.opacity = "0";
      el.style.transform = "translateX(-12px)";
      el.style.transition = "opacity 0.25s, transform 0.25s";
      setTimeout(() => {
        el.remove();
        delete qtys[id];
        recalc();
      }, 250);
    }

    function applyCoupon() {
      const code = document
        .getElementById("couponInput")
        .value.trim()
        .toUpperCase();
      const msg = document.getElementById("couponMsg");
      if (code === "Overgrace10") {
        discount = 40.7;
        msg.textContent = "✓ Cupom aplicado: 10% de desconto";
        msg.style.color = "#3a6248";
      } else if (code === "FRETE") {
        msg.textContent = "✓ Frete grátis aplicado";
        msg.style.color = "#3a6248";
      } else {
        discount = 0;
        msg.textContent = "Cupom inválido ou expirado.";
        msg.style.color = "#8b3a2a";
        document.getElementById("discountRow").style.display = "none";
      }
      recalc();
    }
  </script>
</body>

</html>