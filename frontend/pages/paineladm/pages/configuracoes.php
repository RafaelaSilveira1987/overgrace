<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OverGrace Admin – Configurações</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/configuracao.css" />
  <link rel="stylesheet" href="/overgrace/frontend/css/utils.css">
</head>

<body>


  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <h2>Configurações da loja</h2>
          <p>
            Gerencie as preferências, notificações e integrações da sua loja.
          </p>
        </div>

        <!-- Tabs -->
        <div class="tabs">
          <button class="tab-btn active" onclick="switchTab(this, 'geral')">
            Geral
          </button>
          <button class="tab-btn" onclick="switchTab(this, 'entrega')">
            Frete & Entrega
          </button>
          <button class="tab-btn" onclick="switchTab(this, 'pagamento')">
            Pagamento
          </button>
          <button class="tab-btn" onclick="switchTab(this, 'notificacoes')">
            Notificações
          </button>
          <button class="tab-btn" onclick="switchTab(this, 'avancado')">
            Avançado
          </button>
        </div>

        <!-- ── Aba Geral ── -->
        <div class="tab-panel active" id="tab-geral">
          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Informações da loja</h3>
                <p>Nome, descrição e dados de contato públicos</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Nome da loja</label>
                  <input
                    type="text"
                    class="form-input"
                    value="OverGrace"
                    oninput="markDirty()" />
                </div>
                <div class="form-group">
                  <label class="form-label">Slug / URL</label>
                  <input
                    type="text"
                    class="form-input"
                    value="overgrace"
                    oninput="markDirty()" />
                  <span class="form-hint">overgrace.vercel.app</span>
                </div>
              </div>
              <div class="form-row single">
                <div class="form-group">
                  <label class="form-label">Descrição</label>
                  <textarea class="form-input" oninput="markDirty()">
                    Camisas e bonés feitos com tecidos nobres, cortes precisos e identidade de quem não precisa gritar para ser visto.
                </textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">E-mail de contato</label>
                  <input
                    type="email"
                    class="form-input"
                    placeholder="contato@overgrace.com"
                    oninput="markDirty()" />
                </div>
                <div class="form-group">
                  <label class="form-label">WhatsApp</label>
                  <input
                    type="text"
                    class="form-input"
                    placeholder="+55 11 99999-9999"
                    oninput="markDirty()" />
                </div>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar alterações
                </button>
              </div>
            </div>
          </div>

          <!-- <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Aparência</h3>
                <p>Cor de destaque usada na loja</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label">Cor principal</label>
                <div class="color-row">
                  <div
                    class="color-swatch selected"
                    style="background: #1a1a1a"
                    onclick="selectColor(this)"
                    title="Preto"
                  ></div>
                  <div
                    class="color-swatch"
                    style="background: #2d4a3e"
                    onclick="selectColor(this)"
                    title="Verde Escuro"
                  ></div>
                  <div
                    class="color-swatch"
                    style="background: #5c3317"
                    onclick="selectColor(this)"
                    title="Marrom"
                  ></div>
                  <div
                    class="color-swatch"
                    style="background: #3d3870"
                    onclick="selectColor(this)"
                    title="Roxo"
                  ></div>
                  <div
                    class="color-swatch"
                    style="background: #b5452a"
                    onclick="selectColor(this)"
                    title="Terracota"
                  ></div>
                  <div
                    class="color-swatch"
                    style="background: #3a5f7d"
                    onclick="selectColor(this)"
                    title="Azul Petróleo"
                  ></div>
                </div>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar
                </button>
              </div>
            </div>
          </div> -->
        </div>

        <!-- ── Aba Frete ── -->
        <div class="tab-panel" id="tab-entrega">
          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Regras de frete</h3>
                <p>Configure os métodos de envio disponíveis</p>
              </div>
              <button
                class="btn btn-primary"
                onclick="saveChanges()"
                style="flex-shrink: 0">
                + Adicionar regra
              </button>
            </div>
            <div class="card-body">
              <div
                class="shipping-rule"
                style="
                  font-size: 11px;
                  letter-spacing: 0.08em;
                  text-transform: uppercase;
                  color: var(--text-muted);
                  padding-top: 0;
                ">
                <span>Método</span><span>Prazo</span><span>Valor</span><span>Status</span>
              </div>
              <div class="shipping-rule">
                <span class="shipping-rule-label">PAC (Correios)</span>
                <span class="shipping-rule-value">5–8 dias úteis</span>
                <span class="shipping-rule-value">R$ 18,90</span>
                <span class="badge-status badge-active">Ativo</span>
              </div>
              <div class="shipping-rule">
                <span class="shipping-rule-label">SEDEX (Correios)</span>
                <span class="shipping-rule-value">1–3 dias úteis</span>
                <span class="shipping-rule-value">R$ 34,90</span>
                <span class="badge-status badge-active">Ativo</span>
              </div>
              <div class="shipping-rule">
                <span class="shipping-rule-label">Frete grátis</span>
                <span class="shipping-rule-value">Acima de R$ 299</span>
                <span class="shipping-rule-value">Grátis</span>
                <span class="badge-status badge-active">Ativo</span>
              </div>
              <div class="shipping-rule">
                <span class="shipping-rule-label">Retirada local</span>
                <span class="shipping-rule-value">—</span>
                <span class="shipping-rule-value">Grátis</span>
                <span class="badge-status badge-inactive">Inativo</span>
              </div>
            </div>
          </div>

          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Endereço de origem</h3>
                <p>Endereço de onde os pedidos são despachados</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">CEP</label>
                  <input
                    type="text"
                    class="form-input"
                    placeholder="00000-000"
                    oninput="markDirty()" />
                </div>
                <div class="form-group">
                  <label class="form-label">Estado</label>
                  <select class="form-input" onchange="markDirty()">
                    <option>MG</option>
                    <option>SP</option>
                    <option>RJ</option>
                    <option>BA</option>
                    <option>RS</option>
                    <option>PR</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Cidade</label>
                  <input
                    type="text"
                    class="form-input"
                    placeholder="Sua cidade"
                    oninput="markDirty()" />
                </div>
                <div class="form-group">
                  <label class="form-label">Endereço</label>
                  <input
                    type="text"
                    class="form-input"
                    placeholder="Rua, número"
                    oninput="markDirty()" />
                </div>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Aba Pagamento ── -->
        <div class="tab-panel" id="tab-pagamento">
          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Métodos de pagamento</h3>
                <p>Ative ou desative formas de pagamento aceitas</p>
              </div>
            </div>
            <div class="card-body">
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Cartão de crédito</h4>
                  <p>
                    Visa, Mastercard, Elo — parcelamento em até 6x sem juros
                  </p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Pix</h4>
                  <p>Pagamento instantâneo com 5% de desconto</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Boleto bancário</h4>
                  <p>Prazo de 3 dias úteis para compensação</p>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Cartão de débito</h4>
                  <p>Débito à vista</p>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
            </div>
          </div>

          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Parcelamento</h3>
                <p>Defina as opções de parcelamento no cartão de crédito</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Máximo de parcelas sem juros</label>
                  <select class="form-input" onchange="markDirty()">
                    <option>3x</option>
                    <option>6x</option>
                    <option selected>6x</option>
                    <option>12x</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Valor mínimo da parcela</label>
                  <input
                    type="text"
                    class="form-input"
                    value="R$ 50,00"
                    oninput="markDirty()" />
                </div>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Aba Notificações ── -->
        <div class="tab-panel" id="tab-notificacoes">
          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>E-mails automáticos para clientes</h3>
                <p>Ative os e-mails enviados em cada etapa do pedido</p>
              </div>
            </div>
            <div class="card-body">
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Confirmação de pedido</h4>
                  <p>Enviado logo após o pagamento ser aprovado</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Pedido enviado</h4>
                  <p>Enviado com o código de rastreio</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Avaliação de produto</h4>
                  <p>Solicitar avaliação 7 dias após a entrega</p>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Carrinho abandonado</h4>
                  <p>Lembrete 2h depois de abandono do carrinho</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
            </div>
          </div>

          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Notificações para o admin</h3>
                <p>Alertas enviados para você</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-row single" style="margin-bottom: 16px">
                <div class="form-group">
                  <label class="form-label">E-mail do administrador</label>
                  <input
                    type="email"
                    class="form-input"
                    placeholder="admin@overgrace.com"
                    oninput="markDirty()" />
                </div>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Novo pedido</h4>
                  <p>Alerta sempre que um novo pedido chegar</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Estoque baixo</h4>
                  <p>Alerta quando um produto tiver menos de 5 unidades</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Aba Avançado ── -->
        <div class="tab-panel" id="tab-avancado">
          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>Integrações</h3>
                <p>Conecte ferramentas externas à sua loja</p>
              </div>
            </div>
            <div class="card-body">
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Google Analytics</h4>
                  <p>Rastreamento de visitas e conversões</p>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Meta Pixel (Facebook)</h4>
                  <p>Pixel para campanhas de anúncio</p>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
              <div class="toggle-row">
                <div class="toggle-info">
                  <h4>Google Tag Manager</h4>
                  <p>Gerenciador de scripts e tags</p>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                    class="toggle-slider"></span></label>
              </div>
            </div>
          </div>

          <div class="settings-card">
            <div class="card-header">
              <div class="card-header-text">
                <h3>SEO</h3>
                <p>Configurações de indexação e metadados</p>
              </div>
            </div>
            <div class="card-body">
              <div class="form-row single">
                <div class="form-group">
                  <label class="form-label">Título (meta title)</label>
                  <input
                    type="text"
                    class="form-input"
                    value="OverGrace – Camisas e Bonés Oversized"
                    oninput="markDirty()" />
                </div>
              </div>
              <div class="form-row single">
                <div class="form-group">
                  <label class="form-label">Descrição (meta description)</label>
                  <textarea class="form-input" oninput="markDirty()">
            Camisas e bonés feitos com tecidos nobres para quem veste com propósito.</textarea>
                </div>
              </div>
              <div class="btn-row">
                <button class="btn btn-ghost" onclick="cancelChanges()">
                  Cancelar
                </button>
                <button class="btn btn-primary" onclick="saveChanges()">
                  Salvar
                </button>
              </div>
            </div>
          </div>

          <!-- Danger zone -->
          <div class="danger-zone">
            <div class="settings-card">
              <div class="card-header">
                <div class="card-header-text">
                  <h3>Zona de perigo</h3>
                  <p>Ações irreversíveis — proceda com cuidado</p>
                </div>
              </div>
              <div class="card-body">
                <div class="toggle-row">
                  <div class="toggle-info">
                    <h4>Colocar loja em manutenção</h4>
                    <p>
                      Visitantes verão uma página de "em breve" enquanto você
                      atualiza
                    </p>
                  </div>
                  <label class="toggle"><input type="checkbox" onchange="markDirty()" /><span
                      class="toggle-slider"></span></label>
                </div>
                <div style="padding-top: 16px">
                  <button
                    class="btn btn-danger-ghost"
                    onclick="
                      alert('Ação de exclusão não implementada nesta demo.')
                    ">
                    Excluir todos os dados de teste
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Save banner -->
  <div class="save-banner" id="saveBanner">
    Alterações não salvas
    <button onclick="saveChanges()">Salvar agora</button>
  </div>

  <script>
    let dirty = false;

    function markDirty() {
      if (!dirty) {
        dirty = true;
        document.getElementById("saveBanner").classList.add("show");
      }
    }

    function saveChanges() {
      dirty = false;
      const banner = document.getElementById("saveBanner");
      banner.textContent = "✓ Alterações salvas";
      setTimeout(() => {
        banner.classList.remove("show");
        setTimeout(() => {
          banner.innerHTML =
            'Alterações não salvas <button onclick="saveChanges()">Salvar agora</button>';
        }, 400);
      }, 2000);
    }

    function cancelChanges() {
      dirty = false;
      document.getElementById("saveBanner").classList.remove("show");
    }

    function switchTab(btn, tabId) {
      document
        .querySelectorAll(".tab-btn")
        .forEach((b) => b.classList.remove("active"));
      document
        .querySelectorAll(".tab-panel")
        .forEach((p) => p.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById("tab-" + tabId).classList.add("active");
    }

    function selectColor(el) {
      document
        .querySelectorAll(".color-swatch")
        .forEach((s) => s.classList.remove("selected"));
      el.classList.add("selected");
      markDirty();
    }
  </script>


</body>

</html>