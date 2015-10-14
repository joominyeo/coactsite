-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2015 at 09:05 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test_sqlite_to_mysql`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_denied_log`
--

CREATE TABLE IF NOT EXISTS `access_denied_log` (
`id` int(10) unsigned NOT NULL,
  `access_type` varchar(250) DEFAULT NULL COMMENT 'Page Name passed to route in request ',
  `denied_type` enum('country','ip') DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_as_ip_addr` varchar(50) DEFAULT NULL COMMENT 'This is used ip address other places',
  `remote_addr` varchar(50) DEFAULT NULL,
  `http_user_agent` varchar(250) DEFAULT NULL,
  `http_x_forwarded` varchar(50) DEFAULT NULL,
  `http_x_forwarded_for` varchar(50) DEFAULT NULL,
  `http_x_forwarded_host` varchar(50) DEFAULT NULL,
  `http_x_forwarded_server` varchar(50) DEFAULT NULL,
  `http_client_ip` varchar(50) DEFAULT NULL,
  `http_referer` varchar(3000) DEFAULT NULL,
  `http_accept_language` varchar(250) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `country_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

CREATE TABLE IF NOT EXISTS `access_log` (
`id` int(10) unsigned NOT NULL,
  `access_type` varchar(250) DEFAULT NULL COMMENT 'Page Name passed to route in request ',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_as_ip_addr` varchar(50) DEFAULT NULL COMMENT 'This is used ip address other places',
  `remote_addr` varchar(50) DEFAULT NULL,
  `http_user_agent` varchar(250) DEFAULT NULL,
  `http_x_forwarded` varchar(50) DEFAULT NULL,
  `http_x_forwarded_for` varchar(50) DEFAULT NULL,
  `http_x_forwarded_host` varchar(50) DEFAULT NULL,
  `http_x_forwarded_server` varchar(50) DEFAULT NULL,
  `http_client_ip` varchar(50) DEFAULT NULL,
  `http_referer` varchar(3000) DEFAULT NULL,
  `http_accept_language` varchar(250) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `country_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28009 ;

-- --------------------------------------------------------

--
-- Table structure for table `action_log`
--

CREATE TABLE IF NOT EXISTS `action_log` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'user did action',
  `action` varchar(100) NOT NULL COMMENT 'login,create, delete etc',
  `type` varchar(100) NOT NULL COMMENT 'activity, product , technique etc',
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11748 ;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_countries`
--

CREATE TABLE IF NOT EXISTS `blocked_countries` (
`id` int(11) NOT NULL,
  `countries_id` int(11) NOT NULL,
  `notes` text,
  `created` datetime NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_ips`
--

CREATE TABLE IF NOT EXISTS `blocked_ips` (
`id` int(11) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `ip_range_from` varchar(50) DEFAULT NULL,
  `ip_range_to` varchar(50) DEFAULT NULL,
  `subnet_mask` varchar(55) DEFAULT NULL COMMENT 'ip/cidr',
  `type` enum('ip','iprange','subnet') NOT NULL DEFAULT 'ip',
  `notes` text,
  `created` datetime NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `name_en` varchar(64) DEFAULT NULL,
  `capital` varchar(100) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `currency_name` varchar(25) NOT NULL,
  `telephone` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `login_failed`
--

CREATE TABLE IF NOT EXISTS `login_failed` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `failed_on` datetime NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `remembered_logins`
--

CREATE TABLE IF NOT EXISTS `remembered_logins` (
`id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  `series_id` varchar(100) NOT NULL,
  `ipaddr` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE IF NOT EXISTS `support` (
`id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('contact','advertise') DEFAULT 'contact',
  `email` varchar(75) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','ignore','processing','closed','deleted') NOT NULL DEFAULT 'new'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `status_date` datetime DEFAULT NULL COMMENT 'datetime status set',
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(75) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `php_tz_identifier` varchar(35) NOT NULL DEFAULT 'UTC' COMMENT 'listIdentifiers php function',
  `phash` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `lastlogindt` datetime DEFAULT NULL,
  `lastloginip` varchar(50) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `email2` varchar(75) DEFAULT NULL,
  `user_type` enum('user','admin') NOT NULL DEFAULT 'user',
  `locked` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

ALTER TABLE `user` ADD  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP;

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE IF NOT EXISTS `verifications` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('email','password','email2') NOT NULL COMMENT 'if value=email, email verfication, if password reset password',
  `code` varchar(32) NOT NULL COMMENT 'this must be a unique code',
  `created` datetime NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=215 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_denied_log`
--
ALTER TABLE `access_denied_log`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_log`
--
ALTER TABLE `access_log`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `action_log`
--
ALTER TABLE `action_log`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked_countries`
--
ALTER TABLE `blocked_countries`
 ADD PRIMARY KEY (`id`), ADD KEY `blocked_countries_countries_id` (`countries_id`);

--
-- Indexes for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
 ADD PRIMARY KEY (`id`), ADD KEY `blocked_ips_ip_address` (`ip_address`), ADD KEY `blocked_ips_type` (`type`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_failed`
--
ALTER TABLE `login_failed`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_denied_log`
--
ALTER TABLE `access_denied_log`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `access_log`
--
ALTER TABLE `access_log`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28009;
--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11748;
--
-- AUTO_INCREMENT for table `blocked_countries`
--
ALTER TABLE `blocked_countries`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `login_failed`
--
ALTER TABLE `login_failed`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=215;


-- -------------------------------------------------------------------
-- default data
-- -------------------------------------------------------------------

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `iso2`, `iso3`, `name_en`, `capital`, `currency_code`, `currency_name`, `telephone`) VALUES
(1, 'AF', 'AFG', 'Afghanistan', 'Kabul', 'AFN', 'Afghani', '93'),
(2, 'AL', 'ALB', 'Albania', 'Tirana', 'ALL', 'Lek', '355'),
(3, 'DZ', 'DZA', 'Algeria', 'Algiers', 'DZD', 'Dinar', '213'),
(4, 'AD', 'AND', 'Andorra', 'Andorra la Vella', 'EUR', 'Euro', '376'),
(5, 'AO', 'AGO', 'Angola', 'Luanda', 'AOA', 'Kwanza', '244'),
(6, 'AG', 'ATG', 'Antigua and Barbuda', 'Saint John''s', 'XCD', 'Dollar', '-267'),
(7, 'AR', 'ARG', 'Argentina', 'Buenos Aires', 'ARS', 'Peso', '54'),
(8, 'AM', 'ARM', 'Armenia', 'Yerevan', 'AMD', 'Dram', '374'),
(9, 'AU', 'AUS', 'Australia', 'Canberra', 'AUD', 'Dollar', '61'),
(10, 'AT', 'AUT', 'Austria', 'Vienna', 'EUR', 'Euro', '43'),
(11, 'AZ', 'AZE', 'Azerbaijan', 'Baku', 'AZN', 'Manat', '994'),
(12, 'BS', 'BHS', 'Bahamas, The', 'Nassau', 'BSD', 'Dollar', '-241'),
(13, 'BH', 'BHR', 'Bahrain', 'Manama', 'BHD', 'Dinar', '973'),
(14, 'BD', 'BGD', 'Bangladesh', 'Dhaka', 'BDT', 'Taka', '880'),
(15, 'BB', 'BRB', 'Barbados', 'Bridgetown', 'BBD', 'Dollar', '-245'),
(16, 'BY', 'BLR', 'Belarus', 'Minsk', 'BYR', 'Ruble', '375'),
(17, 'BE', 'BEL', 'Belgium', 'Brussels', 'EUR', 'Euro', '32'),
(18, 'BZ', 'BLZ', 'Belize', 'Belmopan', 'BZD', 'Dollar', '501'),
(19, 'BJ', 'BEN', 'Benin', 'Porto-Novo', 'XOF', 'Franc', '229'),
(20, 'BT', 'BTN', 'Bhutan', 'Thimphu', 'BTN', 'Ngultrum', '975'),
(21, 'BO', 'BOL', 'Bolivia', 'La Paz (administrative/legislative) and Sucre (judical)', 'BOB', 'Boliviano', '591'),
(22, 'BA', 'BIH', 'Bosnia and Herzegovina', 'Sarajevo', 'BAM', 'Marka', '387'),
(23, 'BW', 'BWA', 'Botswana', 'Gaborone', 'BWP', 'Pula', '267'),
(24, 'BR', 'BRA', 'Brazil', 'Brasilia', 'BRL', 'Real', '55'),
(25, 'BN', 'BRN', 'Brunei', 'Bandar Seri Begawan', 'BND', 'Dollar', '673'),
(26, 'BG', 'BGR', 'Bulgaria', 'Sofia', 'BGN', 'Lev', '359'),
(27, 'BF', 'BFA', 'Burkina Faso', 'Ouagadougou', 'XOF', 'Franc', '226'),
(28, 'BI', 'BDI', 'Burundi', 'Bujumbura', 'BIF', 'Franc', '257'),
(29, 'KH', 'KHM', 'Cambodia', 'Phnom Penh', 'KHR', 'Riels', '855'),
(30, 'CM', 'CMR', 'Cameroon', 'Yaounde', 'XAF', 'Franc', '237'),
(31, 'CA', 'CAN', 'Canada', 'Ottawa', 'CAD', 'Dollar', '1'),
(32, 'CV', 'CPV', 'Cape Verde', 'Praia', 'CVE', 'Escudo', '238'),
(33, 'CF', 'CAF', 'Central African Republic', 'Bangui', 'XAF', 'Franc', '236'),
(34, 'TD', 'TCD', 'Chad', 'N''Djamena', 'XAF', 'Franc', '235'),
(35, 'CL', 'CHL', 'Chile', 'Santiago (administrative/judical) and Valparaiso (legislative)', 'CLP', 'Peso', '56'),
(36, 'CN', 'CHN', 'China, People''s Republic of', 'Beijing', 'CNY', 'Yuan Renminbi', '86'),
(37, 'CO', 'COL', 'Colombia', 'Bogota', 'COP', 'Peso', '57'),
(38, 'KM', 'COM', 'Comoros', 'Moroni', 'KMF', 'Franc', '269'),
(39, 'CD', 'COD', 'Congo, Democratic Republic of the (Congo & Kinshasa)', 'Kinshasa', 'CDF', 'Franc', '243'),
(40, 'CG', 'COG', 'Congo, Republic of the (Congo & Brazzaville)', 'Brazzaville', 'XAF', 'Franc', '242'),
(41, 'CR', 'CRI', 'Costa Rica', 'San Jose', 'CRC', 'Colon', '506'),
(42, 'CI', 'CIV', 'Cote d''Ivoire (Ivory Coast)', 'Yamoussoukro', 'XOF', 'Franc', '225'),
(43, 'HR', 'HRV', 'Croatia', 'Zagreb', 'HRK', 'Kuna', '385'),
(44, 'CU', 'CUB', 'Cuba', 'Havana', 'CUP', 'Peso', '53'),
(45, 'CY', 'CYP', 'Cyprus', 'Nicosia', 'CYP', 'Pound', '357'),
(46, 'CZ', 'CZE', 'Czech Republic', 'Prague', 'CZK', 'Koruna', '420'),
(47, 'DK', 'DNK', 'Denmark', 'Copenhagen', 'DKK', 'Krone', '45'),
(48, 'DJ', 'DJI', 'Djibouti', 'Djibouti', 'DJF', 'Franc', '253'),
(49, 'DM', 'DMA', 'Dominica', 'Roseau', 'XCD', 'Dollar', '-766'),
(50, 'DO', 'DOM', 'Dominican Republic', 'Santo Domingo', 'DOP', 'Peso', '+1-809 and'),
(51, 'EC', 'ECU', 'Ecuador', 'Quito', 'USD', 'Dollar', '593'),
(52, 'EG', 'EGY', 'Egypt', 'Cairo', 'EGP', 'Pound', '20'),
(53, 'SV', 'SLV', 'El Salvador', 'San Salvador', 'USD', 'Dollar', '503'),
(54, 'GQ', 'GNQ', 'Equatorial Guinea', 'Malabo', 'XAF', 'Franc', '240'),
(55, 'ER', 'ERI', 'Eritrea', 'Asmara', 'ERN', 'Nakfa', '291'),
(56, 'EE', 'EST', 'Estonia', 'Tallinn', 'EEK', 'Kroon', '372'),
(57, 'ET', 'ETH', 'Ethiopia', 'Addis Ababa', 'ETB', 'Birr', '251'),
(58, 'FJ', 'FJI', 'Fiji', 'Suva', 'FJD', 'Dollar', '679'),
(59, 'FI', 'FIN', 'Finland', 'Helsinki', 'EUR', 'Euro', '358'),
(60, 'FR', 'FRA', 'France', 'Paris', 'EUR', 'Euro', '33'),
(61, 'GA', 'GAB', 'Gabon', 'Libreville', 'XAF', 'Franc', '241'),
(62, 'GM', 'GMB', 'Gambia, The', 'Banjul', 'GMD', 'Dalasi', '220'),
(63, 'GE', 'GEO', 'Georgia', 'Tbilisi', 'GEL', 'Lari', '995'),
(64, 'DE', 'DEU', 'Germany', 'Berlin', 'EUR', 'Euro', '49'),
(65, 'GH', 'GHA', 'Ghana', 'Accra', 'GHS', 'Cedi', '233'),
(66, 'GR', 'GRC', 'Greece', 'Athens', 'EUR', 'Euro', '30'),
(67, 'GD', 'GRD', 'Grenada', 'Saint George''s', 'XCD', 'Dollar', '-472'),
(68, 'GT', 'GTM', 'Guatemala', 'Guatemala', 'GTQ', 'Quetzal', '502'),
(69, 'GN', 'GIN', 'Guinea', 'Conakry', 'GNF', 'Franc', '224'),
(70, 'GW', 'GNB', 'Guinea-Bissau', 'Bissau', 'XOF', 'Franc', '245'),
(71, 'GY', 'GUY', 'Guyana', 'Georgetown', 'GYD', 'Dollar', '592'),
(72, 'HT', 'HTI', 'Haiti', 'Port-au-Prince', 'HTG', 'Gourde', '509'),
(73, 'HN', 'HND', 'Honduras', 'Tegucigalpa', 'HNL', 'Lempira', '504'),
(74, 'HU', 'HUN', 'Hungary', 'Budapest', 'HUF', 'Forint', '36'),
(75, 'IS', 'ISL', 'Iceland', 'Reykjavik', 'ISK', 'Krona', '354'),
(76, 'IN', 'IND', 'India', 'New Delhi', 'INR', 'Rupee', '91'),
(77, 'ID', 'IDN', 'Indonesia', 'Jakarta', 'IDR', 'Rupiah', '62'),
(78, 'IR', 'IRN', 'Iran', 'Tehran', 'IRR', 'Rial', '98'),
(79, 'IQ', 'IRQ', 'Iraq', 'Baghdad', 'IQD', 'Dinar', '964'),
(80, 'IE', 'IRL', 'Ireland', 'Dublin', 'EUR', 'Euro', '353'),
(81, 'IL', 'ISR', 'Israel', 'Jerusalem', 'ILS', 'Shekel', '972'),
(82, 'IT', 'ITA', 'Italy', 'Rome', 'EUR', 'Euro', '39'),
(83, 'JM', 'JAM', 'Jamaica', 'Kingston', 'JMD', 'Dollar', '-875'),
(84, 'JP', 'JPN', 'Japan', 'Tokyo', 'JPY', 'Yen', '81'),
(85, 'JO', 'JOR', 'Jordan', 'Amman', 'JOD', 'Dinar', '962'),
(86, 'KZ', 'KAZ', 'Kazakhstan', 'Astana', 'KZT', 'Tenge', '7'),
(87, 'KE', 'KEN', 'Kenya', 'Nairobi', 'KES', 'Shilling', '254'),
(88, 'KI', 'KIR', 'Kiribati', 'Tarawa', 'AUD', 'Dollar', '686'),
(89, 'KP', 'PRK', 'Korea, Democratic People''s Republic of (North Korea)', 'Pyongyang', 'KPW', 'Won', '850'),
(90, 'KR', 'KOR', 'Korea, Republic of (South Korea)', 'Seoul', 'KRW', 'Won', '82'),
(91, 'KW', 'KWT', 'Kuwait', 'Kuwait', 'KWD', 'Dinar', '965'),
(92, 'KG', 'KGZ', 'Kyrgyzstan', 'Bishkek', 'KGS', 'Som', '996'),
(93, 'LA', 'LAO', 'Laos', 'Vientiane', 'LAK', 'Kip', '856'),
(94, 'LV', 'LVA', 'Latvia', 'Riga', 'LVL', 'Lat', '371'),
(95, 'LB', 'LBN', 'Lebanon', 'Beirut', 'LBP', 'Pound', '961'),
(96, 'LS', 'LSO', 'Lesotho', 'Maseru', 'LSL', 'Loti', '266'),
(97, 'LR', 'LBR', 'Liberia', 'Monrovia', 'LRD', 'Dollar', '231'),
(98, 'LY', 'LBY', 'Libya', 'Tripoli', 'LYD', 'Dinar', '218'),
(99, 'LI', 'LIE', 'Liechtenstein', 'Vaduz', 'CHF', 'Franc', '423'),
(100, 'LT', 'LTU', 'Lithuania', 'Vilnius', 'LTL', 'Litas', '370'),
(101, 'LU', 'LUX', 'Luxembourg', 'Luxembourg', 'EUR', 'Euro', '352'),
(102, 'MK', 'MKD', 'Macedonia', 'Skopje', 'MKD', 'Denar', '389'),
(103, 'MG', 'MDG', 'Madagascar', 'Antananarivo', 'MGA', 'Ariary', '261'),
(104, 'MW', 'MWI', 'Malawi', 'Lilongwe', 'MWK', 'Kwacha', '265'),
(105, 'MY', 'MYS', 'Malaysia', 'Kuala Lumpur (legislative/judical) and Putrajaya (administrative)', 'MYR', 'Ringgit', '60'),
(106, 'MV', 'MDV', 'Maldives', 'Male', 'MVR', 'Rufiyaa', '960'),
(107, 'ML', 'MLI', 'Mali', 'Bamako', 'XOF', 'Franc', '223'),
(108, 'MT', 'MLT', 'Malta', 'Valletta', 'MTL', 'Lira', '356'),
(109, 'MH', 'MHL', 'Marshall Islands', 'Majuro', 'USD', 'Dollar', '692'),
(110, 'MR', 'MRT', 'Mauritania', 'Nouakchott', 'MRO', 'Ouguiya', '222'),
(111, 'MU', 'MUS', 'Mauritius', 'Port Louis', 'MUR', 'Rupee', '230'),
(112, 'MX', 'MEX', 'Mexico', 'Mexico', 'MXN', 'Peso', '52'),
(113, 'FM', 'FSM', 'Micronesia', 'Palikir', 'USD', 'Dollar', '691'),
(114, 'MD', 'MDA', 'Moldova', 'Chisinau', 'MDL', 'Leu', '373'),
(115, 'MC', 'MCO', 'Monaco', 'Monaco', 'EUR', 'Euro', '377'),
(116, 'MN', 'MNG', 'Mongolia', 'Ulaanbaatar', 'MNT', 'Tugrik', '976'),
(117, 'ME', 'MNE', 'Montenegro', 'Podgorica', 'EUR', 'Euro', '382'),
(118, 'MA', 'MAR', 'Morocco', 'Rabat', 'MAD', 'Dirham', '212'),
(119, 'MZ', 'MOZ', 'Mozambique', 'Maputo', 'MZM', 'Meticail', '258'),
(120, 'MM', 'MMR', 'Myanmar (Burma)', 'Naypyidaw', 'MMK', 'Kyat', '95'),
(121, 'NA', 'NAM', 'Namibia', 'Windhoek', 'NAD', 'Dollar', '264'),
(122, 'NR', 'NRU', 'Nauru', 'Yaren', 'AUD', 'Dollar', '674'),
(123, 'NP', 'NPL', 'Nepal', 'Kathmandu', 'NPR', 'Rupee', '977'),
(124, 'NL', 'NLD', 'Netherlands', 'Amsterdam (administrative) and The Hague (legislative/judical)', 'EUR', 'Euro', '31'),
(125, 'NZ', 'NZL', 'New Zealand', 'Wellington', 'NZD', 'Dollar', '64'),
(126, 'NI', 'NIC', 'Nicaragua', 'Managua', 'NIO', 'Cordoba', '505'),
(127, 'NE', 'NER', 'Niger', 'Niamey', 'XOF', 'Franc', '227'),
(128, 'NG', 'NGA', 'Nigeria', 'Abuja', 'NGN', 'Naira', '234'),
(129, 'NO', 'NOR', 'Norway', 'Oslo', 'NOK', 'Krone', '47'),
(130, 'OM', 'OMN', 'Oman', 'Muscat', 'OMR', 'Rial', '968'),
(131, 'PK', 'PAK', 'Pakistan', 'Islamabad', 'PKR', 'Rupee', '92'),
(132, 'PW', 'PLW', 'Palau', 'Melekeok', 'USD', 'Dollar', '680'),
(133, 'PA', 'PAN', 'Panama', 'Panama', 'PAB', 'Balboa', '507'),
(134, 'PG', 'PNG', 'Papua New Guinea', 'Port Moresby', 'PGK', 'Kina', '675'),
(135, 'PY', 'PRY', 'Paraguay', 'Asuncion', 'PYG', 'Guarani', '595'),
(136, 'PE', 'PER', 'Peru', 'Lima', 'PEN', 'Sol', '51'),
(137, 'PH', 'PHL', 'Philippines', 'Manila', 'PHP', 'Peso', '63'),
(138, 'PL', 'POL', 'Poland', 'Warsaw', 'PLN', 'Zloty', '48'),
(139, 'PT', 'PRT', 'Portugal', 'Lisbon', 'EUR', 'Euro', '351'),
(140, 'QA', 'QAT', 'Qatar', 'Doha', 'QAR', 'Rial', '974'),
(141, 'RO', 'ROU', 'Romania', 'Bucharest', 'RON', 'Leu', '40'),
(142, 'RU', 'RUS', 'Russia', 'Moscow', 'RUB', 'Ruble', '7'),
(143, 'RW', 'RWA', 'Rwanda', 'Kigali', 'RWF', 'Franc', '250'),
(144, 'KN', 'KNA', 'Saint Kitts and Nevis', 'Basseterre', 'XCD', 'Dollar', '-868'),
(145, 'LC', 'LCA', 'Saint Lucia', 'Castries', 'XCD', 'Dollar', '-757'),
(146, 'VC', 'VCT', 'Saint Vincent and the Grenadines', 'Kingstown', 'XCD', 'Dollar', '-783'),
(147, 'WS', 'WSM', 'Samoa', 'Apia', 'WST', 'Tala', '685'),
(148, 'SM', 'SMR', 'San Marino', 'San Marino', 'EUR', 'Euro', '378'),
(149, 'ST', 'STP', 'Sao Tome and Principe', 'Sao Tome', 'STD', 'Dobra', '239'),
(150, 'SA', 'SAU', 'Saudi Arabia', 'Riyadh', 'SAR', 'Rial', '966'),
(151, 'SN', 'SEN', 'Senegal', 'Dakar', 'XOF', 'Franc', '221'),
(152, 'RS', 'SRB', 'Serbia', 'Belgrade', 'RSD', 'Dinar', '381'),
(153, 'SC', 'SYC', 'Seychelles', 'Victoria', 'SCR', 'Rupee', '248'),
(154, 'SL', 'SLE', 'Sierra Leone', 'Freetown', 'SLL', 'Leone', '232'),
(155, 'SG', 'SGP', 'Singapore', 'Singapore', 'SGD', 'Dollar', '65'),
(156, 'SK', 'SVK', 'Slovakia', 'Bratislava', 'SKK', 'Koruna', '421'),
(157, 'SI', 'SVN', 'Slovenia', 'Ljubljana', 'EUR', 'Euro', '386'),
(158, 'SB', 'SLB', 'Solomon Islands', 'Honiara', 'SBD', 'Dollar', '677'),
(159, 'SO', 'SOM', 'Somalia', 'Mogadishu', 'SOS', 'Shilling', '252'),
(160, 'ZA', 'ZAF', 'South Africa', 'Pretoria (administrative), Cape Town (legislative), and Bloemfontein (judical)', 'ZAR', 'Rand', '27'),
(161, 'ES', 'ESP', 'Spain', 'Madrid', 'EUR', 'Euro', '34'),
(162, 'LK', 'LKA', 'Sri Lanka', 'Colombo (administrative/judical) and Sri Jayewardenepura Kotte (legislative)', 'LKR', 'Rupee', '94'),
(163, 'SD', 'SDN', 'Sudan', 'Khartoum', 'SDG', 'Pound', '249'),
(164, 'SR', 'SUR', 'Suriname', 'Paramaribo', 'SRD', 'Dollar', '597'),
(165, 'SZ', 'SWZ', 'Swaziland', 'Mbabane (administrative) and Lobamba (legislative)', 'SZL', 'Lilangeni', '268'),
(166, 'SE', 'SWE', 'Sweden', 'Stockholm', 'SEK', 'Kronoa', '46'),
(167, 'CH', 'CHE', 'Switzerland', 'Bern', 'CHF', 'Franc', '41'),
(168, 'SY', 'SYR', 'Syria', 'Damascus', 'SYP', 'Pound', '963'),
(169, 'TJ', 'TJK', 'Tajikistan', 'Dushanbe', 'TJS', 'Somoni', '992'),
(170, 'TZ', 'TZA', 'Tanzania', 'Dar es Salaam (administrative/judical) and Dodoma (legislative)', 'TZS', 'Shilling', '255'),
(171, 'TH', 'THA', 'Thailand', 'Bangkok', 'THB', 'Baht', '66'),
(172, 'TL', 'TLS', 'Timor-Leste (East Timor)', 'Dili', 'USD', 'Dollar', '670'),
(173, 'TG', 'TGO', 'Togo', 'Lome', 'XOF', 'Franc', '228'),
(174, 'TO', 'TON', 'Tonga', 'Nuku''alofa', 'TOP', 'Pa''anga', '676'),
(175, 'TT', 'TTO', 'Trinidad and Tobago', 'Port-of-Spain', 'TTD', 'Dollar', '-867'),
(176, 'TN', 'TUN', 'Tunisia', 'Tunis', 'TND', 'Dinar', '216'),
(177, 'TR', 'TUR', 'Turkey', 'Ankara', 'TRY', 'Lira', '90'),
(178, 'TM', 'TKM', 'Turkmenistan', 'Ashgabat', 'TMM', 'Manat', '993'),
(179, 'TV', 'TUV', 'Tuvalu', 'Funafuti', 'AUD', 'Dollar', '688'),
(180, 'UG', 'UGA', 'Uganda', 'Kampala', 'UGX', 'Shilling', '256'),
(181, 'UA', 'UKR', 'Ukraine', 'Kiev', 'UAH', 'Hryvnia', '380'),
(182, 'AE', 'ARE', 'United Arab Emirates', 'Abu Dhabi', 'AED', 'Dirham', '971'),
(183, 'GB', 'GBR', 'United Kingdom', 'London', 'GBP', 'Pound', '44'),
(184, 'US', 'USA', 'United States', 'Washington', 'USD', 'Dollar', '1'),
(185, 'UY', 'URY', 'Uruguay', 'Montevideo', 'UYU', 'Peso', '598'),
(186, 'UZ', 'UZB', 'Uzbekistan', 'Tashkent', 'UZS', 'Som', '998'),
(187, 'VU', 'VUT', 'Vanuatu', 'Port-Vila', 'VUV', 'Vatu', '678'),
(188, 'VA', 'VAT', 'Vatican City', 'Vatican City', 'EUR', 'Euro', '379'),
(189, 'VE', 'VEN', 'Venezuela', 'Caracas', 'VEB', 'Bolivar', '58'),
(190, 'VN', 'VNM', 'Vietnam', 'Hanoi', 'VND', 'Dong', '84'),
(191, 'YE', 'YEM', 'Yemen', 'Sanaa', 'YER', 'Rial', '967'),
(192, 'ZM', 'ZMB', 'Zambia', 'Lusaka', 'ZMK', 'Kwacha', '260'),
(193, 'ZW', 'ZWE', 'Zimbabwe', 'Harare', 'ZWD', 'Dollar', '263'),
(194, 'GE', 'GEO', 'Abkhazia', 'Sokhumi', 'RUB', 'Ruble', '995'),
(195, 'TW', 'TWN', 'China, Republic of (Taiwan)', 'Taipei', 'TWD', 'Dollar', '886'),
(196, 'AZ', 'AZE', 'Nagorno-Karabakh', 'Stepanakert', 'AMD', 'Dram', '277'),
(197, 'CY', 'CYP', 'Northern Cyprus', 'Nicosia', 'TRY', 'Lira', '-302'),
(198, 'MD', 'MDA', 'Pridnestrovie (Transnistria)', 'Tiraspol', '', 'Ruple', '-160'),
(199, 'SO', 'SOM', 'Somaliland', 'Hargeisa', '', 'Shilling', '252'),
(200, 'GE', 'GEO', 'South Ossetia', 'Tskhinvali', 'RUB', 'Ruble and Lari', '995'),
(201, 'AU', 'AUS', 'Ashmore and Cartier Islands', '', '', '', ''),
(202, 'CX', 'CXR', 'Christmas Island', 'The Settlement (Flying Fish Cove)', 'AUD', 'Dollar', '61'),
(203, 'CC', 'CCK', 'Cocos (Keeling) Islands', 'West Island', 'AUD', 'Dollar', '61'),
(204, 'AU', 'AUS', 'Coral Sea Islands', '', '', '', ''),
(205, 'HM', 'HMD', 'Heard Island and McDonald Islands', '', '', '', ''),
(206, 'NF', 'NFK', 'Norfolk Island', 'Kingston', 'AUD', 'Dollar', '672'),
(207, 'NC', 'NCL', 'New Caledonia', 'Noumea', 'XPF', 'Franc', '687'),
(208, 'PF', 'PYF', 'French Polynesia', 'Papeete', 'XPF', 'Franc', '689'),
(209, 'YT', 'MYT', 'Mayotte', 'Mamoudzou', 'EUR', 'Euro', '262'),
(210, 'GP', 'GLP', 'Saint Barthelemy', 'Gustavia', 'EUR', 'Euro', '590'),
(211, 'GP', 'GLP', 'Saint Martin', 'Marigot', 'EUR', 'Euro', '590'),
(212, 'PM', 'SPM', 'Saint Pierre and Miquelon', 'Saint-Pierre', 'EUR', 'Euro', '508'),
(213, 'WF', 'WLF', 'Wallis and Futuna', 'Mata''utu', 'XPF', 'Franc', '681'),
(214, 'TF', 'ATF', 'French Southern and Antarctic Lands', 'Martin-de-Vivi', '', '', ''),
(215, 'PF', 'PYF', 'Clipperton Island', '', '', '', ''),
(216, 'BV', 'BVT', 'Bouvet Island', '', '', '', ''),
(217, 'CK', 'COK', 'Cook Islands', 'Avarua', 'NZD', 'Dollar', '682'),
(218, 'NU', 'NIU', 'Niue', 'Alofi', 'NZD', 'Dollar', '683'),
(219, 'TK', 'TKL', 'Tokelau', '', 'NZD', 'Dollar', '690'),
(220, 'GG', 'GGY', 'Guernsey', 'Saint Peter Port', 'GGP', 'Pound', '44'),
(221, 'IM', 'IMN', 'Isle of Man', 'Douglas', 'IMP', 'Pound', '44'),
(222, 'JE', 'JEY', 'Jersey', 'Saint Helier', 'JEP', 'Pound', '44'),
(223, 'AI', 'AIA', 'Anguilla', 'The Valley', 'XCD', 'Dollar', '-263'),
(224, 'BM', 'BMU', 'Bermuda', 'Hamilton', 'BMD', 'Dollar', '-440'),
(225, 'IO', 'IOT', 'British Indian Ocean Territory', '', '', '', '246'),
(226, '', '', 'British Sovereign Base Areas', 'Episkopi', 'CYP', 'Pound', '357'),
(227, 'VG', 'VGB', 'British Virgin Islands', 'Road Town', 'USD', 'Dollar', '-283'),
(228, 'KY', 'CYM', 'Cayman Islands', 'George Town', 'KYD', 'Dollar', '-344'),
(229, 'FK', 'FLK', 'Falkland Islands (Islas Malvinas)', 'Stanley', 'FKP', 'Pound', '500'),
(230, 'GI', 'GIB', 'Gibraltar', 'Gibraltar', 'GIP', 'Pound', '350'),
(231, 'MS', 'MSR', 'Montserrat', 'Plymouth', 'XCD', 'Dollar', '-663'),
(232, 'PN', 'PCN', 'Pitcairn Islands', 'Adamstown', 'NZD', 'Dollar', ''),
(233, 'SH', 'SHN', 'Saint Helena', 'Jamestown', 'SHP', 'Pound', '290'),
(234, 'GS', 'SGS', 'South Georgia and the South Sandwich Islands', '', '', '', ''),
(235, 'TC', 'TCA', 'Turks and Caicos Islands', 'Grand Turk', 'USD', 'Dollar', '-648'),
(236, 'MP', 'MNP', 'Northern Mariana Islands', 'Saipan', 'USD', 'Dollar', '-669'),
(237, 'PR', 'PRI', 'Puerto Rico', 'San Juan', 'USD', 'Dollar', '+1-787 and'),
(238, 'AS', 'ASM', 'American Samoa', 'Pago Pago', 'USD', 'Dollar', '-683'),
(239, 'UM', 'UMI', 'Baker Island', '', '', '', ''),
(240, 'GU', 'GUM', 'Guam', 'Hagatna', 'USD', 'Dollar', '-670'),
(241, 'UM', 'UMI', 'Howland Island', '', '', '', ''),
(242, 'UM', 'UMI', 'Jarvis Island', '', '', '', ''),
(243, 'UM', 'UMI', 'Johnston Atoll', '', '', '', ''),
(244, 'UM', 'UMI', 'Kingman Reef', '', '', '', ''),
(245, 'UM', 'UMI', 'Midway Islands', '', '', '', ''),
(246, 'UM', 'UMI', 'Navassa Island', '', '', '', ''),
(247, 'UM', 'UMI', 'Palmyra Atoll', '', '', '', ''),
(248, 'VI', 'VIR', 'U.S. Virgin Islands', 'Charlotte Amalie', 'USD', 'Dollar', '-339'),
(249, 'UM', 'UMI', 'Wake Island', '', '', '', ''),
(250, 'HK', 'HKG', 'Hong Kong', '', 'HKD', 'Dollar', '852'),
(251, 'MO', 'MAC', 'Macau', 'Macau', 'MOP', 'Pataca', '853'),
(252, 'FO', 'FRO', 'Faroe Islands', 'Torshavn', 'DKK', 'Krone', '298'),
(253, 'GL', 'GRL', 'Greenland', 'Nuuk (Godthab)', 'DKK', 'Krone', '299'),
(254, 'GF', 'GUF', 'French Guiana', 'Cayenne', 'EUR', 'Euro', '594'),
(255, 'GP', 'GLP', 'Guadeloupe', 'Basse-Terre', 'EUR', 'Euro', '590'),
(256, 'MQ', 'MTQ', 'Martinique', 'Fort-de-France', 'EUR', 'Euro', '596'),
(257, 'RE', 'REU', 'Reunion', 'Saint-Denis', 'EUR', 'Euro', '262'),
(258, 'AX', 'ALA', 'Aland', 'Mariehamn', 'EUR', 'Euro', '340'),
(259, 'AW', 'ABW', 'Aruba', 'Oranjestad', 'AWG', 'Guilder', '297'),
(260, 'AN', 'ANT', 'Netherlands Antilles', 'Willemstad', 'ANG', 'Guilder', '599'),
(261, 'SJ', 'SJM', 'Svalbard', 'Longyearbyen', 'NOK', 'Krone', '47'),
(262, 'AC', 'ASC', 'Ascension', 'Georgetown', 'SHP', 'Pound', '247'),
(263, 'TA', 'TAA', 'Tristan da Cunha', 'Edinburgh', 'SHP', 'Pound', '290'),
(264, 'AQ', 'ATA', 'Antarctica', '', '', '', ''),
(265, 'CS', 'SCG', 'Kosovo', 'Pristina', 'CSD', 'Dinar and Euro', '381'),
(266, 'PS', 'PSE', 'Palestinian Territories (Gaza Strip and West Bank)', 'Gaza City (Gaza Strip) and Ramallah (West Bank)', 'ILS', 'Shekel', '970'),
(267, 'EH', 'ESH', 'Western Sahara', 'El-Aaiun', 'MAD', 'Dirham', '212'),
(268, 'AQ', 'ATA', 'Australian Antarctic Territory', '', '', '', ''),
(269, 'AQ', 'ATA', 'Ross Dependency', '', '', '', ''),
(270, 'AQ', 'ATA', 'Peter I Island', '', '', '', ''),
(271, 'AQ', 'ATA', 'Queen Maud Land', '', '', '', ''),
(272, 'AQ', 'ATA', 'British Antarctic Territory', '', '', '', '');


-- -------------------------------------------------------------------
-- default data end
-- -------------------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
`id` int(11) NOT NULL,
  `code` varchar(2) NOT NULL COMMENT 'lowercase code for lanuage',
  `file_name` varchar(255) NOT NULL COMMENT 'file name in app/lang/',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'default lanuage'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



--
-- Indexes for dumped tables
--

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `code` (`code`), ADD UNIQUE KEY `file_name` (`file_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;

ALTER TABLE `languages` ADD `enabled` BOOLEAN NOT NULL DEFAULT TRUE ;
ALTER TABLE `languages` ADD `name` varchar(255) NOT NULL  COMMENT 'language name to show';
ALTER TABLE `languages` ADD `recaptchar_code` VARCHAR(6) NOT NULL COMMENT 'lang code match to google recaptchar' AFTER `name`; 


--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `code`, `file_name`, `name`, `recaptchar_code`, `default`, `enabled`) VALUES
(1, 'en', 'en.class.php', 'English', 'en', 1, 1);

-- -----------------------------------------------------------------------------------------
-- add lanuage id to user table with default language as 1

ALTER TABLE `user` ADD `language_id` INT NOT NULL DEFAULT '1' ;
-- -----------------------------------------------------------------------------------------

-- -----------------------------------------------------------------------------------------
-- end of v-1.0.0 and start of v-1.0.1 
-- ------------------------------------------------------------------------------------------
ALTER TABLE `countries` ADD `enabled` TINYINT NOT NULL DEFAULT '0';
update `countries` set `enabled`=1 WHERE `id` in (82,183,184);

-- -----------------------------------------------------------------------------------------
-- end of v-1.0.1 and start of v-1.0.2 
-- ------------------------------------------------------------------------------------------

