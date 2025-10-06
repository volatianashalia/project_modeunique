-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 05, 2025 at 06:02 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mode_unique`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `notes` text NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `full_name`, `email`, `phone`, `service`, `date`, `time`, `created_at`, `notes`, `status`) VALUES
(1, 4, 'Ram Nambinintsoa', 'ramshalia@gmail.com', '+261 34 63 460 50', 'consultation', '2025-10-04', '09:30:00', '2025-10-02 09:26:31', '', 'confirmed'),
(2, 4, 'Ram Nambinintsoa', 'ramshalia@gmail.com', '+261 34 63 460 50', 'prise_mesures', '2025-10-07', '10:00:00', '2025-10-02 09:38:58', '', 'confirmed'),
(3, 7, 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'essayage', '2025-10-02', '16:00:00', '2025-10-02 15:51:01', '', 'cancelled'),
(4, 7, 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'prise_mesures', '2025-10-03', '09:00:00', '2025-10-02 21:43:19', '', 'cancelled'),
(5, 7, 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'prise_mesures', '2025-10-04', '09:00:00', '2025-10-04 11:49:53', '', 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `Name`, `image`) VALUES
(1, 'Robes', 'assets/images/categories/category_1_1759432316.jpg'),
(3, 'Hauts', 'assets/images/categories/category_3_1759432594.jpg'),
(4, 'Pantalons', 'assets/images/categories/category_4_1759433043.jpg'),
(5, 'Ensembles', 'assets/images/categories/category_5_1759433929.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('nouveau','lu','traité') DEFAULT 'nouveau',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Little', 'Girl', 'ramshalia@gmail.com', '+261 34 63 460 50', 'Question sur un produit', 'quels est le tissu utiliser pour ce modele', 'lu', '2025-10-02 10:43:56'),
(2, 'Volatiana Shalia', 'Ramarosata', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'Autre', 'Bonjour admin , how are you?', 'lu', '2025-10-02 18:53:02');

-- --------------------------------------------------------

--
-- Table structure for table `creations`
--

DROP TABLE IF EXISTS `creations`;
CREATE TABLE IF NOT EXISTS `creations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `creations`
--

