-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5332
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for wallet
CREATE DATABASE IF NOT EXISTS `friwallet` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `friwallet`;

-- Dumping structure for table wallet.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Super Admin','Administrator','Supervisor') COLLATE utf8mb4_unicode_ci DEFAULT 'Administrator',
  `google_auth_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','delete') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table wallet.admins: ~3 rows (approximately)
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` (`id`, `name`, `username`, `role`, `google_auth_code`, `email`, `password`, `remember_token`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Pentadbir Sistem', 'admin', 'Administrator', 'OGSHKB34JDNZY5IP', 'admin@pinkexc.com', '$2y$10$eeJ9CGdH5yKNP60oDTqbE.ZWAW/yNrx7PeUILx8o8tGGFzRvi2rDu', NULL, 'active', NULL, '2019-07-25 17:06:20'),
	(2, 'fatonah', 'fatonah', 'Super Admin', NULL, 'fatonah83@yahoo.com.my', '$2y$10$vSVg43QScmX5zP.c1EEl8eKfBhF59b/o.lrDqzrBmAt7A0pgBdU2G', NULL, 'active', '2019-07-23 15:34:16', '2019-07-23 15:47:46'),
	(3, 'Pentadbir Sistem4', 'fazrilafiq', 'Supervisor', 'GFF74LID44MBW7HQ', 'fath83@yahoo.com.my', '$2y$10$jpCrwxdI/qen1FM5ZDhkoOgvdx1SxF5eET2edY1rjaAPys0cP2zcq', NULL, 'delete', '2019-07-23 15:53:45', '2019-07-23 15:59:50');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;

-- Dumping structure for table wallet.app_version
CREATE TABLE IF NOT EXISTS `app_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL,
  `ios_version` varchar(50) NOT NULL,
  `ios_version2` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.app_version: ~0 rows (approximately)
/*!40000 ALTER TABLE `app_version` DISABLE KEYS */;
INSERT INTO `app_version` (`id`, `version`, `ios_version`, `ios_version2`, `created_at`, `updated_at`) VALUES
	(1, '1', '1', '1', '2018-12-06 16:47:59', '2019-08-05 08:54:16');
/*!40000 ALTER TABLE `app_version` ENABLE KEYS */;

-- Dumping structure for table wallet.currency
CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(100) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `symbol` varchar(100) DEFAULT NULL,
  `ccode` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.currency: 31 rows
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` (`id`, `country`, `currency`, `code`, `symbol`, `ccode`) VALUES
	(6, 'Australia', 'Dollars', 'AUD', '$', '61'),
	(18, 'Brazil', 'Reais', 'BRL', 'R$', '55'),
	(22, 'Canada', 'Dollars', 'CAD', '$', '1'),
	(71, 'Liechtenstein', 'Switzerland Francs', 'CHF', 'CHF', '423'),
	(24, 'Chile', 'Pesos', 'CLP', '$', '56'),
	(25, 'China', 'Yuan Renminbi', 'CNY', '¥', '86'),
	(31, 'Czech Republic', 'Koruny', 'CZK', 'K?', '420'),
	(32, 'Denmark', 'Kroner', 'DKK', 'kr', '45'),
	(11, 'Belgium', 'Euro', 'EUR', '€', '32'),
	(19, 'Britain (United Kingdom)', 'Pounds', 'GBP', '£', '44'),
	(50, 'Hong Kong', 'Dollars', 'HKD', '$', '852'),
	(51, 'Hungary', 'Forint', 'HUF', 'Ft', '36'),
	(54, 'Indonesia', 'Rupiahs', 'IDR', 'Rp', '62'),
	(53, 'India', 'Rupees', 'INR', 'Rp', '91'),
	(61, 'Japan', 'Yen', 'JPY', '¥', '81'),
	(65, 'Korea (South)', 'Won', 'KRW', '?', '82'),
	(78, 'Mexico', 'Pesos', 'MXN', '$', '52'),
	(130, 'Malaysia', 'Ringgits', 'MYR', 'RM', '6'),
	(89, 'Norway', 'Krone', 'NOK', 'kr', '47'),
	(85, 'New Zealand', 'Dollars', 'NZD', '$', '64'),
	(95, 'Philippines', 'Pesos', 'PHP', 'Php', '63'),
	(91, 'Pakistan', 'Rupees', 'PKR', '?', '92'),
	(96, 'Poland', 'Zlotych', 'PLN', 'z?', '48'),
	(99, 'Russia', 'Rubles', 'RUB', '???', '7'),
	(112, 'Sweden', 'Kronor', 'SEK', 'kr', '46'),
	(104, 'Singapore', 'Dollars', 'SGD', '$', '65'),
	(117, 'Thailand', 'Baht', 'THB', '?', '66'),
	(119, 'Turkey', 'Lira', 'TRY', 'TL', '90'),
	(116, 'Taiwan', 'New Dollars', 'TWD', 'NT$', '886'),
	(108, 'South Africa', 'Rand', 'ZAR', 'R', '27'),
	(132, 'United States of America', 'Dollars', 'USD', '$', '1');
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;

-- Dumping structure for table wallet.menuticket
CREATE TABLE IF NOT EXISTS `menuticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.menuticket: ~7 rows (approximately)
/*!40000 ALTER TABLE `menuticket` DISABLE KEYS */;
INSERT INTO `menuticket` (`id`, `title`) VALUES
	(1, 'Send Issue'),
	(2, 'Receive Issue'),
	(3, 'Forgot Password'),
	(5, 'Verify Email'),
	(6, 'Login'),
	(7, 'E-Wallet'),
	(8, 'General');
/*!40000 ALTER TABLE `menuticket` ENABLE KEYS */;

-- Dumping structure for table wallet.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `typeP` enum('user','admin') NOT NULL DEFAULT 'user',
  `contents` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.messages: ~3 rows (approximately)
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` (`id`, `ticket_id`, `uid`, `typeP`, `contents`, `attachment`, `created_at`, `updated_at`) VALUES
	(1, 1, 9, 'user', '<p>dfdddd<strong>dd</strong> dfdf</p>', 'support/1563944062.jpg', '2019-07-24 12:54:22', '2019-07-24 12:54:22'),
	(2, 1, 9, 'user', '<p>erewr</p>', 'support/1563948806.png', '2019-07-24 14:13:26', '2019-07-24 14:13:26'),
	(3, 1, 1, 'admin', '<p>&nbsp;ok settle</p>', NULL, '2019-07-24 14:32:26', '2019-07-24 14:32:26');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

-- Dumping structure for table wallet.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table wallet.migrations: ~2 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2019_07_22_035826_create_admins_table', 1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table wallet.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table wallet.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table wallet.price_api
CREATE TABLE IF NOT EXISTS `price_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `crypto` enum('BTC','BCH','LTC','DASH','DOGE','ETH','XRP','XLM','LIFE','LINKER') NOT NULL,
  `price` varchar(100) DEFAULT NULL,
  `logo` varchar(400) DEFAULT NULL,
  `logo2` varchar(200) DEFAULT NULL,
  `percentage` varchar(100) DEFAULT NULL,
  `price_pinkexcbuy` varchar(100) DEFAULT NULL,
  `price_pinkexcsell` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_getinfo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.price_api: 10 rows
/*!40000 ALTER TABLE `price_api` DISABLE KEYS */;
INSERT INTO `price_api` (`id`, `name`, `crypto`, `price`, `logo`, `logo2`, `percentage`, `price_pinkexcbuy`, `price_pinkexcsell`, `created_at`, `updated_at`, `ip_getinfo`) VALUES
	(1, 'Bitcoin', 'BTC', '27137.602626651', '<img src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png?1510040391" style="width:50px;">', 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png?1510040391', '0.2 %', '28494.482757983', '25780.722495318', '2018-10-20 18:30:29', '2019-05-20 09:57:11', 'http://206.189.41.69/getinfo3.php'),
	(2, 'Bitcoin Cash', 'BCH', '1860.2829489704', '<img src="https://assets.coingecko.com/coins/images/780/large/bitcoin_cash.png?1529919381" style="width:50px;">', 'https://assets.coingecko.com/coins/images/780/large/bitcoin_cash.png?1529919381', '1.4 %', '1953.2970964189', '1767.2688015219', '2018-10-20 18:30:29', '2018-11-13 10:08:29', 'http://206.189.151.236/getinfo.php'),
	(3, 'Ethereum', 'ETH', '861.00335495069', '<img src="https://assets.coingecko.com/coins/images/279/large/ethereum.png?1510040267" style="width:50px;">', 'https://assets.coingecko.com/coins/images/279/large/ethereum.png?1510040267', '1.1 %', '904.05352269823', '817.95318720316', '2018-10-20 18:30:29', '2018-11-13 10:08:33', ''),
	(4, 'Dash', 'DASH', '657.99172133188', '<img src="https://assets.coingecko.com/coins/images/19/large/dash.png?1528882129" style="width:50px;">', 'https://assets.coingecko.com/coins/images/19/large/dash.png?1528882129', '1.7 %', '690.89130739848', '625.09213526529', '2018-10-20 18:30:29', '2018-11-13 10:08:37', 'http://206.189.47.157/getinfo.php'),
	(5, 'Litecoin', 'LTC', '223.13847462335', '<img src="https://assets.coingecko.com/coins/images/2/large/litecoin.png?1510040295" style="width:50px;">', 'https://assets.coingecko.com/coins/images/2/large/litecoin.png?1510040295', '0.6 %', '234.29539835451', '211.98155089218', '2018-10-20 18:30:29', '2018-11-16 04:25:49', 'http://206.189.149.148/getinfo.php'),
	(6, 'XRP', 'XRP', '1.9287769897003', '<img src="https://assets.coingecko.com/coins/images/44/large/XRP.png?1536205987" style="width:50px;">', 'https://assets.coingecko.com/coins/images/44/large/XRP.png?1536205987', '1.2 %', '2.0252158391853', '1.8323381402153', '2018-10-20 18:30:29', '2018-11-13 10:08:44', ''),
	(7, 'Stellar', 'XLM', '1.0373326683177', '<img src="https://assets.coingecko.com/coins/images/100/large/stellar_lumens.png?;" style="width:50px;">', 'https://assets.coingecko.com/coins/images/100/large/stellar_lumens.png?;', '3.4 %', '1.0891993017336', '0.98546603490179', '2018-10-20 18:30:29', '2018-11-13 10:08:48', ''),
	(8, 'Dogecoin', 'DOGE', '0.018925889752243', '<img src="https://assets.coingecko.com/coins/images/5/large/dogecoin.png?1510040365" style="width:50px;">', 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png?1510040365', '3.5 %', '0.019872184239855', '0.017979595264631', '2018-10-20 18:30:29', '2018-11-13 10:08:53', 'http://206.189.154.116/getinfo.php'),
	(10, 'LIFE', 'LIFE', '1.20', '-', '-', '-', '-', '-', '2018-11-17 17:34:57', '2019-01-03 09:04:11', '-'),
	(11, 'Linker', 'LINKER', '0.50', '-', '-', '-', '-', '-', '2019-01-03 09:03:33', '2019-01-03 11:21:23', NULL);
/*!40000 ALTER TABLE `price_api` ENABLE KEYS */;

-- Dumping structure for table wallet.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `keywords` text,
  `name` varchar(255) DEFAULT NULL,
  `infoemail` varchar(255) DEFAULT NULL,
  `supportemail` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `commission_btc` varchar(255) DEFAULT NULL COMMENT 'in RM',
  `commission_bcb` varchar(255) DEFAULT NULL COMMENT 'in RM',
  `commission_doge` varchar(255) DEFAULT NULL COMMENT 'in RM',
  `fee_btc` varchar(255) DEFAULT NULL COMMENT 'in crypto',
  `fee_bch` varchar(255) DEFAULT NULL COMMENT 'in crypto',
  `fee_doge` varchar(255) DEFAULT NULL COMMENT 'in crypto',
  `template_email` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.settings: ~1 rows (approximately)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `title`, `description`, `keywords`, `name`, `infoemail`, `supportemail`, `url`, `commission_btc`, `commission_bcb`, `commission_doge`, `fee_btc`, `fee_bch`, `fee_doge`, `template_email`, `created_at`, `updated_at`) VALUES
	(1, 'FRIWALLET', NULL, NULL, 'Friwallet', 'noreply@gmail.com', 'noreply@gmail.com', 'http://192.168.0.136/wallet/', '1.00', '1.00', '1.00', '0.0003', '0.002', '100', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html dir="ltr" xmlns="http://www.w3.org/1999/xhtml"><head><meta name="viewport" content="width=device-width" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>{{title}}</title></head><body style="margin:0px; background: #f8f8f8; "><div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;"><div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px"><tbody><tr><td style="vertical-align: top; padding-bottom:30px;" align="center"><img src="{{logo}}" style="border:none"> <img src="{{logotext}}}" style="border:none">  </td></tr></tbody></table><div style="padding: 40px; background: #fff;"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%;"><tbody><tr><td><p>{{message}}</p><b>Sincerely,<br>Friwallet Support Team </b></td></tr></tbody></table></div><div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px"><p>Powered by <img src="{{logo}}" style="padding-left:1px; width:15px;"> FRIWALLET </p></div></div></div></body></html>', '2019-06-11 16:23:21', '2019-08-14 17:42:09');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Dumping structure for table wallet.state
CREATE TABLE IF NOT EXISTS `state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.state: ~18 rows (approximately)
/*!40000 ALTER TABLE `state` DISABLE KEYS */;
INSERT INTO `state` (`id`, `state`) VALUES
	(1, 'Johor'),
	(2, 'Kedah'),
	(3, 'Kelantan'),
	(4, 'Wilayah Persekutuan Kuala Lumpur'),
	(5, 'Wilayah Persesekutuan Labuan'),
	(6, 'Melaka'),
	(7, 'Negeri Sembilan'),
	(8, 'Pahang'),
	(9, 'Pulau Pinang'),
	(10, 'Perak'),
	(11, 'Perlis'),
	(12, 'Putrajaya'),
	(13, 'Sabah'),
	(14, 'Sarawak'),
	(15, 'Selangor'),
	(16, 'Terengganu'),
	(17, 'Langkawi, Kedah'),
	(18, 'Pulau Tioman, Pahang');
/*!40000 ALTER TABLE `state` ENABLE KEYS */;

-- Dumping structure for table wallet.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `uid` int(100) DEFAULT NULL,
  `type` int(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `details` text,
  `status` enum('Open','Closed','Answered','Awaiting Reply') DEFAULT 'Open',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.tickets: ~0 rows (approximately)
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` (`id`, `uid`, `type`, `subject`, `details`, `status`, `created_at`, `updated_at`) VALUES
	(1, 9, 2, 'masalah login', '{"currency":"BCH","blockExp":"Yes","depoAddr":"Yes","transactionID":"4354dfgd","date":"2018-08-29"}', 'Answered', '2019-07-24 12:54:22', '2019-07-24 14:32:26');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;

-- Dumping structure for table wallet.trans_admin
CREATE TABLE IF NOT EXISTS `trans_admin` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `uid` int(100) DEFAULT NULL,
  `account` enum('admin','coinvata') DEFAULT 'admin',
  `toAddress` varbinary(255) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT 'success',
  `crypto` enum('BTC','BCH','DOGE') DEFAULT 'BTC',
  `amount` varchar(255) DEFAULT NULL,
  `balBefore` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.trans_admin: ~0 rows (approximately)
/*!40000 ALTER TABLE `trans_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `trans_admin` ENABLE KEYS */;

-- Dumping structure for table wallet.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verify` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `phone` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_verify` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` int(50) DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `noic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secretpin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_auth_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','delete','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table wallet.users: ~5 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `username`, `label`, `email_hash`, `email`, `email_verify`, `phone`, `phone_verify`, `password`, `country`, `ip`, `noic`, `secretpin`, `google_auth_code`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
	(9, 'saye', 'saye', 'usr_niha_pinkexc', '', 'saye@gmail.co', '1', '0', '0', '$2y$10$jMvtdGqPAWpVoGhJkrORlOnVVouks5r84GrQgXJx62ejKhBvsRnqa', 0, '', '', '123456789', 'GFF74LID44MBW7HQ', 'active', NULL, NULL, '2019-08-02 17:50:55'),
	(10, 'admin', 'admin', 'usr_admin', '', 'admin@pinkexc.com', '1', '0', '0', '$2y$10$jMvtdGqPAWpVoGhJkrORlOnVVouks5r84GrQgXJx62ejKhBvsRnqa', 130, '192.168.0.143', '123456789', '123456789', '25698745', 'active', NULL, '2019-07-23 09:57:28', '2019-07-24 08:26:07'),
	(11, 'fatonah', 'fatonah', 'usr_coinvata', 'e815d9bf18a05ffe4352127a69a9d2b21bb7a318', 'fafai83@gmail.com', '1', NULL, '0', '$2y$10$rlfgFmcViWxcy5DrXDQoR.FF2oG3l9FUJJglZHy8QHsgfMNAmb6GK', NULL, NULL, NULL, '654321', NULL, 'active', NULL, '2019-07-23 09:59:28', '2019-08-10 12:18:32'),
	(13, 'ggggg', 'gggggg', 'usr_gggggg', 'cf686def36529862abaaa0047c6d1829ea0e3b99', 'gggg@gggg.mm', '1', NULL, '0', '$2y$10$jkhGWXTqE0DK9maoAG/XXe83LgsDNnQkqzAscgkNKdUWtKHbjvoyW', NULL, '192.168.0.143', NULL, NULL, '52UINKQYYGT4OPM5', 'active', NULL, '2019-07-24 15:55:21', '2019-07-24 16:00:24'),
	(14, 'Jamal Wahid', 'jamal123', 'usr_jamal123', '40c717dff1acbb79e404e7c5309411813e4d490b', 'jamal@gmail.co', '1', NULL, '0', '$2y$10$tGR9qNDuEu2ifKidfUcCVuptBFt9v5geuFC01X/GtHWPgLi1HpnV6', NULL, '192.168.0.143', NULL, 'jamal123', '6OT5BZWERY44UBDT', 'active', NULL, '2019-08-09 09:25:43', '2019-08-09 09:30:38'),
	(15, 'faridsany', 'faridsany', 'usr_faridsany', '4588c00bdb3841b9fae98397269539bcd919cc8a', 'farid@pinkexc.com', '1', NULL, '0', '$2y$10$BuCA.Y5he6mLR7BkrXfupeU7vOxdTmU09LMaANV7UsOwzaoyBMhbG', NULL, '192.168.0.143', NULL, '123456', 'I56VTTFYEYQWOVQD', 'active', NULL, '2019-08-09 15:22:17', '2019-08-09 15:23:39'),
	(16, 'zainal abidin', 'zainal', 'usr_zainal', '1ca7aaaa88feab96168809b34f39c339173add57', 'fatonah83@yahoo.com.my', '0', NULL, '0', '$2y$10$JyJDSPrNLANsU7FW464DseJGC8xg2.Jc0bollK.a0ZweznYAE0utm', NULL, '192.168.0.156', NULL, '654321', '77DCLCAPKMF7UC22', 'active', NULL, '2019-08-14 17:48:24', '2019-08-14 17:48:24');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table wallet.wallet_address
CREATE TABLE IF NOT EXISTS `wallet_address` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `uid` int(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `private_key` varchar(255) DEFAULT NULL,
  `balance` varchar(255) DEFAULT '0',
  `crypto` enum('BTC','BCH','DOGE') DEFAULT 'BTC',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.wallet_address: ~15 rows (approximately)
/*!40000 ALTER TABLE `wallet_address` DISABLE KEYS */;
INSERT INTO `wallet_address` (`id`, `uid`, `label`, `address`, `private_key`, `balance`, `crypto`, `created_at`, `updated_at`) VALUES
	(1, 9, 'usr_niha_pinkexc', '3BbxgXCTpBbwu8PcjpNS5K6CpaMsHekC1t', NULL, '0.025', 'BTC', '2019-07-10 09:52:48', '2019-07-17 10:39:30'),
	(2, 9, 'usr_niha_pinkexc', 'qqg2p8e3h9eds05a6x5kuafays2q5yvckqhus73taa', NULL, '0.35', 'BCH', '2019-07-10 09:54:15', '2019-07-17 10:39:31'),
	(3, 9, 'usr_niha_pinkexc', 'DH4rxaS51nxJsrj74tTPSYsfDwte1aQUkH', NULL, '0', 'DOGE', '2019-07-10 09:54:53', '2019-07-17 10:39:34'),
	(4, 11, 'usr_coinvata', '3AiLZFpygiMEuN6263Wb6mFKFubghzdcuY', NULL, '0', 'BTC', '2019-07-10 09:55:02', '2019-07-23 10:56:23'),
	(5, 11, 'usr_coinvata', 'qrjxfyk6le3v2kcxf02zjgwgszxa4nvn6qluzvyvya', NULL, '0.356', 'BCH', '2019-07-10 09:55:15', '2019-07-23 10:56:28'),
	(6, 11, 'usr_coinvata', 'DBxkxSvxCPvCX2mXjuqZG6zyseFcPQgfbg', NULL, '0', 'DOGE', '2019-07-10 09:55:27', '2019-07-23 10:56:29'),
	(7, 10, 'usr_admin', '3LTaJ7BfhJYRVuP3W2DpgCpvh5SNtQGvw6', NULL, '0.65', 'BTC', '2019-07-10 09:55:39', '2019-07-23 10:56:04'),
	(8, 10, 'usr_admin', 'qr0yr2jspj0xh39c0pytjcgkm86v9gcatuhn4turlx', NULL, '0', 'BCH', '2019-07-23 10:18:10', '2019-07-23 10:56:11'),
	(9, 10, 'usr_admin', 'DE82y2RopYucSkQXpFT6oZN1tTMVoP4Z1E', NULL, '0', 'DOGE', '2019-07-23 10:18:35', '2019-07-23 10:56:13'),
	(10, 13, 'usr_gggggg', '1', '', '0.00000000', 'BTC', '2019-07-24 16:00:24', '2019-07-24 16:00:24'),
	(11, 13, 'usr_gggggg', '1', '', '0.00000000', 'BCH', '2019-07-24 16:00:24', '2019-07-24 16:00:24'),
	(12, 13, 'usr_gggggg', '1', '', '0.00000000', 'DOGE', '2019-07-24 16:05:57', '2019-07-24 16:05:57'),
	(13, 14, 'usr_jamal123', '1', '', '0.00000000', 'BTC', '2019-08-09 09:28:30', '2019-08-09 09:28:30'),
	(14, 14, 'usr_jamal123', '1', '', '0.00000000', 'BCH', '2019-08-09 09:28:30', '2019-08-09 09:28:30'),
	(15, 14, 'usr_jamal123', '1', '', '0.00000000', 'DOGE', '2019-08-09 09:28:30', '2019-08-09 09:28:30');
/*!40000 ALTER TABLE `wallet_address` ENABLE KEYS */;

-- Dumping structure for table wallet.withdrawal
CREATE TABLE IF NOT EXISTS `withdrawal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  `before_bal` varchar(255) DEFAULT NULL,
  `after_bal` varchar(255) DEFAULT NULL,
  `myr_amount` varchar(200) DEFAULT NULL,
  `rate` varchar(200) DEFAULT NULL,
  `recipient_id` varchar(100) DEFAULT NULL,
  `recipient` varchar(200) DEFAULT NULL,
  `netfee` varchar(20) DEFAULT NULL,
  `walletfee` varchar(20) DEFAULT NULL,
  `txid` varchar(400) DEFAULT NULL,
  `crypto` enum('BTC','BCH','DOGE') NOT NULL,
  `type` enum('internal','external') NOT NULL,
  `using` enum('web','mobile') NOT NULL DEFAULT 'mobile',
  `remarks` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table wallet.withdrawal: ~0 rows (approximately)
/*!40000 ALTER TABLE `withdrawal` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdrawal` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
