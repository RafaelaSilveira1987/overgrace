import { publicProdutoService } from '../../services/publicProdutoService.js';

export async function carregarVitrine() {
  const container = document.getElementById('lista-produtos');
  container.innerHTML = 'Carregando...';

  try {
    const res = await publicProdutoService.listar();

    let html = '';

    res.data.forEach(produto => {

      const img = produto.imagem_principal 
        ? `/overgrace/frontend/uploads/products/${produto.imagem_principal}`
        : '/overgrace/frontend/uploads/placeholders/sem-item.png';

      const semEstoque = produto.estoque <= 0;
 
      html += `

            <a
                href="produto?id=${produto.uuid}"
                class="product-card"
                data-category="${produto.categoria}">
                <div class="product-img-wrap">
                <img src="${img}" class="product-img"/>
                <span class="product-badge">Novo</span>
                </div>
                <div class="product-info">
                <p class="product-name">${produto.descricao}</p>
                    <p class="product-price">
                    ${Number(produto.preco_antigo) > 0 ? `<span class="old-price">R$${produto.preco_antigo}</span>` : '' }

                    R$${produto.preco_atual}
                    </p>
                </div>
            </a>
      `;
    });

    container.innerHTML = html;

  } catch (e) {
    console.error(e);
    container.innerHTML = 'Erro ao carregar produtos';
  }
}

carregarVitrine();
