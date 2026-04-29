
// ── MODAL ────────────────────────────────────────────────────
function openRepor(prod) {
  const modal = document.getElementById("reporModal");
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("f-date").value = today;

  if (prod) {
    document.getElementById("reporProdutoWrap").style.display = "";
    document.getElementById("reporSelectWrap").style.display = "none";
    document.getElementById("reporImg").src = prod.img;
    document.getElementById("reporNome").textContent = prod.name;
    document.getElementById("reporId").textContent = prod.id;
    document.getElementById("reporTitle").textContent = "Repor estoque";
  } else {
    document.getElementById("reporProdutoWrap").style.display = "none";
    document.getElementById("reporSelectWrap").style.display = "";
    document.getElementById("reporTitle").textContent =
      "Registrar entrada de estoque";
  }

  modal.classList.add("open");
  document.body.style.overflow = "hidden";
}

function onSelectProd(val) {
  if (!STOCK[val]) {
    document.getElementById("reporSizesContainer").innerHTML =
      '<p style="font-size:12px;color:var(--ink-3)">Selecione um produto para ver os tamanhos.</p>';
    return;
  }
  renderSizeInputs(STOCK[val].sizes);
}

function renderSizeInputs(sizes) {
  document.getElementById("reporSizesContainer").innerHTML = sizes
    .map(
      (sz) => `
        <div class="repor-row">
          <span class="repor-size-tag">${sz.s}</span>
          <span class="repor-current">Atual: <strong>${sz.qty}</strong> un.</span>
          <span class="repor-arrow">→</span>
          <span style="font-size:12px;color:var(--ink-3)">Adicionar:</span>
          <input type="number" min="0" value="0" class="repor-input" id="repor-qty-${sz.s}">
        </div>
      `,
    )
    .join("");
}

function closeRepor() {
  document.getElementById("reporModal").classList.remove("open");
  document.body.style.overflow = "";
}

function closeReporOutside(e) {
  if (e.target === e.currentTarget) closeRepor();
}

function confirmarRepor() {
  closeRepor();
  // Toast simples de feedback
  const t = document.createElement("div");
  t.textContent = "Estoque atualizado com sucesso";
  t.style.cssText =
    "position:fixed;bottom:24px;right:24px;background:#18181b;color:#fff;font-size:13px;padding:10px 18px;border-radius:8px;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.15)";
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2400);
}

function toggleFornecedor() {
  const tipo = document.querySelector('input[name="movType"]:checked')?.value;
  const fornecedorDiv = document.getElementById('fornecedor-repor');

  if (!fornecedorDiv) return;

  if (tipo === 'ajuste') {
    fornecedorDiv.style.display = 'none';
  } else {
    fornecedorDiv.style.display = 'block';
  }
}


document.addEventListener('change', (e) => {
  if (e.target.name === 'movType') {
    toggleFornecedor();
  }
});


document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeRepor();
});

function renderSizeInputsFromSelector(sizes = []) {
  const container = document.getElementById("reporSizesContainer");

  console.log('container encontrado: ', container);

  if (!container) return;

  container.innerHTML = sizes
    .map((s, index) => `
      <div class="repor-row" data-size="${s}">
        
        <input type="hidden" name="sizes[${index}][tamanho]" value="${s}">

        <span class="repor-size-tag">${s}</span>
        <span class="repor-arrow">→</span>

        <span style="font-size:12px;color:#888">Quantidade:</span>
        <input 
          type="number" 
          min="0" 
          value="0" 
          class="repor-input" 
          name="sizes[${index}][quantidade]">
      </div>
    `)
    .join("");
}



const input = document.getElementById('f-images');

const previewWrap = document.getElementById('imgPreviewWrap');

let imagens = [];

function render() {
  previewWrap.innerHTML = '';

  imagens.forEach((imgObj, index) => {

    const item = document.createElement('div');
    item.className = 'img-item';

    const img = document.createElement('img');
    img.src = imgObj.src;

    // remover
    const btnRemove = document.createElement('button');
    btnRemove.className = 'img-remove';
    btnRemove.innerHTML = '×';

    btnRemove.addEventListener('click', (e) => {
      e.stopPropagation();
      imagens.splice(index, 1);
      render();
    });

    // radio principal
    const radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = 'imagem_principal';
    radio.value = index;
    radio.className = 'img-radio';

    item.appendChild(img);
    item.appendChild(btnRemove);
    item.appendChild(radio);

    previewWrap.appendChild(item);
  });
}


