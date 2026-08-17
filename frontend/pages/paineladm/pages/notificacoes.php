<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notificações — OverGrace</title>
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css?v=22">
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/notificacoes.css?v=1">
</head>
<body>
<div class="shell">
  <?php include 'frontend/pages/paineladm/sidebar.php'; ?>
  <main class="main">
    <?php include 'frontend/pages/paineladm/navbar.php'; ?>
    <section class="page-content og-page">
      <div class="page-header og-page-header">
        <div>
          <span class="og-eyebrow">Central administrativa</span>
          <h1>Notificações</h1>
          <p>Acompanhe pedidos, pagamentos, estoque e falhas de envio.</p>
        </div>
        <div class="page-header-actions">
          <button class="btn btn-outline" id="processQueue">Processar e-mails</button>
          <button class="btn btn-primary" id="markAllRead">Marcar todas como lidas</button>
        </div>
      </div>

      <div class="og-toolbar">
        <div class="og-segmented" role="tablist">
          <button class="active" data-filter="all">Todas</button>
          <button data-filter="unread">Não lidas <span id="unreadCount">0</span></button>
        </div>
        <button class="btn btn-ghost" id="refreshNotifications">Atualizar</button>
      </div>

      <div class="notification-list" id="notificationList">
        <div class="og-empty">Carregando notificações...</div>
      </div>
    </section>
  </main>
</div>
<script type="module" src="/overgrace/frontend/js/modules/notifications/adminNotifications.js?v=1"></script>
</body>
</html>
