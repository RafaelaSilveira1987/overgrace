import { publicProdutoService } from "../../services/publicProdutoService.js";

async function carregarProduto() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  console.log("ID:", id);

  try {
    const res = await publicProdutoService.listar();

    const produto = res.data.find((p) => p.uuid === id);

    if (!produto) {
      console.error("Produto não encontrado");
      return;
    }

    // Preenche a tela
    document.getElementById("f-name").innerText = produto.descricao;
    document.getElementById("f-price").innerText = produto.preco_atual;

    if (produto.preco_antigo > 0) {
      document.getElementById("f-price-old").innerText =
        `R$${produto.preco_antigo}`;
    } else {
      document.getElementById("f-price-old").style.display = "none";
    }

    document.getElementById("f-desc").innerText = produto.descricao;

    // imagem
    const img = produto.imagem_principal
      ? `/overgrace/frontend/uploads/products/${produto.imagem_principal}`
      : "/overgrace/frontend/uploads/placeholders/sem-item.png";

    document.getElementById("mainProductImage").src = img;

    // AQUI entra os relacionados
    carregarRelacionados(produto.categoria, produto.uuid);
  } catch (e) {
    console.error("Erro ao carregar produto", e);
  }
}

async function carregarRelacionados(categoriaAtual, produtoAtualId) {
  const container = document.getElementById("relatedTrack");

  try {
    const res = await publicProdutoService.listar();

    const lista = res.data.data || res.data;

    let relacionados = lista.filter(
      (p) => p.categoria === categoriaAtual && p.uuid !== produtoAtualId,
    );

    if (relacionados.length === 0) {
      console.warn("Sem relacionados da mesma categoria, mostrando outros");
      relacionados = lista.filter((p) => p.uuid !== produtoAtualId);
    }

    relacionados = relacionados.slice(0, 10);

    let html = "";

    relacionados.forEach((produto) => {
      const img = produto.imagem_principal
        ? `/overgrace/frontend/uploads/products/${produto.imagem_principal}`
        : "/overgrace/frontend/uploads/placeholders/sem-item.png";

      html += `
        <a href="produto?id=${produto.uuid}" class="related-card">
          <img src="${img}" />
          <div class="related-info">
            <p class="related-name">${produto.descricao}</p>
            <span class="related-price">R$${produto.preco_atual}</span>
          </div>
        </a>
      `;
    });

    container.innerHTML = html;

    iniciarCarrossel();
  } catch (e) {
    console.error(e);
  }
}

let currentIndex = 0;

function iniciarCarrossel() {
  const track = document.getElementById("relatedTrack");
  const cards = document.querySelectorAll(".related-card");

  if (!cards.length) return;

  const total = cards.length;
  const visible = 3;

  setInterval(() => {
    currentIndex++;

    if (currentIndex > total - visible) {
      currentIndex = 0;
    }

    const cardWidth = cards[0].offsetWidth + 20;

    track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
  }, 3000);
}

carregarProduto();
