INSERT INTO site_content (section_key, field_key, field_type, label, value, sort_order) VALUES
('shop_coming', 'title', 'text', 'Título da seção', 'Novidades em breve', 10),
('shop_coming', 'subtitle', 'textarea', 'Subtítulo da seção', 'Estamos preparando peças exclusivas para a próxima coleção.', 20),
('shop_coming', 'card_1_image', 'image', 'Imagem do primeiro card', 'frontend/pages/assets/img5.png', 30),
('shop_coming', 'card_1_alt', 'text', 'Texto alternativo do primeiro card', 'Nova coleção inverno', 40),
('shop_coming', 'card_1_text', 'text', 'Texto do primeiro card', 'Nova coleção inverno', 50),
('shop_coming', 'card_2_image', 'image', 'Imagem do segundo card', 'frontend/pages/assets/img6.png', 60),
('shop_coming', 'card_2_alt', 'text', 'Texto alternativo do segundo card', 'Novos acessórios', 70),
('shop_coming', 'card_2_text', 'text', 'Texto do segundo card', 'Novos acessórios', 80),
('shop_coming', 'cta_text', 'text', 'Texto da chamada', 'Quer ser avisado primeiro?', 90),
('shop_coming', 'button_text', 'text', 'Texto do botão', 'Receber novidades', 100),
('shop_coming', 'button_link', 'url', 'Link do botão', '#', 110)
ON DUPLICATE KEY UPDATE
    field_type = VALUES(field_type),
    label = VALUES(label),
    sort_order = VALUES(sort_order),
    active = 1;
