-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2025 at 12:08 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rposystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `rpos_admin`
--

CREATE TABLE `rpos_admin` (
  `admin_id` varchar(200) NOT NULL,
  `admin_name` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_admin`
--

INSERT INTO `rpos_admin` (`admin_id`, `admin_name`, `admin_email`, `admin_password`) VALUES
('10e0b6dc958adfb5b094d8935a13aeadbe783c25', 'System Admin', 'admin@mail.com', '10470c3b4b1fed12c3baac014be15fac67c6e815');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_customers`
--

CREATE TABLE `rpos_customers` (
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_customers`
--

INSERT INTO `rpos_customers` (`customer_id`, `customer_name`, `customer_phoneno`, `customer_email`, `customer_password`, `created_at`) VALUES
('0172be2eaea3', 'Leo', '0789784578', 'ltuyi10@gmail.com', '63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1', '2025-02-19 11:58:18.301583'),
('06549ea58afd', 'Ana J. Browne', '4589698780', 'anaj@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:39:48.523820'),
('08f2e0229fba', 'Theogene MUPENZI', '078978578', 'MUPENZI2000@GMAIL.COM', '63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1', '2025-03-01 07:35:01.641384'),
('1fc1f694985d', 'Jane Doe', '2145896547', 'janed@mail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '2022-09-03 13:39:13.076592'),
('27e4a5bc74c2', 'Tammy R. Polley', '4589654780', 'tammy@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:37:47.049438'),
('29c759d624f9', 'Trina L. Crowder', '5896321002', 'trina@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 13:16:18.927595'),
('35135b319ce3', 'Christine Moore', '7412569698', 'customer@mail.com', '10470c3b4b1fed12c3baac014be15fac67c6e815', '2022-09-12 10:14:03.079533'),
('3859d26cd9a5', 'Louise R. Holloman', '7856321000', 'holloman@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:12.149280'),
('57b7541814ed', 'Howard W. Anderson', '8745554589', 'howard@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 08:35:10.959590'),
('7c8f2100d552', 'Melody E. Hance', '3210145550', 'melody@mail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '2022-09-03 13:16:23.996068'),
('9c7fcc067bda', 'Delbert G. Campbell', '7850001256', 'delbert@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:56.944364'),
('9f6378b79283', 'William C. Gallup', '7145665870', 'william@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:39:26.507932'),
('d7c2db8f6cbf', 'Victor A. Pierson', '1458887896', 'victor@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:37:21.568155'),
('df9d110de610', 'Alaine', '0789751597', 'alaine@gmail.com', '63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1', '2025-02-19 04:35:10.113339'),
('e711dcc579d9', 'Julie R. Martin', '3245557896', 'julie@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:33.397498'),
('fe6bb69bdd29', 'Brian S. Boucher', '1020302055', 'brians@mail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '2022-09-03 13:16:29.591980');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_orders`
--

CREATE TABLE `rpos_orders` (
  `order_id` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `prod_id` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `prod_qty` varchar(200) NOT NULL,
  `order_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_orders`
--

INSERT INTO `rpos_orders` (`order_id`, `order_code`, `customer_id`, `customer_name`, `prod_id`, `prod_name`, `prod_price`, `prod_qty`, `order_status`, `created_at`) VALUES
('019661e097', 'AEHM-0653', '06549ea58afd', 'Ana J. Browne', 'bd200ef837', 'Turkish Coffee', '8', '1', 'Paid', '2022-09-03 13:26:00.389027'),
('28eb1ebc95', 'PNMG-4715', 'df9d110de610', 'Alaine', '06dc36c1be', 'Philly Cheesesteak', '7', '55', '', '2025-03-01 07:16:29.306607'),
('49c1bd8086', 'IUSP-9453', 'fe6bb69bdd29', 'Brian S. Boucher', 'd57cd89073', 'Country Fried Steak', '10', '1', 'Paid', '2022-09-03 11:50:40.812796'),
('514ada5047', 'OTEV-8532', '3859d26cd9a5', 'Louise R. Holloman', '0c4b5c0604', 'Spaghetti Bolognese', '15', '1', 'Paid', '2022-09-03 13:13:39.042869'),
('6466fd5ee5', 'COXP-6018', '7c8f2100d552', 'Melody E. Hance', '31dfcc94cf', 'Buffalo Wings', '11', '2', 'Paid', '2022-09-03 12:17:44.680896'),
('68fe2f698b', 'UTNI-3856', '0172be2eaea3', 'Leo', 'e2af35d095', 'Pepperoni Pizza', '7', '5000', 'Paid', '2025-02-19 12:00:47.465918'),
('80ab270866', 'JFMB-0731', '35135b319ce3', 'Christine Moore', '97972e8d63', 'Irish Coffee', '11', '1', 'Paid', '2022-09-04 16:37:03.716697'),
('87e2c88a79', 'NEQG-2046', '08f2e0229fba', 'Theogene MUPENZI', '27', 'Rice', '4.99', '8', 'Paid', '2025-03-01 07:41:52.245053'),
('8815e7edfc', 'QOEH-8613', '29c759d624f9', 'Trina L. Crowder', '2b976e49a0', 'Cheeseburger', '3', '3', 'Paid', '2022-09-03 12:02:32.985451'),
('a27f1d87be', 'EJKA-4501', '35135b319ce3', 'Christine Moore', 'ec18c5a4f0', 'Corn Dogs', '4', '2', 'Paid', '2022-09-04 16:31:54.581984'),
('a61ff273f9', 'ANJL-2361', '35135b319ce3', 'Christine Moore', '25', 'Pasta', '2.49', '4', '', '2025-03-01 08:39:23.216155'),
('a74337db7e', 'ZPXD-6951', 'e711dcc579d9', 'Julie R. Martin', 'a5931158fe', 'Pulled Pork', '8', '2', 'Paid', '2022-09-03 13:12:47.079248'),
('af52d0022d', 'FNAB-9142', '35135b319ce3', 'Christine Moore', '2fdec9bdfb', 'Jambalaya', '9', '2', 'Paid', '2022-09-04 16:32:14.949302'),
('b25c81c140', 'KVZH-2056', '35135b319ce3', 'Christine Moore', '27', 'Rice', '4.99', '1', '', '2025-03-01 07:51:30.343458'),
('b691138c76', 'JDLR-0819', '0172be2eaea3', 'Leo', '06dc36c1be', 'Philly Cheesesteak', '7', '88', 'Paid', '2025-03-01 07:05:39.111277'),
('b94abe3d0e', 'GRMF-4173', '35135b319ce3', 'Christine Moore', '23', 'Bananas', '0.69', '1', 'Paid', '2025-03-01 08:44:35.164640'),
('bbc11ec5e4', 'MTUL-7409', 'df9d110de610', 'Alaine', 'e769e274a3', 'Frappuccino', '3', '9', 'Paid', '2025-02-19 11:40:04.040044'),
('bcc311c3a2', 'RHJZ-4231', '35135b319ce3', 'Christine Moore', '22', 'Sweet Potatoes', '2.29', '3', '', '2025-03-01 08:37:50.398611'),
('bec39ab1ec', 'GTCZ-9214', '08f2e0229fba', 'Theogene MUPENZI', 'f11c6f8a82', 'Honey', '6000', '5', 'Paid', '2025-03-01 07:40:03.162136'),
('c051fc38eb', 'ONSY-2465', '57b7541814ed', 'Howard W. Anderson', '826e6f687f', 'Margherita Pizza', '12', '1', 'Paid', '2022-09-03 08:35:50.570496'),
('c6df8c3d41', 'QDPV-3961', '35135b319ce3', 'Christine Moore', '97972e8d63', 'Irish Coffee', '11', '2', '', '2025-03-01 07:51:13.362478'),
('c8cb8b5c83', 'ZWCL-9581', '35135b319ce3', 'Christine Moore', 'a5931158fe', 'Pulled Pork', '8', '22', '', '2025-03-01 08:40:10.398860'),
('c9b09ed9e9', 'WUGM-9853', '35135b319ce3', 'Christine Moore', '24', 'Strawberries', '3.99', '7', '', '2025-03-01 08:38:55.924755'),
('fc79a55455', 'INHG-0875', '9c7fcc067bda', 'Delbert G. Campbell', '3adfdee116', 'Enchiladas', '10', '1', 'Paid', '2022-09-04 16:35:22.539542');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_pass_resets`
--

CREATE TABLE `rpos_pass_resets` (
  `reset_id` int(20) NOT NULL,
  `reset_code` varchar(200) NOT NULL,
  `reset_token` varchar(200) NOT NULL,
  `reset_email` varchar(200) NOT NULL,
  `reset_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_pass_resets`
--

INSERT INTO `rpos_pass_resets` (`reset_id`, `reset_code`, `reset_token`, `reset_email`, `reset_status`, `created_at`) VALUES
(1, '63KU9QDGSO', '4ac4cee0a94e82a2aedc311617aa437e218bdf68', 'sysadmin@icofee.org', 'Pending', '2020-08-17 15:20:14.318643');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_payments`
--

CREATE TABLE `rpos_payments` (
  `pay_id` varchar(200) NOT NULL,
  `pay_code` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `pay_amt` varchar(200) NOT NULL,
  `pay_method` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_payments`
--

INSERT INTO `rpos_payments` (`pay_id`, `pay_code`, `order_code`, `customer_id`, `pay_amt`, `pay_method`, `created_at`) VALUES
('0bf592', '9UMWLG4BF8', 'EJKA-4501', '35135b319ce3', '8', 'Cash', '2022-09-04 16:31:54.525284'),
('0df5a5', '87e2c88a79', 'NEQG-2046', '08f2e0229fba', '39.92', 'Cash', '2025-03-01 07:41:52.242895'),
('19937e', 'CP8NHL5JMA', 'MTUL-7409', 'df9d110de610', '27', 'Cash', '2025-02-19 11:40:04.037382'),
('1e1705', 'b94abe3d0e', 'GRMF-4173', '35135b319ce3', '0.69', 'Cash', '2025-03-01 08:44:35.161405'),
('4423d7', 'QWERT0YUZ1', 'JFMB-0731', '35135b319ce3', '11', 'Cash', '2022-09-04 16:37:03.655834'),
('442865', '146XLFSC9V', 'INHG-0875', '9c7fcc067bda', '10', 'Paypal', '2022-09-04 16:35:22.470600'),
('65891b', 'MF2TVJA1PY', 'ZPXD-6951', 'e711dcc579d9', '16', 'Cash', '2022-09-03 13:12:46.959558'),
('75ae21', '1QIKVO69SA', 'IUSP-9453', 'fe6bb69bdd29', '10', 'Cash', '2022-09-03 11:50:40.496625'),
('7e1989', 'KLTF3YZHJP', 'QOEH-8613', '29c759d624f9', '9', 'Cash', '2022-09-03 12:02:32.926529'),
('8b7911', 'bec39ab1ec', 'GTCZ-9214', '08f2e0229fba', '30000', 'Cash', '2025-03-01 07:40:03.159761'),
('968488', '5E31DQ2NCG', 'COXP-6018', '7c8f2100d552', '22', 'Cash', '2022-09-03 12:17:44.639979'),
('984539', 'LSBNK1WRFU', 'FNAB-9142', '35135b319ce3', '18', 'Paypal', '2022-09-04 16:32:14.852482'),
('9fcee7', 'AZSUNOKEI7', 'OTEV-8532', '3859d26cd9a5', '15', 'Cash', '2022-09-03 13:13:38.855058'),
('c36fce', 'KPFN8WSL56', 'JDLR-0819', '0172be2eaea3', '616', 'Cash', '2025-03-01 07:05:39.108685'),
('c81d2e', 'WERGFCXZSR', 'AEHM-0653', '06549ea58afd', '8', 'Cash', '2022-09-03 13:26:00.331494'),
('e01e2b', 'VOJHMQK1Z4', 'INKT-0318', '06549ea58afd', '616', 'Cash', '2025-02-19 04:32:15.934944'),
('e10ce5', '24BOJSETWN', 'UTNI-3856', '0172be2eaea3', '35000', 'Cash', '2025-02-19 12:00:47.463382'),
('e46e29', 'QMCGSNER3T', 'ONSY-2465', '57b7541814ed', '12', 'Cash', '2022-09-03 08:35:50.172062');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_products`
--

CREATE TABLE `rpos_products` (
  `prod_id` varchar(200) NOT NULL,
  `prod_code` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_desc` longtext NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_products`
--

INSERT INTO `rpos_products` (`prod_id`, `prod_code`, `prod_name`, `prod_img`, `prod_desc`, `prod_price`, `created_at`) VALUES
('06dc36c1be', 'FCWU-5762', 'Philly Cheesesteak', 'cheesestk.jpg', 'A cheesesteak is a sandwich made from thinly sliced pieces of beefsteak and melted cheese in a long hoagie roll. A popular regional fast food, it has its roots in the U.S. city of Philadelphia, Pennsylvania.', '7', '2022-09-03 11:02:47.738370'),
('0c4b5c0604', 'JRZN-9518', 'Spaghetti Bolognese', 'spaghetti_bolognese.jpg', 'Spaghetti bolognese consists of spaghetti (long strings of pasta) with an Italian ragÃ¹ (meat sauce) made with minced beef, bacon and tomatoes, served with Parmesan cheese. Spaghetti bolognese is one of the most popular pasta dishes eaten outside of Italy.', '15', '2022-09-03 10:43:27.610897'),
('1', 'P001', 'Organic Apples', 'organic apples.webp', 'Fresh organic apples from local farms.', '2.99', '2025-03-01 06:37:26.394706'),
('10', 'P010', 'Chicken Breast', 'chicken breast.jpg', 'Boneless, skinless chicken breast.', '8.99', '2025-03-01 06:39:00.529531'),
('11', 'P011', 'Blueberries', 'Blueberries.jpg', 'Fresh organic blueberries, rich in antioxidants.', '4.49', '2025-03-01 06:40:57.074907'),
('12', 'P012', 'Coconut Oil', 'coconut oil.jpg', 'Cold-pressed virgin coconut oil.', '7.99', '2025-03-01 06:41:43.042765'),
('14', 'P014', 'Green Tea', 'Green Tea.jpg', 'Organic green tea, packed with antioxidants.', '5.99', '2025-03-01 06:47:22.064830'),
('14c7b6370e', 'QZHM-0391', 'Reuben Sandwich', 'reubensandwich.jpg', 'The Reuben sandwich is a North American grilled sandwich composed of corned beef, Swiss cheese, sauerkraut, and Thousand Island dressing or Russian dressing, grilled between slices of rye bread. It is associated with kosher-style delicatessens, but is not kosher because it combines meat and cheese.', '8', '2022-09-03 10:58:04.069144'),
('15', 'P015', 'Oatmeal', 'Oatmeal.jpg', 'Steel-cut oats, great for breakfast.', '3.29', '2025-03-01 06:49:14.927703'),
('16', 'P016', 'Almonds', 'Almonds.webp', 'Raw almonds, a healthy snack.', '6.49', '2025-03-01 06:50:32.633924'),
('17', 'P017', 'Tomatoes', 'Tomatoes.jpg', 'Vine-ripened tomatoes, perfect for salads.', '1.79', '2025-03-01 06:50:55.686623'),
('18', 'P018', 'Cucumbers', 'cucumbers.jpg', 'Fresh cucumbers, great for salads.', '0.99', '2025-03-01 06:51:58.155998'),
('19', 'P019', 'Bell Peppers', 'Bell Peppers.jpg', 'Assorted bell peppers, sweet and crunchy.', '2.99', '2025-03-01 06:53:04.158080'),
('1e0fa41eee', 'ICFU-1406', 'Submarine Sandwich', 'submarine_sndwh.jpg', 'A submarine sandwich, commonly known as a sub, hoagie, hero, Italian, grinder, wedge, or a spuckie, is a type of American cold or hot sandwich made from a cylindrical bread roll split lengthwise and filled with meats, cheeses, vegetables, and condiments. It has many different names.', '8', '2022-09-03 10:55:23.020144'),
('2', 'P002', 'Whole Grain Bread', 'Whole Grain Bread.jpg', 'Healthy whole grain bread, perfect for sandwiches.', '3.49', '2025-03-01 06:53:29.972006'),
('20', 'P020', 'Broccoli', 'broccoli.webp', 'Fresh broccoli, rich in vitamins.', '1.99', '2025-03-01 06:54:35.766882'),
('21', 'P021', 'Carrots', 'carrots.jpg', 'Organic carrots, great for snacking.', '1.49', '2025-03-01 06:55:09.662826'),
('22', 'P022', 'Sweet Potatoes', 'Sweet Potatoes.jpg', 'Nutritious sweet potatoes, perfect for roasting.', '2.29', '2025-03-01 06:55:43.242402'),
('23', 'P023', 'Bananas', 'buffalo_wings.jpg', 'Ripe bananas, a great source of potassium.', '0.69', '2025-03-01 06:56:54.138319'),
('24', 'P024', 'Strawberries', 'iStock-856503922-2-320x320.jpg', 'Fresh strawberries, sweet and juicy.', '3.99', '2025-03-01 06:57:10.992152'),
('25', 'P025', 'Pasta', 'coconut oil.jpg', 'Whole wheat pasta, a healthy carb option.', '2.49', '2025-03-01 06:57:57.556996'),
('26', 'P026', 'Olive Oil', 'chicnuggets.jpeg', 'Extra virgin olive oil, perfect for cooking.', '9.99', '2025-03-01 06:57:26.240569'),
('27', 'P027', 'Rice', 'Oatmeal.jpg', 'Basmati rice, a staple in many cuisines.', '4.99', '2025-03-01 06:58:13.970375'),
('28', 'P028', ' Beans', 'beans.jpg', 'Black beans, a great source of protein.', '1.29', '2025-03-01 06:33:00.159604'),
('29', 'P029', 'Granola Bars', 'turkshcoffee.jpg', 'Healthy granola bars, perfect for on-the-go.', '3.99', '2025-03-01 06:58:30.420158'),
('2b976e49a0', 'CEWV-9438', 'Cheeseburger', 'cheeseburgers.jpg', 'A cheeseburger is a hamburger topped with cheese. Traditionally, the slice of cheese is placed on top of the meat patty. The cheese is usually added to the cooking hamburger patty shortly before serving, which allows the cheese to melt. Cheeseburgers can include variations in structure, ingredients and composition.', '3', '2022-09-03 10:45:47.282634'),
('2fdec9bdfb', 'UJAK-9614', 'Jambalaya', 'Jambalaya.jpg', 'Jambalaya is an American Creole and Cajun rice dish of French, African, and Spanish influence, consisting mainly of meat and vegetables mixed with rice.', '9', '2022-09-03 10:48:49.593618'),
('3', 'P003', 'Almond Milk', 'country_fried_stk.jpg', 'Unsweetened almond milk, dairy-free.', '4.99', '2025-03-01 06:59:03.142284'),
('30', 'P030', 'Sparkling Water', 'coconut oil.jpg', 'Natural sparkling water, refreshing and hydrating.', '1.99', '2025-03-01 06:59:18.329881'),
('31dfcc94cf', 'SYQP-3710', 'Buffalo Wings', 'buffalo_wings.jpg', 'A Buffalo wing in American cuisine is an unbreaded chicken wing section that is generally deep-fried and then coated or dipped in a sauce consisting of a vinegar-based cayenne pepper hot sauce and melted butter prior to serving.', '11', '2022-09-03 10:51:09.829079'),
('3adfdee116', 'HIPF-5346', 'Enchiladas', 'enchiladas.jpg', 'An enchilada is a corn tortilla rolled around a filling and covered with a savory sauce. Originally from Mexican cuisine, enchiladas can be filled with various ingredients, including meats, cheese, beans, potatoes, vegetables, or combinations', '10', '2022-09-03 12:52:26.427554'),
('3d19e0bf27', 'EMBH-6714', 'Cincinnati Chili', 'cincinnatichili.jpg', 'Cincinnati chili is a Mediterranean-spiced meat sauce used as a topping for spaghetti or hot dogs; both dishes were developed by immigrant restaurateurs in the 1920s. In 2013, Smithsonian named one local chili parlor one of the \"20 Most Iconic Food Destinations in America\".', '9', '2022-09-03 12:57:39.265554'),
('4', 'P004', 'Free-Range Eggs', 'crabcakes.jpg', 'Organic free-range eggs, pack of 12.', '5.49', '2025-03-01 06:59:35.600723'),
('4e68e0dd49', 'QLKW-0914', 'Caramel Macchiato', '', 'Steamed milk, espresso and caramel; what could be more enticing? This blissful flavor is a favorite of coffee lovers due to its deliciously bold taste of creamy caramel and strong coffee flavor. These', '4', '2022-09-03 08:55:51.237667'),
('5', 'P005', 'Greek Yogurt', 'Green Tea.jpg', 'Creamy Greek yogurt, high in protein.', '2.79', '2025-03-01 07:00:26.340385'),
('5d66c79953', 'GOEW-9248', 'Cheese Curd', 'cheesecurd.jpg', 'Cheese curds are moist pieces of curdled milk, eaten either alone or as a snack, or used in prepared dishes. These are chiefly found in Quebec, in the dish poutine, throughout Canada, and in the northeastern, midwestern, mountain, and Pacific Northwestern United States, especially in Wisconsin and Minnesota.', '6', '2022-09-03 11:22:25.639690'),
('6', 'P006', 'Quinoa', 'cheesestk.jpg', 'Organic quinoa, great for salads and sides.', '6.99', '2025-03-01 07:00:41.470547'),
('7', 'P007', 'Avocados', 'Cucumbers.jpg', 'Ripe avocados, perfect for guacamole.', '1.99', '2025-03-01 07:00:58.461581'),
('826e6f687f', 'AYFW-2683', 'Margherita Pizza', 'margherita-pizza0.jpg', 'Pizza margherita, as the Italians call it, is a simple pizza hailing from Naples. When done right, margherita pizza features a bubbly crust, crushed San Marzano tomato sauce, fresh mozzarella and basil, a drizzle of olive oil, and a sprinkle of salt.', '12', '2022-09-03 08:02:57.213354'),
('9', 'P009', 'Spinach', 'spinach.jpg', 'Fresh organic spinach, packed with nutrients.', '2.49', '2025-03-01 06:58:50.781989'),
('97972e8d63', 'CVWJ-6492', 'Irish Coffee', 'country_fried_stk.jpg', 'Irish coffee is a caffeinated alcoholic drink consisting of Irish whiskey, hot coffee, and sugar, stirred, and topped with cream The coffee is drunk through the cream', '11', '2025-03-01 06:57:44.891004'),
('a419f2ef1c', 'EPNX-3728', 'Chicken Nugget', 'chicnuggets.jpeg', 'A chicken nugget is a food product consisting of a small piece of deboned chicken meat that is breaded or battered, then deep-fried or baked. Invented in the 1950s, chicken nuggets have become a very popular fast food restaurant item, as well as widely sold frozen for home use', '5', '2022-09-03 12:44:07.749371'),
('a5931158fe', 'ELQN-5204', 'Pulled Pork', 'pulledprk.jpeg', 'Pulled pork is an American barbecue dish, more specifically a dish of the Southern U.S., based on shredded barbecued pork shoulder. It is typically slow-smoked over wood; indoor variations use a slow cooker. The meat is then shredded manually and mixed with a sauce', '8', '2022-09-03 13:04:12.191403'),
('b2f9c250fd', 'XNWR-2768', 'Strawberry Rhubarb Pie', 'rhuharbpie.jpg', 'Rhubarb pie is a pie with a rhubarb filling. Popular in the UK, where rhubarb has been cultivated since the 1600s, and the leaf stalks eaten since the 1700s. Besides diced rhubarb, it almost always contains a large amount of sugar to balance the intense tartness of the plant', '7', '2022-09-03 13:06:28.235333'),
('bd200ef837', 'HEIY-6034', 'Turkish Coffee', 'turkshcoffee.jpg', 'Turkish coffee is a style of coffee prepared in a cezve using very finely ground coffee beans without filtering.', '8', '2022-09-03 13:09:50.234898'),
('bd616316ac', 'KNDF-2590', 'Porridge', 'anthony-delanoix-IIaNFF84-9c-unsplash.jpg', 'This is a great porridge ever !', '700', '2025-02-19 12:11:17.493005'),
('cff0cb495a', 'ZOBW-2640', 'Americano', '', 'Many espresso-based drinks use milk, but not the cafÃ© Americano â€“ or simply \'Americano\'. The drink also uses espresso but is infused with hot water instead of milk. The result is a coffee beverage ', '3', '2022-09-03 08:56:18.824990'),
('d57cd89073', 'ZGQW-9480', 'Country Fried Steak', 'country_fried_stk.jpg', 'Chicken-fried steak, also known as country-fried steak or CFS, is an American breaded cutlet dish consisting of a piece of beefsteak coated with seasoned flour and either deep-fried or pan-fried. It is sometimes associated with the Southern cuisine of the United States.', '10', '2022-09-03 11:00:05.523519'),
('d9aed17627', 'FIKD-9703', 'Crab Cake', 'crabcakes.jpg', 'A crab cake is a variety of fishcake that is popular in the United States. It is composed of crab meat and various other ingredients, such as bread crumbs, mayonnaise, mustard, eggs, and seasonings. The cake is then sautÃ©ed, baked, grilled, deep fried, or broiled.', '16', '2022-09-03 12:54:52.120847'),
('e2195f8190', 'HKCR-2178', 'Carbonara', 'carbonaraimgre.jpg', 'Carbonara is an Italian pasta dish from Rome made with eggs, hard cheese, cured pork, and black pepper. The dish arrived at its modern form, with its current name, in the middle of the 20th century. The cheese is usually Pecorino Romano, Parmigiano-Reggiano, or a combination of the two.', '16', '2022-09-03 10:23:06.266420'),
('e2af35d095', 'IDLC-7819', 'Pepperoni Pizza', 'peperopizza.jpg', 'Pepperoni is an American variety of spicy salami made from cured pork and beef seasoned with paprika or other chili pepper. Prior to cooking, pepperoni is characteristically soft, slightly smoky, and bright red. Thinly sliced pepperoni is one of the most popular pizza toppings in American pizzerias.', '7', '2022-09-03 12:49:01.017677'),
('e769e274a3', 'AHRW-3894', 'Frappuccino', 'frappuccino.jpg', 'Frappuccino is a line of blended iced coffee drinks sold by Starbucks. It consists of coffee or crÃ¨me base, blended with ice and ingredients such as flavored syrups and usually topped with whipped cream and or spices.', '3', '2022-09-03 13:11:30.109467'),
('ec18c5a4f0', 'PQFV-7049', 'Corn Dogs', 'corndog.jpg', 'A corn dog is a sausage on a stick that has been coated in a thick layer of cornmeal batter and deep fried. It originated in the United States and is commonly found in American cuisine', '4', '2022-09-03 13:00:32.787354'),
('f11c6f8a82', 'RULA-4369', 'Honey', 'honey.jpg', 'The best honey you can find on this planet is here in Rwanda-Kamonyi .', '6000', '2025-03-01 07:02:57.487760'),
('f4ce3927bf', 'EAHD-1980', 'Hot Dog', 'hotdog0.jpg', 'A hot dog is a food consisting of a grilled or steamed sausage served in the slit of a partially sliced bun. The term hot dog can also refer to the sausage itself. The sausage used is a wiener or a frankfurter. The names of these sausages also commonly refer to their assembled dish.', '4', '2022-09-03 10:53:04.965223'),
('f9c2770a32', 'YXLA-2603', 'Whipped Milk Shake', 'milkshake.jpeg', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', '8', '2022-09-03 08:54:02.727645');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_staff`
--

CREATE TABLE `rpos_staff` (
  `staff_id` int(20) NOT NULL,
  `staff_name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_staff`
--

INSERT INTO `rpos_staff` (`staff_id`, `staff_name`, `staff_number`, `staff_email`, `staff_password`, `created_at`) VALUES
(2, 'Cashier James', 'QEUY-9042', 'cashier@mail.com', '10470c3b4b1fed12c3baac014be15fac67c6e815', '2022-09-12 10:13:37.930915');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rpos_admin`
--
ALTER TABLE `rpos_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `rpos_customers`
--
ALTER TABLE `rpos_customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `CustomerOrder` (`customer_id`),
  ADD KEY `ProductOrder` (`prod_id`);

--
-- Indexes for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `rpos_payments`
--
ALTER TABLE `rpos_payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `order` (`order_code`);

--
-- Indexes for table `rpos_products`
--
ALTER TABLE `rpos_products`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  MODIFY `reset_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD CONSTRAINT `CustomerOrder` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ProductOrder` FOREIGN KEY (`prod_id`) REFERENCES `rpos_products` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
