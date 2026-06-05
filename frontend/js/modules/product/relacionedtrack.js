import { publicProdutoService } from "../../services/publicProdutoService.js";

const PRODUCT_IMAGE_BASE = "/overgrace/frontend/uploads/products/";
const PLACEHOLDER_IMAGE = "/overgrace/frontend/uploads/placeholders/sem-item.png";

let galleryImages = [];
let currentImageIndex = 0;

function getEl(id) {
  return document.getElementById(id);
}

function productImagePath(imageName) {
  if (!imageName) {
    return PLACEHOLDER_IMAGE;
  }

  if (
    imageName.startsWith("http") ||
    imageName.startsWith("/") ||
    imageName.startsWith("data:")
  ) {
    return imageName;
  }

  return `${PRODUCT_IMAGE_BASE}${imageName}`;
}

function normalizeProductList(response) {
  return response?.data?.data || response?.data || response || [];
}

function normalizeGalleryImages(produto) {
  if (Array.isArray(produto.imagens) && produto.imagens.length > 0) {
    return produto.imagens
      .map((img) => img.nome || img.imagem || img)
      .filter(Boolean)
      .map(productImagePath);
  }

  if (produto.imagem_principal) {
    return [productImagePath(produto.imagem_principal)];
  }

  return [PLACEHOLDER_IMAGE];
}

function updateGallery(index) {
  if (!galleryImages.length) {
    return;
  }

  currentImageIndex = (index + galleryImages.length) % galleryImages.length;

  const mainImage = getEl("mainProductImage");
  const lightboxImage = getEl("lightboxImage");

  if (mainImage) {
    mainImage.src = galleryImages[currentImageIndex];
  }

  if (lightboxImage) {
    lightboxImage.src = galleryImages[currentImageIndex];
  }

  document.querySelectorAll(".thumb-list img").forEach((img, imgIndex) => {
    img.classList.toggle("active", imgIndex === currentImageIndex);
  });
}

function renderGallery(produto) {
  galleryImages = normalizeGalleryImages(produto);
  currentImageIndex = 0;

  const thumbList = getEl("thumbList");
  const prevButton = getEl("galleryPrev");
  const nextButton = getEl("galleryNext");

  const hasMultipleImages = galleryImages.length > 1;

  if (prevButton) {
    prevButton.hidden = !hasMultipleImages;
  }

  if (nextButton) {
    nextButton.hidden = !hasMultipleImages;
  }

  if (thumbList) {
    thumbList.innerHTML = galleryImages
      .map(
        (src, index) => `
          <img
            src="${src}"
            alt="Miniatura ${index + 1}"
            class="${index === 0 ? "active" : ""}"
            data-gallery-index="${index}"
          />
        `,
      )
      .join("");

    thumbList.querySelectorAll("img").forEach((thumb) => {
      thumb.addEventListener("click", () => {
        updateGallery(Number(thumb.dataset.galleryIndex));
      });
    });
  }

  updateGallery(0);
}

function setupGalleryControls() {
  const mainImage = getEl("mainProductImage");
  const prevButton = getEl("galleryPrev");
  const nextButton = getEl("galleryNext");

  const lightbox = getEl("productLightbox");
  const lightboxClose = getEl("lightboxClose");
  const lightboxPrev = getEl("lightboxPrev");
  const lightboxNext = getEl("lightboxNext");

  if (prevButton) {
    prevButton.addEventListener("click", () => {
      updateGallery(currentImageIndex - 1);
    });
  }

  if (nextButton) {
    nextButton.addEventListener("click", () => {
      updateGallery(currentImageIndex + 1);
    });
  }

  if (mainImage && lightbox) {
    mainImage.addEventListener("click", () => {
      if (!galleryImages.length) {
        return;
      }

      lightbox.hidden = false;
      document.body.classList.add("lightbox-open");
      updateGallery(currentImageIndex);
    });
  }

  function closeLightbox() {
    if (!lightbox) {
      return;
    }

    lightbox.hidden = true;
    document.body.classList.remove("lightbox-open");
  }

  if (lightboxClose) {
    lightboxClose.addEventListener("click", closeLightbox);
  }

  if (lightboxPrev) {
    lightboxPrev.addEventListener("click", (event) => {
      event.stopPropagation();
      updateGallery(currentImageIndex - 1);
    });
  }

  if (lightboxNext) {
    lightboxNext.addEventListener("click", (event) => {
      event.stopPropagation();
      updateGallery(currentImageIndex + 1);
    });
  }

  if (lightbox) {
    lightbox.addEventListener("click", (event) => {
      if (event.target === lightbox) {
        closeLightbox();
      }
    });
  }

  document.addEventListener("keydown", (event) => {
    if (!galleryImages.length) {
      return;
    }

    const lightboxIsOpen = lightbox && !lightbox.hidden;

    if (lightboxIsOpen) {
      if (event.key === "Escape") {
        closeLightbox();
      }

      if (event.key === "ArrowLeft") {
        updateGallery(currentImageIndex - 1);
      }

      if (event.key === "ArrowRight") {
        updateGallery(currentImageIndex + 1);
      }

      return;
    }

    if (event.key === "ArrowLeft") {
      updateGallery(currentImageIndex - 1);
    }

    if (event.key === "ArrowRight") {
      updateGallery(currentImageIndex + 1);
    }
  });
}

