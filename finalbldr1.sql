-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2023 at 03:31 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finalbldr`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `timestamp`, `user_id`, `action`, `details`) VALUES
(123456801, '2023-05-26 03:59:53', 123456791, 'Logged in', NULL),
(123456802, '2023-05-26 04:01:32', 577124, 'Logged out', NULL),
(123456803, '2023-05-26 04:10:48', 123456791, 'Logged in', NULL),
(123456808, '2023-05-26 04:25:12', 577124, 'Added equipment', 'Name: tests ID: 125202 '),
(123456809, '2023-05-28 02:06:45', 123456791, 'Logged in', NULL),
(123456872, '2023-05-28 03:39:23', 577124, 'Added materials', '(Project ID: 8, Material Name: Bricks, Quantity: 12)'),
(123456873, '2023-05-28 03:45:46', 577124, 'Added Product', '(Product Name: gagwaga)'),
(123456874, '2023-05-28 03:53:00', 577124, 'Added Project', '(Project Name: gwgaw)'),
(123456875, '2023-05-28 04:01:05', 577124, 'Added Product', '(Product Name: etete)'),
(123456876, '2023-05-28 10:17:26', 123456817, 'Login', NULL),
(123456877, '2023-05-28 04:17:26', 577124, 'Added User', '(User Name: shesh)'),
(123456878, '2023-05-28 04:20:36', 577124, 'Deleted Equipment', '(Equipment Name: Scissor Lift)'),
(123456879, '2023-05-28 04:21:52', 577124, 'Deleted Product', '(Product Name: test)'),
(123456880, '2023-05-28 04:28:41', 577124, 'Deleted User', '(User ID: 123456817)'),
(123456883, '2023-05-28 04:36:57', 577124, 'Edited Equipment', 'Equipment Name: Air Compressor'),
(123456884, '2023-05-28 04:44:04', 577124, 'Edited Product', 'Product Name: pako'),
(123456885, '2023-05-28 04:46:10', 577124, 'Edited Project', '(Project Name: gaming room ni kraeg1 aw)'),
(123456886, '2023-05-28 04:48:31', 577124, 'Edited User', '(User Name: )'),
(123456890, '2023-05-28 04:54:57', 577124, 'Edited User', 'User Name: kraegpogi'),
(123456892, '2023-05-28 05:46:53', 577124, 'Ordered to kraeg', '(kraeg: 222)'),
(123456893, '2023-05-28 05:48:12', 577124, 'Deleted Product', '(Product Name: kraeg)'),
(123456895, '2023-05-28 05:51:36', 577124, 'Ordered to andal', '(Steel Rebars: 2)'),
(123456897, '2023-05-28 05:58:38', 577124, 'Restored equipment', '(Equipment ID: 125186)'),
(123456898, '2023-05-28 06:00:07', 577124, 'Restored Product', '(Product ID: 110)'),
(123456899, '2023-05-28 06:00:38', 577124, 'Deleted Product', '(Product Name: kraeg)'),
(123456910, '2023-05-28 06:49:13', 123456791, 'Logged in', NULL),
(123456911, '2023-05-28 06:50:07', 577124, 'Edited Product', '(Product Name: Bricks)'),
(123456914, '2023-05-28 06:54:01', 577124, 'Logged out', NULL),
(123456915, '2023-05-28 06:54:13', 123656486, 'Logged in', NULL),
(123456916, '2023-05-28 06:54:51', 123656486, 'Logged out', NULL),
(123456917, '2023-05-28 06:54:54', 2147483647, 'Logged in', NULL),
(123456918, '2023-05-28 06:56:50', 2147483647, 'Restored user', '(User ID: 10)'),
(123456919, '2023-05-28 06:58:31', 2147483647, 'Logged out', NULL),
(123456920, '2023-05-28 06:58:35', 577124, 'Logged in', NULL),
(123456921, '2023-05-28 07:05:22', 577124, 'Logged in', NULL),
(123456922, '2023-05-28 07:14:21', 577124, 'Deleted User', '(User ID: 123456817)'),
(123456923, '2023-05-28 07:21:40', 577124, 'Deleted User', '(User ID: 123456804)'),
(123456924, '2023-05-28 07:25:25', 577124, 'Deleted User', '(User ID: 10)'),
(123456925, '2023-05-28 07:25:35', 577124, 'Restored user', '(User ID: 123456804)'),
(123456926, '2023-05-28 07:25:39', 577124, 'Restored user', '(User ID: 123456817)'),
(123456927, '2023-05-28 07:25:42', 577124, 'Logged out', NULL),
(123456928, '2023-05-28 07:27:19', 577124, 'Logged in', NULL),
(123456929, '2023-05-28 07:27:31', 577124, 'Restored user', '(User ID: 10)'),
(123456930, '2023-05-28 07:27:34', 577124, 'Logged out', NULL),
(123456931, '2023-05-28 07:27:39', 123656486, 'Logged in', NULL),
(123456932, '2023-05-28 07:29:40', 123656486, 'Logged out', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `available` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `description`, `category`, `quantity`, `available`, `image`) VALUES
(2, 'Backhoe', 'Used for digging and moving soil, rocks, and other materials, and for small demolition projects.', 'Heavy', 3, '2', ''),
(4, 'Scaffolding', 'Used to provide temporary support and access for construction workers.', 'Support', 10, '0', ''),
(6, 'Air Compressor', 'Used for powering tools and equipment that require compressed air, such as nail guns and sanders.', 'light', 6, '0', NULL),
(125182, 'L300', 'mabilis', 'vehicle', 2, '2', 'uploads/645745eb98364.jpg'),
(125184, 'Excavator', 'Used for digging and moving large amounts of earth or debris.', 'Heavy', 2, '1', NULL),
(125187, 'Crane', 'Used for lifting and moving heavy materials on construction sites.', 'Heavy', 1, '1', NULL),
(125188, 'Bulldozer', 'Used for clearing and leveling land by pushing or dragging materials.', 'Heavy', 2, '2', NULL),
(125190, 'Dump Truck', 'Used for transporting loose materials, such as sand or gravel, on construction sites.', 'heavy', 5, '5', NULL),
(125191, 'Jackhammer', 'Used for breaking up concrete, asphalt, or other hard surfaces.', 'Light', 8, '6', NULL),
(125192, 'Cement Mixer', 'Used for mixing and pouring cement on construction sites.', 'Medium', 4, '4', NULL),
(125193, 'Compactor', 'Used for compacting soil, gravel, or asphalt during construction.', 'Medium', 6, '6', NULL),
(125194, 'Crusher', 'Used for crushing rocks and other materials for construction purposes.', 'Heavy', 2, '1', NULL),
(125195, 'Trencher', 'Used for digging trenches for pipes, cables, or drainage systems.', 'Medium', 3, '3', NULL),
(125202, 'tests', 'tete', 'heavy', 3, '3', '');

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `material_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`material_id`, `project_id`, `name`, `quantity`, `price`, `total_price`) VALUES
(24, 8, 'hfoawfwaih', 60, 22.00, 1320.00),
(25, 8, 'hwigwoa', 29, 242.00, 7018.00),
(26, 7, 'hfoawfwaih', 16, 22.00, 352.00),
(27, 10, 'hfoawfwaih', 5, 22.00, 110.00),
(28, 10, 'hwigwoa', 10, 242.00, 2420.00),
(29, 8, 'Plywood Sheets', 10, 20.00, 200.00),
(30, 8, 'Cement Bags', 18, 8.00, 144.00),
(31, 8, 'Steel Bars', 20, 15.75, 315.00),
(32, 8, 'Insulation Foam', 11, 8.50, 93.50),
(33, 8, 'Drywall Sheets', 15, 12.75, 191.25),
(34, 8, 'Ceramic Tiles', 23, 6.25, 143.75),
(35, 8, 'Concrete Blocks', 36, 1.00, 36.00),
(36, 8, 'Bricks', 12, 1.00, 12.00),
(37, 8, 'Plumbing Pipes', 26, 3.00, 78.00),
(38, 8, 'Roofing Shingles', 5, 5.00, 25.00),
(39, 7, 'Concrete Blocks', 250, 10.50, 2625.00),
(40, 10, 'Plywood Sheets', 100, 20.00, 2000.00),
(41, 7, 'Steel Rebars', 308, 12.00, 3696.00),
(42, 8, 'Steel Rebars', 116, 12.00, 1392.00),
(43, 10, 'Cement Bags', 1, 8.00, 8.00),
(44, 10, 'test', 101, 100.00, 10100.00),
(45, 8, 'sheshesh', 1901, 0.00, 0.00),
(46, 10, 'Bricks', 2, 1.00, 2.00),
(47, 8, 'pako', 2, 222.00, 444.00),
(48, 8, 'damina', 1, 25.00, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `read_status`, `created_at`) VALUES
(130, 'Product Reorder', 'Reorder Required! \"Bricks\" (ID: 63) has reached or fallen below reorder point!', 0, '2023-05-28 10:01:34'),
(132, 'Product Reorder', 'Reorder Required! \"Bricks\" (ID: 63) has reached or fallen below reorder point!', 0, '2023-05-28 10:09:43'),
(134, 'Product Reorder', 'Reorder Required! \"Bricks\" (ID: 63) has reached or fallen below reorder point!', 1, '2023-05-28 10:10:53'),
(135, 'Product Reorder', 'Reorder Required! \"Bricks\" (ID: 63) has reached or fallen below reorder point!', 0, '2023-05-28 10:10:53'),
(136, 'Product Reorder', 'Reorder Required! \"pako\" (ID: 87) has reached or fallen below reorder point!', 0, '2023-05-28 10:44:04'),
(137, 'Product Reorder', 'Reorder Required! \"Bricks\" (ID: 63) has reached or fallen below reorder point!', 0, '2023-05-28 12:50:07');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `received_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `supplier_id`, `quantity`, `order_date`, `received_date`) VALUES
(20, 123456791, 63, 4, 1000, '2023-05-16 22:00:00', '2023-05-20 19:22:45'),
(21, 123456791, 77, 47, 1000, '2023-05-16 22:01:29', '2023-05-16 23:36:44'),
(22, 123456791, 77, 47, 11, '2023-05-16 22:10:39', '2023-05-16 23:36:44'),
(23, 123456791, 77, 47, 11, '2023-05-16 22:14:09', '2023-05-16 23:36:44'),
(24, 123456791, 77, 4, 1, '2023-05-16 22:19:00', '2023-05-16 23:36:44'),
(25, 123456791, 76, 47, 1, '2023-05-16 22:30:58', '2023-05-16 22:33:44'),
(26, 123456791, 63, 4, 10, '2023-05-16 22:33:55', '2023-05-20 19:22:45'),
(28, 123456791, 77, 47, 1, '2023-05-16 22:44:28', '2023-05-16 23:36:44'),
(29, 123456791, 77, 47, 2, '2023-05-16 22:47:34', '2023-05-16 23:36:44'),
(30, 123456791, 77, 47, 8, '2023-05-16 23:08:41', '2023-05-16 23:36:44'),
(31, 123456791, 77, 47, 11, '2023-05-16 23:19:19', '2023-05-16 23:36:44'),
(32, 123456791, 77, 47, 1, '2023-05-16 23:24:32', '2023-05-16 23:36:44'),
(33, 123456791, 77, 47, 2, '2023-05-16 23:27:49', '2023-05-16 23:36:44'),
(34, 123456791, 63, 4, 2, '2023-05-16 23:32:47', '2023-05-20 19:22:45'),
(35, 123456791, 77, 47, 1, '2023-05-16 23:34:44', '2023-05-16 23:36:44'),
(36, 123456791, 77, 4, 1, '2023-05-16 23:36:31', '2023-05-16 23:36:44'),
(37, 10, 63, 4, 1, '2023-05-17 03:34:16', '2023-05-20 19:22:45'),
(38, 123456791, 101, 4, 0, '2023-05-20 18:13:38', '2023-05-20 18:41:39'),
(41, 123456791, 104, 4, 200, '2023-05-20 18:23:51', '2023-05-20 18:26:17'),
(42, 123456791, 105, 4, 1000, '2023-05-20 18:42:05', '2023-05-20 18:42:26'),
(43, 123456791, 106, 4, 2000, '2023-05-20 18:45:00', '2023-05-20 18:45:17'),
(44, 123456791, 107, 4, 22, '2023-05-20 18:47:26', '2023-05-20 19:21:52'),
(45, 123456791, 63, 4, 11, '2023-05-20 19:22:29', '2023-05-20 19:22:45'),
(46, 123456791, 73, 4, 24, '2023-05-20 19:25:22', '2023-05-20 19:29:41'),
(47, 123456791, 73, 4, 12, '2023-05-20 19:29:26', '2023-05-20 19:29:41'),
(48, 123456791, 74, 4, 12, '2023-05-20 19:30:15', '2023-05-20 19:30:25'),
(49, 123456791, 63, 4, 33, '2023-05-20 23:20:34', NULL),
(50, 123456791, 77, 4, 11, '2023-05-23 22:16:51', NULL),
(51, 123456791, 110, 4, 222, '2023-05-28 19:46:49', '2023-05-28 19:47:54'),
(52, 123456791, 111, 4, 222, '2023-05-28 19:46:53', '2023-05-28 19:48:00'),
(53, 123456791, 76, 47, 2, '2023-05-28 19:50:58', NULL),
(54, 123456791, 76, 47, 2, '2023-05-28 19:51:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `user_level` int(11) NOT NULL,
  `permission_key` varchar(50) NOT NULL,
  `has_permission` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `user_level`, `permission_key`, `has_permission`) VALUES
(1, 1, 'Add Equipment', 1),
(2, 1, 'Add Product', 1),
(3, 1, 'Add Project', 1),
(4, 1, 'Add User', 1),
(5, 1, 'Delete Equipment', 1),
(6, 1, 'Delete Product', 1),
(8, 1, 'Edit Equipment', 1),
(9, 1, 'Edit Product', 1),
(10, 1, 'Edit Project', 1),
(11, 1, 'Edit User', 1),
(12, 2, 'Add Equipment', 0),
(13, 2, 'Add Product', 1),
(14, 2, 'Add Project', 1),
(15, 2, 'Add User', 0),
(16, 2, 'Delete Equipment', 1),
(17, 2, 'Delete Product', 1),
(19, 2, 'Edit Equipment', 1),
(20, 2, 'Edit Product', 1),
(21, 2, 'Edit Project', 1),
(22, 2, 'Edit User', 0),
(23, 3, 'Add Equipment', 0),
(24, 3, 'Add Product', 0),
(25, 3, 'Add Project', 0),
(26, 3, 'Add User', 0),
(27, 3, 'Delete Equipment', 0),
(28, 3, 'Delete Product', 0),
(30, 3, 'Edit Equipment', 0),
(31, 3, 'Edit Product', 0),
(32, 3, 'Edit Project', 0),
(33, 3, 'Edit User', 0),
(34, 1, 'View Dashboard', 1),
(35, 2, 'View Dashboard', 0),
(36, 3, 'View Dashboard', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reorder_point` int(11) DEFAULT NULL,
  `status` enum('sufficient','reorder','received','inactive','pending') DEFAULT NULL
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `quantity`, `image`, `created_at`, `updated_at`, `reorder_point`, `status`) VALUES
(63, 'Bricks', 'Building blocks made from clay or concrete', 1.00, 4931, 'path_to_image4.jpg', '2023-05-14 07:00:25', '2023-05-28 12:50:07', 504, 'sufficient'),
(73, 'Roofing Shingles', 'Covering material for roofs', 5.00, 200, 'path_to_image5.jpg', '2023-05-13 12:23:16', '2023-05-20 13:15:17', 300, 'reorder'),
(74, 'Cement Bags', 'Bags of cement for construction projects', 8.00, 16, 'path_to_image6.jpg', '2023-05-13 12:23:16', '2023-05-28 09:16:18', 200, 'reorder'),
(75, 'Plywood Sheets', 'Thin wooden sheets used for construction', 20.00, 10, 'path_to_image7.jpg', '2023-05-13 12:23:16', '2023-05-20 15:03:49', 200, 'reorder'),
(76, 'Steel Rebars', 'Reinforcing bars used in concrete structures', 12.00, 324, 'path_to_image8.jpg', '2023-05-13 12:23:16', '2023-05-28 11:51:01', 400, 'received'),
(77, 'Concrete Blocks', 'Solid blocks used in construction', 1.00, 199, 'path_to_image3.jpg', '2023-05-13 12:23:16', '2023-05-23 14:16:55', 300, 'received'),
(87, 'pako', 'pako', 222.00, 222, '', '2023-05-20 09:10:37', '2023-05-28 10:44:04', 22, 'sufficient'),
(101, 'pogigigigi', 'gigigi', NULL, 0, NULL, '2023-05-20 10:13:38', '2023-05-20 10:27:31', 22, ''),
(104, 'test', 'testsasa', 100.00, 99, NULL, '2023-05-20 10:23:51', '2023-05-28 10:21:52', 100, 'inactive'),
(105, 'hello', 'its me', 25.00, 90, NULL, '2023-05-20 10:42:05', '2023-05-20 14:35:00', 100, 'reorder'),
(106, 'sheshesh', 'hwwahwh', 0.00, 99, NULL, '2023-05-20 10:45:00', '2023-05-21 06:03:32', 100, 'sufficient'),
(107, 'damina', 'ninawin', 25.00, 109, NULL, '2023-05-20 10:47:26', '2023-05-28 08:35:20', 2222, 'reorder'),
(108, 'gagwaga', 'gagawgwa', 222.00, 222, '', '2023-05-28 09:45:46', '2023-05-28 09:45:46', 22, 'sufficient'),
(109, 'etete', 'tetetete', 2222.00, 22, '', '2023-05-28 10:01:05', '2023-05-28 10:01:05', 2, 'sufficient'),
(110, 'kraeg', 'kraeg', 24.00, 222, '', '2023-05-28 11:46:49', '2023-05-28 12:00:38', 22, 'inactive'),
(111, 'kraeg', 'kraeg', 24.00, 222, NULL, '2023-05-28 11:46:53', '2023-05-28 11:48:00', 22, 'sufficient');

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `trg_product_status_reorder` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
  IF NEW.status = 'Reorder' THEN
    INSERT INTO notifications (type, message) VALUES ('Product Reorder', CONCAT('Reorder Required! "', NEW.name, '" (ID: ', NEW.id, ') has reached or fallen below reorder point!'));
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_supplier`
--

CREATE TABLE `product_supplier` (
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_supplier`
--

