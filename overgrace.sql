-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.4.7 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para overgrace
CREATE DATABASE IF NOT EXISTS `overgrace` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `overgrace`;

-- Copiando estrutura para tabela overgrace.carts
CREATE TABLE IF NOT EXISTS `carts` (
  `id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `session_token` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','converted','abandoned') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `coupon_id` int DEFAULT NULL,
  `coupon_valor` float DEFAULT NULL,
  `coupon_tipo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.carts: ~5 rows (aproximadamente)
INSERT INTO `carts` (`id`, `client_id`, `session_token`, `status`, `created_at`, `updated_at`, `coupon_id`, `coupon_valor`, `coupon_tipo`) VALUES
	(2, NULL, '300b9aa2b3f57a06ee92474636089bd6', 'active', '2026-05-02 16:04:40', '2026-05-03 19:11:16', 7, 45, 'fixo'),
	(2, NULL, '300b9aa2b3f57a06ee92474636089bd6', 'active', '2026-05-02 16:04:40', '2026-05-03 19:11:16', 7, 45, 'fixo'),
	(4, NULL, '37b3fe5aad224d474a0b9e024352000b', 'active', '2026-05-04 19:06:21', '2026-05-12 11:32:32', 7, 45, 'fixo'),
	(5, NULL, '8ae7b777daf06560bcce9d966a88043a', 'active', '2026-05-08 20:33:55', '2026-05-12 11:33:00', NULL, NULL, NULL),
	(3, NULL, '14ec39cc1af42c19cd4739ce8d22937d', 'converted', '2026-05-04 13:41:00', '2026-05-11 21:22:34', 5, 80.1, 'percentual');

-- Copiando estrutura para tabela overgrace.cart_items
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` bigint unsigned NOT NULL,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.cart_items: ~13 rows (aproximadamente)
INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `size`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
	(5, 2, 23, 'Único', 2, 325.50, '2026-05-02 19:50:16', '2026-05-03 19:05:24'),
	(8, 2, 15, 'P', 2, 50.00, '2026-05-02 19:53:38', '2026-05-02 20:11:35'),
	(9, 2, 14, 'Único', 11, 15.00, '2026-05-02 20:12:25', '2026-05-02 20:12:33'),
	(5, 2, 23, 'Único', 2, 325.50, '2026-05-02 19:50:16', '2026-05-03 19:05:24'),
	(8, 2, 15, 'P', 2, 50.00, '2026-05-02 19:53:38', '2026-05-02 20:11:35'),
	(9, 2, 14, 'Único', 11, 15.00, '2026-05-02 20:12:25', '2026-05-02 20:12:33'),
	(5, 2, 23, 'Único', 2, 325.50, '2026-05-02 19:50:16', '2026-05-03 19:05:24'),
	(8, 2, 15, 'P', 2, 50.00, '2026-05-02 19:53:38', '2026-05-02 20:11:35'),
	(9, 2, 14, 'Único', 11, 15.00, '2026-05-02 20:12:25', '2026-05-02 20:12:33'),
	(13, 5, 16, 'M', 2, 5.25, '2026-05-08 20:33:55', '2026-05-12 11:34:14'),
	(10, 3, 23, 'Único', 2, 325.50, '2026-05-04 13:41:00', '2026-05-11 19:02:17'),
	(11, 3, 15, 'M', 1, 50.00, '2026-05-04 13:41:17', '2026-05-11 18:57:53'),
	(12, 3, 17, 'G', 1, 55.25, '2026-05-11 18:57:29', '2026-05-11 18:57:29');

-- Copiando estrutura para tabela overgrace.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `sobrenome` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_general_ci DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.clients: ~3 rows (aproximadamente)
INSERT INTO `clients` (`id`, `uuid`, `nome`, `sobrenome`, `email`, `cpf`, `telefone`, `password`, `google_id`, `status`, `created_at`, `updated_at`) VALUES
	(4, 'e41745dd-472c-11f1-bd1a-f4b5204d1e7e', 'marcos', 'leandro', 'marcosadmleandro@gmail.com', '022.904.366-66', '(32) 99837-3640', '$2y$10$3QosFHKm0s.DgihnBPinquGCqn8jfpIGb39BStyAIPTsqS8JWn5RC', NULL, 'ativo', '2026-05-03 20:15:57', '2026-05-03 20:15:57'),
	(5, '2b7a0288-ad58-4b08-ab92-b7c995d017cb', 'Rafaela', 'Silveira', 'rafaelasilveira1987@gmail.com', '11803473746', '32998416669', '$2y$10$PdzXSBLYSP0v3hgNTpaKxeVKlV0l2Vh1GNGJ4gL3s.HNoU/bgo/Im', NULL, 'ativo', '2026-05-04 19:43:33', '2026-05-04 19:43:33'),
	(8, 'bb410dc0-48c1-11f1-80b7-d09466a5d484', 'Marcos Leandro', 'Silva', 'marcosadmleandro@gmail.com', '022.904.366-66', '(32) 99837-3640', '$2y$10$wsUyz7uTPIE12tI2mnlkLecsDP5uuiP2LL24OvFIVEa2/Gs76rKkS', NULL, 'ativo', '2026-05-05 23:33:53', '2026-05-05 23:33:53');

-- Copiando estrutura para tabela overgrace.clients_address
CREATE TABLE IF NOT EXISTS `clients_address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('entrega','comercial') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'entrega',
  `cep` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `complemento` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` varchar(2) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_adress_client` (`client_id`),
  CONSTRAINT `fk_adress_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.clients_address: ~1 rows (aproximadamente)
INSERT INTO `clients_address` (`id`, `client_id`, `tipo`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `estado`) VALUES
	(2, 8, 'entrega', '36884-081', 'Rua Rui Barbosa', '123', 'Barra', 'Cardoso de Melo', 'Muriaé', 'MG');

-- Copiando estrutura para tabela overgrace.coupons
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `cupom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('percentual','fixo','frete') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'percentual',
  `percentual` decimal(5,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `minimo` decimal(10,2) DEFAULT '0.00',
  `limite` int DEFAULT NULL,
  `validade` datetime DEFAULT NULL,
  `status` enum('ativo','pausado','expirado') COLLATE utf8mb4_general_ci DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.coupons: ~7 rows (aproximadamente)
INSERT INTO `coupons` (`id`, `uuid`, `cupom`, `tipo`, `percentual`, `valor`, `minimo`, `limite`, `validade`, `status`, `created_at`, `updated_at`) VALUES
	(2, 'db781bd8-46a6-11f1-bd1a-f4b5204d1e7e', 'CUPOMTESTE10', 'percentual', 10.00, 10.00, 150.00, 2500, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:16:29', '2026-05-03 04:19:34'),
	(3, 'f25d574c-46a6-11f1-bd1a-f4b5204d1e7e', 'NOVO20', 'percentual', 20.00, 20.00, 0.00, NULL, '2027-01-01 00:00:00', 'pausado', '2026-05-03 04:17:08', '2026-05-03 04:19:31'),
	(4, 'fec3c930-46a6-11f1-bd1a-f4b5204d1e7e', 'CUPOMEXPIRADO', 'percentual', 5.00, 5.00, 0.00, NULL, '2027-01-01 00:00:00', 'expirado', '2026-05-03 04:17:28', '2026-05-03 04:19:29'),
	(5, '20d06e73-46a7-11f1-bd1a-f4b5204d1e7e', 'TESTE', 'percentual', NULL, 10.00, 500.00, 5000, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:18:26', '2026-05-03 04:19:27'),
	(6, '3e955d6a-46a7-11f1-bd1a-f4b5204d1e7e', 'BONES15', 'percentual', NULL, 15.00, 500.00, 5000, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:19:15', '2026-05-03 15:25:53'),
	(7, 'a25c4f37-4704-11f1-bd1a-f4b5204d1e7e', 'CAMISETAS45', 'fixo', NULL, 45.00, 200.00, 5000, '2026-10-01 00:00:00', 'ativo', '2026-05-03 15:27:46', '2026-05-03 15:27:46'),
	(8, 'b722eeed-4704-11f1-bd1a-f4b5204d1e7e', 'FRETEGRATISVOVER', 'frete', NULL, 15.00, 0.00, NULL, '2026-06-01 00:00:00', 'ativo', '2026-05-03 15:28:21', '2026-05-03 15:28:31');

-- Copiando estrutura para tabela overgrace.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `cart_id` bigint DEFAULT NULL,
  `status` enum('pending','paid','canceled','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `subtotal` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `coupon_id` int DEFAULT NULL,
  `payment_status` enum('pending','approved','rejected','refunded','expired') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.orders: ~1 rows (aproximadamente)
INSERT INTO `orders` (`id`, `client_id`, `cart_id`, `status`, `subtotal`, `discount`, `shipping`, `coupon_id`, `payment_status`, `total_amount`, `created_at`, `updated_at`) VALUES
	(7, 8, 3, 'pending', 756.25, 80.10, 0.00, 5, 'pending', 676.15, '2026-05-11 21:22:34', '2026-05-11 21:22:34');

-- Copiando estrutura para tabela overgrace.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.order_items: ~3 rows (aproximadamente)
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
	(19, 7, 23, 'Tenis DC classic 3', 'Único', 2, 325.50, 651.00, '2026-05-11 21:22:34'),
	(20, 7, 15, 'Camisa gola povo alpha co amarela', 'M', 1, 50.00, 50.00, '2026-05-11 21:22:34'),
	(21, 7, 17, 'Novo produto', 'G', 1, 55.25, 55.25, '2026-05-11 21:22:34');

-- Copiando estrutura para tabela overgrace.order_status_history
CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.order_status_history: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela overgrace.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `method` enum('pix','credit_card') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.payments: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela overgrace.payment_credit_card
CREATE TABLE IF NOT EXISTS `payment_credit_card` (
  `id` bigint unsigned NOT NULL,
  `payment_id` bigint unsigned NOT NULL,
  `brand` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last4` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `installments` int DEFAULT NULL,
  `gateway_transaction_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.payment_credit_card: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela overgrace.payment_pix
CREATE TABLE IF NOT EXISTS `payment_pix` (
  `id` bigint unsigned NOT NULL,
  `payment_id` bigint unsigned NOT NULL,
  `pix_code` text COLLATE utf8mb4_general_ci,
  `qr_code` text COLLATE utf8mb4_general_ci,
  `expires_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.payment_pix: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela overgrace.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `desc_slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `preco_atual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_antigo` decimal(10,2) DEFAULT NULL,
  `badge` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `posicao` int DEFAULT NULL,
  `inicio_exibicao` datetime DEFAULT NULL,
  `fim_exibicao` datetime DEFAULT NULL,
  `tamanhos` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao_completa` text COLLATE utf8mb4_general_ci,
  `peso` decimal(10,3) DEFAULT NULL,
  `tags` text COLLATE utf8mb4_general_ci,
  `estoque_inicial` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `exibir_nome` tinyint(1) DEFAULT '1',
  `permitir_compra_sem_estoque` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.products: ~7 rows (aproximadamente)
INSERT INTO `products` (`id`, `uuid`, `descricao`, `desc_slug`, `categoria`, `preco_atual`, `preco_antigo`, `badge`, `posicao`, `inicio_exibicao`, `fim_exibicao`, `tamanhos`, `descricao_completa`, `peso`, `tags`, `estoque_inicial`, `ativo`, `exibir_nome`, `permitir_compra_sem_estoque`, `created_at`, `updated_at`, `deleted`, `deleted_at`) VALUES
	(6, 'd61b373a-5859-11f1-8c05-089798669242', 'Camisa - O que é que os Anjos Veem?', 'camisa-o-que-e-que-os-anjos-veem', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'Inspirada na eternidade e no mistério da presença divina, a camiseta oversized “O Que É Que os Anjos Veem?” traz uma reflexão profunda sobre fé, adoração e o olhar celestial sobre a humanidade. Com design marcante e estética moderna streetwear, essa peça une propósito e estilo em uma composição perfeita para quem carrega sua identidade cristã no dia a dia.\r\n\r\n\r\nConfeccionada em tecido premium de toque macio e caimento oversized, oferece conforto, autenticidade e presença em qualquer ocasião. A estampa transmite uma atmosfera celestial e contemplativa, despertando curiosidade e conexão espiritual através de uma mensagem impactante.\r\n\r\nIdeal para quem busca mais do que moda: uma peça que comunica fé, personalidade e propósito.\r\n\r\n--------------------------------------------------------------------------------\r\n\r\n-> Modelagem oversized\r\n-> Estampa exclusiva com temática bíblica\r\n-> Conforto e estilo para todas as ocasiões\r\n-> Perfeita para compor looks urbanos e cristãos modernos\r\n\r\n--------------------------------------------------------------------------------\r\n\r\n“O que é que os anjos veem quando olham para nós?” — uma pergunta que inspira adoração, entrega e transformação.', NULL, NULL, 0, 1, 1, 0, '2026-05-25 16:50:29', '2026-05-25 19:24:07', 0, NULL),
	(7, '39ba8158-586f-11f1-8c05-089798669242', 'Camisa - Pai Pródigo', 'camisa-pai-prodigo', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Filho Pródigo” representa a história de queda, reencontro e graça que marcou gerações. Inspirada na poderosa parábola bíblica, essa peça carrega a mensagem de que nunca é tarde para voltar para casa e recomeçar.\r\n\r\nCom visual moderno e estética streetwear, a estampa transmite liberdade, arrependimento e amor incondicional — lembrando que existe um Pai esperando de braços abertos. Ideal para quem vive a fé de forma autêntica e quer expressar propósito através do estilo.\r\n\r\nProduzida em tecido premium com modelagem oversized, garante conforto, presença e personalidade em qualquer composição.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa exclusiva inspirada na parábola do Filho Pródigo\r\n✨ Tecido confortável e alta qualidade\r\n✨ Fé, estilo e significado em uma única peça\r\n\r\n“Mesmo distante, o amor do Pai continua chamando você de filho.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:23:35', '2026-05-25 19:24:18', 0, NULL),
	(8, 'ec6640fa-586f-11f1-8c05-089798669242', 'Camisa - Carvalhos de Justiça', 'camisa-carvalhos-de-justica', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Carvalho de Justiça” é inspirada em uma das promessas mais fortes da Bíblia: permanecer firme mesmo após as tempestades. Com a frase “A fim de que se chamem carvalhos de justiça”, a peça transmite força, restauração e identidade para aqueles que permanecem enraizados em Deus.\r\n\r\nSeu design une minimalismo, impacto visual e essência cristã em uma estética streetwear moderna, perfeita para quem deseja vestir propósito sem abrir mão do estilo. Assim como o carvalho simboliza resistência e estabilidade, essa camiseta representa uma fé que permanece firme diante das dificuldades.\r\n\r\nConfeccionada em tecido premium e modelagem oversized, oferece conforto, autenticidade e presença em qualquer ocasião.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em Isaías 61:3\r\n✨ Tecido confortável e acabamento premium\r\n✨ Uma mensagem de força, fé e resiliência\r\n\r\n“Fortes nas raízes, firmes na fé, sustentados pela graça.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:28:35', '2026-05-25 19:28:35', 0, NULL),
	(9, '3ca981c9-5870-11f1-8c05-089798669242', 'Não Apagueis o Espírito', 'nao-apagueis-o-espirito', 'camisas', 129.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Não Apagueis o Espírito” carrega uma mensagem poderosa de sensibilidade, entrega e permanência na presença de Deus. Inspirada em 1 Tessalonicenses 5:19, essa peça representa uma fé viva, intensa e impossível de ser silenciada.\r\n\r\nCom estética streetwear moderna e design impactante, a estampa lembra que o fogo do Espírito deve permanecer aceso em cada detalhe da caminhada cristã. Ideal para quem deseja expressar sua fé com autenticidade, propósito e personalidade.\r\n\r\nProduzida em tecido premium e modelagem oversized, entrega conforto, estilo e significado em uma única peça.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em 1 Tessalonicenses 5:19\r\n✨ Conforto premium para o dia a dia\r\n✨ Uma peça que representa fé, intensidade e presença\r\n\r\n“Queimar por Deus é diferente de apenas conhecer sobre Ele.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:30:50', '2026-05-25 19:30:50', 0, NULL),
	(10, 'a60b099c-5875-11f1-8c05-089798669242', 'Camisa - Frutos do Espírito', 'camisa-frutos-do-espirito', 'camisas', 149.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Frutos do Espírito” representa uma vida transformada pela presença de Deus. Inspirada em Gálatas 5:22-23, a peça carrega a essência de quem escolhe viver guiado pelo amor, paz, bondade, domínio próprio e fé todos os dias.\r\n\r\nCom design moderno e estética streetwear cristã, essa camiseta une propósito e autenticidade em uma mensagem que vai além da moda: refletir Cristo através das atitudes. Uma peça para quem entende que os verdadeiros frutos são visíveis no coração e nas escolhas.\r\n\r\nConfeccionada em tecido premium e modelagem oversized, oferece conforto, estilo e significado em cada detalhe.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em Gálatas 5:22-23\r\n✨ Tecido confortável e acabamento premium\r\n✨ Fé, identidade e estilo em uma única peça\r\n\r\n“Que a sua vida fale mais alto do que as suas palavras.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:09:34', '2026-05-25 20:09:34', 0, NULL),
	(11, '0883383e-5876-11f1-8c05-089798669242', 'Camisa - Discípulo em Meio à Babilônia', 'camisa-discipulo-em-meio-a-babilonia', 'camisas', 159.90, NULL, 'Lançamento', NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Discípulo em Meio à Babilônia” foi criada para quem escolhe permanecer fiel mesmo vivendo em um mundo que tenta afastar princípios, valores e propósito. Inspirada na coragem de homens como Daniel, essa peça representa identidade, resistência espiritual e compromisso com Deus em qualquer ambiente.\r\n\r\nCom uma estética streetwear marcante e mensagem profunda, a estampa transmite a força de quem vive pela fé sem se conformar com a cultura ao redor. Uma peça para discípulos que entendem que é possível permanecer luz, mesmo em meio à Babilônia.\r\n\r\nProduzida em tecido premium e modelagem oversized, entrega conforto, autenticidade e presença em cada detalhe.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa com temática bíblica e urbana\r\n✨ Tecido premium e confortável\r\n✨ Uma mensagem de fé, resistência e identidade\r\n\r\n“Em um mundo que tenta moldar você, permaneça fiel ao Reino.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:12:19', '2026-05-25 20:12:19', 0, NULL),
	(12, '8a247ace-5876-11f1-8c05-089798669242', 'Boné - Isaias 9:6', 'bone-isaias-9-6', 'bones', 99.90, NULL, 'Lançamento', 1, '2026-05-25 00:00:00', NULL, NULL, 'O boné “Isaías 9:6” carrega uma das profecias mais poderosas da Bíblia em um design moderno e cheio de significado. Inspirado no versículo que anuncia o nascimento de Jesus — Maravilhoso Conselheiro, Deus Forte, Pai da Eternidade e Príncipe da Paz — essa peça representa fé, identidade e propósito em cada detalhe.\r\n\r\nCom estilo versátil e acabamento premium, é perfeito para compor looks urbanos enquanto transmite uma mensagem eterna. Mais do que um acessório, é uma declaração de quem Cristo é.\r\n\r\n✨ Design minimalista e marcante\r\n✨ Referência bíblica de Isaías 9:6\r\n✨ Confortável e ajustável para o dia a dia\r\n✨ Estilo, fé e autenticidade em uma única peça\r\n\r\n“Porque um menino nos nasceu, um filho se nos deu.” ✝️', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:15:57', '2026-05-25 20:15:57', 0, NULL);

-- Copiando estrutura para tabela overgrace.products_img
CREATE TABLE IF NOT EXISTS `products_img` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ordem` int DEFAULT '0',
  `destaque` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.products_img: ~27 rows (aproximadamente)
INSERT INTO `products_img` (`id`, `produto_id`, `imagem`, `ordem`, `destaque`, `created_at`) VALUES
	(31, 6, '8ce980e8b27c1d47dd2b0311c5a10609.jpeg', 0, 1, '2026-05-25 19:24:07'),
	(32, 6, '686826728b677b95daf63c5d1dc1b8f9.jpeg', 0, 0, '2026-05-25 19:24:07'),
	(33, 6, 'c4eda0566ff9a052abd4f3ff6b1eeae4.jpeg', 0, 0, '2026-05-25 19:24:07'),
	(34, 6, 'd2ddb526047cb231854e1b0502918e45.jpeg', 0, 0, '2026-05-25 19:24:07'),
	(35, 6, '4edebfedffdebb5c8bcc8f730e1364d4.jpeg', 0, 0, '2026-05-25 19:24:07'),
	(36, 7, '757c29e5004ef435c418e7ec6328acda.jpeg', 0, 1, '2026-05-25 19:24:18'),
	(37, 7, '84cdab28db347a2e62e1a876901e4a74.jpeg', 0, 0, '2026-05-25 19:24:18'),
	(38, 7, '5f1c84fe2876e0130288d187ad8f1411.jpeg', 0, 0, '2026-05-25 19:24:18'),
	(39, 8, 'a4ee36b76d64d35ac65f54baa6be2256.jpeg', 0, 0, '2026-05-25 19:28:35'),
	(40, 8, '0b27da43786df7fe76e1580d3020ad2b.jpeg', 0, 0, '2026-05-25 19:28:35'),
	(41, 8, '0204f163a1560c945ce48bab18a12847.jpeg', 0, 1, '2026-05-25 19:28:35'),
	(42, 8, '5c2e7c0fb1abb8c1fd0dcf385ada3fba.jpeg', 0, 0, '2026-05-25 19:28:35'),
	(43, 9, 'e9a931ffdb58b12f56312029167b3a6f.jpeg', 0, 0, '2026-05-25 19:30:50'),
	(44, 9, 'e0224398eca494f6fef062c94e01beda.jpeg', 0, 0, '2026-05-25 19:30:50'),
	(45, 9, '595689b05d73927d419dbc43ef8a7adb.jpeg', 0, 0, '2026-05-25 19:30:50'),
	(46, 9, 'bb0a3eefe58ee9a495af28a80aabcb5b.jpeg', 0, 1, '2026-05-25 19:30:50'),
	(47, 10, 'cc317bf3dfca8e5a336fb7d4c8020aed.jpeg', 0, 1, '2026-05-25 20:09:34'),
	(48, 10, '8f443939d702e084360bc4903925531e.jpeg', 0, 0, '2026-05-25 20:09:34'),
	(49, 10, 'fc580c17029e3aaa115ebf36b08a5b56.jpeg', 0, 0, '2026-05-25 20:09:34'),
	(50, 10, '3648681f4c3153ecbc63354a46f2833b.jpeg', 0, 0, '2026-05-25 20:09:34'),
	(51, 11, 'f80d43398ef7034289b0533aae3b2bdd.jpeg', 0, 0, '2026-05-25 20:12:19'),
	(52, 11, 'f4099c0eba5faec23b67b0c9094efd73.jpeg', 0, 1, '2026-05-25 20:12:19'),
	(53, 11, '8175d944736bcb6366164a3a2e955817.jpeg', 0, 0, '2026-05-25 20:12:19'),
	(54, 12, '5f788f3a50c4dc1c279c7148b7a5f904.jpeg', 0, 1, '2026-05-25 20:15:57'),
	(55, 12, '73ab76fae426d562192f66f0776ed821.jpeg', 0, 0, '2026-05-25 20:15:57'),
	(56, 12, '1501780392deb734601b900a7b5ee11a.jpeg', 0, 0, '2026-05-25 20:15:57'),
	(57, 12, '5cba33df9c5d26767953d5b1d5e9a8c8.jpeg', 0, 0, '2026-05-25 20:15:57');

-- Copiando estrutura para tabela overgrace.products_stock
CREATE TABLE IF NOT EXISTS `products_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `tamanho` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `estoque` int NOT NULL DEFAULT '0',
  `estoque_reservado` int DEFAULT '0',
  `minimo` int DEFAULT '0',
  `estoque_inicial` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_produto_tamanho` (`produto_id`,`tamanho`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.products_stock: ~7 rows (aproximadamente)
INSERT INTO `products_stock` (`id`, `produto_id`, `tamanho`, `estoque`, `estoque_reservado`, `minimo`, `estoque_inicial`, `ativo`, `created_at`, `updated_at`) VALUES
	(300, 6, 'G', 10, 0, 5, 10, 1, '2026-05-25 16:50:29', '2026-05-25 16:50:29'),
	(307, 7, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:23:35', '2026-05-25 19:23:35'),
	(310, 8, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:28:35', '2026-05-25 19:28:35'),
	(311, 9, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:30:50', '2026-05-25 19:30:50'),
	(312, 10, 'G', 10, 0, 5, 10, 1, '2026-05-25 20:09:34', '2026-05-25 20:09:34'),
	(313, 11, 'G', 10, 0, 5, 10, 1, '2026-05-25 20:12:19', '2026-05-25 20:12:19'),
	(314, 12, 'Único', 10, 0, 5, 10, 1, '2026-05-25 20:15:57', '2026-05-25 20:15:57');

-- Copiando estrutura para tabela overgrace.products_stock_movements
CREATE TABLE IF NOT EXISTS `products_stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `tamanho` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_movimento` enum('entrada','saida','ajuste','saldo_inicial') COLLATE utf8mb4_general_ci NOT NULL,
  `quantidade` int NOT NULL,
  `pedido_id` int DEFAULT NULL,
  `fornecedor` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lote` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_movimento` datetime NOT NULL,
  `custo_unitario` decimal(10,2) DEFAULT NULL,
  `observacao` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.products_stock_movements: ~7 rows (aproximadamente)
INSERT INTO `products_stock_movements` (`id`, `produto_id`, `tamanho`, `tipo_movimento`, `quantidade`, `pedido_id`, `fornecedor`, `lote`, `data_movimento`, `custo_unitario`, `observacao`, `created_at`) VALUES
	(42, 6, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 13:50:29'),
	(43, 7, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:23:35'),
	(44, 8, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:28:35'),
	(45, 9, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:30:50'),
	(46, 10, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:09:34'),
	(47, 11, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:12:19'),
	(48, 12, 'Único', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:15:57');

-- Copiando estrutura para tabela overgrace.products_tags
CREATE TABLE IF NOT EXISTS `products_tags` (
  `id` int NOT NULL,
  `produto_id` int DEFAULT NULL,
  `tag` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.products_tags: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela overgrace.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` text COLLATE utf8mb4_general_ci,
  `nome` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cargo` enum('superadmin','admin','editor','suporte') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `permissoes` json DEFAULT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ativo',
  `criado_por` int DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela overgrace.users: ~2 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `password`, `nome`, `cargo`, `telefone`, `permissoes`, `status`, `criado_por`, `ultimo_acesso`, `criado_em`, `active`, `role`) VALUES
	(1, 'marcosadmleandro@gmail.com', '$2y$10$5FnxEP4Zn.M0XNMci0f52eNXpCpXhSmStWCT8B6Pnok5Z2jjBQwxq', 'Marcos', 'superadmin', NULL, '["dashboard", "pedidos", "produtos", "clientes", "estoque", "financeiro", "configuracoes"]', 'ativo', NULL, NULL, '2026-05-25 20:15:55', 1, 'admin'),
	(2, 'admin@overgrace.com.br', '$2a$12$MmdZiyxRnZig7LKt9M4Ii.UObD1icaNkOZH6/1ckEkv556N680meO', 'Administrador', 'superadmin', '32988880001', '["dashboard", "pedidos", "produtos", "clientes", "estoque", "financeiro", "configuracoes"]', 'ativo', NULL, NULL, '2026-05-25 20:18:25', 1, 'admin'),
	(3, 'rafasilveira.frontend@gmail.com', '$2y$10$CMYGsTlSx1Coyr.vJ1OYp.r8WQX/exqYhnrSoE.z7HszZqmOWVTO2', 'Rafaela', 'admin', NULL, NULL, 'ativo', NULL, NULL, '2026-05-26 16:32:51', 1, 'admin');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
