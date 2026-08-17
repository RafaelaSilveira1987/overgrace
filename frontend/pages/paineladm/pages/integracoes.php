<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integrações — OverGrace Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css?v=22">
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/integracoes.css?v=1">
</head>
<body>
<div class="shell">
  <?php include 'frontend/pages/paineladm/sidebar.php' ?>
  <div class="main">
    <?php include 'frontend/pages/paineladm/navbar.php' ?>
    <main class="page-content integrations-page">
      <div class="page-header integration-header">
        <div>
          <h1>Integrações e notificações</h1>
          <p>Configure e-mail SMTP e WhatsApp pela Evolution API sem depender de n8n ou IA.</p>
        </div>
        <span class="security-note">Credenciais protegidas no banco</span>
      </div>

      <div class="integration-tabs" role="tablist">
        <button class="integration-tab active" data-tab="smtp">E-mail SMTP</button>
        <button class="integration-tab" data-tab="evolution">WhatsApp / Evolution</button>
      </div>

      <section class="integration-panel active" id="panel-smtp">
        <div class="integration-grid">
          <form class="integration-card" id="smtpForm">
            <div class="card-heading">
              <div><span class="eyebrow">E-mail transacional</span><h2>Servidor SMTP</h2></div>
              <label class="switch"><input type="checkbox" id="smtp_enabled"><span></span></label>
            </div>
            <p class="card-description">Use o SMTP do domínio, Gmail, Brevo, Resend SMTP ou outro provedor compatível.</p>

            <div class="form-grid two">
              <label>Host SMTP<input id="smtp_host" placeholder="smtp.seudominio.com.br" required></label>
              <label>Porta<input id="smtp_port" type="number" value="587" min="1" max="65535" required></label>
              <label>Criptografia<select id="smtp_encryption"><option value="tls">TLS</option><option value="ssl">SSL</option><option value="none">Sem criptografia</option></select></label>
              <label>Usuário<input id="smtp_username" autocomplete="username" placeholder="notificacoes@dominio.com"></label>
              <label>Senha<input id="smtp_password" type="password" autocomplete="new-password" placeholder="••••••••"></label>
              <label>E-mail remetente<input id="smtp_from_email" type="email" placeholder="notificacoes@dominio.com" required></label>
              <label>Nome remetente<input id="smtp_from_name" placeholder="OverGrace"></label>
              <label>Administradores destinatários<input id="smtp_admin_recipients" placeholder="admin@dominio.com, financeiro@dominio.com"></label>
            </div>
            <div class="form-actions"><button class="btn-light" type="button" id="smtpTestOpen">Enviar teste</button><button class="btn-dark" type="submit">Salvar SMTP</button></div>
          </form>

          <aside class="integration-card compact">
            <span class="eyebrow">Como funciona</span>
            <h2>Notificações diretas</h2>
            <ol class="steps"><li>A OverGrace grava o evento.</li><li>O worker processa a fila.</li><li>O SMTP envia aos administradores.</li></ol>
            <div class="tip">A senha é criptografada antes de ser salva. Ao editar, deixe “********” para manter a senha atual.</div>
          </aside>
        </div>
      </section>

      <section class="integration-panel" id="panel-evolution">
        <div class="integration-grid">
          <form class="integration-card" id="evolutionForm">
            <div class="card-heading"><div><span class="eyebrow">WhatsApp</span><h2>Evolution API</h2></div><label class="switch"><input type="checkbox" id="evolution_enabled"><span></span></label></div>
            <p class="card-description">Cadastre o servidor, crie a instância, gere o QR Code e selecione os eventos enviados ao webhook.</p>
            <div class="form-grid two">
              <label>URL da Evolution<input id="evolution_base_url" placeholder="https://evolution.seudominio.com"></label>
              <label>API Key global<input id="evolution_api_key" type="password" placeholder="••••••••"></label>
              <label>Nome da instância<input id="evolution_instance_name" placeholder="overgrace"></label>
              <label>Webhook da OverGrace<input id="evolution_webhook_url" placeholder="https://loja.com/api/webhooks/evolution"></label>
              <label class="span-two">Números dos administradores<input id="evolution_admin_numbers" placeholder="5532999999999, 5532888888888"></label>
            </div>
            <div class="notification-options">
              <h3>Notificar administradores quando</h3>
              <label><input type="checkbox" id="evolution_notify_new_order"> Novo pedido for criado</label>
              <label><input type="checkbox" id="evolution_notify_payment_paid"> Pagamento for aprovado</label>
              <label><input type="checkbox" id="evolution_notify_payment_review"> Pagamento exigir revisão</label>
              <label><input type="checkbox" id="evolution_notify_stock_low"> Estoque ficar abaixo do mínimo</label>
            </div>
            <div class="events-box"><div class="events-title"><h3>Eventos do webhook</h3><button type="button" class="text-btn" id="selectRecommended">Selecionar recomendados</button></div><div class="events-grid" id="eventsGrid"></div></div>
            <div class="form-actions"><button class="btn-dark" type="submit">Salvar Evolution</button></div>
          </form>

          <aside class="integration-card evolution-manager">
            <div class="card-heading"><div><span class="eyebrow">Instância</span><h2>Gerenciar WhatsApp</h2></div><span class="status-pill" id="instanceStatus">Não consultado</span></div>
            <div class="manager-actions">
              <button class="btn-dark" id="createInstance" type="button">Criar instância</button>
              <button class="btn-light" id="generateQr" type="button">Gerar QR Code</button>
              <button class="btn-light" id="checkState" type="button">Atualizar estado</button>
              <button class="btn-light" id="setWebhook" type="button">Salvar webhook/eventos</button>
            </div>
            <div class="qr-area" id="qrArea"><div class="qr-placeholder">O QR Code aparecerá aqui.</div></div>
            <div class="pairing-code" id="pairingCode" hidden></div>
            <div class="test-row"><input id="evolution_test_number" placeholder="Número para teste: 5532..."><button class="btn-light" id="testWhatsapp" type="button">Enviar teste</button></div>
            <div class="danger-actions"><button type="button" id="logoutInstance">Desconectar</button><button type="button" id="deleteInstance">Excluir instância</button></div>
          </aside>
        </div>
      </section>
    </main>
  </div>
</div>

<div class="modal-backdrop" id="smtpTestModal" hidden><div class="modal-card"><button class="modal-close" type="button" data-close-modal>×</button><span class="eyebrow">Teste de envio</span><h2>Enviar e-mail de teste</h2><label>Destinatário<input id="smtp_test_email" type="email" placeholder="voce@email.com"></label><div class="form-actions"><button class="btn-light" type="button" data-close-modal>Cancelar</button><button class="btn-dark" type="button" id="sendSmtpTest">Enviar teste</button></div></div></div>

<script>window.APP_BASE_PATH = <?= json_encode($appBasePath ?? '', JSON_UNESCAPED_SLASHES) ?>;</script>
<script type="module" src="/overgrace/frontend/js/modules/integrations/adminIntegrations.js?v=24"></script>
</body>
</html>