INSERT INTO `product_supplier` (`product_id`, `supplier_id`) VALUES
(63, 4),
(73, 4),
(74, 4),
(75, 4),
(76, 47),
(77, 4),
(77, 47),
(87, 4),
(104, 4),
(105, 4),
(106, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4),
(111, 4);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_description` text NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `project_manager` varchar(255) NOT NULL,
  `project_team` text NOT NULL,
  `budget` float NOT NULL,
  `actual_cost` float DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `project_name`, `project_description`, `client_name`, `start_date`, `end_date`, `project_manager`, `project_team`, `budget`, `actual_cost`, `status`, `location`, `image`) VALUES
(6, 'bahay ni kraeg123', 'malaki 123', 'kraeg123', '2023-05-05', '2023-05-26', 'kraeg123', 'kraeg pogi123', 20000, 123, 'in_progress', 'q.c1', 'uploads/bahayko.jfif'),
(7, 'cr ni kraeg1', 'maganda1', 'kraeg pogi1', '2023-05-10', '2023-05-26', 'kraeg1', 'kraeg sakalam1', 10000, 6673, 'completed', 'q.c', 'uploads/crnikeg.jfif'),
(8, 'gaming room ni kraeg1 aw', 'malupet1aw', 'kraegpogi1231awa', '2023-05-05', '2023-05-30', 'malupet kraeg1aw', 'kraegsgs1aw1', 20000, 11437.5, 'in_progress', 'q.c121', 'uploads/gamingroom.jpg'),
(9, 'bahay ni kuya', 'eyyyy', 'avila', '2023-05-24', '2023-05-24', 'kraeg', 'christian', 10000000, 0, 'completed', 'q.c', 'uploads/bahaynikiya.jfif'),
(10, 'bahay ni andal', 'malaki', 'andal', '2023-05-03', '2023-05-31', 'andal', 'andal malakas', 10000000, 14640, 'completed', 'q.c', 'uploads/bahayniandal.jpg'),
(12, 'Court ni kraeg', 'kahit ano', 'sheesh avila', '2023-05-31', '2023-06-09', 'kraeg sheesh', 'avila kraeg', 100000, 0, 'planning', 'q.c', 'uploads/courtko.jfif'),
(13, 'sakit ng ulo ko bes', 'eyy panis', 'oi', '2023-05-18', '2023-05-30', 'kraeg ', 'noin', 10000000, 1, 'cancelled', 'qc', 'uploads/bahaykubo.jpg'),
(15, 'wa', 'gwaga', 'ubiub', '2023-05-10', '2023-05-18', 'uibi', 'buibu', 11, 0, 'planning', 'qc', NULL),
(18, 'test', 'test', 'test', '2023-05-24', '2023-06-07', 'test', 'tetst', 3333, 0, 'planning', 'manila', NULL),
(19, 'gwgaw', 'hawhwa', 'jijij', '2023-05-29', '2023-05-31', 'jiij', 'jijij', 11111, 0, 'planning', 'qc', NULL);

--
-- Triggers `project`
--
DELIMITER $$
CREATE TRIGGER `new_project_trigger` AFTER INSERT ON `project` FOR EACH ROW BEGIN
    DECLARE new_project_name VARCHAR(255);
    DECLARE new_project_id INT;
    DECLARE new_project_status VARCHAR(255);

    SET new_project_name = NEW.project_name;
    SET new_project_id = NEW.project_id;
    SET new_project_status = NEW.status;

    INSERT INTO notifications (type, message, read_status)
    VALUES ('project', CONCAT(new_project_status, ' - New project has been added: "', new_project_name, '" (ID: ', new_project_id, ')'), 0);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `project_end_date_trigger` BEFORE INSERT ON `project` FOR EACH ROW BEGIN
    DECLARE project_end_date DATE;
    DECLARE project_name VARCHAR(255);
    DECLARE notification_message VARCHAR(255);

    SET project_end_date = NEW.end_date;
    SET project_name = NEW.project_name;

    IF DATEDIFF(project_end_date, CURDATE()) <= 3 THEN
        SET notification_message = CONCAT('Project "', project_name, '" (ID: ', NEW.project_id, ') is ending soon within 3 days. End Date: ', project_end_date);
        INSERT INTO notifications (type, message, read_status) VALUES ('project', notification_message, 0);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_equipment`
