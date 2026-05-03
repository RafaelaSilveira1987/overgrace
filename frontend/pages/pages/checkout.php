<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout — OverGrace</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/checkout.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />


</head>

<body>
  <header>
    <div class="header-inner">
      <a href="carrinho" class="header-back">← Carrinho</a>
      <a href="lista" class="logo">OverGrace</a>
      <div class="header-secure">🔒 &nbsp;Pagamento seguro</div>
    </div>
  </header>

  <!-- Steps indicator -->
  <div class="steps-bar" id="stepsBar">
    <div class="step-item active" id="step-ind-1" onclick="goToStep(1)">
      <span class="step-num">1</span> Identificação
    </div>
    <div class="step-sep"></div>
    <div class="step-item" id="step-ind-2" onclick="goToStep(2)">
      <span class="step-num">2</span> Entrega
    </div>
    <div class="step-sep"></div>
    <div class="step-item" id="step-ind-3" onclick="goToStep(3)">
      <span class="step-num">3</span> Pagamento
    </div>
  </div>

  <!-- Layout -->
  <div class="checkout-layout" id="checkoutLayout">
    <!-- COLUNA ESQUERDA: FORMULÁRIOS -->
    <div class="checkout-form-area">
      <!-- STEP 1: Identificação -->
      <div class="panel active" id="panel1">
        <h2 class="panel-title">Identificação</h2>

        <form id="formRegister">
          <div class="form-row">
            <div class="field">
              <label>E-mail</label>
              <input type="email" id="email" placeholder="seu@email.com" />
            </div>
            <div class="field">
              <label>Senha</label>
              <input type="password" id="password" placeholder="*****" />
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>Nome</label>
              <input type="text" id="nome" placeholder="Nome" />
            </div>
            <div class="field">
              <label>Sobrenome</label>
              <input type="text" id="sobrenome" placeholder="Sobrenome" />
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label>CPF</label>
              <input
                type="text"
                id="cpf"
                placeholder="000.000.000-00"
                maxlength="14"
                oninput="maskCPF(this)" />
            </div>
            <div class="field">
              <label>Telefone</label>
              <input
                type="text"
                id="tel"
                placeholder="(00) 00000-0000"
                maxlength="15"
                oninput="maskPhone(this)" />
            </div>
          </div>

          <p class="field-hint">
            Usaremos esses dados apenas para comunicação sobre o seu pedido.
          </p>

          <button class="submit-btn">
            Continuar para entrega <span class="arrow">→</span>
          </button>
        </form>

      </div>

      <!-- STEP 2: Entrega -->
      <div class="panel" id="panel2">
        <h2 class="panel-title">Endereço de entrega</h2>

        <form action="formAddress">
          <div class="form-row">
            <div class="field">
              <label>CEP</label>
              <input
                type="text"
                id="cep"
                placeholder="00000-000"
                maxlength="9"
                oninput="maskCEP(this)"
                onblur="fetchCEP(this.value)" />
            </div>
            <div class="field" style="justify-content: flex-end">
              <a
                href="https://buscacepinter.correios.com.br"
                target="_blank"
                style="
                    font-size: 11px;
                    color: var(--text-muted);
                    text-decoration: underline;
                    text-underline-offset: 3px;
                    padding-bottom: 16px;
                  ">Não sei meu CEP</a>
            </div>
          </div>
  
          <div class="form-row single">
            <div class="field">
              <label>Endereço</label>
              <input type="text" id="endereco" placeholder="Rua, Avenida..." />
            </div>
          </div>
  
          <div class="form-row thirds">
            <div class="field">
              <label>Número</label>
              <input type="text" id="numero" placeholder="123" />
            </div>
            <div class="field">
              <label>Complemento</label>
              <input type="text" id="comp" placeholder="Apto, Bloco..." />
            </div>
            <div class="field">
              <label>Bairro</label>
              <input type="text" id="bairro" placeholder="Bairro" />
            </div>
          </div>
  
          <div class="form-row">
            <div class="field">
              <label>Cidade</label>
              <input type="text" id="cidade" placeholder="Cidade" />
            </div>
            <div class="field">
              <label>Estado</label>
              <select id="estado">
                <option value="">Selecione</option>
                <option>AC</option>
                <option>AL</option>
                <option>AP</option>
                <option>AM</option>
                <option>BA</option>
                <option>CE</option>
                <option>DF</option>
                <option>ES</option>
                <option>GO</option>
                <option>MA</option>
                <option>MT</option>
                <option>MS</option>
                <option selected>MG</option>
                <option>PA</option>
                <option>PB</option>
                <option>PR</option>
                <option>PE</option>
                <option>PI</option>
                <option>RJ</option>
                <option>RN</option>
                <option>RS</option>
                <option>RO</option>
                <option>RR</option>
                <option>SC</option>
                <option>SP</option>
                <option>SE</option>
                <option>TO</option>
              </select>
            </div>
          </div>
  
          <!-- Opções de frete -->
          <h3
            style="
                font-size: 20px;
                font-weight: 700;
                margin: 36px 0 20px;
              ">
            Forma de entrega
          </h3>
  
          <div class="shipping-options">
            <label
              class="ship-option selected"
              onclick="selectShip(this, 0, 'Frete Grátis')">
              <input type="radio" name="ship" value="gratis" checked />
              <div class="ship-radio"></div>
              <div class="ship-info">
                <div>
                  <p class="ship-label">PAC — Entrega Econômica</p>
                  <p class="ship-eta">Entrega em 5 a 8 dias úteis</p>
                </div>
              </div>
              <span class="ship-price free">Grátis</span>
            </label>
  
            <label
              class="ship-option"
              onclick="selectShip(this, 18.9, 'Sedex')">
              <input type="radio" name="ship" value="sedex" />
              <div class="ship-radio"></div>
              <div class="ship-info">
                <div>
                  <p class="ship-label">SEDEX — Entrega Expressa</p>
                  <p class="ship-eta">Entrega em 1 a 3 dias úteis</p>
                </div>
              </div>
              <span class="ship-price">R$ 18,90</span>
            </label>
  
            <label
              class="ship-option"
              onclick="selectShip(this, 12.5, 'Retirada')">
              <input type="radio" name="ship" value="retirada" />
              <div class="ship-radio"></div>
              <div class="ship-info">
                <div>
                  <p class="ship-label">Retirada no Ponto de Coleta</p>
                  <p class="ship-eta">Disponível em até 2 dias úteis</p>
                </div>
              </div>
              <span class="ship-price">R$ 12,50</span>
            </label>
          </div>
  
          <button class="submit-btn" onclick="goToStep(3)">
            Continuar para pagamento <span class="arrow">→</span>
          </button>
          <button class="back-step-btn" onclick="goToStep(1)">← Voltar</button>
        </form>
      </div>

      <!-- STEP 3: Pagamento -->
      <div class="panel" id="panel3">
        <h2 class="panel-title">Pagamento</h2>

        <div class="payment-tabs">
          <button class="pay-tab active" onclick="selectPayTab('pix', this)">
            PIX
          </button>
          <button class="pay-tab" onclick="selectPayTab('boleto', this)">
            Boleto
          </button>
          <button class="pay-tab" onclick="selectPayTab('cartao', this)">
            Cartão
          </button>
        </div>

        <!-- PIX -->
        <div class="pay-panel active" id="pay-pix">
          <div class="pix-box">
            <div class="pix-qr">
              <svg viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg">
                <!-- QR code simulado -->
                <rect width="140" height="140" fill="white" />
                <rect
                  x="10"
                  y="10"
                  width="40"
                  height="40"
                  fill="none"
                  stroke="#1a1814"
                  stroke-width="4" />
                <rect x="18" y="18" width="24" height="24" fill="#1a1814" />
                <rect
                  x="90"
                  y="10"
                  width="40"
                  height="40"
                  fill="none"
                  stroke="#1a1814"
                  stroke-width="4" />
                <rect x="98" y="18" width="24" height="24" fill="#1a1814" />
                <rect
                  x="10"
                  y="90"
                  width="40"
                  height="40"
                  fill="none"
                  stroke="#1a1814"
                  stroke-width="4" />
                <rect x="18" y="98" width="24" height="24" fill="#1a1814" />
                <rect x="60" y="10" width="4" height="4" fill="#1a1814" />
                <rect x="66" y="10" width="4" height="4" fill="#1a1814" />
                <rect x="60" y="16" width="4" height="8" fill="#1a1814" />
                <rect x="66" y="20" width="8" height="4" fill="#1a1814" />
                <rect x="60" y="26" width="4" height="4" fill="#1a1814" />
                <rect x="78" y="10" width="4" height="4" fill="#1a1814" />
                <rect x="60" y="60" width="8" height="4" fill="#1a1814" />
                <rect x="72" y="60" width="4" height="8" fill="#1a1814" />
                <rect x="80" y="64" width="8" height="4" fill="#1a1814" />
                <rect x="60" y="70" width="4" height="4" fill="#1a1814" />
                <rect x="66" y="68" width="4" height="8" fill="#1a1814" />
                <rect x="90" y="60" width="4" height="4" fill="#1a1814" />
                <rect x="96" y="60" width="4" height="8" fill="#1a1814" />
                <rect x="102" y="62" width="8" height="4" fill="#1a1814" />
                <rect x="112" y="60" width="4" height="4" fill="#1a1814" />
                <rect x="60" y="80" width="4" height="4" fill="#1a1814" />
                <rect x="66" y="78" width="4" height="8" fill="#1a1814" />
                <rect x="72" y="80" width="12" height="4" fill="#1a1814" />
                <rect x="86" y="76" width="4" height="8" fill="#1a1814" />
                <rect x="92" y="74" width="4" height="4" fill="#1a1814" />
                <rect x="98" y="76" width="8" height="4" fill="#1a1814" />
                <rect x="108" y="74" width="4" height="8" fill="#1a1814" />
                <rect x="114" y="80" width="4" height="4" fill="#1a1814" />
                <rect x="60" y="86" width="8" height="4" fill="#1a1814" />
                <rect x="70" y="90" width="4" height="4" fill="#1a1814" />
                <rect x="78" y="86" width="4" height="8" fill="#1a1814" />
                <rect x="60" y="96" width="4" height="4" fill="#1a1814" />
                <rect x="68" y="96" width="4" height="8" fill="#1a1814" />
                <rect x="78" y="98" width="4" height="6" fill="#1a1814" />
                <rect x="84" y="92" width="4" height="4" fill="#1a1814" />
                <rect x="92" y="86" width="4" height="6" fill="#1a1814" />
                <rect x="100" y="84" width="4" height="8" fill="#1a1814" />
                <rect x="106" y="88" width="8" height="4" fill="#1a1814" />
                <rect x="116" y="86" width="4" height="4" fill="#1a1814" />
                <rect x="92" y="96" width="4" height="4" fill="#1a1814" />
                <rect x="100" y="96" width="8" height="4" fill="#1a1814" />
                <rect x="110" y="92" width="4" height="8" fill="#1a1814" />
                <rect x="116" y="94" width="4" height="6" fill="#1a1814" />
              </svg>
            </div>
            <p class="pix-code-label">Copia e cola</p>
            <div class="pix-code" id="pixCode">
              00020126580014BR.GOV.BCB.PIX0136f6a3f2c5-3b4a-4c1d-9f8e-1a2b3c4d5e6f5204000053039865802BR5913FORMA
              LOJA6008BRASILIA62090505FORMA6304E2CA
            </div>
            <button class="copy-btn" onclick="copyPix()">
              Copiar código
            </button>
            <p class="pix-note">
              Abra o app do seu banco · Escolha pagar via PIX · Cole o código
              ou escaneie o QR · Confirme o pagamento · A confirmação é
              imediata
            </p>
          </div>
          <button class="submit-btn" onclick="confirmarPedido()">
            Confirmar pedido <span class="arrow">→</span>
          </button>
          <button class="back-step-btn" onclick="goToStep(2)">
            ← Voltar
          </button>
        </div>

        <!-- BOLETO -->
        <div class="pay-panel" id="pay-boleto">
          <div class="boleto-box">
            <p class="boleto-info">
              O boleto será gerado após a confirmação. Você tem até
              <strong>3 dias úteis</strong> para pagar. O pedido é separado
              assim que o pagamento é confirmado.
            </p>
            <div class="boleto-barcode">
              <div class="barcode-lines" id="barcodeSvg"></div>
              <p class="barcode-num">
                1234.56789 01234.567890 12345.678901 1 92340000040700
              </p>
            </div>
            <button class="boleto-download">↓ &nbsp;Baixar boleto PDF</button>
          </div>
          <button class="submit-btn" onclick="confirmarPedido()">
            Confirmar pedido <span class="arrow">→</span>
          </button>
          <button class="back-step-btn" onclick="goToStep(2)">
            ← Voltar
          </button>
        </div>

        <!-- CARTÃO -->
        <div class="pay-panel" id="pay-cartao">
          <div class="card-form">
            <div class="card-preview">
              <div class="card-preview-brand" id="cardBrand">VISA</div>
              <div class="card-preview-number" id="cardNumPreview">
                •••• &nbsp;•••• &nbsp;•••• &nbsp;••••
              </div>
              <div class="card-preview-bottom">
                <div>
                  <div class="card-preview-label">Titular</div>
                  <div class="card-preview-value" id="cardNamePreview">
                    SEU NOME
                  </div>
                </div>
                <div>
                  <div class="card-preview-label">Validade</div>
                  <div class="card-preview-value" id="cardExpPreview">
                    MM/AA
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row single">
              <div class="field">
                <label>Número do cartão</label>
                <input
                  type="text"
                  id="cardNum"
                  placeholder="0000 0000 0000 0000"
                  maxlength="19"
                  oninput="maskCard(this)" />
              </div>
            </div>
            <div class="form-row single">
              <div class="field">
                <label>Nome no cartão</label>
                <input
                  type="text"
                  id="cardName"
                  placeholder="NOME COMO NO CARTÃO"
                  oninput="
                      document.getElementById('cardNamePreview').textContent =
                        this.value.toUpperCase() || 'SEU NOME'
                    " />
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Validade</label>
                <input
                  type="text"
                  id="cardExp"
                  placeholder="MM/AA"
                  maxlength="5"
                  oninput="maskExp(this)" />
              </div>
              <div class="field">
                <label>CVV</label>
                <input
                  type="text"
                  id="cardCvv"
                  placeholder="•••"
                  maxlength="4" />
              </div>
            </div>

            <select class="installments-select" id="parcelas">
              <option value="1">1x de R$ 407,00 (sem juros)</option>
              <option value="2">2x de R$ 203,50 (sem juros)</option>
              <option value="3">3x de R$ 135,67 (sem juros)</option>
              <option value="4">4x de R$ 101,75 (sem juros)</option>
              <option value="5">5x de R$ 81,40 (sem juros)</option>
              <option value="6">6x de R$ 67,83 (sem juros)</option>
            </select>
          </div>
          <button class="submit-btn" onclick="confirmarPedido()">
            Confirmar pagamento <span class="arrow">→</span>
          </button>
          <button class="back-step-btn" onclick="goToStep(2)">
            ← Voltar
          </button>
        </div>
      </div>
    </div>

    <!-- COLUNA DIREITA: RESUMO -->
    <div class="order-summary">
      <p class="summary-label">Seu pedido</p>
      <div class="order-items">
        <div class="order-item">
          <div class="order-thumb-wrap">
            <img
              class="order-thumb"
              src="https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=200&q=80"
              alt="" />
            <span class="order-qty-badge">1</span>
          </div>
          <div style="flex: 1">
            <p class="order-item-name">Camisa Linho Off-White</p>
            <p class="order-item-variant">M · Off-White</p>
          </div>
          <p class="order-item-price">R$ 189,00</p>
        </div>

        <div class="order-item">
          <div class="order-thumb-wrap">
            <img
              class="order-thumb"
              src="https://images.unsplash.com/photo-1534215754734-18e55d13e346?w=200&q=80"
              alt="" />
            <span class="order-qty-badge">1</span>
          </div>
          <div style="flex: 1">
            <p class="order-item-name">Boné Aba Curva Preto</p>
            <p class="order-item-variant">Único · Preto</p>
          </div>
          <p class="order-item-price">R$ 99,00</p>
        </div>

        <div class="order-item">
          <div class="order-thumb-wrap">
            <img
              class="order-thumb"
              src="https://images.unsplash.com/photo-1521369909029-2afed882baee?w=200&q=80"
              alt="" />
            <span class="order-qty-badge">1</span>
          </div>
          <div style="flex: 1">
            <p class="order-item-name">Boné Estruturado Bege</p>
            <p class="order-item-variant">Único · Bege</p>
          </div>
          <p class="order-item-price">R$ 119,00</p>
        </div>
      </div>

      <div class="summary-divider"></div>

      <div class="summary-line">
        <span class="l">Subtotal</span>
        <span class="v">R$ 407,00</span>
      </div>
      <div class="summary-line">
        <span class="l">Frete</span>
        <span class="v" id="shipLabel" style="color: var(--success)">Grátis</span>
      </div>

      <div class="summary-total-line">
        <span class="l">Total</span>
        <span class="v" id="totalFinal">R$ 407,00</span>
      </div>

      <div class="security-note">
        🔒 Dados criptografados com SSL<br />
        Seus dados estão seguros conosco
      </div>
    </div>
  </div>

  <!-- TELA DE CONFIRMAÇÃO (sobrepõe o layout) -->
  <div class="confirm-screen" id="confirmScreen">
    <div class="confirm-icon">✓</div>
    <h1 class="confirm-title">Pedido <em>confirmado!</em></h1>
    <p class="confirm-sub">
      Obrigado pela sua compra. Você receberá um e-mail com os detalhes e o
      acompanhamento do envio.
    </p>
    <p class="confirm-order-num">Número do pedido</p>
    <p class="confirm-order-val" id="confirmOrderNum">#FOR-00000</p>
    <a href="loja.html" class="confirm-cta">Continuar comprando →</a>
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

  <script type="module" src="frontend/js/modules/client/register.js"></script>
  <script src="frontend/js/modules/client/utils.js"></script>

  <script>

  </script>
</body>

</html>