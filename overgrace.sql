-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/07/2026 às 22:18
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_valor` float DEFAULT NULL,
  `coupon_tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carts`
--

INSERT INTO `carts` (`id`, `client_id`, `session_token`, `status`, `created_at`, `updated_at`, `coupon_id`, `coupon_valor`, `coupon_tipo`) VALUES
(9, NULL, 'bff9339cdd98a1ced3794a270ca97d7e', 'active', '2026-07-09 18:37:16', '2026-07-09 19:13:38', 3, 19.98, 'percentual');

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
(8, 6, 12, 'Único', 2, 99.90, '2026-07-07 16:36:59', '2026-07-07 16:38:56'),
(9, 7, 12, 'Único', 2, 99.90, '2026-07-07 16:54:34', '2026-07-07 16:54:34'),
(10, 8, 12, 'Único', 1, 99.90, '2026-07-09 10:39:05', '2026-07-09 10:39:05'),
(11, 9, 12, 'Único', 1, 99.90, '2026-07-09 18:37:16', '2026-07-09 18:37:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `sobrenome` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clients`
--

INSERT INTO `clients` (`id`, `uuid`, `nome`, `sobrenome`, `email`, `cpf`, `telefone`, `password`, `google_id`, `status`, `created_at`, `updated_at`) VALUES
(4, 'e41745dd-472c-11f1-bd1a-f4b5204d1e7e', 'marcos', 'leandro', 'marcosadmleandro@gmail.com', '022.904.366-66', '(32) 99837-3640', '$2y$10$3QosFHKm0s.DgihnBPinquGCqn8jfpIGb39BStyAIPTsqS8JWn5RC', NULL, 'ativo', '2026-05-03 20:15:57', '2026-05-03 20:15:57'),
(5, '2b7a0288-ad58-4b08-ab92-b7c995d017cb', 'Rafaela', 'Silveira', 'rafaelasilveira1987@gmail.com', '11803473746', '32998416669', '$2y$10$PdzXSBLYSP0v3hgNTpaKxeVKlV0l2Vh1GNGJ4gL3s.HNoU/bgo/Im', NULL, 'ativo', '2026-05-04 19:43:33', '2026-05-04 19:43:33'),
(8, 'bb410dc0-48c1-11f1-80b7-d09466a5d484', 'Marcos Leandro', 'Silva', 'marcosadmleandro@gmail.com', '022.904.366-66', '(32) 99837-3640', '$2y$10$wsUyz7uTPIE12tI2mnlkLecsDP5uuiP2LL24OvFIVEa2/Gs76rKkS', NULL, 'ativo', '2026-05-05 23:33:53', '2026-05-05 23:33:53'),
(10, '197f1686-7952-11f1-9a22-d09466a5d484', 'Marcos Leandro', 'Silva', 'app@nanook.com.br', '022.904.366-66', '(32) 99837-3640', '$2y$10$9q5B1kPMAMxE6.pDgdsWquUi.MTG5uPKjZdfxn5bjzX6a1CTzUMDm', NULL, 'ativo', '2026-07-06 15:48:14', '2026-07-06 15:48:14');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clients_address`
--

CREATE TABLE `clients_address` (
  `id` int(11) NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` enum('entrega','comercial') NOT NULL DEFAULT 'entrega',
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `complemento` varchar(150) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clients_address`
--

INSERT INTO `clients_address` (`id`, `client_id`, `tipo`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `estado`) VALUES
(2, 8, 'entrega', '36884-081', 'Rua Rui Barbosa', '123', 'Barra', 'Cardoso de Melo', 'Muriaé', 'MG'),
(4, 10, 'entrega', '36884-081', 'Rua Rui Barbosa', '143', 'Barra', '101', 'Muriaé', 'MG');

-- --------------------------------------------------------

--
-- Estrutura para tabela `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `cupom` varchar(100) NOT NULL,
  `tipo` enum('percentual','fixo','frete') NOT NULL DEFAULT 'percentual',
  `percentual` decimal(5,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `minimo` decimal(10,2) DEFAULT 0.00,
  `limite` int(11) DEFAULT NULL,
  `validade` datetime DEFAULT NULL,
  `status` enum('ativo','pausado','expirado') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coupons`
--

INSERT INTO `coupons` (`id`, `uuid`, `cupom`, `tipo`, `percentual`, `valor`, `minimo`, `limite`, `validade`, `status`, `created_at`, `updated_at`) VALUES
(2, 'db781bd8-46a6-11f1-bd1a-f4b5204d1e7e', 'CUPOMTESTE10', 'percentual', 10.00, 10.00, 150.00, 2500, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:16:29', '2026-05-03 04:19:34'),
(3, 'f25d574c-46a6-11f1-bd1a-f4b5204d1e7e', 'NOVO20', 'percentual', 20.00, 20.00, 0.00, NULL, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:17:08', '2026-07-09 18:40:26'),
(4, 'fec3c930-46a6-11f1-bd1a-f4b5204d1e7e', 'CUPOMEXPIRADO', 'percentual', 5.00, 5.00, 0.00, NULL, '2027-01-01 00:00:00', 'expirado', '2026-05-03 04:17:28', '2026-05-03 04:19:29'),
(5, '20d06e73-46a7-11f1-bd1a-f4b5204d1e7e', 'TESTE', 'percentual', NULL, 10.00, 500.00, 5000, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:18:26', '2026-05-03 04:19:27'),
(6, '3e955d6a-46a7-11f1-bd1a-f4b5204d1e7e', 'BONES15', 'percentual', NULL, 15.00, 500.00, 5000, '2027-01-01 00:00:00', 'ativo', '2026-05-03 04:19:15', '2026-05-03 15:25:53'),
(7, 'a25c4f37-4704-11f1-bd1a-f4b5204d1e7e', 'CAMISETAS45', 'fixo', NULL, 45.00, 200.00, 5000, '2026-10-01 00:00:00', 'ativo', '2026-05-03 15:27:46', '2026-05-03 15:27:46'),
(8, 'b722eeed-4704-11f1-bd1a-f4b5204d1e7e', 'FRETEGRATISVOVER', 'frete', NULL, 15.00, 0.00, NULL, '2026-06-01 00:00:00', 'ativo', '2026-05-03 15:28:21', '2026-05-03 15:28:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) DEFAULT NULL,
  `status` enum('pending','paid','canceled','refunded') DEFAULT 'pending',
  `subtotal` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `payment_status` enum('pending','approved','rejected','refunded','expired') DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `cart_id`, `status`, `subtotal`, `discount`, `shipping`, `coupon_id`, `payment_status`, `total_amount`, `created_at`, `updated_at`) VALUES
(18, 10, 9, 'pending', 99.90, 19.98, 0.00, 3, 'pending', 79.92, '2026-07-09 19:12:56', '2026-07-09 19:12:56');

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
  `subtotal` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(35, 18, 12, 'Boné - Isaias 9:6', 'Único', 1, 99.90, 99.90, '2026-07-09 19:12:56');

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
  `id` bigint(20) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `gateway` varchar(30) DEFAULT NULL,
  `method` enum('pix','credit_card','debit_card','boleto','bank_transfer','wallet') DEFAULT NULL,
  `status` enum('pending','processing','authorized','paid','failed','expired','cancelled','refunded','partially_refunded') DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` char(3) DEFAULT NULL,
  `installments` int(11) NOT NULL DEFAULT 1,
  `gateway_payment_id` varchar(120) DEFAULT NULL,
  `gateway_customer_id` varchar(120) DEFAULT NULL,
  `gateway_reference` varchar(120) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `qr_code_base64` longtext DEFAULT NULL,
  `pix_copy_paste` text DEFAULT NULL,
  `boleto_url` text DEFAULT NULL,
  `boleto_barcode` varchar(255) DEFAULT NULL,
  `authorization_code` varchar(120) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `payments`
--

INSERT INTO `payments` (`id`, `uuid`, `order_id`, `gateway`, `method`, `status`, `amount`, `fee`, `net_amount`, `currency`, `installments`, `gateway_payment_id`, `gateway_customer_id`, `gateway_reference`, `qr_code`, `qr_code_base64`, `pix_copy_paste`, `boleto_url`, `boleto_barcode`, `authorization_code`, `expires_at`, `paid_at`, `cancelled_at`, `gateway_response`, `created_at`, `updated_at`) VALUES
(3, 'dd9299d0-7bcb-11f1-aa52-d09466a5d484', 18, 'mercadopago', 'pix', 'pending', 79.92, 0.00, 79.92, 'BRL', 1, '1327631130', NULL, NULL, '00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540579.925802BR5917MAReKnYcGsLIBRTLV6009Sao Paulo62230519mpqrinter1327631130630425C0', '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', '00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540579.925802BR5917MAReKnYcGsLIBRTLV6009Sao Paulo62230519mpqrinter1327631130630425C0', NULL, NULL, NULL, NULL, NULL, NULL, '{\"accounts_info\":null,\"acquirer_reconciliation\":[],\"additional_info\":{\"tracking_id\":\"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none\"},\"authorization_code\":null,\"binary_mode\":false,\"brand_id\":null,\"build_version\":\"3.163.0-rc-1\",\"call_for_authorize_id\":null,\"callback_url\":null,\"captured\":true,\"card\":[],\"charges_details\":[{\"accounts\":{\"from\":\"collector\",\"to\":\"mp\"},\"amounts\":{\"original\":0.79,\"refunded\":0},\"client_id\":0,\"date_created\":\"2026-07-09T15:24:52.700-04:00\",\"external_charge_id\":\"01KX45E3HEDJHHYNY277PX96QG\",\"id\":\"1327631130-001\",\"last_updated\":\"2026-07-09T15:24:52.700-04:00\",\"metadata\":{\"reason\":\"\",\"source\":\"proc-svc-charges\",\"source_detail\":\"processing_fee_charge\"},\"name\":\"mercadopago_fee\",\"refund_charges\":[],\"reserve_id\":null,\"type\":\"fee\",\"update_charges\":[]}],\"charges_execution_info\":{\"internal_execution\":{\"date\":\"2026-07-09T15:24:52.669-04:00\",\"execution_id\":\"01KX45E3F6CCMZT89MJ6993AHV\"}},\"collector_id\":606550709,\"corporation_id\":null,\"counter_currency\":null,\"coupon_amount\":0,\"currency_id\":\"BRL\",\"date_approved\":null,\"date_created\":\"2026-07-09T15:24:52.696-04:00\",\"date_last_updated\":\"2026-07-09T15:24:52.696-04:00\",\"date_of_expiration\":\"2026-07-10T15:24:52.344-04:00\",\"deduction_schema\":null,\"description\":\"Pedido #18\",\"differential_pricing_id\":null,\"external_reference\":null,\"fee_details\":[],\"financing_group\":null,\"id\":1327631130,\"installments\":1,\"integrator_id\":null,\"issuer_id\":\"12501\",\"live_mode\":false,\"marketplace_owner\":null,\"merchant_account_id\":null,\"merchant_number\":null,\"metadata\":[],\"money_release_date\":null,\"money_release_schema\":null,\"money_release_status\":\"released\",\"notification_url\":null,\"operation_type\":\"regular_payment\",\"order\":[],\"payer\":{\"email\":null,\"entity_type\":null,\"first_name\":null,\"id\":\"3530089187\",\"identification\":{\"number\":null,\"type\":null},\"last_name\":null,\"phone\":{\"area_code\":null,\"extension\":null,\"number\":null},\"type\":null},\"payment_method\":{\"id\":\"pix\",\"issuer_id\":\"12501\",\"type\":\"bank_transfer\"},\"payment_method_id\":\"pix\",\"payment_type_id\":\"bank_transfer\",\"platform_id\":null,\"point_of_interaction\":{\"application_data\":{\"name\":null,\"operating_system\":null,\"version\":null},\"business_info\":{\"branch\":\"Merchant Services\",\"sub_unit\":\"default\",\"unit\":\"online_payments\"},\"location\":{\"source\":null,\"state_id\":null},\"transaction_data\":{\"bank_info\":{\"collector\":{\"account_alias\":null,\"account_holder_name\":\"MaVXVL KZcdkuM tmlv\",\"account_id\":null,\"long_name\":null,\"transfer_account_id\":null},\"is_same_bank_account_owner\":null,\"origin_bank_id\":null,\"origin_wallet_id\":null,\"payer\":{\"account_holder_name\":null,\"account_id\":null,\"branch\":null,\"external_account_id\":null,\"id\":null,\"identification\":{\"number\":null,\"type\":null},\"long_name\":null}},\"bank_transfer_id\":null,\"e2e_id\":null,\"financial_institution\":null,\"is_end_consumer\":null,\"merchant_category_code\":null,\"qr_code\":\"00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540579.925802BR5917MAReKnYcGsLIBRTLV6009Sao Paulo62230519mpqrinter1327631130630425C0\",\"qr_code_base64\":\"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKnklEQVR42uzdQXLyRhMGYFEsWHIEjsLR8NE4Ckfw0gsX+isuxHS3RhjHfz6U1PNuXE4MevTturqnZxARERERERERERERERERERERERERERERERERERGRfza7cZbz7X8dx+vXz9Pt55TT\\/S8\\/2388jOPtQ2P70vf2JcOw+fr9dP\\/Q9utPpg\\/X0NLS0tLS0tLS0tLS0tL+H7SX8vv5ry8e2wNrvh54KNqQcfz4Uift9Ipvf\\/2yv3340LTpG460tLS0tLS0tLS0tLS0a9a2SjNpr19f\\/HYvV4P+EB+Yv+SmzbVuy\\/b2e655W6H8QUtLS0tLS0tLS0tLS\\/vv0obO6b7UvvtW6y43O3ftVWsBnWrfAy0tLS0tLS0tLS0tLe1\\/Q7tvD2wP+rzVwGFk9jJXp4J50r7Fh3Y7p7S0tLS0tLS0tLS0tLS0\\/4S2TAsP3RbtKRbZl9aqHe+\\/hzOn7ZU7I8dfr7xtH\\/rdbDMtLS0tLS0tLS0tLS3tn9TONxdt2nHRNC38mR40nxb+iNqffckv9izR0tLS0tLS0tLS0tLS\\/jHtUqbOaWff7Xz\\/7W5e87bOaafWHWP79XP4fWhpaWlpaWlpaWlpaWn\\/mPbQbk\\/55haVbbcs7ZarxziHG76ktl+rNgloaWlpaWlpaWlpaWlp16cdYtMzTcF27uLcz8vV7vzt2Ft71CmY5z3TR6c4aWlpaWlpaWlpaWlpaWl\\/ru1s620PqMX1NCW8bQ+Y+r3n26tPr3a+v+Kmew1petXDc9PCtLS0tLS0tLS0tLS0tK\\/Sdi79bA+aHrCZ778NTeL5Kw+3D41FO96mhaebQy\\/x5+6Js6a0tLS0tLS0tLS0tLS0r9VOleZHnNXddGd2T7EJ+nDZUD1zul\\/opB7K8twwt0xLS0tLS0tLS0tLS0u7Zm2qeTft9zQ6W2veXK5Oo7OpcB7jGdNp7nbfO7j68Vyfl5aWlpaWlpaWlpaWlpb2Se2uPfC8UKGfxnFh0PczlfelVVu\\/5Nq29YY+b9qzREtLS0tLS0tLS0tLS\\/uv0KZ9Qce7LhwT3bdy9e3bZvFHW3s0DEP60GlWA9c+76PNRbS0tLS0tLS0tLS0tLSv1Q5tSniM17TsS8079pRhc9Hhvvd2TGdNx\\/jq6cxpp2f7wz1LtLS0tLS0tLS0tLS0tH9aGzYXnWO5Wkdnp2ZnuPRzvv92iOVqXXvU6ZyOtyW6l\\/jv9vHgX5WWlpaWlpaWlpaWlpaW9qfaNui7GxfzFovuBw9oZX6YGh7jmdPpAGtd8XtYuHWGlpaWlpaWlpaWlpaWdiXa7gM36YG1z5uWEIWFu+Xs6bVcHNNpEocp4fS\\/vp0WpqWlpaWlpaWlpaWlpX2NNt8cuvCgPOibOqeXuLo2dVCv3Vfvaoc4Pfxkn5eWlpaWlpaWlpaWlpb2BdoHS4eO91p3szx\\/G7RtiW79kjR\\/O6TOaWm3fr+5iJaWlpaWlpaWlpaWlvaF2nqNZrhFpXVOr+0B+9YxbQcxh\\/iqY7o9pXux56Q+xC8Lm4weTQvT0tLS0tLS0tLS0tLS0v5Im2Z0p6L6uLi5KD9gfv3oR+nv7suB1TFu5d2WO19oaWlpaWlpaWlpaWlp16sdSos2XdeSH1Sva6nHRcv1o9f081Re\\/XQ\\/qPr9jTS0tLS0tLS0tLS0tLS0K9IOs3J1aGdOx4XbVC7lVcPmovTK881F27YGKbRf6\\/WjtLS0tLS0tLS0tLS0tCvVHnq3p4TOadtcFI6LptHZ+uGpc\\/re+5nPnKbPXUovl5aWlpaWlpaWlpaWlpb2F9p8Y2g6Jpr6vcMw27OUtFPFfuxt7U3Twp9p39J437cUyvwjLS0tLS0tLS0tLS0t7Tq1Q6o8b\\/3eTat5O0uHxliuXuYPKn3eadQ4XByT8ln6u993pWlpaWlpaWlpaWlpaWlfpX1Q87aEY6PpgblcbTXvGAvlTbs4Zmw1b3vlzyR5bs8SLS0tLS0tLS0tLS0t7Wu0YflQt8YdyqWf+3nZ2p2\\/nd8cOj41f5varrS0tLS0tLS0tLS0tLS0v9Du5jtzj2Pnks9h\\/sD0qofZmdOQqc\\/buTk0nXq9xCVNtLS0tLS0tLS0tLS0tOvThmnh873bel34s23aYNQtU4+L64\\/CzaFp5e+2jBzT0tLS0tLS0tLS0tLSrl2bcoyDvu+3zmnQ1kHf7ura411bbw4N08IPC2VaWlpaWlpaWlpaWlra9Wq7Jz7D7SnT3O17LJTrA8OHp72351LrVm1ru34kCS0tLS0tLS0tLS0tLe1KteFv59doTkuI6hzupTeHu5uf6jzfC+c6vFs7p0+GlpaWlpaWlpaWlpaWlvZH6W7n7SYV19tvSv\\/9\\/MtO9+28WZvWIJ1paWlpaWlpaWlpaWlp16mt17Wkyz836fjo9KBTebflZnH35tDOK08HVw9P3BxKS0tLS0tLS0tLS0tL+0Jtp+ZtD+o8IF36mY+LTh9OXzLGW1TGNnI8r32H9CW0tLS0tLS0tLS0tLS0a9TuenuDNm117dguQDmV1bXdjUVDLKCXrmIZ4+aiztnT44MKnZaWlpaWlpaWlpaWlpb2R9puq7YV12Fb71Rch2nh7sLdumep9Xm35ZU7y5pGWlpaWlpaWlpaWlpa2tVq632dx97W3vd5ubpc++7auqPzwsUxb\\/cDq+Hg6pOzzbS0tLS0tLS0tLS0tLQv1IbLPlvN+z6EPbih+ZmmhT\\/ne2\\/Ps\\/Zr2FwUPtx0nVf+ZlqYlpaWlpaWlpaWlpaW9lXa6aRnmnqtys4NopeivdyvYJm+7FoK5ms6uPo2Uy71cmlpaWlpaWlpaWlpaWlp\\/542nDk9loHdVCfvW2v2LU4Jh0W7aWp40raDq8O8zxu05cO0tLS0tLS0tLS0tLS0a9XW7urxXp5OuabFu3XA9+GD6vqjKae4sWgoo8bf3hxKS0tLS0tLS0tLS0tL+0ptvbYladPq2vfy+frAdufLZqFwzh\\/+8f5bWlpaWlpaWlpaWlpa2tdq69bZNDpbm57vUblNrzrVuOnD57L26C3+U4SO6TNTwrS0tLS0tLS0tLS0tLSv1nb05\\/KgoYzO1jJ1+ZX3reY9zdqxS3lU89LS0tLS0tLS0tLS0tLS\\/n1tKK7rtPBbadW2xbu7tnzoWD5UtWn90Ri39+5oaWlpaWlpaWlpaWlpV6\\/dzYvOc0\\/9fjsmum81b3f\\/7fH2wO4S3c71o\\/OLYwZaWlpaWlpaWlpaWlraNWsvi53TXOOeZpd+hs1FlzgtvCsHV+v1o2FzUS2cn9t\\/S0tLS0tLS0tLS0tLS\\/tKbTr5WbfPtqbntu2\\/vUR1nb\\/dNWVbpnudX8WSbk955uwpLS0tLS0tLS0tLS0tLe0vtZv5Vt6h9HmnaeFui7Z7cHWTmsXp+tGkfObOF1paWlpaWlpaWlpaWtrVaY\\/3MrWztTcN\\/I7t+tHxPi08pDOnw73vG7SXhVceaGlpaWlpaWlpaWlpadesXZgWDuXquDw1nLTncpB1fg3pNhXUaZluOAVLS0tLS0tLS0tLS0tLu1Ltw81FQduOh25vX7idv+q8+Tl1TK\\/lDGrnw+mVaWlpaWlpaWlpaWlpaWl\\/pxURERERERERERERERERERERERERERERERERERFZdf4XAAD\\/\\/ygVccxOAacCAAAAAElFTkSuQmCC\",\"ticket_url\":\"https:\\/\\/www.mercadopago.com.br\\/sandbox\\/payments\\/1327631130\\/ticket?caller_id=3530089187&hash=c6e4a10c-28aa-4a18-b24a-9e7950aedba6\",\"transaction_id\":null},\"type\":\"OPENPLATFORM\"},\"pos_id\":null,\"processing_mode\":\"aggregator\",\"refunds\":[],\"release_info\":null,\"shipping_amount\":0,\"sponsor_id\":null,\"statement_descriptor\":null,\"status\":\"pending\",\"status_detail\":\"pending_waiting_transfer\",\"store_id\":null,\"tags\":null,\"taxes_amount\":0,\"tenant_context\":\"mp\",\"transaction_amount\":79.92,\"transaction_amount_refunded\":0,\"transaction_details\":{\"acquirer_reference\":null,\"bank_transfer_id\":null,\"external_resource_url\":null,\"financial_institution\":null,\"installment_amount\":0,\"net_received_amount\":0,\"overpaid_amount\":0,\"payable_deferral_period\":null,\"payment_method_reference_id\":null,\"total_paid_amount\":79.92,\"transaction_id\":null}}', '2026-07-09 16:24:53', '2026-07-09 16:24:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) NOT NULL,
  `payment_id` bigint(20) DEFAULT NULL,
  `gateway_transaction_id` varchar(120) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payment_webhooks`
--

CREATE TABLE `payment_webhooks` (
  `id` bigint(20) NOT NULL,
  `gateway` varchar(30) DEFAULT NULL,
  `event_id` varchar(120) DEFAULT NULL,
  `event_type` varchar(120) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `processed` enum('Y','N') DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `desc_slug` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `preco_atual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_antigo` decimal(10,2) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `posicao` int(11) DEFAULT NULL,
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
(6, 'd61b373a-5859-11f1-8c05-089798669242', 'Camisa - O que é que os Anjos Veem?', 'camisa-o-que-e-que-os-anjos-veem', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'Inspirada na eternidade e no mistério da presença divina, a camiseta oversized “O Que É Que os Anjos Veem?” traz uma reflexão profunda sobre fé, adoração e o olhar celestial sobre a humanidade. Com design marcante e estética moderna streetwear, essa peça une propósito e estilo em uma composição perfeita para quem carrega sua identidade cristã no dia a dia.\r\n\r\n\r\nConfeccionada em tecido premium de toque macio e caimento oversized, oferece conforto, autenticidade e presença em qualquer ocasião. A estampa transmite uma atmosfera celestial e contemplativa, despertando curiosidade e conexão espiritual através de uma mensagem impactante.\r\n\r\nIdeal para quem busca mais do que moda: uma peça que comunica fé, personalidade e propósito.\r\n\r\n--------------------------------------------------------------------------------\r\n\r\n-> Modelagem oversized\r\n-> Estampa exclusiva com temática bíblica\r\n-> Conforto e estilo para todas as ocasiões\r\n-> Perfeita para compor looks urbanos e cristãos modernos\r\n\r\n--------------------------------------------------------------------------------\r\n\r\n“O que é que os anjos veem quando olham para nós?” — uma pergunta que inspira adoração, entrega e transformação.', NULL, NULL, 0, 1, 1, 0, '2026-05-25 16:50:29', '2026-05-25 19:24:07', 0, NULL),
(7, '39ba8158-586f-11f1-8c05-089798669242', 'Camisa - Pai Pródigo', 'camisa-pai-prodigo', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Filho Pródigo” representa a história de queda, reencontro e graça que marcou gerações. Inspirada na poderosa parábola bíblica, essa peça carrega a mensagem de que nunca é tarde para voltar para casa e recomeçar.\r\n\r\nCom visual moderno e estética streetwear, a estampa transmite liberdade, arrependimento e amor incondicional — lembrando que existe um Pai esperando de braços abertos. Ideal para quem vive a fé de forma autêntica e quer expressar propósito através do estilo.\r\n\r\nProduzida em tecido premium com modelagem oversized, garante conforto, presença e personalidade em qualquer composição.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa exclusiva inspirada na parábola do Filho Pródigo\r\n✨ Tecido confortável e alta qualidade\r\n✨ Fé, estilo e significado em uma única peça\r\n\r\n“Mesmo distante, o amor do Pai continua chamando você de filho.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:23:35', '2026-05-25 19:24:18', 0, NULL),
(8, 'ec6640fa-586f-11f1-8c05-089798669242', 'Camisa - Carvalhos de Justiça', 'camisa-carvalhos-de-justica', 'camisas', 139.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Carvalho de Justiça” é inspirada em uma das promessas mais fortes da Bíblia: permanecer firme mesmo após as tempestades. Com a frase “A fim de que se chamem carvalhos de justiça”, a peça transmite força, restauração e identidade para aqueles que permanecem enraizados em Deus.\r\n\r\nSeu design une minimalismo, impacto visual e essência cristã em uma estética streetwear moderna, perfeita para quem deseja vestir propósito sem abrir mão do estilo. Assim como o carvalho simboliza resistência e estabilidade, essa camiseta representa uma fé que permanece firme diante das dificuldades.\r\n\r\nConfeccionada em tecido premium e modelagem oversized, oferece conforto, autenticidade e presença em qualquer ocasião.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em Isaías 61:3\r\n✨ Tecido confortável e acabamento premium\r\n✨ Uma mensagem de força, fé e resiliência\r\n\r\n“Fortes nas raízes, firmes na fé, sustentados pela graça.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:28:35', '2026-05-25 19:28:35', 0, NULL),
(9, '3ca981c9-5870-11f1-8c05-089798669242', 'Não Apagueis o Espírito', 'nao-apagueis-o-espirito', 'camisas', 129.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Não Apagueis o Espírito” carrega uma mensagem poderosa de sensibilidade, entrega e permanência na presença de Deus. Inspirada em 1 Tessalonicenses 5:19, essa peça representa uma fé viva, intensa e impossível de ser silenciada.\r\n\r\nCom estética streetwear moderna e design impactante, a estampa lembra que o fogo do Espírito deve permanecer aceso em cada detalhe da caminhada cristã. Ideal para quem deseja expressar sua fé com autenticidade, propósito e personalidade.\r\n\r\nProduzida em tecido premium e modelagem oversized, entrega conforto, estilo e significado em uma única peça.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em 1 Tessalonicenses 5:19\r\n✨ Conforto premium para o dia a dia\r\n✨ Uma peça que representa fé, intensidade e presença\r\n\r\n“Queimar por Deus é diferente de apenas conhecer sobre Ele.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 19:30:50', '2026-05-25 19:30:50', 0, NULL),
(10, 'a60b099c-5875-11f1-8c05-089798669242', 'Camisa - Frutos do Espírito', 'camisa-frutos-do-espirito', 'camisas', 149.90, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Frutos do Espírito” representa uma vida transformada pela presença de Deus. Inspirada em Gálatas 5:22-23, a peça carrega a essência de quem escolhe viver guiado pelo amor, paz, bondade, domínio próprio e fé todos os dias.\r\n\r\nCom design moderno e estética streetwear cristã, essa camiseta une propósito e autenticidade em uma mensagem que vai além da moda: refletir Cristo através das atitudes. Uma peça para quem entende que os verdadeiros frutos são visíveis no coração e nas escolhas.\r\n\r\nConfeccionada em tecido premium e modelagem oversized, oferece conforto, estilo e significado em cada detalhe.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa inspirada em Gálatas 5:22-23\r\n✨ Tecido confortável e acabamento premium\r\n✨ Fé, identidade e estilo em uma única peça\r\n\r\n“Que a sua vida fale mais alto do que as suas palavras.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:09:34', '2026-05-25 20:09:34', 0, NULL),
(11, '0883383e-5876-11f1-8c05-089798669242', 'Camisa - Discípulo em Meio à Babilônia', 'camisa-discipulo-em-meio-a-babilonia', 'camisas', 159.90, NULL, 'Lançamento', NULL, '2026-05-25 00:00:00', NULL, NULL, 'A camiseta oversized “Discípulo em Meio à Babilônia” foi criada para quem escolhe permanecer fiel mesmo vivendo em um mundo que tenta afastar princípios, valores e propósito. Inspirada na coragem de homens como Daniel, essa peça representa identidade, resistência espiritual e compromisso com Deus em qualquer ambiente.\r\n\r\nCom uma estética streetwear marcante e mensagem profunda, a estampa transmite a força de quem vive pela fé sem se conformar com a cultura ao redor. Uma peça para discípulos que entendem que é possível permanecer luz, mesmo em meio à Babilônia.\r\n\r\nProduzida em tecido premium e modelagem oversized, entrega conforto, autenticidade e presença em cada detalhe.\r\n\r\n✨ Modelagem oversized com caimento moderno\r\n✨ Estampa com temática bíblica e urbana\r\n✨ Tecido premium e confortável\r\n✨ Uma mensagem de fé, resistência e identidade\r\n\r\n“Em um mundo que tenta moldar você, permaneça fiel ao Reino.”', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:12:19', '2026-05-25 20:12:19', 0, NULL),
(12, '8a247ace-5876-11f1-8c05-089798669242', 'Boné - Isaias 9:6', 'bone-isaias-9-6', 'bones', 99.90, NULL, 'Lançamento', 1, '2026-05-25 00:00:00', NULL, NULL, 'O boné “Isaías 9:6” carrega uma das profecias mais poderosas da Bíblia em um design moderno e cheio de significado. Inspirado no versículo que anuncia o nascimento de Jesus — Maravilhoso Conselheiro, Deus Forte, Pai da Eternidade e Príncipe da Paz — essa peça representa fé, identidade e propósito em cada detalhe.\r\n\r\nCom estilo versátil e acabamento premium, é perfeito para compor looks urbanos enquanto transmite uma mensagem eterna. Mais do que um acessório, é uma declaração de quem Cristo é.\r\n\r\n✨ Design minimalista e marcante\r\n✨ Referência bíblica de Isaías 9:6\r\n✨ Confortável e ajustável para o dia a dia\r\n✨ Estilo, fé e autenticidade em uma única peça\r\n\r\n“Porque um menino nos nasceu, um filho se nos deu.” ✝️', NULL, NULL, 0, 1, 1, 0, '2026-05-25 20:15:57', '2026-05-25 20:15:57', 0, NULL);

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
(300, 6, 'G', 10, 0, 5, 10, 1, '2026-05-25 16:50:29', '2026-05-25 16:50:29'),
(307, 7, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:23:35', '2026-05-25 19:23:35'),
(310, 8, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:28:35', '2026-05-25 19:28:35'),
(311, 9, 'G', 10, 0, 5, 10, 1, '2026-05-25 19:30:50', '2026-05-25 19:30:50'),
(312, 10, 'G', 10, 0, 5, 10, 1, '2026-05-25 20:09:34', '2026-05-25 20:09:34'),
(313, 11, 'G', 10, 0, 5, 10, 1, '2026-05-25 20:12:19', '2026-05-25 20:12:19'),
(314, 12, 'Único', 10, 0, 5, 10, 1, '2026-05-25 20:15:57', '2026-05-25 20:15:57');

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
(42, 6, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 13:50:29'),
(43, 7, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:23:35'),
(44, 8, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:28:35'),
(45, 9, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 16:30:50'),
(46, 10, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:09:34'),
(47, 11, 'G', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:12:19'),
(48, 12, 'Único', 'saldo_inicial', 10, NULL, NULL, NULL, '2026-05-25 00:00:00', NULL, 'Estoque inicial do produto', '2026-05-25 17:15:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products_tags`
--

CREATE TABLE `products_tags` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `site_content`
--

CREATE TABLE `site_content` (
  `id` int(11) NOT NULL,
  `section_key` varchar(80) NOT NULL,
  `field_key` varchar(80) NOT NULL,
  `field_type` enum('text','textarea','html','image','url') NOT NULL DEFAULT 'text',
  `label` varchar(140) NOT NULL,
  `value` longtext DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `site_content`
--

INSERT INTO `site_content` (`id`, `section_key`, `field_key`, `field_type`, `label`, `value`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'global', 'topbar_text', 'text', 'Texto da faixa superior', 'Frete grátis acima de R$ 299 - Parcele em até 6x', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(2, 'global', 'logo_text', 'text', 'Texto da logo', 'OverGrace', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(3, 'global', 'footer_tagline', 'textarea', 'Texto do rodapé', 'Camisas e bonés para quem importa com o que veste - sem abrir mão do conforto e estilo.', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(4, 'global', 'instagram_link', 'url', 'Link do Instagram', '#', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(5, 'global', 'whatsapp_link', 'url', 'Link do WhatsApp', '#', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(6, 'home_hero', 'eyebrow', 'text', 'Chamada pequena', 'NOVA COLEÇÃO', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(7, 'home_hero', 'title_html', 'html', 'Título principal', 'Descubra o <em>melhor</em> da moda Oversize', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(8, 'home_hero', 'description', 'textarea', 'Descrição', 'Camisas masculinas e femininas que combinam estilo e conforto para todas as ocasiões.', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(9, 'home_hero', 'button_text', 'text', 'Texto do botão', 'Ver Coleção', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(10, 'home_hero', 'button_link', 'url', 'Link do botão', 'lista', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(11, 'home_hero', 'image', 'image', 'Imagem do hero', 'frontend/uploads/site-content/home_hero_image_20260604173713_190eb1f6.jpeg', 60, 1, '2026-06-04 20:21:34', '2026-06-04 20:37:13'),
(12, 'home_hero', 'image_alt', 'text', 'Texto alternativo da imagem', 'Modelo oversize', 70, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(13, 'home_hero', 'badge', 'text', 'Selo da imagem', 'Coleção 2026', 80, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(14, 'about_hero', 'title_html', 'html', 'Título da página Sobre', 'Não vendemos apenas roupas.<br />Vestimos propósito.', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(15, 'about_story', 'image', 'image', 'Imagem da história', 'frontend/uploads/site-content/about_story_image_20260604173956_dc8eabc7.jpeg', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:39:56'),
(16, 'about_story', 'image_alt', 'text', 'Texto alternativo da imagem', 'Nossa história', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(17, 'about_story', 'title', 'text', 'Título da história', 'Nossa história', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(18, 'about_story', 'paragraph_1', 'textarea', 'Primeiro parágrafo', 'A OverGrace nasceu do desejo de unir fé, identidade e estilo em cada detalhe. Criamos peças modernas e minimalistas para uma geração que carrega uma mensagem.', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(19, 'about_story', 'paragraph_2', 'textarea', 'Segundo parágrafo', 'Mais do que moda, queremos inspirar vidas a viverem e anunciarem o Evangelho com autenticidade.', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(20, 'about_values', 'mission_title', 'text', 'Título da missão', 'Missão', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(21, 'about_values', 'mission_text', 'textarea', 'Texto da missão', 'Vestir pessoas com propósito e anunciar Cristo através da moda.', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(22, 'about_values', 'vision_title', 'text', 'Título da visão', 'Visão', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(23, 'about_values', 'vision_text', 'textarea', 'Texto da visão', 'Alcançar uma geração com estilo, identidade e fé.', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(24, 'about_values', 'values_title', 'text', 'Título dos valores', 'Valores', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(25, 'about_values', 'values_text', 'textarea', 'Texto dos valores', 'Excelência, autenticidade, propósito e compromisso com o Reino.', 60, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(26, 'about_message', 'title_html', 'html', 'Título da mensagem', 'Cobertos por Ele,<br />vivemos para anunciar.', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(27, 'about_message', 'subtitle', 'textarea', 'Subtítulo da mensagem', 'Uma marca que carrega propósito em cada peça.', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(28, 'about_cta', 'title', 'text', 'Título da chamada final', 'Vista essa mensagem', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(29, 'about_cta', 'text', 'textarea', 'Texto da chamada final', 'Conheça nossas coleções e faça parte desse movimento.', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(30, 'about_cta', 'button_text', 'text', 'Texto do botão', 'Comprar agora', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(31, 'about_cta', 'button_link', 'url', 'Link do botão', 'lista', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(32, 'collections_hero', 'image', 'image', 'Imagem do hero de coleções', 'frontend/pages/assets/img5.png', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(33, 'collections_hero', 'image_alt', 'text', 'Texto alternativo da imagem', 'Coleção OverGrace', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(34, 'collections_hero', 'eyebrow', 'text', 'Chamada pequena', 'DROP 02 • WINTER COLLECTION', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(35, 'collections_hero', 'title_html', 'html', 'Título do hero', 'Estilo que permanece.<br>Presença que marca.', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(36, 'collections_hero', 'description', 'textarea', 'Descrição do hero', 'Peças minimalistas desenvolvidas para quem entende que vestir também comunica identidade.', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(37, 'collections_hero', 'button_text', 'text', 'Texto do botão', 'Explorar coleção', 60, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(38, 'collections_hero', 'button_link', 'url', 'Link do botão', '#lista', 70, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(39, 'collections_manifesto', 'image', 'image', 'Imagem do manifesto', 'frontend/pages/assets/img1.png', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(40, 'collections_manifesto', 'image_alt', 'text', 'Texto alternativo da imagem', 'Sobre a marca', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(41, 'collections_manifesto', 'title', 'text', 'Título do manifesto', 'Mais que roupas.', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(42, 'collections_manifesto', 'paragraph_1', 'textarea', 'Primeiro parágrafo', 'A OverGrace nasceu para vestir quem carrega uma mensagem. Unimos moda, propósito e excelência para criar peças minimalistas, modernas e cheias de significado.', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(43, 'collections_manifesto', 'paragraph_2', 'textarea', 'Segundo parágrafo', 'Nosso desejo é inspirar uma geração a viver o Evangelho em cada detalhe.', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(44, 'collections_drops', 'kicker', 'text', 'Chamada da seção drops', 'OVERGRACE DROPS', 10, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(45, 'collections_drops', 'title_html', 'html', 'Título da seção drops', 'Mais que catálogo. <span>Conceito.</span>', 20, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(46, 'collections_drops', 'description', 'textarea', 'Descrição da seção drops', 'Coleções desenvolvidas para transmitir identidade, estética e propósito.', 30, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(47, 'collections_drops', 'drop_1_image', 'image', 'Imagem do Drop 01', 'frontend/pages/assets/img2.png', 40, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(48, 'collections_drops', 'drop_1_label', 'text', 'Etiqueta do Drop 01', 'DROP 01', 50, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(49, 'collections_drops', 'drop_1_title', 'text', 'Título do Drop 01', 'Essential Lines', 60, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(50, 'collections_drops', 'drop_2_image', 'image', 'Imagem do Drop 02', 'frontend/pages/assets/img5.png', 70, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(51, 'collections_drops', 'drop_2_label', 'text', 'Etiqueta do Drop 02', 'DROP 02', 80, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(52, 'collections_drops', 'drop_2_title', 'text', 'Título do Drop 02', 'Winter Layers', 90, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(53, 'collections_drops', 'drop_3_image', 'image', 'Imagem do Drop 03', 'frontend/pages/assets/img6.png', 100, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(54, 'collections_drops', 'drop_3_label', 'text', 'Etiqueta do Drop 03', 'DROP 03', 110, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(55, 'collections_drops', 'drop_3_title', 'text', 'Título do Drop 03', 'Street Uniform', 120, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(56, 'collections_drops', 'drop_3_description', 'textarea', 'Descrição do Drop 03', 'Modelagens amplas, tons neutros e estética minimalista.', 130, 1, '2026-06-04 20:21:34', '2026-06-04 20:21:34'),
(57, 'shop_coming', 'title', 'text', 'Título da seção', 'Novidades em breve', 10, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(58, 'shop_coming', 'subtitle', 'textarea', 'Subtítulo da seção', 'Estamos preparando peças exclusivas para a próxima coleção.', 20, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(59, 'shop_coming', 'card_1_image', 'image', 'Imagem do primeiro card', 'frontend/pages/assets/img5.png', 30, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(60, 'shop_coming', 'card_1_alt', 'text', 'Texto alternativo do primeiro card', 'Nova coleção inverno', 40, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(61, 'shop_coming', 'card_1_text', 'text', 'Texto do primeiro card', 'Nova coleção inverno', 50, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(62, 'shop_coming', 'card_2_image', 'image', 'Imagem do segundo card', 'frontend/pages/assets/img6.png', 60, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(63, 'shop_coming', 'card_2_alt', 'text', 'Texto alternativo do segundo card', 'Novos acessórios', 70, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(64, 'shop_coming', 'card_2_text', 'text', 'Texto do segundo card', 'Novos acessórios', 80, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(65, 'shop_coming', 'cta_text', 'text', 'Texto da chamada', 'Quer ser avisado primeiro?', 90, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(66, 'shop_coming', 'button_text', 'text', 'Texto do botão', 'Receber novidades', 100, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53'),
(67, 'shop_coming', 'button_link', 'url', 'Link do botão', '#', 110, 1, '2026-06-04 23:16:53', '2026-06-04 23:16:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(500) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `nome` varchar(120) DEFAULT NULL,
  `cargo` enum('superadmin','admin','editor','suporte') DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `permissoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissoes`)),
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `criado_por` int(11) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `role` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nome`, `cargo`, `telefone`, `permissoes`, `status`, `criado_por`, `ultimo_acesso`, `criado_em`, `active`, `role`) VALUES
(1, 'marcosadmleandro@gmail.com', '$2y$10$5FnxEP4Zn.M0XNMci0f52eNXpCpXhSmStWCT8B6Pnok5Z2jjBQwxq', 'Marcos', 'superadmin', NULL, '[\"dashboard\", \"pedidos\", \"produtos\", \"clientes\", \"estoque\", \"financeiro\", \"configuracoes\"]', 'ativo', NULL, NULL, '2026-05-25 20:15:55', 1, 'admin'),
(2, 'admin@overgrace.com.br', '$2a$12$MmdZiyxRnZig7LKt9M4Ii.UObD1icaNkOZH6/1ckEkv556N680meO', 'Administrador', 'superadmin', '32988880001', '[\"dashboard\", \"pedidos\", \"produtos\", \"clientes\", \"estoque\", \"financeiro\", \"configuracoes\"]', 'ativo', NULL, NULL, '2026-05-25 20:18:25', 1, 'admin'),
(3, 'rafasilveira.frontend@gmail.com', '$2y$10$CMYGsTlSx1Coyr.vJ1OYp.r8WQX/exqYhnrSoE.z7HszZqmOWVTO2', 'Rafaela', 'admin', NULL, NULL, 'ativo', NULL, NULL, '2026-05-26 16:32:51', 1, 'admin');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clients_address`
--
ALTER TABLE `clients_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_adress_client` (`client_id`);

--
-- Índices de tabela `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_client_prder` (`client_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_order` (`order_id`);

--
-- Índices de tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_payment` (`order_id`);

--
-- Índices de tabela `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaction_payment` (`payment_id`);

--
-- Índices de tabela `payment_webhooks`
--
ALTER TABLE `payment_webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Índices de tabela `products_img`
--
ALTER TABLE `products_img`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `products_stock`
--
ALTER TABLE `products_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_produto_tamanho` (`produto_id`,`tamanho`);

--
-- Índices de tabela `products_stock_movements`
--
ALTER TABLE `products_stock_movements`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_site_content_section_field` (`section_key`,`field_key`),
  ADD KEY `idx_site_content_section` (`section_key`),
  ADD KEY `idx_site_content_active` (`active`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `clients_address`
--
ALTER TABLE `clients_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payment_webhooks`
--
ALTER TABLE `payment_webhooks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `products_img`
--
ALTER TABLE `products_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de tabela `products_stock`
--
ALTER TABLE `products_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT de tabela `products_stock_movements`
--
ALTER TABLE `products_stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de tabela `site_content`
--
ALTER TABLE `site_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clients_address`
--
ALTER TABLE `clients_address`
  ADD CONSTRAINT `fk_adress_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_client_prder` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_order_payment` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_transaction_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
