    <!-- ── SIDEBAR ──────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <div class="sidebar-logo-mark">OverGrace <span>Admin</span></div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-group">
          <div class="nav-group-label">Visão geral</div>
          <a class="nav-item" href="dashboard" data-page="dashboard">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <rect x="2" y="2" width="5" height="5" rx="1" />
              <rect x="9" y="2" width="5" height="5" rx="1" />
              <rect x="2" y="9" width="5" height="5" rx="1" />
              <rect x="9" y="9" width="5" height="5" rx="1" />
            </svg>
            Dashboard
          </a>
        </div>

        <div class="nav-group">
          <div class="nav-group-label">Catálogo</div>
          <a class="nav-item" href="product" data-page="produtos">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <path d="M2 4h12M4 8h8M6 12h4" />
            </svg>
            Produtos
          </a>
          <a class="nav-item" href="product_stock" data-page="estoque">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <rect x="2" y="10" width="3" height="4" rx="1" />
              <rect x="6.5" y="6" width="3" height="8" rx="1" />
              <rect x="11" y="2" width="3" height="12" rx="1" />
            </svg>
            Estoque
            <span class="nav-badge" id="badge-estoque">3</span>
          </a>
        </div>

        <div class="nav-group">
          <div class="nav-group-label">Vendas</div>
          <a class="nav-item" href="orders" data-page="pedidos">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <path d="M2 3h12l-1.5 8H3.5L2 3z" />
              <circle cx="6" cy="14" r="1" />
              <circle cx="11" cy="14" r="1" />
            </svg>
            Pedidos
            <span class="nav-badge" id="badge-pedidos">7</span>
          </a>
          <a class="nav-item" href="client" data-page="clientes">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <circle cx="8" cy="5" r="3" />
              <path d="M2 14c0-3.3 2.7-6 6-6s6 2.7 6 6" />
            </svg>
            Clientes
          </a>
          <a
            class="nav-item"
            href="coupons"
            data-page="cupons">
            <svg
              class="nav-icon"
              fill="none"
              viewBox="0 0 16 16"
              stroke="currentColor"
              stroke-width="1.5">
              <path
                d="M2 5.5A1.5 1.5 0 013.5 4h9A1.5 1.5 0 0114 5.5V7a1 1 0 000 2v1.5A1.5 1.5 0 0112.5 12h-9A1.5 1.5 0 012 10.5V9a1 1 0 000-2V5.5z" />
              <path d="M6 4v8" stroke-dasharray="1 1" />
            </svg>
            Cupons
          </a>
        </div>

        <div class="nav-group">
          <div class="nav-group-label">Sistema</div>
          <a class="nav-item" href="configuration" data-page="configuracoes">
            <svg class="nav-icon" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
              <circle cx="8" cy="8" r="2.5" />
              <path
                d="M8 1v2M8 13v2M1 8h2M13 8h2M3.1 3.1l1.4 1.4M11.5 11.5l1.4 1.4M3.1 12.9l1.4-1.4M11.5 4.5l1.4-1.4" />
            </svg>
            Configurações
          </a>
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-avatar">MF</div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name">Mary OverGrace</div>
          <div class="sidebar-user-role">Administrador</div>
        </div>
      </div>
    </aside>