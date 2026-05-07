import { publicProdutoService } from "../../services/publicProdutoService.js";

async function carregarMaisVendidos() {

  const track = document.getElementById("bestTrack");

  try {

    const res = await publicProdutoService.listar();

    const produtos = res.data;

    let html = "";

    produtos.forEach(produto => {

      const img = produto.imagem_principal
        ? `/overgrace/frontend/uploads/products/${produto.imagem_principal}`
        : "/overgrace/frontend/uploads/placeholders/sem-item.png";

      html += `
      
        <a
          href="produto?id=${produto.uuid}"
          class="best-card"
        >

          <div class="best-image-wrap">

            <img src="${img}" />

          </div>

          <div class="best-info">

            <p class="best-name">
              ${produto.descricao}
            </p>

            <div class="best-price">

              ${
                Number(produto.preco_antigo) > 0
                ? `<span class="best-old">
                    R$${produto.preco_antigo}
                  </span>`
                : ""
              }

              <span>
                R$${produto.preco_atual}
              </span>

            </div>

            <div class="best-cta">
              Ver produto →
            </div>

          </div>

        </a>
      
      `;
    });

    track.innerHTML = html;

    iniciarCarrossel();

  } catch(e) {

    console.error(e);

  }
}

let current = 0;

function iniciarCarrossel() {

  const track = document.getElementById("bestTrack");

  const cards = document.querySelectorAll(".best-card");

  if(!cards.length) return;

  const visible = 3;

  setInterval(() => {

    current++;

    if(current > cards.length - visible) {
      current = 0;
    }

    const width = cards[0].offsetWidth + 24;

    track.style.transform =
      `translateX(-${current * width}px)`;

  }, 3500);
}

carregarMaisVendidos();