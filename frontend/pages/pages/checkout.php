<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout — OverGrace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="/overgrace/frontend/pages/style.css" />
    <link rel="stylesheet" href="/overgrace/frontend/pages/pages/pages-css/checkout.css" />

    <!-- SDK de pagamento seguro. Configure a chave pública do provedor no ambiente correto. -->
    <script>
        window.OVERGRACE_MP_PUBLIC_KEY = '';
        window.OVERGRACE_MP_LOCALE = 'pt-BR';
        // Enquanto o backend de pagamento/pedido não existir, deixe true para navegar por todas as telas.
        window.OVERGRACE_DEMO_CHECKOUT = true;
    </script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>
    <div class="checkout-topbar">
        Compra protegida · Pagamento seguro · Troca fácil em até 7 dias
    </div>

    <header class="checkout-header">
        <div class="checkout-header-inner">
            <a href="carrinho" class="header-back">← Voltar ao carrinho</a>
            <a href="loja" class="checkout-logo">OverGrace</a>
            <div class="header-secure">🔒 Ambiente seguro</div>
        </div>
    </header>

    <main class="checkout-page">
        <section class="checkout-hero">
            <div>
                <p class="checkout-eyebrow">Checkout</p>
                <h1>Finalizar <em>compra</em></h1>
                <p class="checkout-subtitle">
                    Finalize sua compra de forma rápida e segura. Preencha seus dados, escolha a entrega e pague do
                    jeito que preferir.
                </p>
            </div>

            <!-- <div class="trust-strip" aria-label="Benefícios da compra">
                <span>✓ Dados protegidos</span>
                <span>✓ PIX, boleto e cartão</span>
                <span>✓ Pedido acompanhado</span>
            </div> -->
        </section>

        <div class="steps-bar" id="stepsBar">
            <button type="button" class="step-item active" id="step-ind-1" onclick="goToStep(1, true)">
                <span class="step-num">1</span>
                <span>Identificação</span>
            </button>
            <span class="step-sep"></span>
            <button type="button" class="step-item" id="step-ind-2" onclick="goToStep(2)">
                <span class="step-num">2</span>
                <span>Entrega</span>
            </button>
            <span class="step-sep"></span>
            <button type="button" class="step-item" id="step-ind-3" onclick="goToStep(3)">
                <span class="step-num">3</span>
                <span>Pagamento</span>
            </button>
        </div>

        <section class="checkout-layout" id="checkoutLayout">
            <div class="checkout-form-area">
                <!-- STEP 1 -->
                <section class="panel active" id="panel1" aria-labelledby="checkout-title-identificacao">
                    <div class="panel-card">
                        <div class="panel-card-head">
                            <div>
                                <p class="panel-kicker"></p>
                                <h2 class="panel-title" id="checkout-title-identificacao">Identificação</h2>
                            </div>
                            <span class="panel-badge">Acesse sua conta</span>
                        </div>

                        <div class="notice-card">
                            <strong>Já comprou antes?</strong>
                            Use o mesmo e-mail e senha. <br>
                            Se for novo por aqui, a conta nasce junto com o pedido.
                        </div>

                        <form id="formRegister" class="checkout-form">
                            <div class="form-row">
                                <div class="field">
                                    <label for="email">E-mail</label>
                                    <input type="email" id="email" placeholder="seu@email.com" autocomplete="email" />
                                </div>
                                <div class="field">
                                    <label for="password">Senha</label>
                                    <input type="password" id="password" placeholder="Mínimo 6 caracteres"
                                        autocomplete="current-password" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="field">
                                    <label for="nome">Nome</label>
                                    <input type="text" id="nome" placeholder="Nome" autocomplete="given-name" />
                                </div>
                                <div class="field">
                                    <label for="sobrenome">Sobrenome</label>
                                    <input type="text" id="sobrenome" placeholder="Sobrenome"
                                        autocomplete="family-name" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="field">
                                    <label for="cpf">CPF</label>
                                    <input type="text" id="cpf" placeholder="000.000.000-00" maxlength="14"
                                        autocomplete="off" />
                                </div>
                                <div class="field">
                                    <label for="tel">Telefone</label>
                                    <input type="text" id="tel" placeholder="(00) 00000-0000" maxlength="15"
                                        autocomplete="tel" />
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <div class="panel-card-head compact">
                                <div>
                                    <p class="panel-kicker">Destino do pedido</p>
                                    <h3 class="mini-title">Endereço de entrega</h3>
                                </div>
                                <a href="https://buscacepinter.correios.com.br" target="_blank" rel="noopener"
                                    class="helper-link">Não sei meu CEP</a>
                            </div>

                            <div class="form-row cep-row">
                                <div class="field">
                                    <label for="cep">CEP</label>
                                    <input type="text" id="cep" placeholder="00000-000" maxlength="9"
                                        autocomplete="postal-code" />
                                </div>
                                <div class="address-preview" id="addressPreview">
                                    Digite o CEP para buscar rua, bairro e cidade automaticamente.
                                </div>
                            </div>

                            <div class="form-row single">
                                <div class="field">
                                    <label for="endereco">Endereço</label>
                                    <input type="text" id="endereco" placeholder="Rua, Avenida..."
                                        autocomplete="address-line1" />
                                </div>
                            </div>

                            <div class="form-row thirds">
                                <div class="field">
                                    <label for="numero">Número</label>
                                    <input type="text" id="numero" placeholder="123" autocomplete="address-line2" />
                                </div>
                                <div class="field">
                                    <label for="comp">Complemento</label>
                                    <input type="text" id="comp" placeholder="Apto, bloco..." />
                                </div>
                                <div class="field">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" id="bairro" placeholder="Bairro" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="field">
                                    <label for="cidade">Cidade</label>
                                    <input type="text" id="cidade" placeholder="Cidade" autocomplete="address-level2" />
                                </div>
                                <div class="field">
                                    <label for="estado">Estado</label>
                                    <select id="estado" autocomplete="address-level1">
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

                            <p class="field-hint">Usaremos esses dados apenas para o pedido, entrega e contato sobre a
                                compra.</p>

                            <button type="submit" class="submit-btn">
                                Continuar para entrega <span class="arrow">→</span>
                            </button>
                        </form>
                    </div>
                </section>

                <!-- STEP 2 -->
                <section class="panel" id="panel2" aria-labelledby="checkout-title-entrega">
                    <div class="panel-card">
                        <div class="panel-card-head">
                            <div>
                                <p class="panel-kicker"></p>
                                <h2 class="panel-title" id="checkout-title-entrega">Entrega</h2>
                            </div>
                            <span class="panel-badge">Escolha o frete</span>
                        </div>

                        <form id="formShipping" class="checkout-form">
                            <div class="delivery-map-card">
                                <div>
                                    <span class="map-dot"></span>
                                    <p class="map-title">Entrega para</p>
                                    <p class="map-text" id="deliveryResume">Endereço informado no passo anterior</p>
                                </div>
                                <button type="button" class="text-btn" onclick="goToStep(1, true)">Alterar</button>
                            </div>

                            <h3 class="mini-title shipping-title">Forma de entrega</h3>

                            <div class="shipping-options">
                                <label class="ship-option selected btnFrete" data-cost="0" data-label="Retirada">
                                    <input type="radio" name="ship" value="gratis" />
                                    <span class="ship-radio"></span>
                                    <span class="ship-info">
                                        <strong>Retirada no ponto de coleta</strong>
                                        <small>Disponível em até 2 dias úteis</small>
                                    </span>
                                    <span class="ship-price free">Grátis</span>
                                </label>

                                <label class="ship-option btnFrete" data-cost="12.50" data-label="Pac">
                                    <input type="radio" name="ship" value="price" checked />
                                    <span class="ship-radio"></span>
                                    <span class="ship-info">
                                        <strong>PAC — Entrega Econômica</strong>
                                        <small>Entrega em 5 a 8 dias úteis</small>
                                    </span>
                                    <span class="ship-price">R$ 12,50</span>
                                </label>

                                <label class="ship-option btnFrete" data-cost="18.90" data-label="Sedex">
                                    <input type="radio" name="ship" value="sedex" />
                                    <span class="ship-radio"></span>
                                    <span class="ship-info">
                                        <strong>SEDEX — Entrega Expressa</strong>
                                        <small>Entrega em 1 a 3 dias úteis</small>
                                    </span>
                                    <span class="ship-price">R$ 18,90</span>
                                </label>
                            </div>

                            <button type="submit" class="submit-btn">
                                Continuar para pagamento <span class="arrow">→</span>
                            </button>
                            <button type="button" class="back-step-btn" id="voltarFrete">← Voltar</button>
                        </form>
                    </div>
                </section>

                <!-- STEP 3 -->
                <section class="panel" id="panel3" aria-labelledby="checkout-title-pagamento">
                    <div class="panel-card">
                        <div class="panel-card-head">
                            <div>
                                <p class="panel-kicker"></p>
                                <h2 class="panel-title" id="checkout-title-pagamento">Pagamento</h2>
                            </div>

                        </div>

                        <div class="mp-warning" id="mpPaymentStatus">
                            Modo demonstração ativo: dá para navegar, simular pedido e ver os próximos estados do
                            pagamento.
                        </div> 

                        <div class="payment-tabs" role="tablist" aria-label="Métodos de pagamento">
                            <button type="button" class="pay-tab active" data-method="pix">PIX</button>
                            <button type="button" class="pay-tab" data-method="boleto">Boleto</button>
                            <button type="button" class="pay-tab" data-method="cartao">Cartão</button>
                        </div>

                        <div class="pay-panel active" id="pay-pix">
                            <div class="payment-method-card highlight">
                                <span class="method-icon">◆</span>
                                <div>
                                    <h3>PIX</h3>
                                    <p>Confirmação rápida e melhor para liberar separação do pedido sem enrolação.</p>
                                </div>
                            </div>

                            <button type="button" class="submit-btn" id="btnPix">
                                Gerar PIX <span class="arrow">→</span>

                                <span class="btn-spinner"></span>
                            </button>
                            <button type="button" class="back-step-btn voltarPedido">← Voltar</button>
                        </div>

                        <div class="pay-panel" id="pay-boleto">
                            <div class="payment-method-card">
                                <span class="method-icon">▥</span>
                                <div>
                                    <h3>Boleto</h3>
                                    <p>O boleto será gerado com segurança. Após compensação, o pedido segue para
                                        separação.</p>
                                </div>
                            </div>
                            <div class="demo-payment-box">
                                <p class="demo-title">Prévia demonstrativa</p>
                                <p>Simule boleto gerado, vencimento e linha digitável.</p>
                            </div>
                            <button type="button" class="submit-btn" onclick="confirmarPedido('boleto', 'pending')">
                                Gerar boleto demonstrativo <span class="arrow">→</span>
                            </button>
                            <button type="button" class="back-step-btn voltarPedido">← Voltar</button>
                        </div>

                        <div class="pay-panel" id="pay-cartao">
                            <div class="payment-method-card">
                                <span class="method-icon">▰</span>
                                <div>
                                    <h3>Cartão de crédito</h3>
                                    <p>Checkout próprio, sem redirecionamento. O pagamento é processado em ambiente
                                        seguro.</p>
                                </div>
                            </div>

                            <form id="form-checkout" class="custom-card-checkout" autocomplete="off">
                                <div class="card-preview" aria-hidden="true">
                                    <div class="card-preview-top">
                                        <span class="card-chip"></span>
                                        <span class="card-brand" id="cardPreviewBrand">CARD</span>
                                    </div>
                                    <p class="card-preview-number" id="cardPreviewNumber">•••• •••• •••• ••••</p>
                                    <div class="card-preview-bottom">
                                        <span>
                                            <small>Titular</small>
                                            <strong id="cardPreviewName">NOME IMPRESSO</strong>
                                        </span>
                                        <span>
                                            <small>Validade</small>
                                            <strong id="cardPreviewDate">MM/AA</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="secure-card-note">
                                    <strong>Pagamento protegido:</strong>
                                    os dados sensíveis do cartão são processados em campos seguros. A OverGrace não
                                    armazena número, validade nem CVV.
                                </div>

                                <div class="form-row single">
                                    <div class="field card-field">
                                        <label for="form-checkout__cardNumber">Número do cartão</label>
                                        <div id="form-checkout__cardNumber" class="mp-secure-field"
                                            data-demo-placeholder="0000 0000 0000 0000"></div>
                                        <small class="field-hint-inline">Campo seguro</small>
                                    </div>
                                </div>

                                <div class="form-row thirds">
                                    <div class="field card-field">
                                        <label for="form-checkout__expirationDate">Validade</label>
                                        <div id="form-checkout__expirationDate" class="mp-secure-field"
                                            data-demo-placeholder="MM/AA"></div>
                                    </div>
                                    <div class="field card-field">
                                        <label for="form-checkout__securityCode">CVV</label>
                                        <div id="form-checkout__securityCode" class="mp-secure-field"
                                            data-demo-placeholder="123"></div>
                                    </div>
                                    <div class="field">
                                        <label for="form-checkout__installments">Parcelas</label>
                                        <select id="form-checkout__installments" class="installments-select">
                                            <option value="1">1x sem juros</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="field">
                                        <label for="form-checkout__cardholderName">Nome impresso no cartão</label>
                                        <input type="text" id="form-checkout__cardholderName"
                                            placeholder="Como aparece no cartão" autocomplete="cc-name" />
                                    </div>
                                    <div class="field">
                                        <label for="form-checkout__cardholderEmail">E-mail do pagador</label>
                                        <input type="email" id="form-checkout__cardholderEmail"
                                            placeholder="cliente@email.com" autocomplete="email" />
                                    </div>
                                </div>

                                <div class="form-row thirds card-extra-row">
                                    <div class="field">
                                        <label for="form-checkout__identificationType">Documento</label>
                                        <select id="form-checkout__identificationType">
                                            <option value="CPF">CPF</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label for="form-checkout__identificationNumber">Número</label>
                                        <input type="text" id="form-checkout__identificationNumber"
                                            placeholder="000.000.000-00" maxlength="14" oninput="maskCPF(this)" />
                                    </div>
                                    <div class="field optional-field">
                                        <label for="form-checkout__issuer">Banco emissor</label>
                                        <select id="form-checkout__issuer">
                                            <option value="">Detectado automaticamente</option>
                                        </select>
                                    </div>
                                </div>

                                <progress value="0" class="mp-progress-bar" id="cardProgress">Carregando...</progress>

                                <div class="real-payment-warning" id="realCardWarning" hidden>
                                    Com a configuração real ligada, este botão envia a autorização segura do cartão para
                                    <code>/overgrace/api/payments/card</code>.
                                </div>

                                <div class="demo-payment-box card-demo-box">
                                    <p class="demo-title">Prévia demonstrativa</p>
                                    <p>Enquanto não existe endpoint real, esses botões simulam os possíveis retornos da
                                        operadora de pagamento.</p>
                                    <div class="demo-card-grid">
                                        <button type="button"
                                            onclick="confirmarPedido('cartao', 'approved')">Aprovado</button>
                                        <button type="button" onclick="confirmarPedido('cartao', 'pending')">Em
                                            análise</button>
                                        <button type="button"
                                            onclick="confirmarPedido('cartao', 'rejected')">Recusado</button>
                                    </div>
                                </div>

                                <button type="submit" class="submit-btn" id="form-checkout__submit">
                                    Simular pagamento no cartão <span class="arrow">→</span>
                                </button>
                                <button type="button" class="back-step-btn voltarPedido">←
                                    Voltar</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="order-summary" aria-label="Resumo do pedido">
                <div class="summary-sticky">
                    <p class="summary-label">Seu pedido</p>
                    <div class="summary-mini-status">
                        <span id="summaryItemsCount">0 itens</span>
                        <a href="carrinho">Editar carrinho</a>
                    </div>

                    <div class="order-items" id="order-items">
                        <div class="summary-empty">Carregando carrinho...</div>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-line">
                        <span class="l">Subtotal</span>
                        <span class="v" id="total-items">R$ 0,00</span>
                    </div>
                    <div class="summary-line">
                        <span class="l">Frete</span>
                        <span class="v" id="shipLabel">Grátis</span>
                    </div>
                    <div class="summary-line">
                        <span class="l">Descontos</span>
                        <span class="v discount" id="total-descontos">R$ 0,00</span>
                    </div>

                    <div class="summary-total-line">
                        <span class="l">Total</span>
                        <span class="v" id="total-items-final">R$ 0,00</span>
                    </div>

                    <div class="summary-benefits">
                        <span>🔒 SSL ativo</span>
                        <span>🛡️ Compra protegida</span>
                        <span>💳 Pagamento seguro</span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="confirm-screen" id="confirmScreen">
            <div class="confirm-icon" id="confirmIcon">✓</div>
            <p class="checkout-eyebrow" id="confirmEyebrow">Pedido criado</p>
            <h1 class="confirm-title" id="confirmTitle">Pedido <em>confirmado!</em></h1>
            <p class="confirm-sub" id="confirmSub">
                O visual já está pronto. Quando o endpoint real estiver ligado, essa tela aparece após pagamento
                aprovado ou pedido criado.
            </p>

            <div class="result-card" id="resultCard">
                <div class="result-row">
                    <span>Número do pedido</span>
                    <strong id="confirmOrderNum">#OVG-00000</strong>
                </div>
                <div class="result-row">
                    <span>Total</span>
                    <strong id="confirmTotal">R$ 0,00</strong>
                </div>
                <div class="result-row">
                    <span>Pagamento</span>
                    <strong id="confirmPaymentMethod">PIX</strong>
                </div>
            </div>

            <div class="payment-next-card" id="pixNextCard" hidden>
                <h2>PIX copia e cola</h2>
                <img
                    id="pixQrCode"
                    class="pix-qrcode"
                    alt="QR Code PIX" />
                <p class="copy-code" id="pixCode">
                    00020126580014BR.GOV.BCB.PIX0136overgrace-demo-checkout5204000053039865802BR5920OVERGRACE
                    DEMO6009SAO PAULO62070503***6304ABCD</p>
                <button type="button" class="outline-action" onclick="copyPix(event)">Copiar código PIX</button>
                <button type="button" class="submit-btn compact" onclick="simularStatus('approved')">Simular PIX
                    pago</button>
            </div>

            <div class="payment-next-card" id="boletoNextCard" hidden>
                <h2>Boleto gerado</h2>
                <p>Vencimento demonstrativo: <strong id="boletoDueDate">--/--/----</strong></p>
                <p class="copy-code" id="boletoCode">23790.00009 00000.000000 00000.000000 1 00000000000000</p>
                <button type="button" class="outline-action" onclick="copyBoleto(event)">Copiar linha digitável</button>
                <button type="button" class="submit-btn compact" onclick="simularStatus('approved')">Simular boleto
                    compensado</button>
            </div>

            <div class="payment-next-card" id="analysisNextCard" hidden>
                <h2>Pagamento em análise</h2>
                <p>Essa é a tela para cartão pendente. Geralmente o pedido fica aguardando confirmação da operadora de
                    pagamento.
                </p>
                <button type="button" class="submit-btn compact" onclick="simularStatus('approved')">Simular
                    aprovação</button>
                <button type="button" class="outline-action" onclick="simularStatus('rejected')">Simular recusa</button>
            </div>

            <div class="confirm-actions">
                <button type="button" class="outline-action" onclick="voltarCheckoutDemo()">← Voltar ao
                    checkout</button>
                <a href="loja" class="confirm-cta">Continuar comprando →</a>
            </div>
        </section>
    </main>

    <footer class="checkout-footer">
        <div>
            <strong>OverGrace</strong>
            <span>Moda com presença, checkout sem susto.</span>
        </div>
        <div class="footer-links">
            <a href="sobre">Sobre</a>
            <a href="carrinho">Carrinho</a>
            <a href="lista">Loja</a>
        </div>
    </footer>

    <script type="module" src="frontend/js/utils/notify.js"></script>
    <script type="module" src="frontend/js/modules/checkout/utils.js"></script>
    <script type="module" src="frontend/js/modules/client/register.js"></script>
    <script type="module" src="frontend/js/modules/checkout/list.js"></script>
    <script type="module" src="frontend/js/modules/checkout/payment.js"></script>
</body>

</html>