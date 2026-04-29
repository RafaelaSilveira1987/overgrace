// 🔎 Pega tamanhos selecionados
function getSelectedSizes() {
  return [...document.querySelectorAll(".size-selector .size-opt.active")]
    .map(el => el.textContent.trim());
}


function renderSizeInputsFromSelector() {
  const container = document.getElementById("reporSizesContainer");
  if (!container) return;

  const sizes = getSelectedSizes();

  if (!sizes.length) {
    container.innerHTML =
      '<p style="font-size:12px;color:#888">Nenhum tamanho selecionado.</p>';
    return;
  }

  container.innerHTML = sizes
    .map((s, index) => `
      <div class="repor-row" data-size="${s}">
        
        <input type="hidden" name="sizes[${index}][tamanho]" value="${s}">

        <span class="repor-size-tag">${s}</span>
        <span class="repor-arrow">→</span>

        <span style="font-size:12px;color:#888">Estoque Inicial:</span>
        <input 
          type="number" 
          min="0" 
          value="0" 
          class="repor-input" 
          name="sizes[${index}][estoque_inicial]">

        <span class="repor-arrow">→</span>

        <span style="font-size:12px;color:#888">Estoque Minimo:</span>
        <input 
          type="number" 
          min="0" 
          value="0" 
          class="repor-input" 
          name="sizes[${index}][estoque_minimo]">

      </div>
    `)
    .join("");
}




// 🧲 EVENTO GLOBAL (funciona com modal dinâmico)
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("size-opt")) {
    e.target.classList.toggle("active");

    // Atualiza estoque em tempo real
    renderSizeInputsFromSelector();
  }
});




function openModal() {
  document.getElementById("modalOverlay").classList.add("open");
}

function closeModal() {
  document.getElementById("modalOverlay").classList.remove("open");
}

function closeModalOutside(e) {
  if (e.target === e.currentTarget) closeModal();
}

function updateImgPreview(url) {
  const wrap = document.getElementById("imgPreviewWrap");
  if (url) {
    wrap.innerHTML = `<img src="${url}" alt="preview" onerror="this.parentElement.innerHTML='<div class=img-preview-placeholder><span>URL inválida</span></div>'" />`;
  } else {
    wrap.innerHTML = `<div class="img-preview-placeholder"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" opacity=".4"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg><span>Pré-visualização</span></div>`;
  }
}
// Auto-gera slug pelo nome
document.getElementById("f-name").addEventListener("input", function () {
  document.getElementById("f-id").value = this.value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
});


function validarImagens() {
  if (imagens.length === 0) {
    alert('Adicione pelo menos uma imagem.');
    return false;
  }

  if (principalIndex === null) {
    alert('Selecione uma imagem principal.');
    return false;
  }

  return true;
}


const input = document.getElementById('f-images');
const previewWrap = document.getElementById('imgPreviewWrap');

let imagens = [];

input.addEventListener('change', (e) => {
  const files = Array.from(e.target.files);

  files.forEach(file => {
    const reader = new FileReader();

    reader.onload = (ev) => {
      imagens.push({
        tipo: 'nova',
        src: ev.target.result,
        file: file,
        nome: null
      });

      render();
    };

    reader.readAsDataURL(file);
  });

  input.value = '';
});


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

