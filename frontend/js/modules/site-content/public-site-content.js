import { siteContentService } from '../../services/siteContentService.js';

const BASE_PUBLIC = '/overgrace/';

function get(content, section, field, fallback = '') {
    return content?.[section]?.[field]?.value ?? fallback;
}

function asset(path) {
    if (!path) return '';

    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }

    return BASE_PUBLIC + path.replace(/^\/+/, '');
}

function setText(selector, value) {
    const el = document.querySelector(selector);
    if (el && value !== '') el.textContent = value;
}

function setHtml(selector, value) {
    const el = document.querySelector(selector);
    if (el && value !== '') el.innerHTML = value;
}

function setImage(selector, src, alt = '') {
    const img = document.querySelector(selector);
    if (!img || !src) return;

    img.src = asset(src);

    if (alt) {
        img.alt = alt;
    }
}

function setLink(selector, href, text = null) {
    const link = document.querySelector(selector);
    if (!link) return;

    if (href) link.href = href;
    if (text !== null) link.innerHTML = `${text} <span class="arrow">→</span>`;
}

function setButtonLink(selector, href, text = null) {
    const button = document.querySelector(selector);
    if (!button) return;

    if (text !== null) button.textContent = text;

    if (href) {
        button.type = 'button';
        button.addEventListener('click', () => {
            window.location.href = href;
        });
    }
}

function applyGlobal(content) {
    setText('.topbar', get(content, 'global', 'topbar_text'));

    document.querySelectorAll('.logo, .footer-logo').forEach(el => {
        const logoText = get(content, 'global', 'logo_text');
        if (logoText) el.textContent = logoText;
    });

    setText('.footer-tagline', get(content, 'global', 'footer_tagline'));

    const socialLinks = document.querySelectorAll('.footer-socials a');
    if (socialLinks[0]) socialLinks[0].href = get(content, 'global', 'instagram_link', '#');
    if (socialLinks[1]) socialLinks[1].href = get(content, 'global', 'whatsapp_link', '#');
}

function applyHome(content) {
    if (!document.querySelector('.hero')) return;

    setText('.hero-eyebrow', get(content, 'home_hero', 'eyebrow'));
    setHtml('.hero-title', get(content, 'home_hero', 'title_html'));
    setText('.hero-desc', get(content, 'home_hero', 'description'));
    setLink('.hero .btn-outline', get(content, 'home_hero', 'button_link'), get(content, 'home_hero', 'button_text'));
    setImage('.hero-image img', get(content, 'home_hero', 'image'), get(content, 'home_hero', 'image_alt'));
    setText('.hero-image-badge', get(content, 'home_hero', 'badge'));
}

function applyShop(content) {
    if (!document.querySelector('.coming-section')) return;

    setText('.coming-header h2', get(content, 'shop_coming', 'title'));
    setText('.coming-subtitle', get(content, 'shop_coming', 'subtitle'));

    const comingCards = document.querySelectorAll('.coming-card');

    if (comingCards[0]) {
        setImage('.coming-card:nth-of-type(1) img', get(content, 'shop_coming', 'card_1_image'), get(content, 'shop_coming', 'card_1_alt'));
        const text = comingCards[0].querySelector('.coming-overlay p');
        if (text) text.textContent = get(content, 'shop_coming', 'card_1_text', text.textContent);
    }

    if (comingCards[1]) {
        setImage('.coming-card:nth-of-type(2) img', get(content, 'shop_coming', 'card_2_image'), get(content, 'shop_coming', 'card_2_alt'));
        const text = comingCards[1].querySelector('.coming-overlay p');
        if (text) text.textContent = get(content, 'shop_coming', 'card_2_text', text.textContent);
    }

    setText('.coming-cta p', get(content, 'shop_coming', 'cta_text'));
    setButtonLink('.coming-cta button', get(content, 'shop_coming', 'button_link'), get(content, 'shop_coming', 'button_text'));
}