--

CREATE TABLE `project_equipment` (
  `project_equipment_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `equipment_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_equipment`
--

INSERT INTO `project_equipment` (`project_equipment_id`, `project_id`, `equipment_name`, `quantity`) VALUES
(23, 8, 'Air Compressor', 5),
(24, 7, 'Air Compressor', 2),
(25, 10, 'Air Compressor', 3),
(26, 8, 'Jackhammer', 2),
(27, 8, 'Crusher', 1),
(28, 8, 'Backhoe', 1),
(30, 8, 'Excavator', 1);

-- --------------------------------------------------------

--
-- Table structure for table `recycle_bin_equipment`
--

CREATE TABLE `recycle_bin_equipment` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `available` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recycle_bin_equipment`
--

INSERT INTO `recycle_bin_equipment` (`id`, `item_id`, `item_type`, `deleted_at`, `name`, `description`, `category`, `quantity`, `available`, `image`) VALUES
(3, 125196, 'equipment', '2023-05-20 11:37:50', 'Generator', 'Used for providing temporary electrical power on construction sites.', 'Support', 5, 0, ''),
(5, 4, 'equipment', '2023-05-20 12:10:33', 'Forklift', 'Used for lifting and moving lighter loads, typically in warehouses or construction sites.', 'Light', 4, 0, ''),
(6, 125183, 'equipment', '2023-05-20 12:11:06', 'Forklift', 'Used for lifting and moving lighter loads, typical...', 'Light', 4, 0, ''),
(7, 5, 'equipment', '2023-05-20 12:15:02', 'Concrete Mixer', 'Used for mixing cement, sand, gravel, and water to make concrete.', 'light', 4, 0, ''),
(8, 125197, 'equipment', '2023-05-28 10:20:36', 'Scissor Lift', 'Used for lifting workers and materials to higher levels on construction sites.', 'Light', 4, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `recycle_bin_products`
--

CREATE TABLE `recycle_bin_products` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reorder_point` int(11) DEFAULT NULL,
  `status` enum('reorder','sufficient','inactive','received') NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recycle_bin_products`
--

INSERT INTO `recycle_bin_products` (`id`, `item_id`, `item_type`, `deleted_at`, `name`, `description`, `price`, `quantity`, `image`, `created_at`, `updated_at`, `reorder_point`, `status`) VALUES
(126, 104, 'products', '2023-05-28 10:21:52', 'test', 'testsasa', 100.00, 99, '', '2023-05-20 10:23:51', '2023-05-28 10:21:52', 100, 'inactive'),
(128, 110, 'products', '2023-05-28 12:00:38', 'kraeg', 'kraeg', 24.00, 222, '', '2023-05-28 11:46:49', '2023-05-28 12:00:38', 22, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `recycle_bin_users`
--

CREATE TABLE `recycle_bin_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `user_level` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `sales_amount` decimal(10,2) DEFAULT NULL,
  `sales_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `project_id`, `sales_amount`, `sales_date`) VALUES
(7, 7, 3327.00, '2023-05-26');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `EstimatedDeliveryTime` int(11) DEFAULT 24
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_info`, `EstimatedDeliveryTime`) VALUES
(4, 'kraeg', 'avilakraeg@gmail.com', 2),
(47, 'andal', 'andalmiguel79@gmail.com', 13),
(48, 'mj', 'mjbonak@gmail.com', 24),
(49, 'esdi', 'esdicul6@gmail.com', 24);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_level` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `user_name`, `password`, `date`, `user_level`, `status`) VALUES
(8, 2147483647, 'kraeg', 'kraeg', '2023-05-14 06:52:12', 3, 'active'),
(10, 123656486, 'andal', 'andal', '2023-05-28 13:27:31', 3, 'active'),
(123456791, 577124, 'admin', 'admin', '2023-04-28 15:33:59', 1, 'active'),
(123456804, 8497874851, 'kraegpogi', 'malakas', '2023-05-28 13:25:35', 2, 'active'),
(123456817, 2079114342, 'shesh', 'shesh', '2023-05-28 13:25:39', 1, 'active');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `log_user_login` AFTER INSERT ON `users` FOR EACH ROW BEGIN
   INSERT INTO audit_trail (user_id, action)
   VALUES (NEW.id, 'Login');
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_level_permission_key_unique` (`user_level`,`permission_key`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD PRIMARY KEY (`product_id`,`supplier_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_equipment`
--
ALTER TABLE `project_equipment`
  ADD PRIMARY KEY (`project_equipment_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `recycle_bin_equipment`
--
ALTER TABLE `recycle_bin_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recycle_bin_products`
--
ALTER TABLE `recycle_bin_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recycle_bin_users`
--
ALTER TABLE `recycle_bin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123456933;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `project_equipment`
--
ALTER TABLE `project_equipment`
  MODIFY `project_equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `recycle_bin_equipment`
--
ALTER TABLE `recycle_bin_equipment`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recycle_bin_products`
--
ALTER TABLE `recycle_bin_products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `recycle_bin_users`
--
ALTER TABLE `recycle_bin_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123456818;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD CONSTRAINT `product_supplier_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_supplier_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `project_equipment`
--
ALTER TABLE `project_equipment`
  ADD CONSTRAINT `project_equipment_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
