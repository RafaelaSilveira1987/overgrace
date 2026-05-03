-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/05/2026 às 22:14
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `overgrace`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_token` varchar(100) NOT NULL,
  `status` enum('active','converted','abandoned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carts`
--

INSERT INTO `carts` (`id`, `client_id`, `session_token`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, '300b9aa2b3f57a06ee92474636089bd6', 'active', '2026-05-02 16:04:40', '2026-05-02 16:04:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `size`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(5, 2, 23, 'Único', 2, 325.50, '2026-05-02 19:50:16', '2026-05-02 20:11:19'),
(8, 2, 15, 'P', 2, 50.00, '2026-05-02 19:53:38', '2026-05-02 20:11:35'),
(9, 2, 14, 'Único', 11, 15.00, '2026-05-02 20:12:25', '2026-05-02 20:12:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','paid','canceled','refunded') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `method` enum('pix','credit_card') NOT NULL,
  `status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payment_credit_card`
--

CREATE TABLE `payment_credit_card` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `last4` varchar(4) DEFAULT NULL,
  `installments` int(11) DEFAULT NULL,
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payment_pix`
--

CREATE TABLE `payment_pix` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `pix_code` text DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `desc_slug` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `preco_atual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_antigo` decimal(10,2) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `posicao` int(11) DEFAULT 0,
  `inicio_exibicao` datetime DEFAULT NULL,
  `fim_exibicao` datetime DEFAULT NULL,
  `tamanhos` varchar(500) DEFAULT NULL,
  `descricao_completa` text DEFAULT NULL,
  `peso` decimal(10,3) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `estoque_inicial` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `exibir_nome` tinyint(1) DEFAULT 1,
  `permitir_compra_sem_estoque` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `uuid`, `descricao`, `desc_slug`, `categoria`, `preco_atual`, `preco_antigo`, `badge`, `posicao`, `inicio_exibicao`, `fim_exibicao`, `tamanhos`, `descricao_completa`, `peso`, `tags`, `estoque_inicial`, `ativo`, `exibir_nome`, `permitir_compra_sem_estoque`, `created_at`, `updated_at`, `deleted`, `deleted_at`) VALUES
(4, '9465cd48-40bd-11f1-9993-d09466a5d484', 'teste boné', 'teste-bone', 'camisas', 125.00, 0.00, '', 0, '2026-04-25 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-25 15:44:00', '2026-04-28 00:36:17', 0, NULL),
(5, '39018986-40bf-11f1-b37b-f4b5204d1e7e', 'Camisa oversize', 'camisa-oversize', 'camisas', 89.90, NULL, NULL, NULL, '2026-04-25 00:00:00', NULL, NULL, '', NULL, NULL, 0, 0, 1, 0, '2026-04-25 15:55:46', '2026-04-28 00:36:17', 0, NULL),
(6, 'f64a8789-40c0-11f1-b37b-f4b5204d1e7e', 'teste 3', 'teste-3', 'kits', 20.00, NULL, NULL, NULL, '2026-04-25 00:00:00', NULL, NULL, '', NULL, NULL, 0, 1, 1, 0, '2026-04-25 16:08:13', '2026-04-28 00:36:17', 0, NULL),
(7, 'd7c0a991-40c4-11f1-b37b-f4b5204d1e7e', 'teste vários tamanhos', 'teste-varios-tamanhos', 'camisas', 125.00, 139.00, '-20%', NULL, '2026-04-25 00:00:00', NULL, 'Array', '', NULL, NULL, 0, 1, 1, 0, '2026-04-25 16:36:00', '2026-04-28 00:36:17', 0, NULL),
(8, '3936fbca-40c5-11f1-b37b-f4b5204d1e7e', 'teste 4', 'teste-4', 'camisas', 125.50, 0.00, '', 0, '2026-04-25 00:00:00', '0000-00-00 00:00:00', 'Array', '', 0.000, NULL, 0, 1, 1, 0, '2026-04-25 16:38:43', '2026-04-28 00:36:17', 0, NULL),
(9, 'f3f74b57-40c5-11f1-b37b-f4b5204d1e7e', 'teste 5', 'teste-5', 'camisas', 125.20, 300.00, '-20%', 1, '2026-04-25 00:00:00', '0000-00-00 00:00:00', 'Array', '', 0.000, NULL, 0, 1, 1, 0, '2026-04-25 16:43:57', '2026-04-28 00:36:17', 0, NULL),
(10, 'f859a921-40c6-11f1-b37b-f4b5204d1e7e', 'teste produtos com tag', 'teste-produtos-com-tag', 'camisas', 120.00, 0.00, '', 0, '2026-04-25 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-25 16:51:13', '2026-04-28 00:36:17', 0, NULL),
(11, 'bf808ca7-40c7-11f1-b37b-f4b5204d1e7e', 'Boné vermelho', 'bone-vermelho', 'camisas', 125.50, 149.00, '-20%', 0, '2026-04-26 00:00:00', '0000-00-00 00:00:00', NULL, 'teste descrição item', 25.000, NULL, 0, 1, 1, 0, '2026-04-25 16:56:48', '2026-04-28 00:36:17', 0, NULL),
(13, '2db7e6be-4196-11f1-9a4a-f4b5204d1e7e', 'Camisa gola povo alpha co', 'camisa-gola-povo-alpha-co', 'camisas', 155.00, 189.90, '-20%', 1, '2026-04-26 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-26 17:34:29', '2026-04-28 00:36:17', 0, NULL),
(14, '1c642e87-4197-11f1-9a4a-f4b5204d1e7e', 'teste tag teste', 'teste-tag-teste', 'camisas', 15.00, 0.00, '', 0, '2026-04-26 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-26 17:41:09', '2026-04-28 00:36:17', 0, NULL),
(15, '2b850b23-4197-11f1-9a4a-f4b5204d1e7e', 'Camisa gola povo alpha co amarela', 'camisa-gola-povo-alpha-co-amarela', 'camisas', 50.00, 0.00, '', 0, '2026-04-26 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 35, 1, 1, 0, '2026-04-26 17:41:35', '2026-04-28 00:36:17', 0, NULL),
(16, 'eeaa495d-41a3-11f1-9a4a-f4b5204d1e7e', 'produto novo', 'produto-novo', 'camisas', 5.25, 10.25, 'Novo', 1, '2026-04-26 00:00:00', '0000-00-00 00:00:00', NULL, 'dsfsdfdsf\r\n\r\nsdfsdfdsfsdfsd\r\n\r\nsdfsdfdsfsdfdsfdsfdsf', 0.000, NULL, 12, 1, 1, 0, '2026-04-26 19:12:56', '2026-04-28 00:36:17', 0, NULL),
(17, 'fc2bdc75-4223-11f1-b1c7-d09466a5d484', 'Novo produto', 'novo-produto', 'camisas', 55.25, 79.90, '-20%', 0, '2026-04-27 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-27 10:29:34', '2026-04-28 00:36:17', 0, NULL),
(20, '2629adeb-4296-11f1-a581-f4b5204d1e7e', 'Tenis DC classic', 'tenis-dc-classic', 'kits', 325.50, 510.00, '-20%', 0, '2026-04-27 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-28 00:06:47', '2026-04-28 00:36:17', 0, NULL),
(22, 'debdc512-4296-11f1-a581-f4b5204d1e7e', 'Tenis DC classic 2', 'tenis-dc-classic-2', 'camisas', 325.11, 450.00, '-20%', 0, '2026-04-27 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-28 00:11:57', '2026-04-28 00:36:17', 0, NULL),
(23, '57d4abaa-4297-11f1-a581-f4b5204d1e7e', 'Tenis DC classic 3', 'tenis-dc-classic-3', 'camisas', 325.50, 450.00, '-20%', 1, '2026-04-27 00:00:00', '0000-00-00 00:00:00', NULL, '', 0.000, NULL, 0, 1, 1, 0, '2026-04-28 00:15:20', '2026-04-28 00:15:20', 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `products_img`
--

CREATE TABLE `products_img` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `destaque` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products_img`
--

INSERT INTO `products_img` (`id`, `produto_id`, `imagem`, `ordem`, `destaque`, `created_at`) VALUES
(34, 10, '41f243b3783fa602f303ee686b2074fe.png', 0, 1, '2026-04-26 17:05:12'),
(36, 9, '9efce1442cdd871f22d93b09b519a37c.png', 0, 1, '2026-04-26 17:16:47'),
(37, 8, '6e7022bbf0d5502fe3536fe50775d5aa.png', 0, 1, '2026-04-26 17:19:14'),
(39, 11, '660937a56430678879817edbf696de7b.jpg', 0, 1, '2026-04-26 17:24:43'),
(42, 13, 'ae73960d398c8d20755d4e9d6df79202.png', 0, 1, '2026-04-26 17:40:45'),
(66, 16, 'b2be3cf56e8351c963daed1c2c66adf4.jpg', 0, 1, '2026-04-26 19:58:21'),
(67, 16, '6125146c42ad1e1a138bde465bdebb19.png', 0, 0, '2026-04-26 19:58:21'),
(79, 14, 'c770da862af2f797cfe43b23df2d078d.jpg', 0, 1, '2026-04-26 21:31:14'),
(81, 4, 'afff147a83eb9fce3a02d64dbb0fac2f.jpg', 0, 1, '2026-04-26 21:34:18'),
(82, 15, 'c3823569f4a398191673da8c4be9f25a.jpg', 0, 1, '2026-04-27 10:24:49'),
(86, 17, 'e6aae2578932beda01a175b9fe7c585b.jpeg', 0, 1, '2026-04-27 18:38:32'),
(89, 20, '1c68c006f0b219c4fd29613e2427c37d.png', 0, 1, '2026-04-28 00:06:47'),
(99, 23, '9022e86fa69a0d542937139a591e3713.png', 0, 1, '2026-04-28 00:32:06'),
(100, 22, '80672028ce4312ba7b23a0924b597015.png', 0, 1, '2026-04-28 00:57:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products_stock`
--

CREATE TABLE `products_stock` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tamanho` varchar(20) NOT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `estoque_reservado` int(11) DEFAULT 0,
  `minimo` int(11) DEFAULT 0,
  `estoque_inicial` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products_stock`
--

INSERT INTO `products_stock` (`id`, `produto_id`, `tamanho`, `estoque`, `estoque_reservado`, `minimo`, `estoque_inicial`, `ativo`, `created_at`, `updated_at`) VALUES
(2, 5, 'GG', 0, 0, 0, 0, 1, '2026-04-25 15:55:46', '2026-04-25 15:55:46'),
(3, 6, 'Único', 0, 0, 0, 0, 1, '2026-04-25 16:08:13', '2026-04-25 16:08:13'),
(4, 7, 'P', 0, 0, 0, 0, 1, '2026-04-25 16:36:00', '2026-04-25 16:36:00'),
(5, 7, 'M', 0, 0, 0, 0, 1, '2026-04-25 16:36:00', '2026-04-25 16:36:00'),
(6, 7, 'G', 0, 0, 0, 0, 1, '2026-04-25 16:36:00', '2026-04-25 16:36:00'),
(7, 7, 'GG', 0, 0, 0, 0, 1, '2026-04-25 16:36:00', '2026-04-25 16:36:00'),
(107, 10, 'P', 0, 0, 0, 0, 1, '2026-04-26 17:05:12', '2026-04-26 17:05:12'),
(108, 10, 'M', 0, 0, 0, 0, 1, '2026-04-26 17:05:12', '2026-04-26 17:05:12'),
(109, 10, 'G', 0, 0, 0, 0, 1, '2026-04-26 17:05:12', '2026-04-26 17:05:12'),
(110, 10, 'GG', 0, 0, 0, 0, 1, '2026-04-26 17:05:12', '2026-04-26 17:05:12'),
(112, 9, 'Único', 0, 0, 0, 0, 1, '2026-04-26 17:16:47', '2026-04-26 17:16:47'),
(113, 8, 'Único', 0, 0, 0, 0, 1, '2026-04-26 17:19:14', '2026-04-26 17:19:14'),
(117, 11, 'P', 0, 0, 0, 0, 1, '2026-04-26 17:24:43', '2026-04-26 17:24:43'),
(118, 11, 'M', 0, 0, 0, 0, 1, '2026-04-26 17:24:43', '2026-04-26 17:24:43'),
(119, 11, 'G', 0, 0, 0, 0, 1, '2026-04-26 17:24:43', '2026-04-26 17:24:43'),
(124, 13, 'P', 0, 0, 0, 0, 1, '2026-04-26 17:40:45', '2026-04-26 17:40:45'),
(125, 13, 'M', 0, 0, 0, 0, 1, '2026-04-26 17:40:45', '2026-04-26 17:40:45'),
(126, 13, 'G', 0, 0, 0, 0, 1, '2026-04-26 17:40:45', '2026-04-26 17:40:45'),
(127, 13, 'GG', 0, 0, 0, 0, 1, '2026-04-26 17:40:45', '2026-04-26 17:40:45'),
(161, 16, 'Único', 12, 0, 0, 0, 1, '2026-04-26 19:58:21', '2026-04-26 19:58:21'),
(191, 14, 'Único', 0, 0, 25, 25, 1, '2026-04-26 21:31:14', '2026-04-26 21:31:14'),
(195, 4, 'GG', 0, 0, 0, 0, 1, '2026-04-26 21:34:18', '2026-04-26 21:34:18'),
(196, 15, 'P', 10, 0, 35, 15, 1, '2026-04-27 10:24:49', '2026-04-27 23:38:54'),
(197, 15, 'M', 14, 0, 10, 15, 1, '2026-04-27 10:24:49', '2026-04-27 23:43:28'),
(198, 15, 'G', 72, 0, 10, 15, 1, '2026-04-27 10:24:49', '2026-04-27 23:33:41'),
(205, 17, 'M', 100, 0, 15, 20, 1, '2026-04-27 18:38:32', '2026-04-28 00:57:34'),
(206, 17, 'G', 30, 0, 15, 20, 1, '2026-04-27 18:38:32', '2026-04-27 18:40:04'),
(207, 20, 'Único', 0, 0, 10, 10, 1, '2026-04-28 00:06:47', '2026-04-28 00:06:47'),
(208, 22, 'Único', 15, 0, 10, 15, 1, '2026-04-28 00:11:57', '2026-04-28 00:57:53'),
(209, 23, 'Único', 90, 0, 10, 10, 1, '2026-04-28 00:15:20', '2026-04-28 00:32:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products_stock_movements`
--

CREATE TABLE `products_stock_movements` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tamanho` varchar(10) NOT NULL,
  `tipo_movimento` enum('entrada','saida','ajuste','saldo_inicial') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `fornecedor` varchar(255) DEFAULT NULL,
  `lote` varchar(100) DEFAULT NULL,
  `data_movimento` datetime NOT NULL,
  `custo_unitario` decimal(10,2) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products_stock_movements`
--

INSERT INTO `products_stock_movements` (`id`, `produto_id`, `tamanho`, `tipo_movimento`, `quantidade`, `pedido_id`, `fornecedor`, `lote`, `data_movimento`, `custo_unitario`, `observacao`, `created_at`) VALUES
(13, 17, 'G', 'entrada', 30, NULL, '', '', '2026-04-27 00:00:00', 0.00, '', '2026-04-27 15:40:04'),
(14, 17, 'M', 'entrada', 30, NULL, '', '', '2026-04-27 00:00:00', 0.00, '', '2026-04-27 15:40:04'),
(15, 15, 'G', 'entrada', 72, NULL, '', '', '2026-04-27 00:00:00', 0.00, '', '2026-04-27 20:33:41'),
(16, 15, 'P', 'entrada', 10, NULL, '', '', '2026-04-27 00:00:00', 0.00, '', '2026-04-27 20:38:54'),
(17, 15, 'M', 'entrada', 14, NULL, '', '', '2026-04-27 00:00:00', 0.00, '', '2026-04-27 20:43:28'),
(18, 20, 'Único', '', 10, NULL, NULL, NULL, '2026-04-27 00:00:00', NULL, 'Estoque inicial do produto', '2026-04-27 21:06:47'),
(19, 22, 'Único', 'saldo_inicial', 15, NULL, NULL, NULL, '2026-04-27 00:00:00', NULL, 'Estoque inicial do produto', '2026-04-27 21:11:57'),
(20, 23, 'Único', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-04-27 00:00:00', NULL, 'Estoque inicial do produto', '2026-04-27 21:15:20'),
(22, 23, 'Único', 'entrada', 50, NULL, '', '', '2026-04-28 00:00:00', 0.00, '', '2026-04-27 21:31:10'),
(23, 23, 'Único', 'entrada', 30, NULL, 'DC Calçados', '123', '2026-04-28 00:00:00', 25.00, '', '2026-04-27 21:32:52'),
(24, 17, 'M', 'entrada', 70, NULL, '', '', '2026-04-28 00:00:00', 0.00, '', '2026-04-27 21:57:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products_tags`
--

CREATE TABLE `products_tags` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products_tags`
--

INSERT INTO `products_tags` (`id`, `produto_id`, `tag`) VALUES
(69, 11, 'G'),
(70, 11, 'GG'),
(75, 13, 'P'),
(76, 13, 'M'),
(77, 13, 'G'),
(78, 13, 'GG'),
(112, 16, 'Único'),
(140, 14, 'Único'),
(144, 15, 'GG'),
(145, 15, 'GGG'),
(146, 15, 'Único'),
(153, 17, 'Array'),
(154, 17, 'Array');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(500) DEFAULT NULL,
  `password` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(1, 'marcosadmleandro@gmail.com', '$2y$10$5FnxEP4Zn.M0XNMci0f52eNXpCpXhSmStWCT8B6Pnok5Z2jjBQwxq');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_token`),
  ADD KEY `idx_client` (`client_id`);

--
-- Índices de tabela `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_item` (`cart_id`,`product_id`,`size`);

--
-- Índices de tabela `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_client` (`client_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`);

--
-- Índices de tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_order` (`order_id`);

--
-- Índices de tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_order` (`order_id`);

--
-- Índices de tabela `payment_credit_card`
--
ALTER TABLE `payment_credit_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_card_payment` (`payment_id`);

--
-- Índices de tabela `payment_pix`
--
ALTER TABLE `payment_pix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pix_payment` (`payment_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `desc_slug` (`desc_slug`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_slug` (`desc_slug`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_posicao` (`posicao`);

--
-- Índices de tabela `products_img`
--
ALTER TABLE `products_img`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `products_stock`
--
ALTER TABLE `products_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_produto_tamanho` (`produto_id`,`tamanho`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_tamanho` (`tamanho`);

--
-- Índices de tabela `products_stock_movements`
--
ALTER TABLE `products_stock_movements`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `products_tags`
--
ALTER TABLE `products_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payment_credit_card`
--
ALTER TABLE `payment_credit_card`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payment_pix`
--
ALTER TABLE `payment_pix`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `products_img`
--
ALTER TABLE `products_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de tabela `products_stock`
--
ALTER TABLE `products_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT de tabela `products_stock_movements`
--
ALTER TABLE `products_stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `products_tags`
--
ALTER TABLE `products_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `payment_credit_card`
--
ALTER TABLE `payment_credit_card`
  ADD CONSTRAINT `fk_card_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `payment_pix`
--
ALTER TABLE `payment_pix`
  ADD CONSTRAINT `fk_pix_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `products_img`
--
ALTER TABLE `products_img`
  ADD CONSTRAINT `fk_produto_imagens_produto` FOREIGN KEY (`produto_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `products_stock`
--
ALTER TABLE `products_stock`
  ADD CONSTRAINT `fk_produto_estoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `products_tags`
--
ALTER TABLE `products_tags`
  ADD CONSTRAINT `products_tags_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
