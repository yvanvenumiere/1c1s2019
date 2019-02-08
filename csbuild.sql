-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Feb 08, 2019 at 10:54 AM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `csbuild`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `idadmins` int(11) NOT NULL,
  `admin_login` varchar(45) DEFAULT NULL,
  `admin_password` varchar(45) NOT NULL,
  `admin_mail` varchar(45) NOT NULL,
  `dataproj_iddataproj` int(11) NOT NULL,
  `admin_prenom` varchar(45) DEFAULT NULL,
  `admin_nom` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`idadmins`, `admin_login`, `admin_password`, `admin_mail`, `dataproj_iddataproj`, `admin_prenom`, `admin_nom`) VALUES
(2, 'admin-alectis', 'c4e3b9a1d3e7a12c52431b467d7bb9b0', 'yvan@alectis-lab.com', 1, 'Yvan', 'Vénumière');

-- --------------------------------------------------------

--
-- Table structure for table `cb_actions`
--

CREATE TABLE `cb_actions` (
  `idcb_actions` int(11) NOT NULL,
  `cba_name` varchar(45) DEFAULT NULL COMMENT 'Doit être unique\n',
  `item_types_iditem_types` int(11) NOT NULL,
  `cb_mode` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dataproj`
--

CREATE TABLE `dataproj` (
  `iddataproj` int(11) NOT NULL,
  `dp_name` varchar(105) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dataproj`
--

INSERT INTO `dataproj` (`iddataproj`, `dp_name`) VALUES
(1, 'csbuild');

-- --------------------------------------------------------

--
-- Table structure for table `fixed_datas`
--

CREATE TABLE `fixed_datas` (
  `idfixed_datas` int(11) NOT NULL,
  `fd_label` varchar(100) NOT NULL,
  `fd_content` text,
  `fd_create_at` bigint(20) DEFAULT NULL,
  `fd_update_at` bigint(20) DEFAULT NULL,
  `dataproj_iddataproj` int(11) NOT NULL,
  `fd_code` varchar(45) NOT NULL,
  `fd_type_code` varchar(45) DEFAULT NULL,
  `fd_maxlength` mediumint(30) DEFAULT NULL,
  `fd_mandatory` tinyint(4) DEFAULT '0',
  `fd_explication` varchar(200) DEFAULT NULL,
  `fixed_datas_group_idfixed_datas_group` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_datas_group`
--

CREATE TABLE `fixed_datas_group` (
  `idfixed_datas_group` int(11) NOT NULL,
  `dataproj_iddataproj` int(11) NOT NULL,
  `fd_name` varchar(45) DEFAULT NULL,
  `fdg_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_data_media`
--

CREATE TABLE `fixed_data_media` (
  `idfixed_data_media` int(11) NOT NULL,
  `medias_idmedias` int(11) NOT NULL,
  `fixed_datas_idfixed_datas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `iditem` int(11) NOT NULL,
  `item_date_creation` bigint(12) DEFAULT NULL,
  `item_date_update` bigint(12) DEFAULT NULL,
  `item_types_iditem_types` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_types`
--

CREATE TABLE `item_types` (
  `iditem_types` int(11) NOT NULL,
  `it_name` varchar(100) NOT NULL,
  `dataproj_iddataproj` int(11) NOT NULL,
  `it_code` varchar(45) NOT NULL,
  `it_pagination` int(11) DEFAULT '10',
  `it_menu_visibility` tinyint(4) DEFAULT '1',
  `it_menu_position` int(11) DEFAULT '1',
  `html_link` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_values`
--

CREATE TABLE `item_values` (
  `iditem_values` int(11) NOT NULL,
  `item_iditem` int(11) NOT NULL,
  `model_line_idmodel_line` int(11) NOT NULL,
  `iv_content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_value_media`
--

CREATE TABLE `item_value_media` (
  `iditem_value_media` int(11) NOT NULL,
  `item_values_iditem_values` int(11) NOT NULL,
  `medias_idmedias` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `idlogs` int(11) NOT NULL,
  `log_name` varchar(100) NOT NULL,
  `log_date` bigint(12) NOT NULL,
  `log_content` varchar(45) DEFAULT NULL,
  `logs_type_idlogs_type` int(11) NOT NULL,
  `dataproj_iddataproj` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs_type`
--

CREATE TABLE `logs_type` (
  `idlogs_type` int(11) NOT NULL,
  `lt_name` varchar(100) DEFAULT NULL,
  `lt_code` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medias`
--

CREATE TABLE `medias` (
  `idmedias` int(11) NOT NULL,
  `media_name` varchar(100) DEFAULT NULL,
  `media_path` varchar(100) NOT NULL,
  `media_qquuid` varchar(45) DEFAULT NULL,
  `media_date_creation` bigint(15) DEFAULT NULL,
  `media_date_modification` bigint(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medias`
--

INSERT INTO `medias` (`idmedias`, `media_name`, `media_path`, `media_qquuid`, `media_date_creation`, `media_date_modification`) VALUES
(1, 'media1532713041', '7dab688b486cc0d4740486b870585d15.jpg', 'e5fc415f-008f-418d-ac4a-ad511b0b3040', 1532713041, NULL),
(2, 'media1532713383', 'a6cc94f078461f42ab6589d4ec3589ed.jpg', '59dc5054-b6d2-4ebe-85e5-d0f02a1c72b0', 1532713383, NULL),
(3, 'media1532713438', '31b9bcf2d2ce452fdfac38b0c7f544ab.jpg', '5c6371a1-da6b-42e6-a2b5-a6a09e351acb', 1532713438, NULL),
(4, 'media1532713462', 'd62a7d9a9d85128d11298530a4e6044e.jpg', 'af94172d-4b3b-43f5-b8db-18417e34e20e', 1532713462, NULL),
(6, 'media1532713827', 'e9fc1afb3cb09b2849d1268123f2a7a2.jpg', 'a389560a-4a2a-447f-8a94-85a297418f98', 1532713827, NULL),
(7, 'media1532713891', '4fbffddd6187bfd37bdb5b88f39c2c5b.jpg', '93151525-c671-49db-93d2-9bc9bb39f57c', 1532713891, NULL),
(8, 'media1532713911', '4863a52e20281f2b3885c4b58d6b23f7.jpg', '28b0926b-7ef9-48d7-8b07-86335e131d55', 1532713911, NULL),
(9, 'media1532780590', 'c74f812c486dbd6f03be9b198ca032a8.jpg', '5917d359-2921-4854-a53c-5459df7c8db5', 1532780590, NULL),
(10, 'media1533428849', '12c8501c26295873737a396ac6ba6dbf.png', 'ba183426-17ad-4fd3-b71e-7c0fbab089bd', 1533428849, NULL),
(11, 'media1533428861', 'f7d5de81f8991c2e3eb377431825a9eb.png', '8bc824d2-e598-44ff-b5f2-f9b9c62cd22e', 1533428861, NULL),
(12, 'media1533428869', '77684a5550f8ed8ca81c9ca329d80888.png', 'bb65ce9e-bf83-4a99-950b-1bb1d949b0ab', 1533428869, NULL),
(13, 'media1533428896', '194847a8a42c07c6c093919247561f16.png', '7911a21e-3d95-4a98-a71b-f9146e1a270f', 1533428896, NULL),
(14, 'media1533428942', 'df40504b5b57956a87c76d6007c55a15.png', '4168faee-1f5f-407a-b1ee-d54f8f307ca6', 1533428942, NULL),
(15, 'media1533428959', '456c7b6a6668abe8a612265c3bd8d7e5.png', '6035a34d-0533-4f36-814e-d132baad6b88', 1533428959, NULL),
(16, 'media1533529667', '04396b1f5ae8e9eb8f37f90b33a9ab21.jpg', '1cec1e0a-f00c-4264-a930-af7b804000a2', 1533529667, NULL),
(17, 'media1533529698', 'a595d6bf8967253f8eeaa2ed0b0dac36.jpg', '847902ef-b800-4d74-971f-7cbfe02fed9d', 1533529698, NULL),
(18, 'media1533529772', '281f34ba43a2ad94165712bb5bd049c5.jpg', '3168a125-26fd-43dc-a30f-62c9ae1dc96b', 1533529772, NULL),
(19, 'media1533556134', 'ec6f7394ae132232787019e29d8323ec.jpg', '4287476a-de70-4ce5-9ed7-0ddf63c53763', 1533556134, NULL),
(20, 'media1534432381', 'e6cea3f3f17bf988c1bb5c69f05c2c7b.jpg', '943baefc-29fd-49e0-a41f-54de182b5605', 1534432381, NULL),
(21, 'media1534432496', 'adf7057fa11716727301430528480fe3.jpg', 'd1259041-d82a-46bd-8768-d8bb11089085', 1534432496, NULL),
(22, 'media1534432745', 'e18b3b4c63d0f94e55e0521f62ce91c1.jpg', 'ea2abc58-e7c9-4bea-9452-ce6684a80d62', 1534432745, NULL),
(23, 'media1534433000', 'a096ae292801dd2715251e4acb3b264a.jpg', 'fe58a027-0470-4f5a-b050-6431c9a144ba', 1534433000, NULL),
(24, 'media1534433344', '8ed0912dcd64985260fa812c88c9c570.jpeg', '733d21b9-2702-411a-bb5f-4b97e360e7ed', 1534433344, NULL),
(25, 'media1534433497', '817f20b0593b31e053fa5f36b19c7d4e.jpg', '0ec59e81-b531-4402-bcbd-be4535b7c21c', 1534433497, NULL),
(26, 'media1534433581', 'ee888f9a7bfc795f5d7aeb92d9ee8fb3.jpg', 'bb7f1dba-8280-4e55-a4bc-d359e8beec48', 1534433581, NULL),
(27, 'media1534433613', '4d84d07bd6ba11d68bade4e7f13fefb7.jpg', 'e47958ad-33ae-467a-ae4a-19d03a6d0ac6', 1534433613, NULL),
(28, 'media1534433657', '13aaa9684f9e368fbc578c25d97a63c4.png', '117d6927-ab2e-4dd6-84d2-10106c7f236a', 1534433657, NULL),
(29, 'media1534433716', 'c3ac4457be7c1600e792d5edefa7fcc4.png', '188be3f1-6549-4158-8fea-3c8d9e22a2b8', 1534433716, NULL),
(30, 'media1534434346', 'f9a1c076f0048852466eb3e70d1ed534.jpg', '9b733acb-0053-4a73-8a31-584a1b8a1075', 1534434346, NULL),
(31, 'media1534434374', 'da6d936f14746987cdde4f0a4efa68b5.jpg', 'acceec67-8d20-4964-b517-c5885d1dcdbe', 1534434374, NULL),
(32, 'media1534434435', '0fa7d49a5fe942601c92f6f15bbd94cd.jpg', 'aa538f7c-d19a-4c2e-ae52-a30c06a8aeff', 1534434435, NULL),
(33, 'media1534434469', 'a306daf6268ec927d4698c53a0180e20.png', 'cdfbcb01-7935-44b9-b44f-428084411a17', 1534434469, NULL),
(34, 'media1534434519', '3f0e2a54ea9b54fd689477f4e7de3187.png', '88dd43dd-04c5-4bdd-a54e-50b627a3e119', 1534434519, NULL),
(35, 'media1534434615', '5eec54b4c3f0f2d5c88187892caa4032.png', '9c6b4a46-d99b-4b68-9bc0-6f366d84f8bd', 1534434615, NULL),
(36, 'media1534435967', '22be344e3f24403c391287afbacbc05f.png', '22113eab-fca4-4488-841a-39663d9f4884', 1534435967, NULL),
(37, 'media1534435999', 'fa6e11335697c7ec16eee45c13db7df1.png', '17c4cfb5-bd95-4cd9-a2a3-5af728189814', 1534435999, NULL),
(38, 'media1534995491', '54f99136708d0b2d210bb2cfae2fe8cb.jpg', 'ac7aa5cb-d5f1-45d2-93d4-5cbed325cf35', 1534995491, NULL),
(39, 'media1534995524', '82a249c1a509d9081e24f80ffc4a8d7d.jpg', '6fe20505-74d0-4726-8098-2f429a1fa4ad', 1534995524, NULL),
(40, 'media1535678253', 'f6f22575d56683cecc602e668063f11d.pdf', 'b005547f-a110-4ef9-ac3b-6692414b123e', 1535678253, NULL),
(41, 'media1535678285', 'cbdb2f20f35a7e5c520b541cda4c3b53.jpg', '79f9bada-b397-497b-8503-3df8bd5d8d72', 1535678285, NULL),
(42, 'media1535678318', 'd152e6e01c77fe375e69de953fec7394.pdf', '297b8fe8-6be8-4e00-9a2a-ff04766035e6', 1535678318, NULL),
(43, 'media1535678340', 'a58c77993b8b8ce7f9acbf16159af4a1.jpg', '9d1981b6-a629-410c-b64e-1189ebf17138', 1535678340, NULL),
(45, 'media1535758036', 'de7836325e96a0eed6958dfeca1ee5f3.jpg', '1ffb1731-640f-4902-a258-11879929858a', 1535758036, NULL),
(46, 'media1535798350', '7f03706e193ce3c1f14fd13a3e8b1686.mwb', '33fb94f3-1d8e-46d9-b514-2711726cad52', 1535798350, NULL),
(47, 'media1536492240', '3be98765e261356f9c00109e65366c95.jpg', '8fcfbd1e-b9a1-43f8-9f62-1b16aa792350', 1536492240, NULL),
(48, 'media1536492246', '437e61030145b9d0f327d15fd74decbd.jpg', '3b7e1603-71d5-439c-bd74-257b559fa22f', 1536492246, NULL),
(49, 'media1536492254', 'a4afd79c95f4fbf0c193df1f660ae9b4.jpg', '0cc88060-0868-4cd3-a3a7-42d53ec30c96', 1536492254, NULL),
(50, 'media1536806994', 'cd13c6bbe311c938ddd1fc1142a74d56.png', '686db43c-08e6-4f53-b4ea-5007457e3805', 1536806994, NULL),
(51, 'media1536807059', '2130a0bb8e6cdae252301c0ef8f26e44.png', 'c7d0ac8c-e194-427b-8cf3-3e2c5d82d969', 1536807059, NULL),
(52, 'media1536936287', '79f7b31c013fbdaf1737d6a340bc1ef3.png', '36075ef0-1b1c-42eb-8823-a18260fb9842', 1536936287, NULL),
(53, 'media1536936318', '607ddf63b275ab2cd66d6332210dd267.png', '7bfb290d-883c-4291-ab43-902b14e6fd14', 1536936318, NULL),
(54, 'media1536936345', '27fe654e862988c864ff59d48fa95b0a.jpg', '1e20dc82-180f-486d-9ebd-56416b146ac6', 1536936345, NULL),
(55, 'media1537067363', '56974ccac30363be6c4cffc2f4e7fd3f.png', '527a5ded-d0d2-4f1a-a928-840887fca750', 1537067363, NULL),
(56, 'media1537067392', '14f894234e2a8df6a508acbdca736c5c.jpg', '9d5b62d6-c851-4b20-af5e-5d6472ce7d9f', 1537067392, NULL),
(57, 'media1537067413', '19a499b863c0e36c06ed57101fdb617b.png', '8d0d9f2a-a6ea-444c-89a0-7388b577e52d', 1537067413, NULL),
(58, 'media1537068998', '1ef1c3b4f0eafd2015bde7d00863ae00.jpg', 'b1505868-4b75-4be6-b380-fe74a1e3ba49', 1537068998, NULL),
(59, 'media1539172818', '50809d4318d98a799539032bd04d83f0.png', 'd313072a-d415-467e-b5f9-31eaabdc7a1d', 1539172818, NULL),
(60, 'media1539172988', '0f7fe306c7711a913f6ee7865c9dd8c4.jpeg', 'fc66d536-afa9-4a5f-865e-4d9bdf8bd7ee', 1539172988, NULL),
(61, 'media1539173077', 'add71ee4ca0bcaf53d19b1ae85cf87db.jpeg', 'a2540653-00f8-439b-88d9-1b87dc161286', 1539173077, NULL),
(62, 'media1539173350', '1af25de2128bc7ff0d284a2304f06ed8.png', '91fc1143-552e-406c-86a0-79ed7bb8e3b9', 1539173350, NULL),
(63, 'media1539174278', '0048ac3e7885cb0b02e0d0a95b8bf4cb.jpg', 'b897e4ae-36b2-49f5-b3dd-51bfc1bbe59e', 1539174278, NULL),
(64, 'media1539174294', '4b72eb7a20e7edf40803a5b6e016d29e.pdf', '668b9d94-a4fb-4c33-b330-cb74b76f7e36', 1539174294, NULL),
(65, 'media1539181447', '9e7bac69b284537c90c814c7a0f8da10.png', '72418b3d-0d15-4404-b170-81f16bd20e05', 1539181447, NULL),
(66, 'media1539744227', 'ee19008fc9431317eb82e2f2aeb58adb.pdf', 'baad94d9-2f34-44a1-ad28-a4acc793aa55', 1539744227, NULL),
(67, 'media1539744235', '40434043f59fabcafa61867c38f595ed.pdf', 'f4a6d92f-293a-4c0c-b3e0-098484a4bf05', 1539744235, NULL),
(68, 'media1539744242', 'a9f12b2fbaf57b673217727479cddecc.pdf', '8bb06c49-3457-4f8e-ace6-66b4193f9fd0', 1539744242, NULL),
(69, 'media1540083209', '25c8914eea7c1d759067ecb5870967ba.pdf', '3a22f0b5-b4b9-4589-8c6a-9abc1e46e4ad', 1540083209, NULL),
(70, 'media1540165385', '5a061835bc79f7533bb751a150f96b93.jpg', 'a2e85e8f-6fa5-46a0-b7f6-b9d82ee727cc', 1540165385, NULL),
(71, 'media1540165547', '88c01713ba8d90fe0ff721320c15aead.jpg', '826c1cda-9301-417a-b5e2-f08cd1d04d00', 1540165547, NULL),
(72, 'media1540258278', '4e5bc72f6465eec18fef79b0b7ed6ee4.pdf', '69f89d1f-4c02-4fba-81a1-e6106c1bfeb9', 1540258278, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `model_line`
--

CREATE TABLE `model_line` (
  `idmodel_line` int(11) NOT NULL,
  `ml_name` varchar(80) NOT NULL,
  `ml_type_code` varchar(45) NOT NULL,
  `ml_maxlength` mediumint(30) DEFAULT NULL,
  `ml_mandatory` tinyint(4) DEFAULT NULL,
  `ml_choice_list` varchar(1000) DEFAULT NULL,
  `item_types_iditem_types` int(11) NOT NULL,
  `ml_code` varchar(45) NOT NULL COMMENT 'Doit être unique',
  `ml_is_unique` tinyint(4) DEFAULT '0',
  `ml_is_in_list` tinyint(4) DEFAULT '1',
  `ml_is_in_crud` tinyint(1) DEFAULT NULL,
  `ml_ext_files` varchar(45) DEFAULT NULL,
  `item_types_iditem_types1` int(11) DEFAULT NULL,
  `fk_col` varchar(100) DEFAULT NULL,
  `ml_readonly` tinyint(4) DEFAULT '0',
  `ml_explications` varchar(85) DEFAULT NULL,
  `ml_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`idadmins`),
  ADD KEY `fk_admins_dataproj1_idx` (`dataproj_iddataproj`);

--
-- Indexes for table `cb_actions`
--
ALTER TABLE `cb_actions`
  ADD PRIMARY KEY (`idcb_actions`),
  ADD KEY `fk_cb_actions_item_types1_idx` (`item_types_iditem_types`);

--
-- Indexes for table `dataproj`
--
ALTER TABLE `dataproj`
  ADD PRIMARY KEY (`iddataproj`);

--
-- Indexes for table `fixed_datas`
--
ALTER TABLE `fixed_datas`
  ADD PRIMARY KEY (`idfixed_datas`),
  ADD KEY `fk_fixed_datas_dataproj1_idx` (`dataproj_iddataproj`),
  ADD KEY `fk_fixed_datas_fixed_datas_group1_idx` (`fixed_datas_group_idfixed_datas_group`);

--
-- Indexes for table `fixed_datas_group`
--
ALTER TABLE `fixed_datas_group`
  ADD PRIMARY KEY (`idfixed_datas_group`),
  ADD KEY `fk_fixed_datas_group_dataproj1_idx` (`dataproj_iddataproj`);

--
-- Indexes for table `fixed_data_media`
--
ALTER TABLE `fixed_data_media`
  ADD PRIMARY KEY (`idfixed_data_media`),
  ADD KEY `fk_fixed_data_media_medias1_idx` (`medias_idmedias`),
  ADD KEY `fk_fixed_data_media_fixed_datas1_idx` (`fixed_datas_idfixed_datas`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`iditem`),
  ADD KEY `fk_item_item_types_idx` (`item_types_iditem_types`);

--
-- Indexes for table `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`iditem_types`),
  ADD KEY `fk_item_types_dataproj1_idx` (`dataproj_iddataproj`);

--
-- Indexes for table `item_values`
--
ALTER TABLE `item_values`
  ADD PRIMARY KEY (`iditem_values`),
  ADD KEY `fk_item_values_item1_idx` (`item_iditem`),
  ADD KEY `fk_item_values_model_line1_idx` (`model_line_idmodel_line`);

--
-- Indexes for table `item_value_media`
--
ALTER TABLE `item_value_media`
  ADD PRIMARY KEY (`iditem_value_media`),
  ADD KEY `fk_item_value_media_item_values1_idx` (`item_values_iditem_values`),
  ADD KEY `fk_item_value_media_medias1_idx` (`medias_idmedias`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`idlogs`),
  ADD KEY `fk_logs_logs_type1_idx` (`logs_type_idlogs_type`),
  ADD KEY `fk_logs_dataproj1_idx` (`dataproj_iddataproj`);

--
-- Indexes for table `logs_type`
--
ALTER TABLE `logs_type`
  ADD PRIMARY KEY (`idlogs_type`);

--
-- Indexes for table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`idmedias`);

--
-- Indexes for table `model_line`
--
ALTER TABLE `model_line`
  ADD PRIMARY KEY (`idmodel_line`),
  ADD KEY `fk_model_line_item_types1_idx` (`item_types_iditem_types`),
  ADD KEY `fk_model_line_item_types2_idx` (`item_types_iditem_types1`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `idadmins` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `cb_actions`
--
ALTER TABLE `cb_actions`
  MODIFY `idcb_actions` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `dataproj`
--
ALTER TABLE `dataproj`
  MODIFY `iddataproj` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `fixed_datas`
--
ALTER TABLE `fixed_datas`
  MODIFY `idfixed_datas` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fixed_datas_group`
--
ALTER TABLE `fixed_datas_group`
  MODIFY `idfixed_datas_group` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fixed_data_media`
--
ALTER TABLE `fixed_data_media`
  MODIFY `idfixed_data_media` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `iditem` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `iditem_types` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `item_values`
--
ALTER TABLE `item_values`
  MODIFY `iditem_values` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `item_value_media`
--
ALTER TABLE `item_value_media`
  MODIFY `iditem_value_media` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `idlogs` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_type`
--
ALTER TABLE `logs_type`
  MODIFY `idlogs_type` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `medias`
--
ALTER TABLE `medias`
  MODIFY `idmedias` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `model_line`
--
ALTER TABLE `model_line`
  MODIFY `idmodel_line` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admins_dataproj1` FOREIGN KEY (`dataproj_iddataproj`) REFERENCES `dataproj` (`iddataproj`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cb_actions`
--
ALTER TABLE `cb_actions`
  ADD CONSTRAINT `fk_cb_actions_item_types1` FOREIGN KEY (`item_types_iditem_types`) REFERENCES `item_types` (`iditem_types`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `fixed_datas`
--
ALTER TABLE `fixed_datas`
  ADD CONSTRAINT `fk_fixed_datas_dataproj1` FOREIGN KEY (`dataproj_iddataproj`) REFERENCES `dataproj` (`iddataproj`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_fixed_datas_fixed_datas_group1` FOREIGN KEY (`fixed_datas_group_idfixed_datas_group`) REFERENCES `fixed_datas_group` (`idfixed_datas_group`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `fixed_datas_group`
--
ALTER TABLE `fixed_datas_group`
  ADD CONSTRAINT `fk_fixed_datas_group_dataproj1` FOREIGN KEY (`dataproj_iddataproj`) REFERENCES `dataproj` (`iddataproj`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `fixed_data_media`
--
ALTER TABLE `fixed_data_media`
  ADD CONSTRAINT `fk_fixed_data_media_fixed_datas1` FOREIGN KEY (`fixed_datas_idfixed_datas`) REFERENCES `fixed_datas` (`idfixed_datas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_fixed_data_media_medias1` FOREIGN KEY (`medias_idmedias`) REFERENCES `medias` (`idmedias`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_item_types` FOREIGN KEY (`item_types_iditem_types`) REFERENCES `item_types` (`iditem_types`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `item_types`
--
ALTER TABLE `item_types`
  ADD CONSTRAINT `fk_item_types_dataproj1` FOREIGN KEY (`dataproj_iddataproj`) REFERENCES `dataproj` (`iddataproj`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `item_values`
--
ALTER TABLE `item_values`
  ADD CONSTRAINT `fk_item_values_item1` FOREIGN KEY (`item_iditem`) REFERENCES `item` (`iditem`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_item_values_model_line1` FOREIGN KEY (`model_line_idmodel_line`) REFERENCES `model_line` (`idmodel_line`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `item_value_media`
--
ALTER TABLE `item_value_media`
  ADD CONSTRAINT `fk_item_value_media_item_values1` FOREIGN KEY (`item_values_iditem_values`) REFERENCES `item_values` (`iditem_values`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_item_value_media_medias1` FOREIGN KEY (`medias_idmedias`) REFERENCES `medias` (`idmedias`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_dataproj1` FOREIGN KEY (`dataproj_iddataproj`) REFERENCES `dataproj` (`iddataproj`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_logs_logs_type1` FOREIGN KEY (`logs_type_idlogs_type`) REFERENCES `logs_type` (`idlogs_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `model_line`
--
ALTER TABLE `model_line`
  ADD CONSTRAINT `fk_model_line_item_types1` FOREIGN KEY (`item_types_iditem_types`) REFERENCES `item_types` (`iditem_types`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_model_line_item_types2` FOREIGN KEY (`item_types_iditem_types1`) REFERENCES `item_types` (`iditem_types`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
