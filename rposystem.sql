/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: rposystem
-- ------------------------------------------------------
-- Server version	11.4.5-MariaDB-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `rpos_admin`
--

DROP TABLE IF EXISTS `rpos_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_admin` (
  `admin_id` varchar(200) NOT NULL,
  `admin_name` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_password` varchar(200) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_admin`
--

LOCK TABLES `rpos_admin` WRITE;
/*!40000 ALTER TABLE `rpos_admin` DISABLE KEYS */;
INSERT INTO `rpos_admin` VALUES
('10e0b6dc958adfb5b094d8935a13aeadbe783c25','System Admin','admin@mail.com','10470c3b4b1fed12c3baac014be15fac67c6e815');
/*!40000 ALTER TABLE `rpos_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_customers`
--

DROP TABLE IF EXISTS `rpos_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_customers` (
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_customers`
--

LOCK TABLES `rpos_customers` WRITE;
/*!40000 ALTER TABLE `rpos_customers` DISABLE KEYS */;
INSERT INTO `rpos_customers` VALUES
('0172be2eaea3','Leo','0789784578','ltuyi10@gmail.com','63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1','2025-02-19 11:58:18.301583'),
('06549ea58afd','Ana J. Browne','4589698780','anaj@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:39:48.523820'),
('08f2e0229fba','Theogene MUPENZI','078978578','MUPENZI2000@GMAIL.COM','63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1','2025-03-01 07:35:01.641384'),
('1fc1f694985d','Jane Doe','2145896547','janed@mail.com','a69681bcf334ae130217fea4505fd3c994f5683f','2022-09-03 13:39:13.076592'),
('27e4a5bc74c2','Tammy R. Polley','4589654780','tammy@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:37:47.049438'),
('29c759d624f9','Trina L. Crowder','5896321002','trina@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 13:16:18.927595'),
('35135b319ce3','Christine Moore','7412569698','customer@mail.com','10470c3b4b1fed12c3baac014be15fac67c6e815','2022-09-12 10:14:03.079533'),
('3859d26cd9a5','Louise R. Holloman','7856321000','holloman@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:38:12.149280'),
('57b7541814ed','Howard W. Anderson','8745554589','howard@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 08:35:10.959590'),
('7c8f2100d552','Melody E. Hance','3210145550','melody@mail.com','a69681bcf334ae130217fea4505fd3c994f5683f','2022-09-03 13:16:23.996068'),
('9c7fcc067bda','Delbert G. Campbell','7850001256','delbert@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:38:56.944364'),
('9f6378b79283','William C. Gallup','7145665870','william@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:39:26.507932'),
('d7c2db8f6cbf','Victor A. Pierson','1458887896','victor@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:37:21.568155'),
('df9d110de610','Alaine','0789751597','alaine@gmail.com','63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1','2025-02-19 04:35:10.113339'),
('e711dcc579d9','Julie R. Martin','3245557896','julie@mail.com','55c3b5386c486feb662a0785f340938f518d547f','2022-09-03 12:38:33.397498'),
('fe6bb69bdd29','Brian S. Boucher','1020302055','brians@mail.com','a69681bcf334ae130217fea4505fd3c994f5683f','2022-09-03 13:16:29.591980');
/*!40000 ALTER TABLE `rpos_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_messages`
--

DROP TABLE IF EXISTS `rpos_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` varchar(200) DEFAULT NULL,
  `receiver_id` varchar(200) DEFAULT NULL,
  `type` enum('chat','notification','system') NOT NULL,
  `content` text NOT NULL,
  `status` enum('sent','read') DEFAULT 'sent',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_messages`
--

LOCK TABLES `rpos_messages` WRITE;
/*!40000 ALTER TABLE `rpos_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpos_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_orders`
--

DROP TABLE IF EXISTS `rpos_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(200) DEFAULT NULL,
  `order_type` enum('online','in_person') NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `status` enum('pending','packed','delivered','cancelled') DEFAULT 'pending',
  `payment_id` int(11) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `fk_orders_payment_id` (`payment_id`),
  CONSTRAINT `fk_orders_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `rpos_payments` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_orders`
--

LOCK TABLES `rpos_orders` WRITE;
/*!40000 ALTER TABLE `rpos_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpos_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_pass_resets`
--

DROP TABLE IF EXISTS `rpos_pass_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_pass_resets` (
  `reset_id` int(20) NOT NULL AUTO_INCREMENT,
  `reset_code` varchar(200) NOT NULL,
  `reset_token` varchar(200) NOT NULL,
  `reset_email` varchar(200) NOT NULL,
  `reset_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  PRIMARY KEY (`reset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_pass_resets`
--

LOCK TABLES `rpos_pass_resets` WRITE;
/*!40000 ALTER TABLE `rpos_pass_resets` DISABLE KEYS */;
INSERT INTO `rpos_pass_resets` VALUES
(1,'63KU9QDGSO','4ac4cee0a94e82a2aedc311617aa437e218bdf68','sysadmin@icofee.org','Pending','2020-08-17 15:20:14.318643');
/*!40000 ALTER TABLE `rpos_pass_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_payments`
--

DROP TABLE IF EXISTS `rpos_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `method` enum('cash','momo','airtel','stripe') NOT NULL,
  `status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `rpos_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `rpos_orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_payments`
--

LOCK TABLES `rpos_payments` WRITE;
/*!40000 ALTER TABLE `rpos_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpos_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_products`
--

DROP TABLE IF EXISTS `rpos_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_products` (
  `prod_id` varchar(200) NOT NULL,
  `prod_code` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_desc` longtext NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `qr_code` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `min_stocks` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_products`
--

LOCK TABLES `rpos_products` WRITE;
/*!40000 ALTER TABLE `rpos_products` DISABLE KEYS */;
INSERT INTO `rpos_products` VALUES
('06dc36c1be','FCWU-5762','Philly Cheesesteak','','A cheesesteak is a sandwich made from thinly sliced pieces of beefsteak and melted cheese in a long hoagie roll. A popular regional fast food, it has its roots in the U.S. city of Philadelphia, Pennsylvania.','7','2025-07-16 14:49:31.080892','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','xxzffddffd',NULL,'active',0,0),
('0c4b5c0604','JRZN-9518','Spaghetti Bolognese','spaghetti_bolognese.jpg','Spaghetti bolognese consists of spaghetti (long strings of pasta) with an Italian ragÃ¹ (meat sauce) made with minced beef, bacon and tomatoes, served with Parmesan cheese. Spaghetti bolognese is one of the most popular pasta dishes eaten outside of Italy.','15','2022-09-03 10:43:27.610897',NULL,NULL,NULL,'active',0,0),
('1','P001','Organic Apples','organic apples.webp','Fresh organic apples from local farms.','2.99','2025-03-01 06:37:26.394706',NULL,NULL,NULL,'active',0,0),
('10','P010','Chicken Breast','','Boneless, skinless chicken breast.s','8.99','2025-07-16 13:00:18.480941','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','',NULL,'active',3,10),
('11','P011','Blueberries','Blueberries.jpg','Fresh organic blueberries, rich in antioxidants.','4.49','2025-03-01 06:40:57.074907',NULL,NULL,NULL,'active',0,0),
('12','P012','Coconut Oil','coconut oil.jpg','Cold-pressed virgin coconut oil.','7.99','2025-03-01 06:41:43.042765',NULL,NULL,NULL,'active',0,0),
('14','P014','Green Tea','Green Tea.jpg','Organic green tea, packed with antioxidants.','5.99','2025-03-01 06:47:22.064830',NULL,NULL,NULL,'active',0,0),
('14c7b6370e','QZHM-0391','Reuben Sandwich','reubensandwich.jpg','The Reuben sandwich is a North American grilled sandwich composed of corned beef, Swiss cheese, sauerkraut, and Thousand Island dressing or Russian dressing, grilled between slices of rye bread. It is associated with kosher-style delicatessens, but is not kosher because it combines meat and cheese.','8','2022-09-03 10:58:04.069144',NULL,NULL,NULL,'active',0,0),
('15','P015','Oatmeal','Oatmeal.jpg','Steel-cut oats, great for breakfast.','3.29','2025-03-01 06:49:14.927703',NULL,NULL,NULL,'active',0,0),
('16','P016','Almonds','Almonds.webp','Raw almonds, a healthy snack.','6.49','2025-03-01 06:50:32.633924',NULL,NULL,NULL,'active',0,0),
('17','P017','Tomatoes','Tomatoes.jpg','Vine-ripened tomatoes, perfect for salads.','1.79','2025-03-01 06:50:55.686623',NULL,NULL,NULL,'active',0,0),
('18','P018','Cucumbers','cucumbers.jpg','Fresh cucumbers, great for salads.','0.99','2025-03-01 06:51:58.155998',NULL,NULL,NULL,'active',0,0),
('19','P019','Bell Peppers','Bell Peppers.jpg','Assorted bell peppers, sweet and crunchy.','2.99','2025-03-01 06:53:04.158080',NULL,NULL,NULL,'active',0,0),
('1e0fa41eee','ICFU-1406','Submarine Sandwich','submarine_sndwh.jpg','A submarine sandwich, commonly known as a sub, hoagie, hero, Italian, grinder, wedge, or a spuckie, is a type of American cold or hot sandwich made from a cylindrical bread roll split lengthwise and filled with meats, cheeses, vegetables, and condiments. It has many different names.','8','2022-09-03 10:55:23.020144',NULL,NULL,NULL,'active',0,0),
('2','P002','Whole Grain Bread','','Healthy whole grain bread, perfect for sandwiches.','3.49','2025-07-16 12:26:34.757300','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','','{\"name\":null,\"full_path\":null,\"type\":null,\"tmp_name\":null,\"error\":null,\"size\":null}','active',0,0),
('20','P020','Broccoli','broccoli.webp','Fresh broccoli, rich in vitamins.','1.99','2025-03-01 06:54:35.766882',NULL,NULL,NULL,'active',0,0),
('21','P021','Carrots','carrots.jpg','Organic carrots, great for snacking.','1.49','2025-03-01 06:55:09.662826',NULL,NULL,NULL,'active',0,0),
('22','P022','Sweet Potatoes','Sweet Potatoes.jpg','Nutritious sweet potatoes, perfect for roasting.','2.29','2025-03-01 06:55:43.242402',NULL,NULL,NULL,'active',0,0),
('23','P023','Bananas','buffalo_wings.jpg','Ripe bananas, a great source of potassium.','0.69','2025-03-01 06:56:54.138319',NULL,NULL,NULL,'active',0,0),
('24','P024','Strawberries','iStock-856503922-2-320x320.jpg','Fresh strawberries, sweet and juicy.','3.99','2025-03-01 06:57:10.992152',NULL,NULL,NULL,'active',0,0),
('25','P025','Pasta','coconut oil.jpg','Whole wheat pasta, a healthy carb option.','2.49','2025-03-01 06:57:57.556996',NULL,NULL,NULL,'active',0,0),
('26','P026','Olive Oil','chicnuggets.jpeg','Extra virgin olive oil, perfect for cooking.','9.99','2025-03-01 06:57:26.240569',NULL,NULL,NULL,'active',0,0),
('27','P027','Rice','Oatmeal.jpg','Basmati rice, a staple in many cuisines.','4.99','2025-03-01 06:58:13.970375',NULL,NULL,NULL,'active',0,0),
('28','P028',' Beans','beans.jpg','Black beans, a great source of protein.','1.29','2025-03-01 06:33:00.159604',NULL,NULL,NULL,'active',0,0),
('29','P029','Granola Bars','turkshcoffee.jpg','Healthy granola bars, perfect for on-the-go.','3.99','2025-03-01 06:58:30.420158',NULL,NULL,NULL,'active',0,0),
('2b976e49a0','CEWV-9438','Cheeseburger','cheeseburgers.jpg','A cheeseburger is a hamburger topped with cheese. Traditionally, the slice of cheese is placed on top of the meat patty. The cheese is usually added to the cooking hamburger patty shortly before serving, which allows the cheese to melt. Cheeseburgers can include variations in structure, ingredients and composition.','3','2022-09-03 10:45:47.282634',NULL,NULL,NULL,'active',0,0),
('2fdec9bdfb','UJAK-9614','Jambalaya','Jambalaya.jpg','Jambalaya is an American Creole and Cajun rice dish of French, African, and Spanish influence, consisting mainly of meat and vegetables mixed with rice.','9','2022-09-03 10:48:49.593618',NULL,NULL,NULL,'active',0,0),
('3','P003','Almond Milk','country_fried_stk.jpg','Unsweetened almond milk, dairy-free.','4.99','2025-03-01 06:59:03.142284',NULL,NULL,NULL,'active',0,0),
('30','P030','Sparkling Water','coconut oil.jpg','Natural sparkling water, refreshing and hydrating.','1.99','2025-03-01 06:59:18.329881',NULL,NULL,NULL,'active',0,0),
('31dfcc94cf','SYQP-3710','Buffalo Wings','buffalo_wings.jpg','A Buffalo wing in American cuisine is an unbreaded chicken wing section that is generally deep-fried and then coated or dipped in a sauce consisting of a vinegar-based cayenne pepper hot sauce and melted butter prior to serving.','11','2022-09-03 10:51:09.829079',NULL,NULL,NULL,'active',0,0),
('3adfdee116','HIPF-5346','Enchiladas','enchiladas.jpg','An enchilada is a corn tortilla rolled around a filling and covered with a savory sauce. Originally from Mexican cuisine, enchiladas can be filled with various ingredients, including meats, cheese, beans, potatoes, vegetables, or combinations','10','2022-09-03 12:52:26.427554',NULL,NULL,NULL,'active',0,0),
('3d19e0bf27','EMBH-6714','Cincinnati Chili','cincinnatichili.jpg','Cincinnati chili is a Mediterranean-spiced meat sauce used as a topping for spaghetti or hot dogs; both dishes were developed by immigrant restaurateurs in the 1920s. In 2013, Smithsonian named one local chili parlor one of the \"20 Most Iconic Food Destinations in America\".','9','2022-09-03 12:57:39.265554',NULL,NULL,NULL,'active',0,0),
('3f742c8b05','RJVQ-6908','Whole Grain Bread','photo-1541348263662-e068662d82af.avif','shhzfjcbmnxb jcnx dfjcxnm ddc','1000','2025-07-16 14:48:38.686582','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','Beverages','[\"photo-1541348263662-e068662d82af.avif\",\"photo-1627161683077-e34782c24d81.avif\",\"Screenshot_2025-07-12_11_27_23.png\",\"Screenshot_2025-07-12_11_27_45.png\"]','active',5,50),
('4','P004','Free-Range Eggs','crabcakes.jpg','Organic free-range eggs, pack of 12.','5.49','2025-03-01 06:59:35.600723',NULL,NULL,NULL,'active',0,0),
('4e68e0dd49','QLKW-0914','Caramel Macchiato','','Steamed milk, espresso and caramel; what could be more enticing? This blissful flavor is a favorite of coffee lovers due to its deliciously bold taste of creamy caramel and strong coffee flavor. These','4','2022-09-03 08:55:51.237667',NULL,NULL,NULL,'active',0,0),
('5','P005','Greek Yogurt','Green Tea.jpg','Creamy Greek yogurt, high in protein.','2.79','2025-03-01 07:00:26.340385',NULL,NULL,NULL,'active',0,0),
('5d66c79953','GOEW-9248','Cheese Curd','cheesecurd.jpg','Cheese curds are moist pieces of curdled milk, eaten either alone or as a snack, or used in prepared dishes. These are chiefly found in Quebec, in the dish poutine, throughout Canada, and in the northeastern, midwestern, mountain, and Pacific Northwestern United States, especially in Wisconsin and Minnesota.','6','2022-09-03 11:22:25.639690',NULL,NULL,NULL,'active',0,0),
('6d0c387fbd','WQAB-1483','ssdds','Screenshot_2025-07-12_11_27_45.png','sas','221','2025-07-16 11:48:22.628036',NULL,NULL,NULL,'active',0,0),
('7','P007','Avocados','Cucumbers.jpg','Ripe avocados, perfect for guacamole.','1.99','2025-03-01 07:00:58.461581',NULL,NULL,NULL,'active',0,0),
('7331492f9a','YRIC-2583','sdsds','Screenshot_2025-07-12_11_27_45.png','dsdssdsddssdds','dsdds','2025-07-16 11:55:03.016921','assets/img/products/qr/YRIC-2583.png',NULL,NULL,'active',0,0),
('826e6f687f','AYFW-2683','Margherita Pizza','margherita-pizza0.jpg','Pizza margherita, as the Italians call it, is a simple pizza hailing from Naples. When done right, margherita pizza features a bubbly crust, crushed San Marzano tomato sauce, fresh mozzarella and basil, a drizzle of olive oil, and a sprinkle of salt.','12','2022-09-03 08:02:57.213354',NULL,NULL,NULL,'active',0,0),
('9','P009','Spinach','spinach.jpg','Fresh organic spinach, packed with nutrients.','2.49','2025-03-01 06:58:50.781989',NULL,NULL,NULL,'active',0,0),
('97972e8d63','CVWJ-6492','Irish Coffee','country_fried_stk.jpg','Irish coffee is a caffeinated alcoholic drink consisting of Irish whiskey, hot coffee, and sugar, stirred, and topped with cream The coffee is drunk through the cream','11','2025-03-01 06:57:44.891004',NULL,NULL,NULL,'active',0,0),
('a419f2ef1c','EPNX-3728','Chicken Nugget','chicnuggets.jpeg','A chicken nugget is a food product consisting of a small piece of deboned chicken meat that is breaded or battered, then deep-fried or baked. Invented in the 1950s, chicken nuggets have become a very popular fast food restaurant item, as well as widely sold frozen for home use','5','2022-09-03 12:44:07.749371',NULL,NULL,NULL,'active',0,0),
('a5931158fe','ELQN-5204','Pulled Pork','pulledprk.jpeg','Pulled pork is an American barbecue dish, more specifically a dish of the Southern U.S., based on shredded barbecued pork shoulder. It is typically slow-smoked over wood; indoor variations use a slow cooker. The meat is then shredded manually and mixed with a sauce','8','2022-09-03 13:04:12.191403',NULL,NULL,NULL,'active',0,0),
('b2f9c250fd','XNWR-2768','Strawberry Rhubarb Pie','rhuharbpie.jpg','Rhubarb pie is a pie with a rhubarb filling. Popular in the UK, where rhubarb has been cultivated since the 1600s, and the leaf stalks eaten since the 1700s. Besides diced rhubarb, it almost always contains a large amount of sugar to balance the intense tartness of the plant','7','2022-09-03 13:06:28.235333',NULL,NULL,NULL,'active',0,0),
('b5ea97f51c','DXWK-6190','Whole Grain Bread','photo-1541348263662-e068662d82af.avif','This will help users easily identify and fill in all required fields before submitting the form. If you need further improvements or want this on other forms, let me know!','5000','2025-07-16 12:45:40.107721','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','medical','[\"Screenshot_2025-07-12_11_27_23.png\",\"Screenshot_2025-07-12_11_27_45.png\",\"Screenshot_2025-07-12_11_27_59.png\",\"Screenshot_2025-07-14_21_41_30.png\"]','active',2,19),
('bd200ef837','HEIY-6034','Turkish Coffee','turkshcoffee.jpg','Turkish coffee is a style of coffee prepared in a cezve using very finely ground coffee beans without filtering.','8','2022-09-03 13:09:50.234898',NULL,NULL,NULL,'active',0,0),
('bd616316ac','KNDF-2590','Porridge','anthony-delanoix-IIaNFF84-9c-unsplash.jpg','This is a great porridge ever !','700','2025-02-19 12:11:17.493005',NULL,NULL,NULL,'active',0,0),
('c7b0421c26','EVRS-5814','Whole Grain Bread','photo-1541348263662-e068662d82af.avif','You can now add products with a proper category from a predefined list on both','1000','2025-07-16 14:21:31.009455','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','Snacks','[\"Screenshot_2025-07-12_11_27_59.png\"]','active',10,100),
('cff0cb495a','ZOBW-2640','Americano','','Many espresso-based drinks use milk, but not the cafÃ© Americano â€“ or simply \'Americano\'. The drink also uses espresso but is infused with hot water instead of milk. The result is a coffee beverage ','3','2022-09-03 08:56:18.824990',NULL,NULL,NULL,'active',0,0),
('d57cd89073','ZGQW-9480','Country Fried Steak','country_fried_stk.jpg','Chicken-fried steak, also known as country-fried steak or CFS, is an American breaded cutlet dish consisting of a piece of beefsteak coated with seasoned flour and either deep-fried or pan-fried. It is sometimes associated with the Southern cuisine of the United States.','10','2022-09-03 11:00:05.523519',NULL,NULL,NULL,'active',0,0),
('d9aed17627','FIKD-9703','Crab Cake','crabcakes.jpg','A crab cake is a variety of fishcake that is popular in the United States. It is composed of crab meat and various other ingredients, such as bread crumbs, mayonnaise, mustard, eggs, and seasonings. The cake is then sautÃ©ed, baked, grilled, deep fried, or broiled.','16','2022-09-03 12:54:52.120847',NULL,NULL,NULL,'active',0,0),
('e2195f8190','HKCR-2178','Carbonara','carbonaraimgre.jpg','Carbonara is an Italian pasta dish from Rome made with eggs, hard cheese, cured pork, and black pepper. The dish arrived at its modern form, with its current name, in the middle of the 20th century. The cheese is usually Pecorino Romano, Parmigiano-Reggiano, or a combination of the two.','16','2022-09-03 10:23:06.266420',NULL,NULL,NULL,'active',0,0),
('e2af35d095','IDLC-7819','Pepperoni Pizza','peperopizza.jpg','Pepperoni is an American variety of spicy salami made from cured pork and beef seasoned with paprika or other chili pepper. Prior to cooking, pepperoni is characteristically soft, slightly smoky, and bright red. Thinly sliced pepperoni is one of the most popular pizza toppings in American pizzerias.','7','2022-09-03 12:49:01.017677',NULL,NULL,NULL,'active',0,0),
('e769e274a3','AHRW-3894','Frappuccino','frappuccino.jpg','Frappuccino is a line of blended iced coffee drinks sold by Starbucks. It consists of coffee or crÃ¨me base, blended with ice and ingredients such as flavored syrups and usually topped with whipped cream and or spices.','3','2022-09-03 13:11:30.109467',NULL,NULL,NULL,'active',0,0),
('ec18c5a4f0','PQFV-7049','Corn Dogs','corndog.jpg','A corn dog is a sausage on a stick that has been coated in a thick layer of cornmeal batter and deep fried. It originated in the United States and is commonly found in American cuisine','4','2022-09-03 13:00:32.787354',NULL,NULL,NULL,'active',0,0),
('f11c6f8a82','RULA-4369','Honey','honey.jpg','The best honey you can find on this planet is here in Rwanda-Kamonyi .','6000','2025-03-01 07:02:57.487760',NULL,NULL,NULL,'active',0,0),
('f4ce3927bf','EAHD-1980','Hot Dog','hotdog0.jpg','A hot dog is a food consisting of a grilled or steamed sausage served in the slit of a partially sliced bun. The term hot dog can also refer to the sausage itself. The sausage used is a wiener or a frankfurter. The names of these sausages also commonly refer to their assembled dish.','4','2025-07-16 12:46:28.454979',NULL,NULL,NULL,'inactive',0,0),
('f9c2770a32','YXLA-2603','Whipped Milk Shake','milkshake.jpeg','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,','8','2025-07-16 12:46:41.741260',NULL,NULL,NULL,'inactive',0,0),
('fa5b77b2bd','TOMN-7935g','sdsds','photo-1541348263662-e068662d82af.avif','dsdsdsdddsdsddsds','221','2025-07-16 12:30:31.552240','PHN2ZyBpZD0iYmFyY29kZSI+PC9zdmc+','fgg','{\"name\":null,\"full_path\":null,\"type\":null,\"tmp_name\":null,\"error\":null,\"size\":null}','active',3,30);
/*!40000 ALTER TABLE `rpos_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_staff`
--

DROP TABLE IF EXISTS `rpos_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_staff` (
  `staff_id` int(20) NOT NULL AUTO_INCREMENT,
  `staff_name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_staff`
--

LOCK TABLES `rpos_staff` WRITE;
/*!40000 ALTER TABLE `rpos_staff` DISABLE KEYS */;
INSERT INTO `rpos_staff` VALUES
(2,'Cashier James','QEUY-9042','cashier@mail.com','10470c3b4b1fed12c3baac014be15fac67c6e815','2022-09-12 10:13:37.930915'),
(4,'Amani','GUZY-1870','amani@mail.com','10470c3b4b1fed12c3baac014be15fac67c6e815','2025-07-16 12:59:16.373971');
/*!40000 ALTER TABLE `rpos_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpos_wishlists`
--

DROP TABLE IF EXISTS `rpos_wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rpos_wishlists` (
  `wishlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(200) NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`wishlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpos_wishlists`
--

LOCK TABLES `rpos_wishlists` WRITE;
/*!40000 ALTER TABLE `rpos_wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpos_wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-07-16 16:51:37
