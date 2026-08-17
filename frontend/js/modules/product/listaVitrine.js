import { publicProdutoService } from "../../services/publicProdutoService.js";

export async function carregarVitrine() {
  const container = document.getElementById("lista-produtos");
  container.innerHTML = "Carregando...";

  try {
    const res = await publicProdutoService.listar();

    let html = "";

    res.data.forEach((produto) => {
      const img = produto.imagem_principal
        ? `/overgrace/frontend/uploads/products/${produto.imagem_principal}`
        : "/overgrace/frontend/uploads/placeholders/sem-item.png";

      // ✅ calcula estoque total
      let totalEstoque = 0;

      produto.tamanhos?.forEach((t) => {
        totalEstoque += Number(t.estoque) || 0;
      });

      // ✅ cria badge
      let estoqueBadge = "";

      if (totalEstoque <= 0) {
        estoqueBadge = `
      <span class="stock-badge sold-out">
        Esgotado
      </span>
    `;
      } else if (totalEstoque <= 3) {
        estoqueBadge = `
      <span class="stock-badge low-stock">
        Últimas peças
      </span>
    `;
      }

      let badgeFinal = "";

      /* ESGOTADO */
      if (totalEstoque <= 0) {
        badgeFinal = `
    <span class="stock-badge sold-out">
      Esgotado
    </span>
  `;
      } else if (totalEstoque <= 3) {

      /* ÚLTIMAS PEÇAS */
        badgeFinal = `
    <span class="stock-badge low-stock">
      Últimas peças
    </span>
  `;
      } else if (produto.badge) {

      /* BADGE DO PRODUTO */
        badgeFinal = `
    <span class="product-badge">
      ${produto.badge}
    </span>
  `;
      }

      // ✅ renderiza card
      html += `

    <a href="produto?id=${produto.uuid}" class="product-card" data-category="${produto.categoria}">
  
      <div class="product-img-wrap">

        <img src="${img}" class="product-img"/>
        ${badgeFinal}

      </div>

      <div class="product-info">
        
        <div class="product-text">
          <p class="product-name">${produto.descricao}</p>

          <p class="product-price">
            ${
              Number(produto.preco_antigo) > 0
                ? `<span class="old-price">R$${produto.preco_antigo}</span>`
                : ""
            }

            <strong>R$${produto.preco_atual}</strong>
          </p>
        </div>

        <span class="product-link">Ver mais →</span>

      </div>

    </a>
  `;
    });

    container.innerHTML = html;
  } catch (e) {
    console.error(e);
    container.innerHTML = "Erro ao carregar produtos";
  }
}

carregarVitrine();
