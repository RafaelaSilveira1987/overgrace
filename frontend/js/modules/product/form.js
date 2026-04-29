import { carregarProdutos } from '../../modules/product/lista.js';
import { produtoService } from '../../services/productService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';
import { alertConfirm } from '../../utils/alerts.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';


let produtoEditandoId = null;

document.getElementById('formProd').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        // 🔹 IDs obrigatórios
        const idsObrigatorios = [
            'f-name',
            'f-id',
            'f-cat',
            'f-price',
            'f-date-start'
        ];

        let erro = false;
        const campos = {};

        // 🔥 valida antes de usar .value
        idsObrigatorios.forEach(id => {
            const campo = document.getElementById(id);

            if (!campo) {
                console.error('❌ Campo não encontrado:', id);
                erro = true;
                return;
            }

            if (!campo.value) {
                marcarErro(campo);
                erro = true;
            }

            campos[id] = campo;
        });

        if (erro) {
            throw new Error('Preencha todos os campos obrigatórios.');
        }

        // 🔹 tamanhos
        const tamanhos = [...document.querySelectorAll('#reporSizesContainer .repor-row')]
            .map(row => {
                const tamanho = row.dataset.size;

                const estoqueInicial = row.querySelector(
                    'input[name*="[estoque_inicial]"]'
                ).value;

                const estoqueMinimo = row.querySelector(
                    'input[name*="[estoque_minimo]"]'
                ).value;

                return {
                    tamanho,
                    estoque_inicial: Number(estoqueInicial),
                    estoque_minimo: Number(estoqueMinimo)
                };
            });


        if (tamanhos.length === 0) {
            throw new Error('Selecione pelo menos um tamanho.');
        }


        let principal = document.querySelector('input[name="imagem_principal"]:checked');

        // 🔥 nenhuma imagem
        if (imagens.length === 0) {
            throw new Error('Adicione pelo menos uma imagem');
        }

        // 🔥 só uma imagem → automaticamente principal
        if (imagens.length === 1) {
            principal = { value: 0 };
        }

        // 🔥 mais de uma e nenhuma selecionada
        if (imagens.length > 1 && !principal) {
            throw new Error('Selecione pelo menos uma imagem principal');
        }



        // 🔹 montar dados com segurança
        const dados = {
            nome: campos['f-name'].value,
            slug: campos['f-id'].value,
            categoria: campos['f-cat'].value,

            preco: parseFloat(campos['f-price'].value),
            preco_antigo: document.getElementById('f-price-old')?.value
                ? parseFloat(document.getElementById('f-price-old').value)
                : null,

            badge: document.getElementById('f-badge')?.value || null,
            posicao: document.getElementById('f-position')?.value || null,

            data_inicio: campos['f-date-start'].value,
            data_fim: document.getElementById('f-date-end')?.value || null,

            descricao: document.getElementById('f-desc')?.value || '',
            material: document.getElementById('f-material')?.value || null,

            peso: document.getElementById('f-weight')?.value
                ? parseFloat(document.getElementById('f-weight').value)
                : null,

            tags: document.getElementById('f-tags')?.value
                ? document.getElementById('f-tags').value.split(',').map(t => t.trim())
                : [],

            estoque_inicial: parseInt(document.getElementById('f-stock')?.value || 0),

            ativo: document.getElementById('f-active')?.checked ?? false,
            destaque: document.getElementById('f-featured')?.checked ?? false,
            backorder: document.getElementById('f-backorder')?.checked ?? false,
            tamanhos: tamanhos
        };

        // 🔹 montar FormData
        const formData = new FormData();

        Object.entries(dados).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                formData.append(key, JSON.stringify(value));
            } else {
                formData.append(key, value ?? '');
            }
        });

        // 🔥 usar o array que você criou no JS
        const imagensAntigas = [];

        imagens.forEach((img, index) => {

            if (img.tipo === 'nova') {
                formData.append('imagens[]', img.file);
            }

            if (img.tipo === 'antiga') {
                imagensAntigas.push(img.nome);
            }
        });

        // 🔹 manter no backend
        formData.append('imagens_existentes', JSON.stringify(imagensAntigas));

        // 🔹 principal
        formData.append('imagem_principal', principal.value);

        // 🔥 DEBUG (se quiser ver o que está indo)
        // for (let [k, v] of formData.entries()) console.log(k, v);

        // 🔥 CREATE ou UPDATE
        if (produtoEditandoId) {
            await produtoService.atualizar(produtoEditandoId, formData);
            notify.success('Produto atualizado com sucesso!');
        } else {
            await produtoService.criar(formData);
            notify.success('Produto cadastrado com sucesso!');
        }

        // 🔹 reset
        produtoEditandoId = null;

        document.getElementById('formProd').reset();
        document.querySelectorAll('#sizeSelector .size-opt')
            .forEach(el => el.classList.remove('active'));

        await carregarProdutos();
        closeModal();

    } catch (e) {
        console.error(e);
        notify.error(e.message || e.error || 'Erro ao executar ação');
    }
});