async function carregarProduto() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    console.error("ID do produto não informado");
    return;
  }

  try {
    const produto = await publicProdutoService.buscar(id);

    if (!produto) {
      console.error("Produto não encontrado");
      return;
    }

    const fieldId = getEl("f-id");
    const fieldName = getEl("f-name");
    const fieldPrice = getEl("f-price");
    const fieldPriceOld = getEl("f-price-old");
    const fieldDesc = getEl("f-desc");

    if (fieldId) {
      fieldId.value = produto.id;
    }

    if (fieldName) {
      fieldName.innerText = produto.descricao || "Produto";
    }

    if (fieldPrice) {
      fieldPrice.innerText = produto.preco_atual || "0,00";
    }

    if (fieldPriceOld) {
      if (Number(produto.preco_antigo) > 0) {
        fieldPriceOld.innerText = `R$${produto.preco_antigo}`;
        fieldPriceOld.style.display = "inline";
      } else {
        fieldPriceOld.style.display = "none";
      }
    }

    if (fieldDesc) {
      fieldDesc.innerText = produto.descricao_completa || produto.descricao || "";
    }

    renderGallery(produto);
    carregarRelacionados(produto.categoria, produto.uuid);
  } catch (e) {
    console.error("Erro ao carregar produto", e);
  }
}

async function carregarRelacionados(categoriaAtual, produtoAtualId) {
  const container = getEl("relatedTrack");

  if (!container) {
    return;
  }

  try {
    const res = await publicProdutoService.listar();
    const lista = normalizeProductList(res);

    let relacionados = lista.filter(
      (p) => p.categoria === categoriaAtual && p.uuid !== produtoAtualId,
    );

    if (relacionados.length === 0) {
      relacionados = lista.filter((p) => p.uuid !== produtoAtualId);
    }

    relacionados = relacionados.slice(0, 10);

    container.innerHTML = relacionados
      .map((produto) => {
        const img = produto.imagem_principal
          ? productImagePath(produto.imagem_principal)
          : PLACEHOLDER_IMAGE;

        return `
          <a href="produto?id=${produto.uuid}" class="related-card">
            <img src="${img}" alt="${produto.descricao || "Produto relacionado"}" />
            <div class="related-info">
              <p class="related-name">${produto.descricao || "Produto"}</p>
              <span class="related-price">R$${produto.preco_atual || "0,00"}</span>
            </div>
          </a>
        `;
      })
      .join("");

    iniciarCarrossel();
  } catch (e) {
    console.error("Erro ao carregar produtos relacionados", e);
  }
}

let currentRelatedIndex = 0;
let relatedInterval = null;

function iniciarCarrossel() {
  const track = getEl("relatedTrack");
  const cards = document.querySelectorAll(".related-card");

  if (!track || !cards.length) {
    return;
  }

  if (relatedInterval) {
    clearInterval(relatedInterval);
  }

  currentRelatedIndex = 0;

  const total = cards.length;
  const visible = 3;

  if (total <= visible) {
    track.style.transform = "translateX(0)";
    return;
  }

  relatedInterval = setInterval(() => {
    currentRelatedIndex++;

    if (currentRelatedIndex > total - visible) {
      currentRelatedIndex = 0;
    }

    const cardWidth = cards[0].offsetWidth + 20;
    track.style.transform = `translateX(-${currentRelatedIndex * cardWidth}px)`;
  }, 3000);
}

setupGalleryControls();
carregarProduto();