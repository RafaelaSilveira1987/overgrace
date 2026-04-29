<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <style>
    body {
      background: var(--bg);
      overflow-y: auto;
    }

    .page {
      padding: 28px 28px 40px;
    }
  </style>
</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "dashboard"
    }, "*");
  </script>

  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>
    
    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <div class="page-header-left">
            <h1>Dashboard</h1>
            <p>Resumo de hoje, 24 de abril de 2025</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">
              <svg
                width="14"
                height="14"
                fill="none"
                viewBox="0 0 14 14"
                stroke="currentColor"
                stroke-width="1.5">
                <rect x="1" y="2" width="12" height="11" rx="1.5" />
                <path d="M4 1v2M10 1v2M1 6h12" />
              </svg>
              Abril 2025
            </button>
            <button class="btn btn-outline">Exportar</button>
          </div>
        </div>
  
        <!-- KPIs -->
        <div class="kpi-grid">
          <div class="kpi">
            <div class="kpi-label">
              <svg
                width="13"
                height="13"
                fill="none"
                viewBox="0 0 13 13"
                stroke="currentColor"
                stroke-width="1.5">
                <path d="M6.5 1v11M9.5 4H5a2 2 0 000 4h3a2 2 0 010 4H4" />
              </svg>
              Faturamento (mês)
            </div>
            <div class="kpi-value">R$ 18.420</div>
            <span class="kpi-delta up">↑ 12,4% vs mês anterior</span>
          </div>
          <div class="kpi">
            <div class="kpi-label">
              <svg
                width="13"
                height="13"
                fill="none"
                viewBox="0 0 13 13"
                stroke="currentColor"
                stroke-width="1.5">
                <path d="M2 3h9l-1.2 7H3.2L2 3z" />
                <circle cx="5" cy="12" r=".8" />
                <circle cx="8.5" cy="12" r=".8" />
              </svg>
              Pedidos (mês)
            </div>
            <div class="kpi-value">94</div>
            <span class="kpi-delta up">↑ 8,1% vs mês anterior</span>
          </div>
          <div class="kpi">
            <div class="kpi-label">
              <svg
                width="13"
                height="13"
                fill="none"
                viewBox="0 0 13 13"
                stroke="currentColor"
                stroke-width="1.5">
                <circle cx="6.5" cy="4.5" r="2.5" />
                <path d="M1.5 12c0-2.8 2.2-5 5-5s5 2.2 5 5" />
              </svg>
              Novos clientes
            </div>
            <div class="kpi-value">31</div>
            <span class="kpi-delta down">↓ 3,2% vs mês anterior</span>
          </div>
          <div class="kpi">
            <div class="kpi-label">
              <svg
                width="13"
                height="13"
                fill="none"
                viewBox="0 0 13 13"
                stroke="currentColor"
                stroke-width="1.5">
                <path d="M6.5 1a5.5 5.5 0 100 11A5.5 5.5 0 006.5 1z" />
                <path d="M6.5 4v3l2 2" />
              </svg>
              Ticket médio
            </div>
            <div class="kpi-value">R$ 196</div>
            <span class="kpi-delta up">↑ 4,7% vs mês anterior</span>
          </div>
        </div>
  
        <!-- Gráfico + Mais vendidos -->
        <div class="dashboard-grid">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Receita por dia — Abril</span>
              <select
                style="
                  font-size: 12px;
                  border: 1px solid var(--border);
                  border-radius: 5px;
                  padding: 3px 7px;
                  background: #fff;
                  color: var(--ink-2);
                ">
                <option>Este mês</option>
                <option>Mês anterior</option>
                <option>Este ano</option>
              </select>
            </div>
            <div class="card-body">
              <div class="chart-bars">
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 40%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 55%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 35%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 70%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 60%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 48%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 80%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 65%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 50%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 72%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 90%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 68%"></div>
                </div>
                <div class="chart-bar">
                  <div class="chart-bar-fill" style="height: 55%"></div>
                </div>
                <div class="chart-bar accent">
                  <div class="chart-bar-fill" style="height: 100%"></div>
                </div>
              </div>
              <div class="chart-labels">
                <div class="chart-label">11</div>
                <div class="chart-label">12</div>
                <div class="chart-label">13</div>
                <div class="chart-label">14</div>
                <div class="chart-label">15</div>
                <div class="chart-label">16</div>
                <div class="chart-label">17</div>
                <div class="chart-label">18</div>
                <div class="chart-label">19</div>
                <div class="chart-label">20</div>
                <div class="chart-label">21</div>
                <div class="chart-label">22</div>
                <div class="chart-label">23</div>
                <div class="chart-label">24</div>
              </div>
            </div>
          </div>
  
          <div class="card">
            <div class="card-header">
              <span class="card-title">Mais vendidos</span>
            </div>
            <div class="card-body">
              <div class="top-products">
                <div class="top-product-row">
                  <img
                    class="top-product-img"
                    src="https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=80&q=70"
                    alt="" />
                  <div class="top-product-info">
                    <div class="top-product-name">Camisa Linho Off-White</div>
                    <div class="top-product-cat">Camisas · 38 vendas</div>
                    <div class="top-product-bar">
                      <div class="top-product-bar-fill" style="width: 100%"></div>
                    </div>
                  </div>
                  <div class="top-product-val">R$ 7.182</div>
                </div>
                <div class="top-product-row">
                  <img
                    class="top-product-img"
                    src="https://images.unsplash.com/photo-1521369909029-2afed882baee?w=80&q=70"
                    alt="" />
                  <div class="top-product-info">
                    <div class="top-product-name">Boné Estruturado Bege</div>
                    <div class="top-product-cat">Bonés · 29 vendas</div>
                    <div class="top-product-bar">
                      <div class="top-product-bar-fill" style="width: 76%"></div>
                    </div>
                  </div>
                  <div class="top-product-val">R$ 3.451</div>
                </div>
                <div class="top-product-row">
                  <img
                    class="top-product-img"
                    src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=80&q=70"
                    alt="" />
                  <div class="top-product-info">
                    <div class="top-product-name">Kit Camisa + Boné</div>
                    <div class="top-product-cat">Kits · 21 vendas</div>
                    <div class="top-product-bar">
                      <div class="top-product-bar-fill" style="width: 55%"></div>
                    </div>
                  </div>
                  <div class="top-product-val">R$ 5.439</div>
                </div>
                <div class="top-product-row">
                  <img
                    class="top-product-img"
                    src="https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=80&q=70"
                    alt="" />
                  <div class="top-product-info">
                    <div class="top-product-name">Camisa Oversized Cáqui</div>
                    <div class="top-product-cat">Camisas · 15 vendas</div>
                    <div class="top-product-bar">
                      <div class="top-product-bar-fill" style="width: 39%"></div>
                    </div>
                  </div>
                  <div class="top-product-val">R$ 2.625</div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Pedidos recentes -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Pedidos recentes</span>
            <a
              onclick="
                window.parent.postMessage(
                  { type: 'navigate', page: 'pedidos' },
                  '*',
                )
              "
              style="
                font-size: 12px;
                color: var(--ink-3);
                cursor: pointer;
                text-decoration: none;
              ">
              Ver todos →
            </a>
          </div>
          <div class="card-body" style="padding: 0">
            <div class="recent-orders" style="padding: 0 18px">
              <div class="recent-order-row">
                <span class="recent-order-num">#10094</span>
                <span class="recent-order-client">Ana Beatriz Souza</span>
                <span class="status-pill status-enviado">Enviado</span>
                <span class="recent-order-val">R$ 259,00</span>
              </div>
              <div class="recent-order-row">
                <span class="recent-order-num">#10093</span>
                <span class="recent-order-client">Carlos Henrique M.</span>
                <span class="status-pill status-pago">Pago</span>
                <span class="recent-order-val">R$ 189,00</span>
              </div>
              <div class="recent-order-row">
                <span class="recent-order-num">#10092</span>
                <span class="recent-order-client">Fernanda Oliveira</span>
                <span class="status-pill status-pendente">Pendente</span>
                <span class="recent-order-val">R$ 318,00</span>
              </div>
              <div class="recent-order-row">
                <span class="recent-order-num">#10091</span>
                <span class="recent-order-client">Rafael Teixeira</span>
                <span class="status-pill status-pago">Pago</span>
                <span class="recent-order-val">R$ 99,00</span>
              </div>
              <div class="recent-order-row">
                <span class="recent-order-num">#10090</span>
                <span class="recent-order-client">Juliana Ferreira</span>
                <span class="status-pill status-cancelado">Cancelado</span>
                <span class="recent-order-val">R$ 175,00</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>

</html>