INSERT INTO `creations` (`id`, `title`, `image`, `created_at`, `description`) VALUES
(3, 'Robe de Cérémonie', 'images/creations/68e0f8e8128c8.jpeg', '2025-09-29 16:20:11', 'Cette pièce maîtresse se distingue par son corsage ajusté en V orné de plissés délicats qui sculptent et affinent la silhouette. L\'épaule est mise en valeur par un détail floral tridimensionnel, un appliqué de fleur raffiné qui apporte une touche de romantisme et d\'originalité.\r\nLa jupe évasée (coupe A-line), d\'une longueur majestueuse, tombe en plis fluides et aériens, assurant un mouvement gracieux et une allure royale. Parfaite pour un bal, un gala ou une cérémonie spéciale, cette robe incarne l\'élégance intemporelle et le glamour.'),
(4, 'Ensemble Coordonné Jupe Longue et Crop Top', 'images/68e0f9e7cfab3.jpeg', '2025-10-04 13:41:43', 'Découvrez notre ravissant Ensemble Coordonné incarnant l\'esprit de l\'été et de la légèreté. Confectionné dans un tissu de couleur crème ou jaune très pâle, cet ensemble est dynamisé par un imprimé floral graphique dans des nuances de bleu saphir et turquoise.\r\nLe haut est un Crop Top à l\'encolure élastiquée (type bardot ou bateau) avec des manches courtes bouffantes, offrant un look décontracté et féminin.\r\n\r\nLa jupe longue assortie est structurée par de volants étagés (jupe à étages), qui lui confèrent un volume doux et un mouvement aérien. Sa taille haute assure une silhouette flatteuse et un confort optimal.\r\nIdéal pour les sorties estivales, les journées ensoleillées ou les vacances, cet ensemble est une célébration de la mode joyeuse et décontractée.'),
(6, 'Ensemble Palazzo et Crop Top Brodé', 'images/68e0faba7d31d.jpeg', '2025-10-04 13:45:14', 'Découvrez notre Ensemble Coordonné dans une teinte bleu ciel douce et tendance, parfait pour un look bohème-chic raffiné.\r\nLe haut est un Crop Top ajusté à fines bretelles, caractérisé par un empiècement de broderie anglaise et des détails texturés qui ajoutent un charme artisanal.\r\nLa pièce maîtresse est son pantalon palazzo spectaculaire. Sa taille haute allonge la silhouette, tandis que sa coupe large et fluide assure un mouvement élégant et un confort absolu, idéal par temps chaud. Le pantalon est orné de bandes de broderie anglaise ou de dentelle incrustées horizontalement, créant un effet de volants subtil et aéré.\r\nC\'est la tenue idéale pour une escapade estivale, un événement en plein air ou une journée de détente avec une touche de sophistication.'),
(7, 'Ensemble Chemisier Manches Ballons et Jupe Asymétrique', 'images/68e0fcc6010d3.jpeg', '2025-10-04 13:53:58', 'Découvrez notre Ensemble Coordonné dans une teinte de beige naturel ou lin clair, parfait pour un style décontracté et chic. Confectionné dans un tissu léger (inspiré du lin ou du coton), cet ensemble est l\'incarnation du confort estival.\r\nLe haut est un Chemisier Léger à la coupe ample et courte. Il est doté d\'un décolleté en V subtil et d\'une patte de boutonnage partielle. Ses manches longues à effet ballon (ou bouffantes) avec poignets élastiqués ajoutent une touche de volume et de romantisme.\r\nLa jupe midi assortie présente une silhouette fluide et élégante, mise en valeur par une coupe asymétrique à volant qui crée un mouvement dynamique et gracieux.\r\nIdéal pour les journées de détente, les sorties en bord de mer ou un look de vacances décontracté, cet ensemble offre une élégance simple et naturelle.'),
(8, 'Ensemble Tailleur Pantalon et Haut Peplum Bicolore', 'images/68e100a38eaff.jpeg', '2025-10-04 14:10:27', 'Découvrez notre Ensemble Tailleur Pantalon d\'une élégance moderne et graphique. Cet ensemble bicolore, en blanc cassé ou crème, est rehaussé d\'un passepoil noir contrastant qui souligne magnifiquement la structure de la coupe.\r\nLe haut est un peplum sans manches à la fois structuré et flatteur. Le passepoil noir met en évidence le décolleté asymétrique (coupe portefeuille) et les volants peplum étagés, qui cintrèrent la taille et ajoutent un mouvement chic.\r\nLe pantalon assorti est un pantalon à jambe large et taille haute, offrant une silhouette allongée et raffinée, digne d\'un look de bureau haut de gamme ou d\'un cocktail.\r\nCet ensemble est la définition du chic minimaliste, parfait pour les réunions professionnelles, les événements semi-formels ou toute occasion nécessitant une touche de sophistication contemporaine.'),
(9, 'Robe Midi Décontractée-Chic à Manches Ballons', 'images/68e1010539852.jpeg', '2025-10-04 14:12:05', 'Découvrez notre Robe Midi qui allie sophistication et confort. Confectionnée dans un tissu au fini légèrement froissé ou texturé, sa teinte beige camel clair ou nude est à la fois intemporelle et polyvalente.\r\nCette robe se distingue par son haut à décolleté en V et ses manches longues volumineuses (effet ballon/bouffant), qui apportent une touche de dramatisme chic. La silhouette est magnifiquement sculptée par une large bande de ceinture intégrée et plissée, qui cintre la taille pour un effet amincissant spectaculaire.\r\nLa jupe, d\'une longueur midi élégante, suit une coupe évasée (légère A-line) et présente une fente subtile sur le côté, ajoutant une touche de fluidité et de mouvement.\r\nParfaite pour les événements semi-formels, les brunchs ou une tenue de bureau raffinée, cette robe est une pièce maîtresse pour une allure moderne et féminine.'),
(10, 'Robe de Soirée Fourreau avec Manches Ailes', 'images/68e1018be25e5.jpeg', '2025-10-04 14:14:19', 'Découvrez notre Robe Longue Fourreau d\'une élégance minimaliste et théâtrale. Confectionnée dans un tissu au fini satiné et légèrement extensible, sa couleur blanc pur ou blanc cassé illumine la silhouette.\r\nCette pièce se distingue par une coupe fourreau ajustée qui souligne gracieusement les courbes. Son décolleté est simple et rond, mais son impact réside dans ses manches : de longues manches moulantes qui se transforment en panneaux de tissu fluides (appelés manches ailes ou ailes de chauve-souris) qui tombent majestueusement jusqu\'au sol.\r\nCe design crée un effet de cape dramatique à chaque mouvement, conférant une allure puissante et royale. Parfaite pour un gala, un mariage (pour une invitée ou une cérémonie civile), ou un événement de tapis rouge.'),
(11, 'Robe de Soirée Fourreau avec Cape Fluide Intégrée', 'images/68e1024f34b6e.jpeg', '2025-10-04 14:17:35', 'Découvrez notre Robe de Soirée Fourreau dans un rouge écarlate intense et passionné, une pièce qui garantit une entrée remarquée.\r\nLa robe présente une coupe fourreau ajustée, épousant les courbes de la silhouette avec une élégance intemporelle. L\'impact visuel vient de sa cape drapée en mousseline ou en tissu léger assorti. Ce drapé commence au niveau de l\'épaule et se prolonge en deux pans fluides qui tombent majestueusement jusqu\'au sol.\r\nLa conception unique du haut, qui crée un effet de manche longue fendue et de châle intégré, apporte une dimension à la fois sophistiquée et mystérieuse. La fluidité de la cape contraste avec la coupe ajustée de la robe, créant une allure dramatique et royale.\r\nIdéale pour un gala, un mariage de prestige ou tout événement nécessitant un look de tapis rouge.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('carte','paypal','virement','especes') NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `payment_method`, `payment_status`, `full_name`, `email`, `phone`, `address`, `city`, `postal_code`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 'CMD-20250929-4EE552', 13000000.00, 'cancelled', 'especes', 'pending', 'Little Girl', 'littlegirl@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-09-29 04:51:48', '2025-10-04 08:55:30'),
(2, 3, 'CMD-20250929-D2F2F3', 13000000.00, 'cancelled', 'especes', 'pending', 'Little Girl', 'littlegirl@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-09-29 04:52:13', '2025-10-04 08:55:25'),
(3, 3, 'CMD-20250929-B00757', 1000000.00, 'cancelled', 'especes', 'pending', 'Little Girl', 'littlegirl@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', 'Livraison', '2025-09-29 10:17:15', '2025-10-04 08:55:20'),
(4, 3, 'CMD-20250929-522ACC', 2000000.00, 'cancelled', 'especes', 'pending', 'Little Girl', 'littlegirl@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', 'Livraison 2', '2025-09-29 10:23:49', '2025-09-29 10:31:58'),
(5, 7, 'CMD-20251002-BE14AD', 2000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 12:49:15', '2025-10-04 08:52:45'),
(6, 7, 'CMD-20251002-E4F74E', 2000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 17:27:58', '2025-10-04 08:52:51'),
(7, 7, 'CMD-20251002-44CAA5', 1000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 17:30:44', '2025-10-04 08:52:56'),
(8, 7, 'CMD-20251002-5804D6', 1000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 17:38:45', '2025-10-04 08:53:02'),
(9, 7, 'CMD-20251002-FEC328', 1000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 18:25:51', '2025-10-04 08:53:07'),
(10, 7, 'CMD-20251002-CEFFBC', 1000000.00, 'cancelled', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-02 18:38:04', '2025-10-04 08:52:40'),
(11, 7, 'CMD-20251004-BD6316', 30000.00, 'delivered', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-04 08:52:11', '2025-10-05 13:40:45'),
(12, 3, 'CMD-20251004-071E9E', 30000.00, 'cancelled', 'especes', 'pending', 'Little Girl', 'littlegirl@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', '', '2025-10-04 08:54:56', '2025-10-04 08:55:15'),
(13, 7, 'CMD-20251004-46E8CC', 30000.00, 'delivered', 'especes', 'pending', 'Ramarosata Volatiana Shalia', 'ramarosatashalia@gmail.com', '+261 34 63 460 50', 'VK3PU Morarano Andrangaranga', 'Anananarivo', '101', 'Bien emballé le colis s\'il vous plait.', '2025-10-04 11:44:20', '2025-10-04 13:10:23');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 5, 'Robes de ceremonies', 3, 2000000.00, 6000000.00, '2025-09-29 04:51:48'),
(2, 2, 5, 'Robes de ceremonies', 3, 2000000.00, 6000000.00, '2025-09-29 04:52:13'),
(3, 3, 4, 'Robe de ceremonie', 1, 1000000.00, 1000000.00, '2025-09-29 10:17:15'),
(4, 4, 5, 'Robes de ceremonies', 1, 2000000.00, 2000000.00, '2025-09-29 10:23:49'),
(5, 5, 5, 'Robes de ceremonies', 1, 2000000.00, 2000000.00, '2025-10-02 12:49:15'),
(6, 6, 5, 'Robes de ceremonies', 1, 2000000.00, 2000000.00, '2025-10-02 17:27:58'),
(7, 7, 3, 'Robe de ceremonie', 1, 1000000.00, 1000000.00, '2025-10-02 17:30:44'),
(8, 8, 3, 'Robe de ceremonie', 1, 1000000.00, 1000000.00, '2025-10-02 17:38:45'),
(9, 9, 4, 'Robe de ceremonie', 1, 1000000.00, 1000000.00, '2025-10-02 18:25:51'),
(10, 10, 4, 'Robe de ceremonie', 1, 1000000.00, 1000000.00, '2025-10-02 18:38:04'),
(11, 11, 20, 'Pantalon large à taille haute', 1, 30000.00, 30000.00, '2025-10-04 08:52:11'),
(12, 12, 20, 'Pantalon large à taille haute', 1, 30000.00, 30000.00, '2025-10-04 08:54:56'),
(13, 13, 20, 'Pantalon large à taille haute', 1, 30000.00, 30000.00, '2025-10-04 11:44:20');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `description` text,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `created_at`, `description`, `price`, `stock`, `size`, `category_id`) VALUES
(8, 'Robe longue bicolore style bohème-chic', 'images/products/68dfb8ee60594.jpg', '2025-10-03 14:52:14', 'Robe longue bicolore en coton léger – idéale pour allier confort et élégance ✨\r\n\r\n💛 Haut beige avec manches volantées\r\n🤎 Bas marron à volants fluides\r\n👗 Coupe cintrée à la taille, évasée pour un tombé parfait\r\n\r\nParfaite pour vos sorties, soirées ou événements spéciaux 💃', 100000.00, 2, 'S,M', 1),
(9, 'Robe midi élégante', 'images/products/68dfba7a3b89e.jpg', '2025-10-03 14:58:50', 'Design chic : Décolleté carré tendance et manches courtes.\r\nCoupe flatteuse : Cintrée à la taille, avec une jupe fluide qui met en valeur la silhouette.\r\nLongueur midi : Élégante et confortable, parfaite pour le quotidien ou les événements.\r\nTissu : En lin doux (respirant et agréable à porter).\r\nCouleur : Beige crème, une teinte neutre et sophistiquée facile à accessoiriser.', 90000.00, 3, 'S,M,L', 1),
(10, 'Robe midi élégante', 'images/products/68dfbbb8d2461.jpg', '2025-10-03 15:04:08', 'Col asymétrique drapé pour un style unique et chic\r\n\r\nCoupe cintrée à la taille qui met en valeur la silhouette\r\n\r\nJupe évasée longueur midi pour une allure féminine et élégante\r\nTissu : Satin duchesse, offrant un tombé fluide et brillant\r\nCouleur : Jaune moutarde lumineux, parfait pour se démarquer\r\nDétails pratiques : Poches discrètes sur les côtés', 90000.00, 2, 'M,L', 1),
(11, 'Robe chemise sans manches', 'images/products/68e011053754b.jpg', '2025-10-03 21:08:05', 'Design chic : Col chemise structuré et style sans manches pour une allure estivale.\r\nCoupe flatteuse : Cintrée à la taille et évasée (A-line), créant une silhouette élégante.\r\nDétail clé : Patte de boutonnage complète sur le devant avec des boutons blancs contrastants.\r\nLongueur midi : Élégante et polyvalente, s\'arrêtant sous le genou.\r\nTissu :  crêpe\r\nCouleur : Rouge vif , une teinte audacieuse et accrocheuse.', 90000.00, 2, '[]', 1),
(12, 'Robe maxi boutonnée sans manches', 'images/products/68e012f5ac91b.jpg', '2025-10-03 21:16:21', 'Design chic : Encolure haute et dos nu (probable) avec un style sans manches pour une allure épurée et élégante.\r\nCoupe flatteuse : Très cintrée au niveau du buste et de la taille, avec une jupe droite ou légèrement évasée qui allonge la silhouette.\r\nDétail clé : Patte de boutonnage complète sur le devant ornée de grands boutons dorés ou argentés nacrés qui créent un contraste frappant.\r\nLongueur maxi : Très longue, s\'arrêtant aux chevilles ou au sol, idéale pour les événements ou les soirées.\r\nFonctionnalité : Possède des poches latérales discrètes, également ornées de boutons décoratifs sur l\'ouverture.\r\nTissu : Crêpe lourd \r\nCouleur : Noir classique, une teinte intemporelle et sophistiquée.', 80000.00, 2, 'L,XL', 1),
(13, 'Robe longue volantée (maxi) sans manches', 'images/products/68e015b16cffd.jpg', '2025-10-03 21:28:01', 'Design chic : Encolure ras-du-cou (col rond) et un style sans manches élégant.\r\nCoupe flatteuse : Très cintrée au niveau du buste et de la taille, avec une jupe ample et fluide qui crée un mouvement magnifique.\r\nDétail clé : La jupe se termine par un volant (ou ourlet volanté) qui ajoute du volume et une touche romantique.\r\nLongueur maxi : Très longue, idéale pour les vacances, les événements de jour ou les tenues de soirée décontractées.\r\nTissu : Coton léger\r\nCouleur : Rouge orangé (ou corail vif), une teinte chaleureuse et lumineuse, parfaite pour l\'été.', 75000.00, 3, 'L,XL,XXL', 1),
(14, 'Top sans manches asymétrique', 'images/products/68e017625ef57.jpg', '2025-10-03 21:35:14', 'Design chic : Encolure asymétrique avec une découpe ou un col déstructuré, ajoutant une touche moderne et graphique.\r\nCoupe flatteuse : Coupe ajustée ou légèrement cintrée au niveau du buste et de la taille.\r\nDétail clé : L\'ourlet est asymétrique et fendu sur le côté (split hem), avec une pointe sur le devant. Un léger drapé ou des fronces se trouvent sur le côté, juste avant la fente.\r\nManches : Sans manches (coupe débardeur ou muscle tee).\r\nTissu : Un jersey .\r\nCouleur : Bordeaux \r\nStyle : Idéal pour un look décontracté-chic, porté rentré ou non, comme sur la photo avec un jean.', 25000.00, 2, 'S,M', 3),
(15, 'Top de style officier/militaire chic.', 'images/products/68e01c2ad0038.jpg', '2025-10-03 21:55:38', 'Design chic : Col montant (ou col cheminée) pour un look sophistiqué et structuré, avec un style sans manches.\r\nCoupe flatteuse : Très cintrée au buste et à la taille, avec une coupe de type peplum ou une légère basque qui s\'évase sur les hanches, créant une silhouette en sablier.\r\nDétail clé : Patte de boutonnage verticale ornée de grands boutons dorés ou nacrés qui rappellent les uniformes. Deux fausses poches sont également décorées des mêmes boutons au niveau des hanches.\r\nFermeture : Le haut se ferme par les boutons jusqu\'au col, mais il est porté ici légèrement ouvert au bas pour dévoiler le ventre.\r\nTissu : Crêpe lourd.\r\nCouleur : Noir intense et classique.\r\nStyle : Un haut audacieux et élégant, parfait pour une tenue de soirée ou un look de bureau très chic, porté avec un pantalon ajusté ou une jupe crayon.', 30000.00, 2, 'XS,S', 3),
(16, 'Corset à col Bardot/carré romantique.', 'images/products/68e01ee16896b.jpg', '2025-10-03 22:07:13', 'Design chic : Encolure carrée ou Bardot (peut être portée sur ou sous les épaules), ornée de volants et d\'une petite dentelle. Un lien à nouer au cou est également présent, soulignant le style romantique.\r\nCoupe flatteuse : Coupe très cintrée et façon corset au niveau du buste et de la taille (bustier désossé ou style corset), avec un ourlet pointu (basque) à la taille.\r\nManches : Manches longues et volumineuses (bouffantes) en tissu transparent, resserrées par un élastique smocké au niveau des poignets pour créer un volant évasé.\r\n\r\nDétail clé : Patte de boutonnage verticale sur le devant de la partie bustier et un lien à nouer sur la poitrine, ajoutant une touche délicate.\r\nTissu : Mousseline de soie .\r\nCouleur : Terre cuite, rouille ou marron chaud, une teinte riche et tendance.\r\nStyle : Un haut très féminin et audacieux, parfait pour les sorties, les événements ou un look de soirée.', 30000.00, 1, 'M', 3),
(17, 'Top bustier croisé', 'images/products/68e01f5d4d412.jpg', '2025-10-03 22:09:17', 'Design chic : Encolure bustier (sans bretelles) avec une coupe cœur (sweetheart neckline) créée par le drapé.\r\nCoupe flatteuse : Coupe ajustée et courte (crop top), épousant la forme du corps.\r\nDétail clé : Un nœud ou une torsade complexe au centre du buste, créant un effet drapé élégant et structuré. L\'ourlet est asymétrique avec des pointes sur le devant.\r\nTissu :Polyester.\r\nCouleur : Rose poudré ou rose bébé, une teinte douce et féminine.\r\nStyle : Un haut parfait pour les sorties estivales, les soirées, ou un look de vacances, porté avec des bas à taille haute pour un look moderne.', 25000.00, 2, 'S,M', 3),
(18, 'Crop top à bretelles volantées', 'images/products/68e022d7222d7.jpg', '2025-10-03 22:24:07', 'Design chic : Encolure cœur (sweetheart neckline) et un style court (crop top), mettant en valeur le décolleté et la taille.\r\nCoupe flatteuse : Coupe très ajustée et structurée, avec des coutures princesse (ou baleines) visibles qui ajoutent de la forme, faisant penser à un bustier.\r\nDétail clé : Les bretelles sont ornées de grands volants superposés qui ajoutent du volume et une touche romantique/féminine. L\'ourlet est pointu sur le devant.\r\nTissu : Polyester de bonne qualité .\r\nCouleur : Blanc éclatant, une teinte classique et polyvalente, parfaite pour l\'été.\r\nStyle : Idéal pour un look de soirée, de festival ou d\'été chic, porté avec un jean ou une jupe taille haute.', 20000.00, 2, 'M,L', 3),
(19, 'Top corset (bustier) à manches bouffantes courtes', 'images/products/68e0245b70753.jpg', '2025-10-03 22:30:35', 'Design chic : Encolure cœur (sweetheart neckline) et un style court et très ajusté.\r\nCoupe flatteuse : Coupe très cintrée et façon corset (avec des coutures structurantes) au niveau du buste et de la taille. L\'ourlet est pointu ou asymétrique sur le devant.\r\nDétail clé : Il est orné de magnifiques broderies florales (roses et vertes) sur l\'ensemble du tissu blanc. Il possède une fermeture à crochets ou à glissière invisible sur le devant.\r\nManches : Manches courtes et bouffantes (puff sleeves) avec des petits fronces au niveau des épaules pour un effet romantique.\r\nTissu : Coton imprimé.\r\nCouleur : Fond blanc avec des motifs floraux brodés principalement roses et verts.\r\nStyle : Un haut romantique, féminin et tendance, parfait pour les sorties estivales ou les looks décontractés-chic, souvent associé à un jean taille haute.', 30000.00, 1, 'M', 3),
(20, 'Pantalon large à taille haute', 'images/products/68e026435aa0c.jpg', '2025-10-03 22:38:43', 'Design chic : Coupe épurée et minimaliste, sans poches apparentes ni pinces sur le devant, offrant un look tailleur moderne.\r\nCoupe flatteuse : Taille très haute pour allonger la silhouette. La coupe est ample et droite à partir des hanches, avec des jambes très larges qui confèrent beaucoup de fluidité et d\'aisance.\r\nDétail clé : Fermeture à glissière invisible et crochets/bouton unique à la taille. L\'absence de plis ou de poches apparentes crée un devant lisse et raffiné.\r\nLongueur : Longueur classique, tombant sur le dessus des chaussures.\r\nTissu : Sergé léger.\r\nCouleur : Gris anthracite ou gris foncé, une teinte neutre et sophistiquée.\r\nStyle : Pantalon polyvalent et élégant, idéal pour le bureau ou pour un look décontracté chic, souvent porté avec un haut court (crop top) ou un chemisier rentré.', 30000.00, 4, 'XS,S,M,L', 4),
(21, 'Pantalon large de tailleur à taille haute', 'images/products/68e027ef31b57.jpg', '2025-10-03 22:45:51', 'Design chic : Combinaison d\'un style de tailleur formel et d\'un confort décontracté grâce au cordon de serrage.\r\nCoupe flatteuse : Taille haute pour une silhouette allongée. Coupe droite et très large (wide leg) à partir des hanches, conférant fluidité et élégance.\r\nDétail clé : Présence d\'un cordon de serrage à la taille pour un ajustement facile. Il comporte des plis ou des pinces sur le devant pour une meilleure structure, et une ligne de couture centrale le long des jambes. Il a des poches latérales discrètes.\r\nLongueur : Longueur maxi, tombant sur le dessus des pieds.\r\nTissu :Crêpe.\r\nCouleur : Blanc pur ou blanc cassé, une teinte lumineuse et sophistiquée.\r\nStyle : Pantalon parfait pour un look de bureau élégant, un événement estival, ou une tenue de vacances chic.', 30000.00, 2, 'M,XL', 4),
(22, 'Pantalon patte d\'éléphant (flare) de costume.', 'images/products/68e028a855eff.jpg', '2025-10-03 22:48:56', 'Design chic : Style de pantalon de costume formel avec une coupe épurée et sophistiquée.\r\nCoupe flatteuse : Taille très haute et ajustée pour allonger la silhouette. La coupe est ajustée jusqu\'aux hanches, puis s\'évase progressivement vers le bas pour créer un effet très large ou évasé (flare).\r\nDétail clé : Il présente une couture centrale verticale distincte sur toute la longueur des jambes, devant et derrière, qui ajoute de la structure et allonge la jambe. Fermeture classique à glissière et agrafe/bouton.\r\nLongueur : Longueur maxi, tombant sur le dessus des pieds.\r\nTissu : Crêpe.\r\nCouleur : Bleu ciel clair ou bleu poudre, une teinte pastel douce et élégante.\r\nStyle : Pantalon idéal pour un look de bureau moderne, une tenue de mariage d\'été chic, ou un style élégant et décontracté.', 35000.00, 2, 'XS,S', 4),
(23, 'Pantalon palazzo imprimé à taille haute', 'images/products/68e02ad047cee.jpg', '2025-10-03 22:58:08', 'Design chic : Style audacieux et estival, idéal pour les vacances ou un look bohème-chic.\r\nCoupe flatteuse : Taille très haute et ajustée. La coupe est extrêmement large et fluide (palazzo), garantissant confort et mouvement.\r\nDétail clé : Le tissu est orné d\'un imprimé géométrique ou médaillon de style ornemental sur l\'ensemble du pantalon. Il semble être lisse et épuré à la taille.\r\nLongueur : Longueur maxi, tombant sur le dessus des pieds.\r\nTissu :Vviscose .\r\nCouleur : Fond blanc avec un motif contrastant en fuchsia vif ou rose magenta.\r\nStyle : Pantalon parfait pour la plage, une croisière, ou un événement estival décontracté, souvent porté avec un haut court (crop top) et des sandales.', 25000.00, 2, 'XS,S', 4),
(24, 'Pantalon palazzo imprimé floral', 'images/products/68e092c136b4d.jpg', '2025-10-04 06:21:37', 'Design chic : Style décontracté et estival, idéal pour un look de vacances ou bohème.\r\nCoupe flatteuse : Taille très haute et ajustée. La coupe est extrêmement large et fluide (palazzo), offrant un confort et un mouvement maximal, donnant l\'impression d\'une jupe longue.\r\nDétail clé : Le tissu est orné d\'un grand motif floral couvrant toute la surface, avec des fleurs et des feuilles stylisées. La taille est lisse et épurée.\r\nLongueur : Longueur maxi, tombant jusqu\'au sol ou sur le dessus des pieds.\r\nTissu : Viscose.\r\nCouleur : Fond beige/camel clair ou nude, avec des motifs floraux noirs contrastants.\r\nStyle : Pantalon parfait pour la plage, une journée décontractée chic, ou un look de vacances, souvent porté avec un haut court (crop top) comme sur la photo.', 30000.00, 2, 'S,M', 4),
(25, 'Ensemble deux pièces (Co-ord set)', 'images/products/68e09e9546133.jpg', '2025-10-04 07:12:05', 'Design : Imprimé floral graphique audacieux en noir et blanc.\r\nHaut : Crop top dos nu (halter neck) avec un col noir et un détail découpé (keyhole) sur le buste.\r\nBas : Jupe longue (maxi), taille haute et très évasée (fluide et volumineuse).\r\nTissu : Léger et de bonne tenue, idéal pour l\'été.\r\nStyle : Chic, contrasté et parfait pour les événements de jour.', 70000.00, 2, 'M,L', 5),
(26, 'Ensemble coordonné deux pièces (co-ord set)', 'images/products/68e0a023a7969.jpg', '2025-10-04 07:18:43', 'Design : Imprimé végétal/feuillage élégant sur l\'ensemble.\r\nHaut : Crop top dos nu (halter neck) avec un col montant drapé ajusté.\r\nBas : Pantalon large (palazzo), taille haute et très fluide, créant une silhouette longue et aérienne.\r\nTissu : Satin légère et fluide, avec une légère brillance.\r\nCouleur : Fond blanc cassé/crème avec un motif en brun clair/terracotta.\r\nStyle : Sophistiqué, confortable et parfait pour une destination de vacances ou un événement estival.', 75000.00, 1, 'S', 5),
(27, 'Ensemble deux pièces (Co-ord set) style bohème maxi.', 'images/products/68e0a2a88bf30.jpg', '2025-10-04 07:29:28', 'Haut : Bustier bandeau court avec bordures festonnées noires.\r\nBas : Jupe longue à niveaux (tierce), taille haute, avec bordures festonnées noires assorties.\r\nTissu : Léger et texturé ( crêpe), très fluide.\r\nCouleur : Écru/blanc cassé et noir.\r\nStyle : Parfait pour les vacances et les journées d\'été.', 75000.00, 2, 'L,XL', 5),
(28, 'Ensemble deux pièces (Co-ord set) avec découpes.', 'images/products/68e0a41c0c1bc.jpg', '2025-10-04 07:35:40', 'Haut : Crop top smocké avec bretelles décoratives et nœud au dos.\r\nBas : Jupe longue fendue à la taille, décorée de motifs perforés (cut-outs) sur le bas et le devant.\r\nTissu : Léger et aérien, idéal pour les fortes chaleurs.\r\nCouleur : Jaune pastel très clair.\r\nStyle : Parfait pour un look de destination chic et estival', 80000.00, 1, 'M', 5),
(29, 'Ensemble deux pièces (Co-ord set), style chic', 'images/products/68e0a609d3278.jpg', '2025-10-04 07:43:53', 'Haut : Crop top bustier à bretelles épaisses et encolure cœur.\r\nBas : Pantalon large (palazzo), taille haute et très fluide.\r\nTissu : Fluide et léger, avec un beau tombé.\r\nCouleur : Fond blanc avec imprimé bleu marine.\r\nStyle : Audacieux, sophistiqué et parfait pour l\'été.', 80000.00, 2, 'S,L', 5),
(30, 'Ensemble deux pièces (Co-ord set), style maxi d\'été.', 'images/products/68e0a6f7ae4ce.jpg', '2025-10-04 07:47:51', 'Haut : Crop top jaune uni à encolure droite avec des bretelles à volants.\r\nBas : Jupe longue à niveaux (tierce), taille haute smockée, avec un imprimé floral jaune et bleu sur fond blanc.\r\nTissu : Léger et fluide, idéal pour l\'été.\r\nCouleur : Jaune citron et imprimé floral jaune/bleu sur blanc.\r\nStyle : Audacieux, confortable et très estival.', 85000.00, 1, 'XS,S', 5),
(31, 'Pantalon de tailleur à taille haute', 'images/products/68e0a8813cedf.jpg', '2025-10-04 07:54:25', 'Design chic : Style audacieux et sophistiqué, combinant l\'apparence d\'une jupe longue et la praticité d\'un pantalon.\r\nCoupe flatteuse : Taille très haute et ajustée pour souligner la silhouette. La coupe est très large et droite sur les jambes, assurant un tombé structuré et dramatique.\r\nDétail clé : La caractéristique principale est le pan de tissu superposé sur le devant (sorte de tablier ou overskirt), qui descend jusqu\'à l\'ourlet et dissimule la braguette et les jambes intérieures, donnant l\'impression d\'une jupe.\r\nLongueur : Longueur maxi, tombant jusqu\'au sol.\r\nTissu : Crêpe lourd .\r\nCouleur : Rouge foncé ou bordeaux, une teinte riche et puissante.\r\nStyle : Un pantalon idéal pour un événement formel, un look de soirée, ou une tenue de bureau très chic.', 25000.00, 4, '[]', 4);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL DEFAULT '5',
  `review_text` text NOT NULL,
  `is_visible` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating`, `review_text`, `is_visible`, `created_at`) VALUES
(1, 4, 5, 'Je suis satisfaite du résultat, le tissu est vraiment de bonne qualité.', 1, '2025-10-03 04:32:54'),
(2, 7, 5, 'Les accessoires sont tous de bonne qualité. J&#39;espère que vous allez toujours offrir  les meilleures.', 1, '2025-10-03 08:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('client','admin') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime NOT NULL,
  `status` enum('online','offline') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `profile_image`, `password`, `role`, `created_at`, `last_login`, `status`) VALUES
(2, 'First', 'Admin', 'shaliapage2025@gmail.com', NULL, '$argon2id$v=19$m=65536,t=4,p=1$Sk5GbGlpMXhpMUo3dWNqNg$iggZIgw0YEXUg0ICbriWvdYMt5hAScToYcGJN/he7KQ', 'admin', '2025-09-20 17:46:35', '2025-10-05 16:40:30', ''),
(3, 'Little', 'Girl', 'littlegirl@gmail.com', NULL, '$argon2id$v=19$m=65536,t=4,p=1$YWpsRE9lRG0yOXZPN3FjTQ$6sszIwoMlcrYm7qwAhJwvn+LEAZNBpq66jR08n61Bbg', 'client', '2025-09-22 21:46:07', '2025-10-04 11:54:30', ''),
(4, 'Ram', 'Nambinintsoa', 'ramshalia@gmail.com', NULL, '$argon2id$v=19$m=65536,t=4,p=1$eDhkTXl0SnpnM2MxdE1MUg$RbeCKRox2LbQHRV/hbs9+ANHG9e8KBH5pxToOhtAbW4', 'client', '2025-10-02 07:57:59', '2025-10-03 07:31:12', ''),
(5, 'Just', 'Girl', 'justgirl@gmail.com', NULL, '$argon2id$v=19$m=65536,t=4,p=1$alVubUVobE9DNjZRNDhKNg$0iizg5x84hGbi6ivYLR7eVqzhEzfFMPBfgd6fmaHGwY', 'client', '2025-10-02 08:00:46', '0000-00-00 00:00:00', ''),
(7, 'Ramarosata', 'Volatiana Shalia', 'ramarosatashalia@gmail.com', 'uploads/avatars/avatar_7_1759481522.jpeg', '$argon2id$v=19$m=65536,t=4,p=1$ckNyc1FKUjVqckIyaTBsRw$db8I2hxQyq9OA1iujd7cG4B04sszL2jlfEHbHQrUlEU', 'client', '2025-10-02 15:48:29', '2025-10-05 16:41:49', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
