import { siteContentService } from '../../services/siteContentService.js';

const form = document.getElementById('siteContentForm');
const container = document.getElementById('siteContentSections');
const alertBox = document.getElementById('siteContentAlert');
const submitButton = document.getElementById('saveSiteContent');

const SECTION_LABELS = {
    global: 'Geral do site',
    home_hero: 'Página inicial · Hero principal',
    shop_coming: 'Loja · Novidades no final da página',
    collections_hero: 'Coleções · Hero',
    collections_manifesto: 'Coleções · Manifesto',
    collections_drops: 'Coleções · Drops',
    about_hero: 'Sobre · Hero',
    about_story: 'Sobre · Nossa história',
    about_values: 'Sobre · Missão, visão e valores',
    about_message: 'Sobre · Mensagem',
    about_cta: 'Sobre · Chamada final'
};

const PAGE_GROUPS = [
    {
        title: 'Página inicial',
        description: 'Campos que aparecem logo na primeira dobra da home.',
        sections: ['home_hero']
    },
    {
        title: 'Loja',
        description: 'Campos da página de produtos, incluindo a área de novidades no fim da página.',
        sections: ['shop_coming']
    },
    {
        title: 'Coleções',
        description: 'Campos organizados na mesma sequência visual da página Coleções.',
        sections: ['collections_hero', 'collections_manifesto', 'collections_drops']
    },
    {
        title: 'Sobre',
        description: 'Campos organizados na mesma sequência visual da página Sobre.',
        sections: ['about_hero', 'about_story', 'about_values', 'about_message', 'about_cta']
    },
    {
        title: 'Geral do site',
        description: 'Itens usados em mais de uma página, como faixa superior, logo e rodapé.',
        sections: ['global']
    }
];

function showAlert(message, type = 'success') {
    alertBox.textContent = message;
    alertBox.className = `cms-alert ${type}`;
    alertBox.hidden = false;

    setTimeout(() => {
        alertBox.hidden = true;
    }, 4500);
}

function escapeHtml(value = '') {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function resolveAsset(path) {
    if (!path) return '';

    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }

    return `/overgrace/${path.replace(/^\/+/, '')}`;
}

function groupRows(rows) {
    return rows.reduce((acc, row) => {
        acc[row.section_key] ??= [];
        acc[row.section_key].push(row);
        return acc;
    }, {});
}

function renderField(row) {
    const value = row.value ?? '';
    const name = `content[${row.section_key}][${row.field_key}]`;

    if (row.field_type === 'textarea' || row.field_type === 'html') {
        return `
            <div class="cms-field cms-field-full">
                <label>${escapeHtml(row.label)}</label>
                <textarea name="${name}" rows="${row.field_type === 'html' ? 3 : 4}">${escapeHtml(value)}</textarea>
                ${row.field_type === 'html' ? '<small>Esse campo aceita HTML simples, como &lt;br&gt;, &lt;span&gt; e &lt;em&gt;.</small>' : ''}
            </div>
        `;
    }

    if (row.field_type === 'image') {
        const preview = value ? `
            <div class="cms-preview">
                <img src="${escapeHtml(resolveAsset(value))}" alt="${escapeHtml(row.label)}">
            </div>
        ` : '';

        return `
            <div class="cms-field cms-field-full">
                <label>${escapeHtml(row.label)}</label>
                ${preview}
                <input type="hidden" name="${name}" value="${escapeHtml(value)}">
                <input type="file" name="images[${row.section_key}][${row.field_key}]" accept="image/jpeg,image/png,image/webp">
                <small>Formatos aceitos: JPG, PNG e WEBP. A imagem atual só muda se você enviar outra.</small>
            </div>
        `;
    }

    return `
        <div class="cms-field">
            <label>${escapeHtml(row.label)}</label>
            <input type="text" name="${name}" value="${escapeHtml(value)}">
        </div>
    `;
}

function renderSection(sectionKey, fields) {
    return `
        <section class="cms-card">
            <div class="cms-card-header">
                <div>
                    <span>Conteúdo editável</span>
                    <h3>${escapeHtml(SECTION_LABELS[sectionKey] || sectionKey)}</h3>
                </div>
            </div>
            <div class="cms-grid">
                ${fields.map(renderField).join('')}
            </div>
        </section>
    `;
}

function renderSections(rows) {
    const grouped = groupRows(rows);
    const renderedSections = new Set();

    const html = PAGE_GROUPS.map(group => {
        const sectionsHtml = group.sections
            .filter(sectionKey => grouped[sectionKey])
            .map(sectionKey => {
                renderedSections.add(sectionKey);
                return renderSection(sectionKey, grouped[sectionKey]);
            })
            .join('');

        if (!sectionsHtml) return '';

        return `
            <div class="cms-page-group">
                <div class="cms-page-group-title">
                    <span>Ordem da página</span>
                    <h2>${escapeHtml(group.title)}</h2>
                    <p>${escapeHtml(group.description)}</p>
                </div>
                ${sectionsHtml}
            </div>
        `;
    }).join('');

    const unknownSectionsHtml = Object.entries(grouped)
        .filter(([sectionKey]) => !renderedSections.has(sectionKey))
        .map(([sectionKey, fields]) => renderSection(sectionKey, fields))
        .join('');

    container.innerHTML = html + unknownSectionsHtml;
}

async function loadContent() {
    try {
        container.innerHTML = '<div class="cms-loading">Carregando conteúdo...</div>';
        const response = await siteContentService.listarAdmin();
        renderSections(response.data || []);
    } catch (error) {
        console.error(error);
        container.innerHTML = '';
        showAlert('Não foi possível carregar o conteúdo do site.', 'error');
    }
}

async function saveContent(event) {
    event.preventDefault();

    try {
        submitButton.disabled = true;
        submitButton.textContent = 'Salvando...';

        const formData = new FormData(form);
        await siteContentService.salvar(formData);

        showAlert('Conteúdo salvo com sucesso. A loja já pode exibir as alterações.');
        await loadContent();
    } catch (error) {
        console.error(error);
        showAlert(error?.data?.error || error?.message || 'Erro ao salvar conteúdo.', 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Salvar alterações';
    }
}

form.addEventListener('submit', saveContent);
loadContent();