function applyAbout(content) {
    if (!document.querySelector('.about-hero')) return;

    setHtml('.about-hero h1', get(content, 'about_hero', 'title_html'));

    setImage('.story img', get(content, 'about_story', 'image'), get(content, 'about_story', 'image_alt'));
    setText('.story h2', get(content, 'about_story', 'title'));

    const storyParagraphs = document.querySelectorAll('.story p');
    if (storyParagraphs[0]) storyParagraphs[0].textContent = get(content, 'about_story', 'paragraph_1', storyParagraphs[0].textContent);
    if (storyParagraphs[1]) storyParagraphs[1].textContent = get(content, 'about_story', 'paragraph_2', storyParagraphs[1].textContent);

    const valueCards = document.querySelectorAll('.value-card');
    if (valueCards[0]) {
        valueCards[0].querySelector('h3').textContent = get(content, 'about_values', 'mission_title', 'Missão');
        valueCards[0].querySelector('p').textContent = get(content, 'about_values', 'mission_text', valueCards[0].querySelector('p').textContent);
    }
    if (valueCards[1]) {
        valueCards[1].querySelector('h3').textContent = get(content, 'about_values', 'vision_title', 'Visão');
        valueCards[1].querySelector('p').textContent = get(content, 'about_values', 'vision_text', valueCards[1].querySelector('p').textContent);
    }
    if (valueCards[2]) {
        valueCards[2].querySelector('h3').textContent = get(content, 'about_values', 'values_title', 'Valores');
        valueCards[2].querySelector('p').textContent = get(content, 'about_values', 'values_text', valueCards[2].querySelector('p').textContent);
    }

    setHtml('.message h2', get(content, 'about_message', 'title_html'));
    setText('.message p', get(content, 'about_message', 'subtitle'));

    setText('.cta h2', get(content, 'about_cta', 'title'));
    setText('.cta p', get(content, 'about_cta', 'text'));
    setLink('.cta .btn-outline', get(content, 'about_cta', 'button_link'), get(content, 'about_cta', 'button_text'));
}

function applyCollections(content) {
    if (!document.querySelector('.brand-hero')) return;

    setImage('.brand-hero .hero-bg', get(content, 'collections_hero', 'image'), get(content, 'collections_hero', 'image_alt'));
    setText('.brand-hero .eyebrow', get(content, 'collections_hero', 'eyebrow'));
    setHtml('.brand-hero h1', get(content, 'collections_hero', 'title_html'));
    setText('.brand-hero p', get(content, 'collections_hero', 'description'));
    setLink('.brand-hero .btn-outline', get(content, 'collections_hero', 'button_link'), get(content, 'collections_hero', 'button_text'));

    setImage('.manifesto img', get(content, 'collections_manifesto', 'image'), get(content, 'collections_manifesto', 'image_alt'));
    setText('.manifesto h2', get(content, 'collections_manifesto', 'title'));

    const manifestoParagraphs = document.querySelectorAll('.manifesto p');
    if (manifestoParagraphs[0]) manifestoParagraphs[0].textContent = get(content, 'collections_manifesto', 'paragraph_1', manifestoParagraphs[0].textContent);
    if (manifestoParagraphs[1]) manifestoParagraphs[1].textContent = get(content, 'collections_manifesto', 'paragraph_2', manifestoParagraphs[1].textContent);

    setText('.drops-kicker', get(content, 'collections_drops', 'kicker'));
    setHtml('.drops-heading h2', get(content, 'collections_drops', 'title_html'));
    setText('.drops-heading p', get(content, 'collections_drops', 'description'));

    const dropCards = document.querySelectorAll('.drop-card');
    if (dropCards[0]) {
        setImage('.drop-card:nth-of-type(1) img', get(content, 'collections_drops', 'drop_1_image'));
        dropCards[0].querySelector('span').textContent = get(content, 'collections_drops', 'drop_1_label', 'DROP 01');
        dropCards[0].querySelector('h3').textContent = get(content, 'collections_drops', 'drop_1_title', 'Essential Lines');
    }
    if (dropCards[1]) {
        setImage('.drop-card:nth-of-type(2) img', get(content, 'collections_drops', 'drop_2_image'));
        dropCards[1].querySelector('span').textContent = get(content, 'collections_drops', 'drop_2_label', 'DROP 02');
        dropCards[1].querySelector('h3').textContent = get(content, 'collections_drops', 'drop_2_title', 'Winter Layers');
    }
    if (dropCards[2]) {
        dropCards[2].querySelector('img').src = asset(get(content, 'collections_drops', 'drop_3_image'));
        dropCards[2].querySelector('span').textContent = get(content, 'collections_drops', 'drop_3_label', 'DROP 03');
        dropCards[2].querySelector('h2').textContent = get(content, 'collections_drops', 'drop_3_title', 'Street Uniform');
        dropCards[2].querySelector('p').textContent = get(content, 'collections_drops', 'drop_3_description', dropCards[2].querySelector('p').textContent);
    }
}

async function loadSiteContent() {
    try {
        const response = await siteContentService.listarPublico();
        const content = response.data || {};

        applyGlobal(content);
        applyHome(content);
        applyShop(content);
        applyCollections(content);
        applyAbout(content);
    } catch (error) {
        console.warn('[SITE CONTENT] Não foi possível carregar conteúdo dinâmico.', error);
    }
}

loadSiteContent();