export async function editarProduto(id) {
    try {
        const produto = await produtoService.buscar(id);

        produtoEditandoId = id;

        // 🔹 preencher campos
        document.getElementById('f-name').value = produto.descricao;
        document.getElementById('f-id').value = produto.desc_slug;
        document.getElementById('f-cat').value = produto.categoria;

        document.getElementById('f-price').value = produto.preco_atual;
        document.getElementById('f-price-old').value = produto.preco_antigo || '';

        document.getElementById('f-badge').value = produto.badge || '';
        document.getElementById('f-position').value = produto.posicao || '';

        document.getElementById('f-date-start').value = dataUtil(produto.inicio_exibicao, 'format', 'Y-m-d');
        document.getElementById('f-date-end').value = dataUtil(produto.fim_exibicao, 'format', 'Y-m-d') || '';

        document.getElementById('f-desc').value = produto.descricao_completa || '';
        document.getElementById('f-material').value = produto.material || '';
        document.getElementById('f-weight').value = produto.peso || '';

        document.getElementById('f-tags').value = (produto.tags || []).join(', ');

        document.getElementById('f-active').checked = produto.ativo == 1;

        document.getElementById('f-stock').value = produto.estoque_inicial;

        // 🔹 imagens
        // 🔹 limpar imagens atuais
        imagens = [];

        // 🔹 montar caminho base (ajusta conforme seu servidor)
        const BASE_IMG = '/overgrace/frontend/uploads/products/';

        let principalIndex = 0;

        produto.imagens.forEach((img, index) => {

            if (img.destaque == 1) {
                principalIndex = index;
            }

            imagens.push({
                tipo: 'antiga',
                src: BASE_IMG + img.nome,
                file: null,
                nome: img.nome
            });
        });

        // 🔥 se só tem 1 imagem → ela é principal
        if (imagens.length === 1) {
            principalIndex = 0;
        }

        // 🔥 fallback se nenhuma tiver destaque
        if (principalIndex === null && imagens.length > 0) {
            principalIndex = 0;
        }

        // renderizar
        render();

        setTimeout(() => {
            const radios = document.querySelectorAll('input[name="imagem_principal"]');

            if (radios[principalIndex]) {
                radios[principalIndex].checked = true;

                radios[principalIndex]
                    .closest('.img-item')
                    .classList.add('principal');
            }
        }, 0);




        // 🔹 tamanhos (resetar e ativar)
        document.querySelectorAll('#sizeSelector .size-opt')
            .forEach(el => el.classList.remove('active'));

        produto.tamanhos.forEach(t => {
            const el = [...document.querySelectorAll('#sizeSelector .size-opt')]
                .find(e => e.textContent.trim() === t.tamanho);

            if (el) el.classList.add('active');
        });

        // 🔥 renderiza os inputs
        renderSizeInputsFromSelector();

        // 🔥 agora preenche os valores vindos do backend
        setTimeout(() => {
            produto.tamanhos.forEach(t => {

                const row = document.querySelector(
                    `.repor-row[data-size="${t.tamanho}"]`
                );

                if (!row) return;

                const inputInicial = row.querySelector(
                    'input[name*="[estoque_inicial]"]'
                );

                const inputMinimo = row.querySelector(
                    'input[name*="[estoque_minimo]"]'
                );

                if (inputInicial) inputInicial.value = t.estoque_inicial ?? 0;
                if (inputMinimo) inputMinimo.value = t.minimo ?? 0;
            });
        }, 0);

        openModal();

    } catch (e) {
        notify.error('Erro ao carregar produto');
        console.error(e);
    }
}

export async function removerProduto(id) {
    const confirm = await alertConfirm(
        "Deseja realmente excluir este produto?",
        "confirm",
        "Excluir produto"
    );

    if (!confirm) return;

    try {
        await produtoService.deletar(id);

        await alertConfirm(
            "Produto excluído com sucesso!",
            "success"
        );

        // atualizar grid
        carregarProdutos();

    } catch (e) {
        await alertConfirm(
            "Erro ao excluir produto.",
            "error"
        );
    }
}


document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-editar')) {
        const id = e.target.closest('.btn-editar').dataset.id;
        editarProduto(id);
    }
});

