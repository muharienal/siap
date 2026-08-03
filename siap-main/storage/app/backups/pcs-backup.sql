/*!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.5.25-MariaDB, for Linux (x86_64)
--
-- Host: database-1.chycaykeabp0.ap-southeast-2.rds.amazonaws.com    Database: pcs
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `approval_logs`
--

DROP TABLE IF EXISTS `approval_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trans_id` bigint NOT NULL,
  `approval_type_id` tinyint NOT NULL,
  `approval_status_id` tinyint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_ids` json NOT NULL,
  `approver_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_logs_trans_id_index` (`trans_id`),
  KEY `approval_logs_approval_type_id_index` (`approval_type_id`),
  KEY `approval_logs_approval_status_id_index` (`approval_status_id`),
  KEY `approval_logs_approver_id_index` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_logs`
--

LOCK TABLES `approval_logs` WRITE;
/*!40000 ALTER TABLE `approval_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `approval_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trans_id` bigint NOT NULL,
  `approval_type_id` tinyint NOT NULL,
  `approval_status_id` tinyint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_ids` json NOT NULL,
  `approver_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_trans_id_index` (`trans_id`),
  KEY `approvals_approval_type_id_index` (`approval_type_id`),
  KEY `approvals_approval_status_id_index` (`approval_status_id`),
  KEY `approvals_approver_id_index` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trans_date` timestamp NOT NULL,
  `ref_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_status_id` tinyint NOT NULL,
  `user_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `complaints_ref_number_unique` (`ref_number`),
  KEY `complaints_trans_date_index` (`trans_date`),
  KEY `complaints_approval_status_id_index` (`approval_status_id`),
  KEY `complaints_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `handphone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `org_structure_id` int DEFAULT NULL,
  `job_level_id` int DEFAULT NULL,
  `job_position_id` int DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'P',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  KEY `employees_user_id_index` (`user_id`),
  KEY `employees_name_index` (`name`),
  KEY `user_city_id` (`city_id`),
  KEY `user_org_structure_id` (`org_structure_id`),
  KEY `user_job_level_id` (`job_level_id`),
  KEY `user_job_position_id` (`job_position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,2,'Rizaldy Farhananda','10000001','1-224-377-9154','TK000001','farrell.rudy@runolfsson.com','483 Gislason Lodge Apt. 878\nO\'Reillychester, AK 18953-8861',NULL,NULL,2,4,4,'1999-12-04','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(2,3,'Hadi Winarso','13195077','+12547047136','TK2960121','marc24@bechtelar.com','9231 Hartmann Knoll Suite 877\nBorisfort, TX 90028-3279',NULL,NULL,2,4,4,'1992-07-18','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(3,4,'Nur Su Udi','13207502','+1-732-532-9635','TK2980121','ufay@okon.com','76008 Edwin Port\nEast Elroyborough, OH 74133-6373',NULL,NULL,2,4,4,'2005-11-22','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(4,5,'Dias Argha P.','13207512','626.409.6455','TK000002','corrine71@schiller.com','16987 Daija Squares\nSouth Janmouth, GA 16682-6589',NULL,NULL,2,4,4,'2003-11-12','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(5,6,'Teguh Hariyo U.','13207548','626-910-3922','TK000003','padberg.neva@gmail.com','1693 Halle Junction\nKubshire, IN 05238-7119',NULL,NULL,2,4,4,'2000-06-21','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(6,7,'Rochmanu Surya','5499503','(732) 246-1038','K1141016','zackary74@hotmail.com','6956 Gibson Pines\nLake Corine, NC 18961',NULL,NULL,2,4,4,'1990-10-28','L','2025-07-19 20:25:48','2025-07-19 20:25:49'),(7,8,'M. Salim','5608966','+1-646-613-9565','9','smitham.ian@hotmail.com','908 Lafayette Heights\nCotyborough, KS 56046',NULL,NULL,2,4,4,'1998-12-18','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(8,9,'M. Fujianto','5589523','(689) 695-4278','K1890121','drake33@gmail.com','51556 Sipes Isle Apt. 014\nDuBuquefort, RI 33524',NULL,NULL,2,4,4,'1989-10-08','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(9,10,'M Mufidin','5647778','360.949.7435','TK2560419','otilia43@purdy.com','995 Emmerich Point\nSchadenside, MA 47943',NULL,NULL,2,4,4,'1999-12-14','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(10,11,'Lutfi Zainudin','5625666','+15012865078','K470507','jakubowski.einar@hotmail.com','81813 Yundt Lock\nMrazville, TX 35135',NULL,NULL,2,4,4,'1996-03-14','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(11,12,'Muhamad Wafi Musabbih','5574391','1-602-869-7708','TP341021','hill.scotty@considine.net','82612 Ethyl Valleys\nNew Dorothea, NC 48217',NULL,NULL,2,4,4,'2000-02-04','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(12,13,'Nadya Maharani','5595121','(859) 549-4312','TP300721','gail.altenwerth@friesen.com','15417 Kulas Isle\nNorth Dannymouth, FL 11314-0638',NULL,NULL,2,4,4,'1994-05-28','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(13,14,'Sufaroham','5572932','360-355-6255','TP310721','ortiz.virginia@koelpin.com','65818 Jast Wells Suite 844\nKuphalhaven, ID 06360',NULL,NULL,2,4,4,'1998-01-06','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(14,15,'M. Isnaini','5577861','938.634.3801','K1001013','nedra.ledner@wuckert.info','882 Herzog Camp\nNew Felix, FL 30643-9764',NULL,NULL,2,4,4,'1998-12-30','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(15,16,'Suyono SBY','5569704','1-312-392-8002','TP320821','asha.keeling@wiza.com','1335 Casimir Bypass\nWest Sophieborough, OH 67436',NULL,NULL,2,4,4,'2001-12-13','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(16,17,'Rudolphus AQ Radya P','10000002','272-538-4706','T000000','sauer.jacinto@lehner.com','5457 Elwyn Knolls\nYundtton, IA 41560',NULL,NULL,2,4,4,'2002-12-17','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(17,18,'Finny Ryanita','10000003','+1-279-775-1606','K1820121','mazie.hahn@hotmail.com','6782 Stamm Port\nBartonbury, AR 01215',NULL,NULL,2,4,4,'2003-07-12','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(18,19,'Sugianto','10000004','(862) 313-5372','TP250320','imelda95@mclaughlin.com','194 Walsh Pass\nGretchenshire, WY 26455',NULL,NULL,2,4,4,'1992-02-23','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(19,20,'Indra Mahabat','5625947','1-910-469-5153','TK2970121','mohr.reggie@feil.com','44570 Jeanne Parkways Suite 433\nLake Chauncey, VA 83065-0743',NULL,NULL,2,4,4,'2001-07-06','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(20,21,'Takrip','10000005','541-962-3774','TP070218','pborer@hotmail.com','975 Hilpert Island Suite 590\nWest Cole, ND 44368',NULL,NULL,2,4,4,'1989-03-06','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(21,22,'Wikan Sutirto Aribowo','10000006','+1-760-880-4210','T314620','abshire.elva@gmail.com','6584 Davon Crossroad Suite 383\nWest Jayceview, NM 09288',NULL,NULL,2,4,4,'1992-09-27','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(22,23,'Witan Hardianto','10000007','651.393.4306','T324675','gcrona@herman.info','8620 Elmore Road\nNew Hillardstad, MA 94962',NULL,NULL,2,4,4,'2002-04-03','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(23,24,'Yoga K','10000008','(864) 626-3590','123456','hlesch@yahoo.com','2734 Enoch Extension\nWest Diamondstad, MA 11182',NULL,NULL,2,4,4,'2002-07-27','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(24,25,'Yudo','10000009','+1-360-646-0692','12345','dion64@dibbert.net','92350 Kutch Prairie Suite 161\nLake Judeberg, AZ 16933-6212',NULL,NULL,2,4,4,'1991-03-29','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(25,26,'Zainul Arifin','5657523','276.894.4234','TP080218','roxane.bins@torphy.com','9652 Hammes Overpass Apt. 143\nSouth Palmafort, MA 43316-5090',NULL,NULL,2,4,4,'1991-06-06','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(26,27,'Syaifullah','5494751','+1.480.441.3213','K1111116','garrett.erdman@yahoo.com','562 Gladys Cliffs\nEast Jermain, OH 76076-8762',NULL,NULL,2,4,4,'1993-12-16','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(27,28,'Kusnan','5520658','608-691-2460','K360496','dolores22@hotmail.com','149 Macejkovic Way\nKaiabury, SC 63055-7958',NULL,NULL,2,4,4,'1998-11-02','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(28,29,'Yudha Ardhi Saputro','5654466','+1.272.231.2801','K630409','kira02@carroll.net','556 Scot Knolls Apt. 167\nLake Coltonstad, KS 49053-5758',NULL,NULL,2,4,4,'1991-05-08','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(29,30,'Khuirul Huda','5547467','(605) 504-5170','K530507','gerard.crona@gutkowski.com','266 Karlie Fields Apt. 071\nLakinville, NC 94111-3526',NULL,NULL,2,4,4,'1990-01-26','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(30,31,'Sigit Setiawan','5569536','352-523-4865','K2070121','ptremblay@hotmail.com','30093 Otho Junction\nEast Royceport, NV 26255-5021',NULL,NULL,2,4,4,'1996-08-04','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(31,32,'Setiawan','5532105','+1-657-825-5225','K1570120','wilburn.mckenzie@satterfield.net','6132 Predovic Turnpike\nDallasside, HI 09633',NULL,NULL,2,4,4,'1993-01-05','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(32,33,'Suwandi','5588443','1-863-247-2173','K951013','sauer.enola@schroeder.com','998 Nadia Stream Apt. 825\nPort Laila, RI 35569',NULL,NULL,2,4,4,'2004-08-08','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(33,34,'Wahyu Nanang Setiawan','5578032','1-718-386-1388','K2130121','brody.little@kshlerin.info','726 Margarete Hill Suite 572\nSouth Malcolmmouth, AR 70797',NULL,NULL,2,4,4,'1992-01-08','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(34,35,'Winardi','5581802','+1.725.372.7641','K1241016','rath.ashlee@gmail.com','3142 Fritsch Inlet Apt. 062\nNorth Minerva, TN 88404',NULL,NULL,2,4,4,'2002-05-20','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(35,36,'Wandira Gustiani','5558641','463.485.7767','K1600120','heaney.eileen@oconnell.net','346 Candida Row\nRodolfoview, MO 36665-3901',NULL,NULL,2,4,4,'1994-11-02','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(36,37,'Sulistiowati','5521618','(947) 539-2483','K510507','adriel.crona@gmail.com','6033 Schimmel Walk Suite 481\nWillton, TX 35388-1415',NULL,NULL,2,4,4,'1997-04-28','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(37,38,'Yulianto','5627234','551-646-2914','K650310','jarrett.hahn@gmail.com','332 Rippin Prairie\nNitzscheview, CA 57694-7384',NULL,NULL,2,4,4,'1993-05-27','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(38,39,'Fendi Tristanto','5554609','423-248-6115','K911013','enoch.schowalter@sawayn.net','8874 Grant Garden\nFeeneymouth, MD 19722',NULL,NULL,2,4,4,'1997-02-10','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(39,40,'Fatkhul Muin','5623351','+1-929-303-5550','K480507','pkohler@hansen.com','533 Birdie Plain\nEast Edahaven, WV 62143-6282',NULL,NULL,2,4,4,'1994-10-09','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(40,41,'Johan Ari Nofiyanto','5539316','+1.586.826.0719','K1501017','eichmann.tanya@hammes.com','7557 Samir Ferry Apt. 661\nNorth Adriel, IN 31714',NULL,NULL,2,4,4,'2003-02-03','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(41,42,'Tony Murdianto','5499231','+1 (301) 689-7841','K1271216','hettinger.lacy@gmail.com','2816 Genevieve Mews Apt. 135\nNew Darien, MD 38304',NULL,NULL,2,4,4,'2003-08-26','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(42,43,'Karenda Achmad Fatoni','5619716','+1-551-243-5881','K1640120','zulauf.willie@stark.com','2560 Kuhn Village\nRebashire, UT 95608-2634',NULL,NULL,2,4,4,'1991-03-04','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(43,44,'Sunu Arief Budianto','5638985','1-984-377-8965','K2220221','axel99@ohara.com','5903 Laurel Rue Apt. 719\nEast Todside, NC 62000',NULL,NULL,2,4,4,'1993-06-10','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(44,45,'Ida Rifdah','5543954','332-269-2081','K1850121','lbaumbach@oconnell.com','561 Kelvin Circles\nDillanberg, WI 10645',NULL,NULL,2,4,4,'1994-09-06','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(45,46,'Suhendra Trifandani','5521472','(308) 475-7596','K2090121','sage85@tremblay.org','377 Marjolaine Circle Suite 870\nAnaisport, KY 61765',NULL,NULL,2,4,4,'1998-05-20','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(46,47,'Wahyu Hidayat','5572929','+1 (864) 217-1683','K1620120','kling.jacquelyn@hotmail.com','372 Jerde Port Apt. 736\nSouth Brad, OR 97516-7593',NULL,NULL,2,4,4,'1994-04-12','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(47,48,'Zulfah Cahya Dresta Ramad','5560255','878.920.0219','K2140121','lesch.david@kerluke.biz','63752 Patrick Locks Suite 622\nEast Brandiview, CO 24695-9351',NULL,NULL,2,4,4,'2003-08-09','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(48,49,'Widiarti Kuswardani','5541076','913.205.3144','K750412','kathryne.cremin@gmail.com','36605 Effertz Glen Suite 287\nWunschberg, WI 90271-2677',NULL,NULL,2,4,4,'1990-09-11','P','2025-07-19 20:25:48','2025-07-19 20:25:50'),(49,50,'Yan Wenesa','5534601','209.728.6153','K941013','effertz.grayce@gmail.com','3132 Liliane Garden Suite 467\nNorth Vinceport, NC 88039-6040',NULL,NULL,2,4,4,'2001-06-07','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(50,51,'Samsul Ichwan','5632186','858.370.8782','K1321216','phyllis68@hammes.com','8272 Wilhelmine Way Suite 098\nWest Rhett, AZ 15532-5668',NULL,NULL,2,4,4,'1989-02-28','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(51,52,'Gresma Afif Awanis','5595198','872.997.6163','K2180221','cormier.citlalli@hotmail.com','747 Friesen Vista\nZemlakborough, CO 06019-9463',NULL,NULL,2,4,4,'1995-03-09','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(52,53,'Ferdiyanto Indrayana','5604651','+14583362876','K780412','zosinski@gmail.com','119 Schowalter River\nBrianaton, NC 73555-2193',NULL,NULL,2,4,4,'2003-05-19','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(53,54,'Kasbolah','5603609','210.876.6178','K820412','lakin.jessika@senger.com','4743 Jerde Forges Apt. 627\nSouth Floydberg, ID 39876',NULL,NULL,2,4,4,'1994-08-07','L','2025-07-19 20:25:48','2025-07-19 20:25:50'),(54,55,'Sumardiono','5533833','414-974-8364','K2100121','alyce.streich@gmail.com','51293 Strosin Drive Apt. 179\nKennaborough, GA 56171-2975',NULL,NULL,2,4,4,'1991-04-19','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(55,56,'Venti Istriani','5504101','520-462-8670','K850712','charity48@hotmail.com','630 Kyla Lakes\nEast Jerald, KS 82925',NULL,NULL,2,4,4,'2004-07-31','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(56,57,'Jojok Nursijo','5524737','323-755-5184','K191191','jarrell70@hotmail.com','6621 Brenda Island Suite 542\nBodetown, RI 58192',NULL,NULL,2,4,4,'2000-11-25','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(57,58,'Syamsul Ma Arif','5511814','517-587-1431','K2230221','abuckridge@herzog.com','66719 Barbara Valley Apt. 853\nNorth Hoseaburgh, TN 58403-3224',NULL,NULL,2,4,4,'1989-07-14','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(58,59,'Sugiono','5602162','479-639-9233','K871013','kylie11@swaniawski.com','6236 Becker Throughway\nBlandastad, IL 49383-0643',NULL,NULL,2,4,4,'1989-01-03','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(59,60,'Hadi Kuswoyo','5634682','1-424-608-5605','K560507','darren88@gmail.com','287 Lockman Glen Apt. 184\nWest Priscillahaven, AL 14301',NULL,NULL,2,4,4,'2004-01-24','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(60,61,'Trilaksono Widiyanto','5582497','+1-470-527-9464','K1360117','loy02@rempel.com','518 Clemmie Mill\nBaumbachfurt, SD 20972-4317',NULL,NULL,2,4,4,'2001-06-26','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(61,62,'Junaidi Salat','5535420','(743) 258-9571','K1880121','agustina33@yahoo.com','577 Serena Station\nWizaburgh, PA 38748',NULL,NULL,2,4,4,'1997-02-21','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(62,63,'Iswandi','5628036','1-878-474-8427','K931013','eladio34@yahoo.com','759 Sophie Field Apt. 004\nPort Alden, AZ 70920',NULL,NULL,2,4,4,'1995-12-19','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(63,64,'Iqbal Verdianata Rifer','5571440','678.558.6701','TP230220','qrogahn@hotmail.com','89943 Hickle Stravenue\nJoyceview, MA 58932',NULL,NULL,2,4,4,'1990-05-08','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(64,65,'Joko Setiono','5495514','+1-463-381-4547','K600409','pheaney@kemmer.net','396 Gutmann Throughway Suite 525\nKuphalfort, MT 45381',NULL,NULL,2,4,4,'2001-05-28','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(65,66,'Isa Ardiansyah Afandi','5521925','(239) 716-8242','K1870121','lily97@yahoo.com','1424 Isom Skyway\nNorth Stephanyton, WA 60415',NULL,NULL,2,4,4,'2002-01-19','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(66,67,'Gunawan','5561346','+1-640-312-8718','K520507','wisoky.einar@gmail.com','229 Ortiz Squares\nPort Alejandrinton, MD 13148-4461',NULL,NULL,2,4,4,'1994-12-25','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(67,68,'Ikha Nur Diana','5542959','+1 (912) 558-2628','K1491017','ara16@hodkiewicz.org','44610 Leanna Center\nNorth Furmantown, MN 33486',NULL,NULL,2,4,4,'2002-07-14','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(68,69,'Swacita Ayu Fazhari','5518759','660-340-0976','K2120121','ykirlin@breitenberg.com','8720 Turner Branch Apt. 132\nWest Thea, KY 48218',NULL,NULL,2,4,4,'1990-04-18','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(69,70,'Siti Marufah','10000010','+1 (432) 412-5099','K1580120','verda.goodwin@carroll.info','1760 Caroline Crest\nLake Hubertborough, MN 90936',NULL,NULL,2,4,4,'2002-07-29','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(70,71,'Kanika Nanda Agung Budiawan','','(585) 914-7126','K1121116','sarah76@yahoo.com','659 Becker Dam Apt. 075\nRowemouth, NH 18538-3511',NULL,NULL,2,4,4,'1995-07-24','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(71,72,'Rr Chintiadewi P.','5108583','+1.979.991.5425','K2040121','jamaal20@schaefer.com','1031 Hahn Field\nAlizeberg, TX 19541',NULL,NULL,2,4,4,'2001-04-06','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(72,73,'Roy Bagus Baskoro','10000011','754.884.5212','K1410617','virgil72@hotmail.com','2184 Cyrus Islands Apt. 233\nWest Garrett, IL 25953-0544',NULL,NULL,2,4,4,'2004-04-18','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(73,74,'Sugeng Hariadi','5582489','364-427-0921','TK3030221','dusty54@olson.com','630 Marquis Mall Apt. 323\nGerryview, IL 65973-1833',NULL,NULL,2,4,4,'1990-11-27','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(74,75,'Fahrizal Rizky Putra P.','15190664','574-571-3499','K1810121','sauer@yahoo.com','14152 Cruz Mount\nNorth Dustin, NV 39701',NULL,NULL,2,4,4,'2000-07-07','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(75,76,'Seftianita N','5579938','301-889-8159','K2060121','mohr.alvina@roob.info','599 Ian Points Suite 313\nHermanchester, MI 52061-5296',NULL,NULL,2,4,4,'1990-10-24','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(76,77,'Sugeng Triantoro','5564953','+1 (858) 403-5398','K1311216','curtis82@daniel.org','24911 Mariana Divide\nSheamouth, TN 85581',NULL,NULL,2,4,4,'1996-06-29','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(77,78,'Sugi Yanto','5588057','+1 (847) 292-8670','K2080121','bmosciski@hotmail.com','5412 Harris Green Apt. 391\nVandervortfort, GA 60283',NULL,NULL,2,4,4,'1991-06-25','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(78,79,'Hendra Kurniawan','5513870','412.294.3120','K1840121','torrey45@hotmail.com','12857 Hermina Islands Suite 763\nEast Pearlie, ME 60658-7442',NULL,NULL,2,4,4,'1990-05-25','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(79,80,'Yolanda Noviyanti Arifin','5626922','(425) 497-3666','K2240221','huel.sallie@gmail.com','33615 Prohaska Light Suite 833\nEast Diana, MA 33187',NULL,NULL,2,4,4,'2003-09-25','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(80,81,'Kusenen','5610333','+1 (929) 938-2257','K1380117','ignacio.boyer@rempel.com','83516 Cartwright Spurs\nSouth Cristopherville, OR 16126',NULL,NULL,2,4,4,'1990-10-10','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(81,82,'Supriyana','5498370','1-352-679-7881','K450600','marilie22@murazik.com','874 Langosh Courts\nWatersmouth, VT 63293-9528',NULL,NULL,2,4,4,'1997-05-26','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(82,83,'Izza Millati','5602415','+19294286594','K730412','lenora03@cartwright.com','64533 Ferry Terrace Suite 177\nPort Noblemouth, IN 83518',NULL,NULL,2,4,4,'1996-10-20','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(83,84,'Iwan Kumoro','5573395','+1 (934) 666-7007','K790412','florian89@gmail.com','23300 Gerhold Spring Apt. 853\nSouth Kirsten, MO 09747-4090',NULL,NULL,2,4,4,'2002-06-24','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(84,85,'Heri Purnomo','5574931','+1-843-917-4545','K1301216','cmaggio@orn.com','50914 Baby Mills Apt. 386\nClayhaven, OK 12233',NULL,NULL,2,4,4,'1995-09-08','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(85,86,'Suripto','5566132','339.512.9573','K1370117','madelynn.farrell@hotmail.com','1674 Aaron Stravenue Suite 808\nBauchberg, NH 58338',NULL,NULL,2,4,4,'1990-11-18','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(86,87,'Hendra Nur Suherman','5630555','215.798.8395','TK2520419','ewell.langosh@yahoo.com','8430 Olson Isle Suite 509\nSouth Jocelyn, IN 16514',NULL,NULL,2,4,4,'1989-04-29','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(87,88,'Iwan Hermansah','5494972','838.922.9568','K1011013','beau.lockman@moen.com','923 Streich Island Suite 891\nEast Kenny, AK 67757',NULL,NULL,2,4,4,'1991-06-16','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(88,89,'Sunoto','5496397','+1-762-560-4814','K1061013','schuppe.sammy@kerluke.biz','98767 O\'Kon Shoals\nNorth Jaydon, GA 79697',NULL,NULL,2,4,4,'2005-04-13','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(89,90,'Suroso','5579713','417-548-4252','TP160119','nicolas.rupert@ward.info','4819 Vincenzo Mountains\nOfeliaville, WY 44348',NULL,NULL,2,4,4,'2001-03-08','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(90,91,'Sukarso','10000012','1-361-225-1726','K1291216','isobel92@gmail.com','4759 Waters Mountains Apt. 551\nBlickburgh, CA 57063-4916',NULL,NULL,2,4,4,'1994-06-08','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(91,92,'Suyono LMG','5587216','+1-725-422-2928','K2110121','irving63@bartell.com','86029 Maci Way Apt. 925\nNew Joanny, CO 54678-4319',NULL,NULL,2,4,4,'1996-11-11','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(92,93,'Siti Rahayu','5573726','(731) 549-4803','K891013','shea99@kautzer.com','49836 Grady Canyon\nNorth Joanneburgh, MT 09271-8116',NULL,NULL,2,4,4,'2001-07-31','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(93,94,'Siti Rofikoh','5525057','(251) 915-6723','K810412','lvolkman@robel.biz','65726 Makayla Mission Apt. 338\nSouth Cindy, WV 23722',NULL,NULL,2,4,4,'2005-01-01','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(94,95,'Sara Nita Anggraini','5617284','+16169640305','K2050121','gislason.tyrique@yahoo.com','534 Strosin Port\nBoyleshire, ME 51428',NULL,NULL,2,4,4,'2005-08-16','P','2025-07-19 20:25:48','2025-07-19 20:25:51'),(95,96,'Fabian Lazarus Ramadhan','5566412','(872) 220-0694','K1800121','adelle.kulas@hotmail.com','18694 Ida Pike Suite 912\nEltonland, WY 46351-2023',NULL,NULL,2,4,4,'2005-12-24','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(96,97,'Sindu Anggoro','5505516','(928) 984-4366','K1481017','judson29@oconnell.com','267 Karina Coves Suite 318\nSouth Nelson, CA 71134-7313',NULL,NULL,2,4,4,'1999-08-25','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(97,98,'Saifudin Arif','5648337','+1.505.296.1344','K1511119','samantha75@greenholt.net','77109 Jacobi Neck Apt. 976\nNorth Micah, SC 09548-7832',NULL,NULL,2,4,4,'2000-02-07','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(98,99,'Indra Kusuma Kurniawan','5609947','213-527-1459','K1860121','gyost@hotmail.com','213 Princess Forks Suite 676\nPort Marques, WV 16360',NULL,NULL,2,4,4,'2004-09-09','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(99,100,'Fath Iqbal Alfarisi','5584334','(838) 616-1181','K1610120','dashawn75@wuckert.org','2725 Terry Ridge Suite 659\nWest Raoul, MD 46549',NULL,NULL,2,4,4,'2001-11-16','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(100,101,'Lelana Arie Soekamto','5588984','+1 (830) 365-8035','K700310','friedrich20@hotmail.com','668 Izabella Hill Apt. 239\nNellefurt, CT 61287-0399',NULL,NULL,2,4,4,'2000-04-26','L','2025-07-19 20:25:48','2025-07-19 20:25:51'),(101,102,'Fairuzi Zakiyah','5507925','(713) 687-0934','K1630120','tanner.mcclure@douglas.com','27582 O\'Hara Mountains\nMilanside, TX 10742-0620',NULL,NULL,2,4,4,'2000-06-05','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(102,103,'Fajar Lazuardi','5513322','802-702-6982','K1130616','marjorie60@gmail.com','6954 Josue Road Apt. 411\nNew Caesar, MS 50867-1588',NULL,NULL,2,4,4,'2003-04-04','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(103,104,'Siti Nur Hasanah','5506214','832.631.1674','K2210221','ritchie.lyda@yahoo.com','501 Eugenia Crescent\nPort Josie, MN 38785-1169',NULL,NULL,2,4,4,'1994-12-01','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(104,105,'Frida Gasiani','5499035','+17038089552','K1830121','matteo.rogahn@kerluke.biz','12303 Vivian Mission\nNorth Celestineberg, VA 46480',NULL,NULL,2,4,4,'1989-01-10','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(105,106,'Ferry Kustianto','5623218','408.790.0130','K981013','eleuschke@yahoo.com','693 Melvin Keys\nGreenfelderville, CO 40089-0532',NULL,NULL,2,4,4,'1999-03-11','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(106,107,'Siswanto','5594940','(831) 570-8152','TK3020221','brekke.julianne@funk.info','4708 Rosario Burgs Suite 311\nLake Sheaville, IL 04035-0373',NULL,NULL,2,4,4,'1998-05-12','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(107,108,'Early Hidayat','5494359','1-616-617-9864','TK3000221','jgraham@kautzer.com','9672 Stevie Mountains Apt. 958\nHeathcoteland, OK 52086',NULL,NULL,2,4,4,'2004-03-11','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(108,109,'Nuzul Farida Arini','7778549','1-539-468-8998','K1520120','shaina66@gmail.com','558 Rozella Manor Apt. 740\nPort Ericka, TN 95087',NULL,NULL,2,4,4,'2004-02-02','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(109,110,'Rico Gally Pradana','5558813','475.565.4859','K1281216','lhilpert@kuhlman.com','330 Reilly Green Suite 262\nNorth Agustin, OK 49437',NULL,NULL,2,4,4,'2003-06-25','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(110,111,'Chusnatun Kamilah','5501846','779-259-9218','K2150221','brittany72@gmail.com','4087 Rath Parks Apt. 508\nNew Eileen, ME 13887',NULL,NULL,2,4,4,'2001-06-02','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(111,112,'Choirunita','5534915','607.978.9691','K1181016','brendan.krajcik@aufderhar.biz','1680 Leannon Roads Apt. 225\nKyleberg, WA 01534-2407',NULL,NULL,2,4,4,'1999-11-07','P','2025-07-19 20:25:48','2025-07-19 20:25:52'),(112,113,'Arif Gunawan','5564849','(606) 900-2903','K971013','jazmin.adams@hotmail.com','1962 Giuseppe Pines\nOndrickamouth, MI 81007',NULL,NULL,2,4,4,'1998-09-10','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(113,114,'Dodi Ismono','15193247','+1-754-744-1823','K2170221','fmann@hotmail.com','23574 Audie Overpass Suite 447\nSalmaland, NH 17479',NULL,NULL,2,4,4,'2004-08-05','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(114,115,'Angga Bagus Prasetya W.','5647350','(906) 423-4691','K760412','angie77@kuhlman.com','4355 Mireille Parkway Suite 702\nNew Adanbury, HI 45629',NULL,NULL,2,4,4,'2002-07-28','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(115,116,'Dhika Pratama Putra','5557756','+18034650284','K1560120','tony.gusikowski@rath.com','258 Eladio Course Suite 329\nCliftonmouth, NJ 02947',NULL,NULL,2,4,4,'1991-04-25','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(116,117,'Parjianto','5512125','(260) 594-5824','K570507','krystel54@yahoo.com','7651 Newton Islands\nYundtchester, WA 78817-7963',NULL,NULL,2,4,4,'1999-05-16','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(117,118,'Adityo Wibowo','5559634','+1.816.242.5255','pcs2','bridget.rice@hotmail.com','711 Gottlieb Unions Apt. 866\nHaskellland, MN 24156-5698',NULL,NULL,2,4,4,'2005-07-08','L','2025-07-19 20:25:48','2025-07-19 20:25:52'),(118,119,'Dhanniar Oktiara Caesarre','5610034','1-551-804-1910','K1780121','camron.cremin@monahan.com','3503 Chesley Underpass\nSouth Anna, OR 86850-2244',NULL,NULL,2,4,4,'1993-04-24','P','2025-07-19 20:25:49','2025-07-19 20:25:52'),(119,120,'Muhammad Ainur Rizki','7778546','541.482.2772','TK2530419','eleonore09@hotmail.com','4478 Monique Islands\nWest Earline, SC 10410-1557',NULL,NULL,2,4,4,'2004-09-22','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(120,121,'Arif Budiarto','1031514','1-516-641-3997','TP180719','ervin64@schneider.biz','9466 Ova Mount\nWest Marioport, PA 99732-9613',NULL,NULL,2,4,4,'1993-01-29','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(121,122,'Ade Rossa Jibriliastiti','5590788','681-706-4044','K1660121','ernie.jerde@white.com','1281 Dickens Ramp\nNew Jerodtown, GA 23660',NULL,NULL,2,4,4,'1992-01-25','P','2025-07-19 20:25:49','2025-07-19 20:25:52'),(122,123,'Mucholis Choir','5631582','+1-863-466-1299','K350496','kenneth.brekke@auer.com','4208 Renee Greens Apt. 703\nStarkton, VT 96506',NULL,NULL,2,4,4,'2003-08-04','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(123,124,'Misnan','5542385','331.931.4068','1','fatima79@ondricka.com','3997 Howell Divide Apt. 595\nNew Flossietown, NM 06394',NULL,NULL,2,4,4,'1991-10-13','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(124,125,'Dwi Purwanto','5095486','915.803.4714','2','stroman.norwood@collins.com','155 Dessie Island\nWymanhaven, NY 97662-4281',NULL,NULL,2,4,4,'2004-04-05','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(125,126,'Sutiyono','5533382','859.646.4948','3','heidenreich.jared@boyle.com','79911 Leone Union\nNew Kelton, OK 62343-5157',NULL,NULL,2,4,4,'1992-05-27','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(126,127,'Nurlaila','5506786','+12486363201','4','cristobal.abbott@kemmer.com','670 Jalen Center Suite 785\nPort Trisha, WA 98207-2427',NULL,NULL,2,4,4,'2000-10-17','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(127,128,'Sochibul Humam','5578420','662.447.1914','6','abe.prosacco@pouros.info','275 Jacobson Tunnel Suite 940\nWest Marietta, WI 45595-6415',NULL,NULL,2,4,4,'2003-07-21','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(128,129,'Danis Woro','5601809','617.814.2314','K610409','mlesch@yahoo.com','826 Kuhn Cape Suite 229\nElizabethport, MA 75633',NULL,NULL,2,4,4,'2003-09-07','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(129,130,'Muhammad Syaiful Fathoni','5506776','682-572-7309','K1980121','cschinner@gmail.com','369 Rogahn Ferry Suite 991\nSchroederfurt, UT 01246-6824',NULL,NULL,2,4,4,'1989-07-01','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(130,131,'Choirul Chusnah','10000013','870-804-1848','K410600','kathryne43@yahoo.com','10053 Gaylord Squares\nLindmouth, NY 30611-4048',NULL,NULL,2,4,4,'2003-05-30','P','2025-07-19 20:25:49','2025-07-19 20:25:52'),(131,132,'Danang Jaka H P C P Ar','5597379','(864) 279-6251','K1051013','constance.stracke@yahoo.com','3743 Noemie Points\nNew Krystinatown, SD 89915',NULL,NULL,2,4,4,'2001-01-22','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(132,133,'Muhammad Dhofir','5575649','(603) 552-7620','K1331216','ernest.lind@hotmail.com','778 Nicolas Terrace Suite 247\nHamillborough, MD 42964-3617',NULL,NULL,2,4,4,'1989-01-28','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(133,134,'Rizqi Camalia','5587522','+1 (689) 247-1726','K851013','aurelie.kautzer@hotmail.com','627 Strosin Glens\nPort Leola, AR 88105-9345',NULL,NULL,2,4,4,'2003-03-23','P','2025-07-19 20:25:49','2025-07-19 20:25:52'),(134,135,'Muhammad Aries Supriyono','5546557','+1-605-389-5751','K1341216','lillie.ebert@abernathy.com','237 Lexus Underpass\nNorth Lelah, MS 25874',NULL,NULL,2,4,4,'1994-10-06','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(135,136,'Darmaji','15192490','1-951-631-4890','K1091013','seamus.kub@zieme.com','9515 Weissnat Drive Apt. 361\nSouth Efrain, WY 76117-9402',NULL,NULL,2,4,4,'2000-12-14','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(136,137,'M. Shofil','5614087','+18186467679','7','curt.wiza@kulas.com','83341 Thompson Knoll Apt. 173\nPort Makaylafort, NE 79646',NULL,NULL,2,4,4,'2001-06-01','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(137,138,'Suprapto','5536214','575-271-4376','8','vickie.rodriguez@jast.com','442 Angelica Meadows Suite 820\nJastchester, IL 80279',NULL,NULL,2,4,4,'1997-11-12','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(138,139,'Muji Widodo','5622395','(239) 842-8222','TP260520','harmon.renner@gmail.com','34649 Lonzo Squares\nNew Hipolitoview, MA 98156-3614',NULL,NULL,2,4,4,'2001-07-30','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(139,140,'Muhammad Toha Mahsun','5653182','1-814-898-0173','K2000121','orland35@smith.org','576 Anika Keys Apt. 301\nWest Hortense, DE 49518',NULL,NULL,2,4,4,'1997-10-01','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(140,141,'Nanang Triantoro','7778550','1-469-227-1287','K1041013','martin31@gmail.com','9239 Hansen Via\nNorth Hillard, CO 05193-3625',NULL,NULL,2,4,4,'2001-11-12','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(141,142,'Drs. Ec. Arka Widya Udaka','','+18637855025','TP100218','keeling.terrance@huel.com','474 Montana Gateway Apt. 075\nBernierhaven, FL 29940-9977',NULL,NULL,2,4,4,'1997-07-25','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(142,143,'Eko Anjasmoro','5095485','1-706-652-4591','5','rolfson.amely@gmail.com','3525 Emerson Station Suite 003\nBrendonshire, PA 03524-4438',NULL,NULL,2,4,4,'1993-11-28','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(143,144,'Ongky Martha Dwiyananda','5574309','806-215-3375','K1420617','truecker@yahoo.com','8027 Klocko Wells Apt. 159\nNorth Travistown, WI 59631',NULL,NULL,2,4,4,'1993-06-06','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(144,145,'Dody Kurniawan','5548402','(763) 901-6550','K1790121','daron.kub@parisian.com','95737 Shields Dam Apt. 409\nPort Diegoland, WY 90613',NULL,NULL,2,4,4,'2000-08-01','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(145,146,'Pujianto Nur Romadhon','5658051','203-910-1021','TP220120','arno01@mitchell.com','58721 Tiana Overpass Suite 584\nFarrellfurt, OR 17865-8984',NULL,NULL,2,4,4,'1999-06-21','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(146,147,'Agung Priyanto','5556061','1-248-869-2622','K1680121','keebler.alyce@gmail.com','447 Baumbach Village Apt. 701\nLake Eugeniaborough, NV 90694-3336',NULL,NULL,2,4,4,'2003-07-25','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(147,148,'Arif Suriawan','5595998','1-934-493-8925','K500507','william.wisozk@yahoo.com','147 Rippin Station Apt. 514\nOlsonburgh, AR 05665-3724',NULL,NULL,2,4,4,'2001-01-26','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(148,149,'Dwi Jatmiko Utomo','5627760','(503) 978-9223','K1441017','kertzmann.kariane@hotmail.com','81627 Tamia Villages\nPort Carolina, LA 12331-9818',NULL,NULL,2,4,4,'1993-09-26','L','2025-07-19 20:25:49','2025-07-19 20:25:52'),(149,150,'Edy Supatmoko','5590222','+1-571-538-2135','K550507','iferry@watsica.com','92320 Akeem Square Suite 485\nPort Joesph, OH 48886',NULL,NULL,2,4,4,'1992-08-16','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(150,151,'Nur Rohman','7778548','269-912-1363','K1261216','vcrona@gmail.com','1689 Walker Oval\nQuitzonshire, VA 28653-8922',NULL,NULL,2,4,4,'2001-01-28','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(151,152,'Putri Ayu Dwi Lestari','5501462','+1-678-497-5331','K2030121','hrussel@yahoo.com','55323 Itzel Drive Suite 749\nConnview, NE 91834',NULL,NULL,2,4,4,'1992-03-27','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(152,153,'Aurelia Agatha','5535894','918-584-5246','TK2670919','maria43@mayert.info','9294 Thad Light\nSouth Lamar, TX 38332-5052',NULL,NULL,2,4,4,'1990-04-21','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(153,154,'Rizky Kokoh Pranadita','5550568','+1-520-908-3296','K1451017','welch.sarai@stracke.biz','94710 Ola Loop Apt. 151\nNew Vincehaven, SD 33866-6794',NULL,NULL,2,4,4,'1990-06-12','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(154,155,'Evandi Prima Putra','5653671','+1-985-377-4194','K840412','collins.abraham@windler.com','48691 Jane Alley\nSteuberhaven, AL 20196',NULL,NULL,2,4,4,'1997-08-08','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(155,156,'Muhammad Noor Ady Pratama','5529207','(520) 904-4711','K1171016','prosacco.andreane@yahoo.com','2830 Imelda Place\nKenyattaside, WA 88195',NULL,NULL,2,4,4,'1991-06-05','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(156,157,'Aginta Erbinda S.S','15185095','+1-323-806-5288','K1670121','lysanne.green@harber.org','26803 Celia Prairie Apt. 372\nNorth Gloria, TX 86721',NULL,NULL,2,4,4,'2002-09-01','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(157,158,'Moh.Mahfudz','5550777','1-520-346-3967','K540507','strosin.jimmie@gmail.com','8643 Reichert Village\nJohanbury, CA 69449-1541',NULL,NULL,2,4,4,'1999-10-31','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(158,159,'Eko Agus Purwanto','5619621','+1 (346) 318-3078','TP021019','wilkinson.eda@yahoo.com','7180 Moshe Haven Suite 374\nSouth George, CT 87028-9486',NULL,NULL,2,4,4,'1993-10-17','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(159,160,'Ayu Rahajeng Lalityasari','5536425','+1-341-513-8493','K1760121','vsteuber@hotmail.com','9942 Destin Isle\nEast Abdielhaven, MA 58644-0568',NULL,NULL,2,4,4,'1990-09-02','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(160,161,'Rischa Sandia Anggriani','','1-713-995-2946','K720412','bartell.sammie@stehr.com','114 Bell Club\nKaitlynview, NH 58720-3392',NULL,NULL,2,4,4,'1994-08-17','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(161,162,'Muhammad Iwan Hariadi St','5520833','+1-936-745-2099','K770412','daphne.hirthe@hotmail.com','78197 Kreiger Circles Suite 792\nSporermouth, NE 78493-4999',NULL,NULL,2,4,4,'1997-09-24','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(162,163,'Aristo Ilham Wiratama','5596556','1-863-487-4860','K1750121','eddie47@koss.org','1598 Thiel Extensions\nEast Brenden, RI 45385',NULL,NULL,2,4,4,'1994-11-07','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(163,164,'Elly Sugianita','5604661','+1 (283) 594-5961','K800412','kaitlin65@gmail.com','182 Schroeder Freeway\nTimothyside, KS 40911',NULL,NULL,2,4,4,'2003-09-04','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(164,165,'Rizka Novianadiar','5512551','469-996-3737','K2200221','zieme.roderick@gmail.com','354 Crooks Roads Suite 167\nBeckerton, OK 12687',NULL,NULL,2,4,4,'1995-01-22','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(165,166,'Aris Yanuar Setianto','5613056','+1.314.430.7532','K1740121','myriam21@wiza.com','95896 Heathcote Heights Apt. 292\nPredovictown, NH 99875',NULL,NULL,2,4,4,'2004-05-20','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(166,167,'Mohammad Reza Hafidz','5599087','262-709-0076','TK2570919','erna.rogahn@kuhic.com','589 Kohler Underpass Apt. 131\nErynton, OK 42488-5847',NULL,NULL,2,4,4,'1996-11-02','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(167,168,'Muhammad Junaidi Abdillah','5588816','(806) 796-5149','K881013','sebert@rosenbaum.com','9268 Hassan Club\nWest Lilla, AZ 80666-4468',NULL,NULL,2,4,4,'2004-06-14','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(168,169,'Carolina Indriani','5641157','+1 (463) 621-9936','K370496','zemlak.delphine@ryan.com','568 Hammes Crest Suite 010\nHackettville, MI 78147',NULL,NULL,2,4,4,'2004-08-10','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(169,170,'Mohamad Zainul Arifin','5509902','(940) 263-7157','K1211016','sgreen@yahoo.com','7478 Frederik Fords\nNew Jerrellberg, ID 59139-9178',NULL,NULL,2,4,4,'1989-06-17','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(170,171,'Rachmad Surya Pratama','5597622','+1.321.222.2911','TK2540419','trenton.purdy@quitzon.org','8189 Zoe Harbor Suite 997\nMarcellushaven, WI 54467',NULL,NULL,2,4,4,'1996-09-27','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(171,172,'Anton Auliya M','5532182','786-991-5949','K1191016','kilback.tara@yahoo.com','757 Ondricka River Apt. 482\nEast Zachariah, HI 75757-9138',NULL,NULL,2,4,4,'1992-05-19','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(172,173,'Muhammad Rizqi Radja Mahe','5507358','(215) 788-3347','K2190221','elissa.jast@gmail.com','4694 Nova Row\nHeidiville, DC 31118-6102',NULL,NULL,2,4,4,'1991-01-25','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(173,174,'Nur Fuadati','5496274','234.917.7927','K2020121','bethel.klocko@hills.info','82010 Lubowitz Plain\nMajormouth, IL 46876-5401',NULL,NULL,2,4,4,'2000-12-25','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(174,175,'Doni Tri Wardono','5629625','+1-908-550-1722','TP281220','jerrold14@kreiger.com','324 Hyatt Loop Suite 443\nJaquelinborough, NM 04089-2418',NULL,NULL,2,4,4,'1993-11-19','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(175,176,'Ragil Catur Perwoko','5560156','1-417-378-2571','K1461017','ortiz.angelita@yahoo.com','81398 Schuster Mission Suite 759\nNew Willard, AR 18037-0598',NULL,NULL,2,4,4,'1996-01-21','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(176,177,'Deviana','5605792','(332) 428-7585','K1550120','onie41@stiedemann.org','10644 Reinger Road\nKuvalisland, MI 64263-9321',NULL,NULL,2,4,4,'1996-06-01','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(177,178,'Muzammil Walid','5574905','+1-484-792-1004','K2010121','gislason.liliane@hansen.info','2442 Homenick Cove\nGabestad, KY 31515',NULL,NULL,2,4,4,'2004-05-24','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(178,179,'Nur Indah Sari','','+1 (816) 418-5341','K1650120','dejuan21@gmail.com','602 Crona Vista\nKesslertown, LA 95311',NULL,NULL,2,4,4,'1989-09-26','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(179,180,'Endah Suhesti Wardani','5509740','628-995-7281','K1161016','jed90@flatley.info','30900 Lynch Land\nMorarport, MA 85130',NULL,NULL,2,4,4,'1993-04-19','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(180,181,'Didik Prasetyo','5591046','1-279-785-8468','K1031013','carroll.sincere@carroll.net','246 Bartell Drives\nNew Danika, IN 11878-9928',NULL,NULL,2,4,4,'1996-06-23','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(181,182,'Muhammad Indra Arifianto','5507296','(219) 822-2000','K1960121','ahmed.cruickshank@pouros.com','64100 Emiliano Falls Apt. 009\nPort Ottilieview, MD 88522-5421',NULL,NULL,2,4,4,'2002-01-18','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(182,183,'Muhammad Amin','5578888','+1 (480) 892-1307','K1221016','ewell.gaylord@yahoo.com','1426 Kristy Shoal Suite 231\nBaileyview, MO 32617-5311',NULL,NULL,2,4,4,'1992-01-16','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(183,184,'Agus Santoso','5577839','681-648-5011','K1690121','ekozey@yahoo.com','141 Kerluke Ville\nPurdyton, WY 90068',NULL,NULL,2,4,4,'1999-12-12','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(184,185,'Adilia Prisma Wulantika','5542050','1-510-774-4183','K861013','ernesto69@hotmail.com','207 Johns Circles Suite 300\nSchmittport, WV 59639-6684',NULL,NULL,2,4,4,'1994-11-03','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(185,186,'Budi Mulyanto','5649093','(712) 277-0799','K320594','ileffler@yahoo.com','3508 Wilmer Station\nJohnnyfort, CO 48956',NULL,NULL,2,4,4,'2004-11-06','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(186,187,'Nursoleh','5615853','586-766-7341','K460507','rosina43@gmail.com','1938 Bridgette Fields\nNew Shyannfurt, ND 18542-0438',NULL,NULL,2,4,4,'1993-09-18','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(187,188,'Dhea Sundawa Istiazis','5575324','+1-660-967-5510','K2160221','dbrakus@jenkins.com','8970 Parker Trafficway Apt. 637\nLangoshberg, NE 40243-3489',NULL,NULL,2,4,4,'2003-02-01','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(188,189,'Dewi Masito','5500766','272-431-0480','K1770121','dwest@beahan.biz','45023 Kira Glen\nClotildeport, AZ 66335',NULL,NULL,2,4,4,'1999-08-12','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(189,190,'Munandifah','5505103','+1.618.881.6066','K1101013','adonis.kiehn@gmail.com','50341 Homenick Parkways Apt. 409\nMcClurestad, OH 68794',NULL,NULL,2,4,4,'1996-12-30','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(190,191,'Aris Budiman S.P','5501292','+1 (704) 904-2242','K1530120','alexandria58@leannon.info','959 Kuhlman Corner Suite 076\nDurganland, RI 50164',NULL,NULL,2,4,4,'1989-04-25','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(191,192,'Arif Rachman','5541291','1-986-596-6626','K1730121','uokon@yahoo.com','71688 Kshlerin Lights\nSouth Aaronborough, VT 73987-2497',NULL,NULL,2,4,4,'1992-04-23','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(192,193,'Muhammad Irsyad Arken','5583703','(469) 443-4959','K1970121','ona.walter@lakin.com','2358 Terry Cape\nEast Koby, HI 37899-6169',NULL,NULL,2,4,4,'1994-11-16','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(193,194,'Muhammad Subekhi','1039138','+15414182236','K1390117','bauch.deangelo@kautzer.com','863 Hoeger Cliffs\nKristinshire, TX 58235-9077',NULL,NULL,2,4,4,'1993-03-17','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(194,195,'Arif Untoro','5558855','+17348149507','K141191','vonrueden.litzy@nikolaus.biz','3936 Hailie Square\nKuhicport, WA 85267-6757',NULL,NULL,2,4,4,'1999-02-19','L','2025-07-19 20:25:49','2025-07-19 20:25:53'),(195,196,'Anik Susilowati','5554386','1-872-881-2221','K740412','fhirthe@walter.com','91610 Kunze Road Suite 088\nPietroville, VT 29913-3845',NULL,NULL,2,4,4,'1991-05-14','P','2025-07-19 20:25:49','2025-07-19 20:25:53'),(196,197,'Nur Ahlinah','5524005','(215) 871-4584','K710412','collier.keeley@heidenreich.net','40523 Jeffry Trace Apt. 717\nKeeblerhaven, HI 99726-3974',NULL,NULL,2,4,4,'1990-09-10','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(197,198,'Ricci Septian Putra','5563612','+1.830.741.5856','TK2791019','roger66@kuvalis.com','77637 Asia Hill Suite 654\nEast Benedict, MA 04407-4606',NULL,NULL,2,4,4,'2002-07-08','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(198,199,'Muhammad Syaiful R. A.','7778547','770-275-6487','K1990121','isadore02@schuster.biz','79197 Albert Harbors Apt. 302\nAshlybury, IN 22566-2491',NULL,NULL,2,4,4,'1994-07-05','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(199,200,'Andi Prasetiyo','5633218','(605) 344-0089','K1351216','maddison.swift@yahoo.com','7611 Simonis Plains\nEast Linda, PA 74466',NULL,NULL,2,4,4,'1994-01-18','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(200,201,'Marbella Rindang Hapsari','5513695','1-781-518-8178','K1920121','vivian74@bayer.com','5769 Vance Bypass Apt. 501\nLake Kathlyn, CA 61145',NULL,NULL,2,4,4,'2004-05-12','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(201,202,'Abdul Haris Nasution','5650104','+1-603-688-9005','K1430617','gsipes@yahoo.com','31429 Angelita Pines\nJordybury, ID 17772-9878',NULL,NULL,2,4,4,'1999-05-18','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(202,203,'Maulida Rahma','5574577','+1 (828) 923-9679','K1940121','connelly.ivah@wiegand.com','315 Oran Springs\nHelgatown, AL 95566',NULL,NULL,2,4,4,'2001-05-09','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(203,204,'Jatmiko Agung Nugroho','5533982','+1-706-264-0754','TP331021','rdurgan@gmail.com','8533 Heidenreich Expressway\nWest Aliyahberg, CO 78015-9486',NULL,NULL,2,4,4,'1993-11-20','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(204,205,'Miftakhul Robbach','10000015','+1.610.344.9957','K921013','santino23@hamill.net','8589 Mertz Place\nEast Lindaburgh, NC 58395',NULL,NULL,2,4,4,'2001-01-21','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(205,206,'Safarianto Prio Santoso','10000016','513.340.8082','T5052015','strosin.elza@yahoo.com','3259 Stroman Knoll Apt. 971\nDannyport, KY 53650',NULL,NULL,2,4,4,'1989-07-19','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(206,207,'Miseri','5547448','423-371-5795','K230592','cmetz@kessler.com','7025 Hans Rest Suite 251\nLake Lucienne, HI 12991-2325',NULL,NULL,2,4,4,'2001-05-13','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(207,208,'Machfud','5550009','+1-435-488-3310','K1910121','jenifer07@cummerata.biz','92931 Gillian Rapid Suite 485\nSouth Landenbury, NV 71781',NULL,NULL,2,4,4,'1992-04-01','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(208,209,'Venny Indah Saputri','10000017','407-384-4387','T6052015','payton78@kohler.net','98790 Jast Overpass\nSouth Demarcochester, MD 21811',NULL,NULL,2,4,4,'1998-02-18','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(209,210,'Mochamad Abdul Aziz','5542853','+1 (623) 780-1157','K1471017','bosco.morris@murphy.org','43400 Darryl Curve Apt. 152\nSchneiderborough, DC 64189-5779',NULL,NULL,2,4,4,'2005-04-20','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(210,211,'Akhmad Zaelani','5108580','(229) 284-6860','K1700121','jdubuque@kerluke.info','163 Hill Expressway\nEast Sheldon, NH 65523',NULL,NULL,2,4,4,'2003-04-18','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(211,212,'Andria Pratama','5562088','(423) 330-6855','K1720121','vwisoky@hotmail.com','904 Laurence Shoals Suite 832\nNew Elviefort, KS 22908-7834',NULL,NULL,2,4,4,'2000-05-14','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(212,213,'Mardada','1047566','1-402-203-8630','T344747','adah.koch@hotmail.com','714 Aubree Plains Suite 808\nGussieland, VA 56844',NULL,NULL,2,4,4,'2002-09-07','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(213,214,'Anang Dwi Santoso','5640516','+1.678.441.3445','K1081013','howe.efrain@feil.com','68508 King Motorway Suite 275\nRyleyland, DC 77547',NULL,NULL,2,4,4,'1990-06-04','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(214,215,'Moh Mustaqim','5505209','283-306-1102','K1950121','sschneider@hotmail.com','375 Bins Creek Apt. 958\nHoegermouth, NM 10603-8064',NULL,NULL,2,4,4,'2002-08-26','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(215,216,'Zulkarnain Salasa','','+17319337483','K01122020','kreiger.emilio@schoen.com','40401 Welch Rapids Suite 294\nPort Saraiton, TN 83480-0524',NULL,NULL,2,4,4,'1999-02-02','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(216,217,'Delvi Kusuma Fibia R','','1-956-929-6148','T12052021','kautzer.misael@hotmail.com','9838 Rosella Tunnel\nWest Jeremyhaven, GA 36325-4619',NULL,NULL,2,4,4,'1991-11-07','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(217,218,'Ahmad Rosyid','5511868','272-565-2259','K961013','blick.kim@hotmail.com','877 Krystal Wells\nRyanton, SD 06478',NULL,NULL,2,4,4,'1993-03-07','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(218,219,'Andi Suryaningrat Muhammad S.E','','+19167612293','K1710121','baumbach.newton@gmail.com','9096 Luther Squares Suite 779\nPort Corenestad, TN 31920-3718',NULL,NULL,2,4,4,'2002-02-04','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(219,220,'Mas Afif Wahyudi','5595803','320-576-7681','K1930121','destany79@stark.com','69996 Francisca Extension Suite 599\nOliverborough, NV 28656-0488',NULL,NULL,2,4,4,'1997-08-06','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(220,221,'Diar Hilmi Damara','10000018','+1-973-391-9772','K04042021','cecelia35@gmail.com','30609 Donna Canyon Suite 192\nStephontown, ND 46155-8800',NULL,NULL,2,4,4,'2001-12-20','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(221,222,'Poernomo','5528212','+1 (952) 488-3669','pcs1','jake90@yahoo.com','514 Ismael Glen Suite 994\nWest Lorichester, MA 22645',NULL,NULL,2,4,4,'2002-09-03','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(222,223,'Agus Suwandi','5634776','(763) 844-7548','K300594','cfeest@pollich.org','5743 Norval Wall\nIgnatiusmouth, NC 47492-1015',NULL,NULL,2,4,4,'1997-09-11','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(223,224,'Marsim','5565669','1-930-221-3080','K121191','cruickshank.terrance@yahoo.com','3774 Misty Hollow Suite 809\nEast Jorgebury, MN 49904-9898',NULL,NULL,2,4,4,'2005-04-09','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(224,225,'Ainurrochim','5514811','+17067189475','K1540120','ewilkinson@stoltenberg.biz','49020 Karine Port\nQuentinberg, AK 45286',NULL,NULL,2,4,4,'1998-02-06','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(225,226,'M. Rozikin','5634888','838.574.2673','K1900121','nadia33@wolf.net','88126 Doyle Circle\nNew Betteshire, NM 34906-8545',NULL,NULL,2,4,4,'1990-08-30','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(226,227,'Andika Perdana Putra','5590048','+12407963509','K1590120','eldred.bruen@yahoo.com','499 Jena Creek\nWest Katlynn, KY 28814-5120',NULL,NULL,2,4,4,'2001-02-17','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(227,228,'Nur Yahya','10000020','678.742.4322','K18012022','kristopher41@maggio.com','44028 Montana Mission Suite 722\nWest Marina, NH 49617',NULL,NULL,2,4,4,'2003-07-04','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(228,229,'Suhendik','10000021','+1 (773) 596-9990','K19092018','mgoyette@corwin.com','107 Pollich Mountains\nSouth Henriburgh, TN 36139',NULL,NULL,2,4,4,'1990-02-09','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(229,230,'Siti Roudhoh','10000019','(669) 299-4682','T10052020','prohaska.jackeline@rath.com','6168 Christiansen Causeway Suite 625\nVonRuedenshire, OK 84947-5616',NULL,NULL,2,4,4,'1999-04-03','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(230,231,'Nur Maulidia','10000022','+1 (651) 916-5125','T11052020','vada.bergstrom@kshlerin.biz','4656 Trace Throughway\nPort Clara, AL 17656',NULL,NULL,2,4,4,'1990-07-29','P','2025-07-19 20:25:49','2025-07-19 20:25:54'),(231,232,'Agus Purwito','10000023','+1.610.717.0178','T9022020','jkulas@klein.org','59615 Gussie Oval\nOndrickabury, OH 92011-7379',NULL,NULL,2,4,4,'1992-05-21','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(232,233,'Ray Rayandika','10000024','520.676.3029','T8092018','jfunk@kiehn.com','24468 Simonis Cape\nEast Aurorechester, FL 75480-4375',NULL,NULL,2,4,4,'1996-09-18','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(233,234,'Samsul Arifin','10000025','+16828618075','T7022017','bobby33@ledner.com','286 Annabel Harbor\nWest Maeve, VA 86119',NULL,NULL,2,4,4,'2005-09-10','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(234,235,'Mas Prabawa','15185472','541-819-9802','K1021013','abe88@mccullough.org','15031 Harvey Vista\nSouth Audraview, AZ 01464-2177',NULL,NULL,2,4,4,'1990-06-14','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(235,236,'Moh.Fauzi','15186660','260.626.1318','K830412','justine79@okeefe.com','275 Hildegard Dale\nEast Richie, RI 87134',NULL,NULL,2,4,4,'2003-03-10','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(236,237,'Achmad Nairi','5585102','(316) 870-3316','K1251216','iluettgen@von.net','380 Mohamed Loop Suite 017\nLake Nya, TX 20297',NULL,NULL,2,4,4,'1993-07-08','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(237,238,'Ali Chandra Lestiawan','5534941','(763) 846-3695','K1400117','maryam.weissnat@murray.com','668 Reid Gateway\nBatzmouth, IN 70785-0194',NULL,NULL,2,4,4,'1996-08-14','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(238,239,'Abdul Wachid','5644115','724-529-4869','K670310','lindgren.corine@gmail.com','7162 Bosco Crest\nDeckowside, CA 17454',NULL,NULL,2,4,4,'1994-07-26','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(239,240,'Ahmad Syafii','5623301','+1-260-299-3722','TP351121','hkovacek@hotmail.com','20575 Eldred Ports Apt. 105\nPort Cecelia, GA 61414-6404',NULL,NULL,2,4,4,'2004-04-28','L','2025-07-19 20:25:49','2025-07-19 20:25:54'),(240,241,'Arie Indriyani','10000030','212.652.3754','K20072020','roosevelt58@gmail.com','8585 Durgan Curve Apt. 056\nEast Ivoryhaven, NV 99355',NULL,NULL,2,4,4,'1996-05-26','P','2025-07-19 20:25:49','2025-07-19 20:25:54');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_levels`
--

DROP TABLE IF EXISTS `job_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_levels_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_levels`
--

LOCK TABLES `job_levels` WRITE;
/*!40000 ALTER TABLE `job_levels` DISABLE KEYS */;
INSERT INTO `job_levels` VALUES (1,'CEO',NULL,NULL),(2,'Commisioner',NULL,NULL),(3,'Manager',NULL,NULL),(4,'Staff',NULL,NULL),(5,'Supervisor',NULL,NULL);
/*!40000 ALTER TABLE `job_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_positions`
--

DROP TABLE IF EXISTS `job_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_positions_name_index` (`name`),
  KEY `job_positions_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_positions`
--

LOCK TABLES `job_positions` WRITE;
/*!40000 ALTER TABLE `job_positions` DISABLE KEYS */;
INSERT INTO `job_positions` VALUES (1,'CEO',NULL,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(2,'IT Manager',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(3,'IT Supervisor',2,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(4,'IT Staff',3,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(5,'Sales & Marketting Manager',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(6,'Sales & Marketting Supervisor',5,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(7,'Sales Staff',6,NULL,NULL),(8,'Marketting Staff',6,NULL,NULL),(9,'HRGA Manager',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(10,'HRGA Supervisor',9,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(11,'HRD Staff',10,NULL,NULL),(12,'GA Staff',10,NULL,NULL),(13,'Finance Accounting Manager',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(14,'Accounting Supervisor',13,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(15,'Accounting Staff',14,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(16,'Finance Supervisor',13,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(17,'Finance Staff',16,'2025-07-19 20:25:48','2025-07-19 20:25:48');
/*!40000 ALTER TABLE `job_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2023_06_17_094208_create_employees_table',1),(6,'2023_06_17_101922_create_job_levels_table',1),(7,'2023_06_17_101938_create_job_positions_table',1),(8,'2023_06_17_102022_create_org_structures_table',1),(9,'2023_06_17_124801_create_complaints_table',1),(10,'2023_06_17_124819_create_rent_rooms_table',1),(11,'2023_07_15_025410_create_permission_tables',1),(12,'2023_08_13_052010_create_rooms_table',1),(13,'2023_09_10_083656_create_office_tools_table',1),(14,'2023_09_12_125352_create_approvals_table',1),(15,'2023_09_12_153945_create_approval_logs_table',1),(16,'2023_09_24_090224_create_numbers_table',1),(17,'2023_11_11_005406_create_posts_table',1),(18,'2023_11_18_231755_create_media_table',1),(19,'2024_03_21_000000_add_title_to_permissions_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(2,'App\\Models\\User',3),(2,'App\\Models\\User',4),(2,'App\\Models\\User',5),(2,'App\\Models\\User',6),(2,'App\\Models\\User',7),(2,'App\\Models\\User',8),(2,'App\\Models\\User',9),(2,'App\\Models\\User',10),(2,'App\\Models\\User',11),(2,'App\\Models\\User',12),(2,'App\\Models\\User',13),(2,'App\\Models\\User',14),(2,'App\\Models\\User',15),(2,'App\\Models\\User',16),(2,'App\\Models\\User',17),(2,'App\\Models\\User',18),(2,'App\\Models\\User',19),(2,'App\\Models\\User',20),(2,'App\\Models\\User',21),(2,'App\\Models\\User',22),(2,'App\\Models\\User',23),(2,'App\\Models\\User',24),(2,'App\\Models\\User',25),(2,'App\\Models\\User',26),(2,'App\\Models\\User',27),(2,'App\\Models\\User',28),(2,'App\\Models\\User',29),(2,'App\\Models\\User',30),(2,'App\\Models\\User',31),(2,'App\\Models\\User',32),(2,'App\\Models\\User',33),(2,'App\\Models\\User',34),(2,'App\\Models\\User',35),(2,'App\\Models\\User',36),(2,'App\\Models\\User',37),(2,'App\\Models\\User',38),(2,'App\\Models\\User',39),(2,'App\\Models\\User',40),(2,'App\\Models\\User',41),(2,'App\\Models\\User',42),(2,'App\\Models\\User',43),(2,'App\\Models\\User',44),(2,'App\\Models\\User',45),(2,'App\\Models\\User',46),(2,'App\\Models\\User',47),(2,'App\\Models\\User',48),(2,'App\\Models\\User',49),(2,'App\\Models\\User',50),(2,'App\\Models\\User',51),(2,'App\\Models\\User',52),(2,'App\\Models\\User',53),(2,'App\\Models\\User',54),(2,'App\\Models\\User',55),(2,'App\\Models\\User',56),(2,'App\\Models\\User',57),(2,'App\\Models\\User',58),(2,'App\\Models\\User',59),(2,'App\\Models\\User',60),(2,'App\\Models\\User',61),(2,'App\\Models\\User',62),(2,'App\\Models\\User',63),(2,'App\\Models\\User',64),(2,'App\\Models\\User',65),(2,'App\\Models\\User',66),(2,'App\\Models\\User',67),(2,'App\\Models\\User',68),(2,'App\\Models\\User',69),(2,'App\\Models\\User',70),(2,'App\\Models\\User',71),(2,'App\\Models\\User',72),(2,'App\\Models\\User',73),(2,'App\\Models\\User',74),(2,'App\\Models\\User',75),(2,'App\\Models\\User',76),(2,'App\\Models\\User',77),(2,'App\\Models\\User',78),(2,'App\\Models\\User',79),(2,'App\\Models\\User',80),(2,'App\\Models\\User',81),(2,'App\\Models\\User',82),(2,'App\\Models\\User',83),(2,'App\\Models\\User',84),(2,'App\\Models\\User',85),(2,'App\\Models\\User',86),(2,'App\\Models\\User',87),(2,'App\\Models\\User',88),(2,'App\\Models\\User',89),(2,'App\\Models\\User',90),(2,'App\\Models\\User',91),(2,'App\\Models\\User',92),(2,'App\\Models\\User',93),(2,'App\\Models\\User',94),(2,'App\\Models\\User',95),(2,'App\\Models\\User',96),(2,'App\\Models\\User',97),(2,'App\\Models\\User',98),(2,'App\\Models\\User',99),(2,'App\\Models\\User',100),(2,'App\\Models\\User',101),(2,'App\\Models\\User',102),(2,'App\\Models\\User',103),(2,'App\\Models\\User',104),(2,'App\\Models\\User',105),(2,'App\\Models\\User',106),(2,'App\\Models\\User',107),(2,'App\\Models\\User',108),(2,'App\\Models\\User',109),(2,'App\\Models\\User',110),(2,'App\\Models\\User',111),(2,'App\\Models\\User',112),(2,'App\\Models\\User',113),(2,'App\\Models\\User',114),(2,'App\\Models\\User',115),(2,'App\\Models\\User',116),(2,'App\\Models\\User',117),(2,'App\\Models\\User',118),(2,'App\\Models\\User',119),(2,'App\\Models\\User',120),(2,'App\\Models\\User',121),(2,'App\\Models\\User',122),(2,'App\\Models\\User',123),(2,'App\\Models\\User',124),(2,'App\\Models\\User',125),(2,'App\\Models\\User',126),(2,'App\\Models\\User',127),(2,'App\\Models\\User',128),(2,'App\\Models\\User',129),(2,'App\\Models\\User',130),(2,'App\\Models\\User',131),(2,'App\\Models\\User',132),(2,'App\\Models\\User',133),(2,'App\\Models\\User',134),(2,'App\\Models\\User',135),(2,'App\\Models\\User',136),(2,'App\\Models\\User',137),(2,'App\\Models\\User',138),(2,'App\\Models\\User',139),(2,'App\\Models\\User',140),(2,'App\\Models\\User',141),(2,'App\\Models\\User',142),(2,'App\\Models\\User',143),(2,'App\\Models\\User',144),(2,'App\\Models\\User',145),(2,'App\\Models\\User',146),(2,'App\\Models\\User',147),(2,'App\\Models\\User',148),(2,'App\\Models\\User',149),(2,'App\\Models\\User',150),(2,'App\\Models\\User',151),(2,'App\\Models\\User',152),(2,'App\\Models\\User',153),(2,'App\\Models\\User',154),(2,'App\\Models\\User',155),(2,'App\\Models\\User',156),(2,'App\\Models\\User',157),(2,'App\\Models\\User',158),(2,'App\\Models\\User',159),(2,'App\\Models\\User',160),(2,'App\\Models\\User',161),(2,'App\\Models\\User',162),(2,'App\\Models\\User',163),(2,'App\\Models\\User',164),(2,'App\\Models\\User',165),(2,'App\\Models\\User',166),(2,'App\\Models\\User',167),(2,'App\\Models\\User',168),(2,'App\\Models\\User',169),(2,'App\\Models\\User',170),(2,'App\\Models\\User',171),(2,'App\\Models\\User',172),(2,'App\\Models\\User',173),(2,'App\\Models\\User',174),(2,'App\\Models\\User',175),(2,'App\\Models\\User',176),(2,'App\\Models\\User',177),(2,'App\\Models\\User',178),(2,'App\\Models\\User',179),(2,'App\\Models\\User',180),(2,'App\\Models\\User',181),(2,'App\\Models\\User',182),(2,'App\\Models\\User',183),(2,'App\\Models\\User',184),(2,'App\\Models\\User',185),(2,'App\\Models\\User',186),(2,'App\\Models\\User',187),(2,'App\\Models\\User',188),(2,'App\\Models\\User',189),(2,'App\\Models\\User',190),(2,'App\\Models\\User',191),(2,'App\\Models\\User',192),(2,'App\\Models\\User',193),(2,'App\\Models\\User',194),(2,'App\\Models\\User',195),(2,'App\\Models\\User',196),(2,'App\\Models\\User',197),(2,'App\\Models\\User',198),(2,'App\\Models\\User',199),(2,'App\\Models\\User',200),(2,'App\\Models\\User',201),(2,'App\\Models\\User',202),(2,'App\\Models\\User',203),(2,'App\\Models\\User',204),(2,'App\\Models\\User',205),(2,'App\\Models\\User',206),(2,'App\\Models\\User',207),(2,'App\\Models\\User',208),(2,'App\\Models\\User',209),(2,'App\\Models\\User',210),(2,'App\\Models\\User',211),(2,'App\\Models\\User',212),(2,'App\\Models\\User',213),(2,'App\\Models\\User',214),(2,'App\\Models\\User',215),(2,'App\\Models\\User',216),(2,'App\\Models\\User',217),(2,'App\\Models\\User',218),(2,'App\\Models\\User',219),(2,'App\\Models\\User',220),(2,'App\\Models\\User',221),(2,'App\\Models\\User',222),(2,'App\\Models\\User',223),(2,'App\\Models\\User',224),(2,'App\\Models\\User',225),(2,'App\\Models\\User',226),(2,'App\\Models\\User',227),(2,'App\\Models\\User',228),(2,'App\\Models\\User',229),(2,'App\\Models\\User',230),(2,'App\\Models\\User',231),(2,'App\\Models\\User',232),(2,'App\\Models\\User',233),(2,'App\\Models\\User',234),(2,'App\\Models\\User',235),(2,'App\\Models\\User',236),(2,'App\\Models\\User',237),(2,'App\\Models\\User',238),(2,'App\\Models\\User',239),(2,'App\\Models\\User',240),(2,'App\\Models\\User',241),(2,'App\\Models\\User',242);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `numbers`
--

DROP TABLE IF EXISTS `numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `numbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numbers_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `numbers`
--

LOCK TABLES `numbers` WRITE;
/*!40000 ALTER TABLE `numbers` DISABLE KEYS */;
INSERT INTO `numbers` VALUES (1,'rent_room','Pinjam ruangan','RR/[NUMBER]/[DD][MM][YY]',0,NULL,NULL),(2,'complaint','Pengaduan','CO/[NUMBER]/[DD][MM][YY]',0,NULL,NULL);
/*!40000 ALTER TABLE `numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_tools`
--

DROP TABLE IF EXISTS `office_tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office_tools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office_tools_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_tools`
--

LOCK TABLES `office_tools` WRITE;
/*!40000 ALTER TABLE `office_tools` DISABLE KEYS */;
INSERT INTO `office_tools` VALUES (1,'Speaker',NULL,NULL),(2,'Mic',NULL,NULL),(3,'Spidol Hitam',NULL,NULL),(4,'Spidol Biru',NULL,NULL),(5,'Proyektor',NULL,NULL);
/*!40000 ALTER TABLE `office_tools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_structures`
--

DROP TABLE IF EXISTS `org_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_structures_name_index` (`name`),
  KEY `org_structures_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_structures`
--

LOCK TABLES `org_structures` WRITE;
/*!40000 ALTER TABLE `org_structures` DISABLE KEYS */;
INSERT INTO `org_structures` VALUES (1,'BOD',NULL,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(2,'IT Division',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(3,'Finance & Accounting',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(4,'Accounting',3,NULL,NULL),(5,'Finance',3,NULL,NULL),(6,'Marketting',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(7,'Sales',6,NULL,NULL),(8,'Customer Support',6,NULL,NULL),(9,'HRGA',1,'2025-07-19 20:25:48','2025-07-19 20:25:48'),(10,'HRD',9,NULL,NULL),(11,'GA',9,NULL,NULL);
/*!40000 ALTER TABLE `org_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_parent_id_foreign` (`parent_id`),
  CONSTRAINT `permissions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','Dashboard',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(2,'employees','Karyawan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(3,'employees_create','Tambah',2,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(4,'employees_edit','Ubah',2,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(5,'employees_delete','Hapus',2,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(6,'rent_rooms','Pinjam Ruangan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(7,'rent_rooms_create','Tambah',6,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(8,'rent_rooms_edit','Ubah',6,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(9,'rent_rooms_delete','Hapus',6,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(10,'complaints','Pengaduan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(11,'complaints_create','Tambah',10,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(12,'complaints_edit','Ubah',10,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(13,'complaints_delete','Hapus',10,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(14,'posts','Berita',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(15,'posts_create','Tambah',14,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(16,'posts_edit','Ubah',14,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(17,'posts_delete','Hapus',14,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(18,'settings','Pengaturan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(19,'settings_rooms','Ruangan',18,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(20,'settings_rooms_create','Tambah',19,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(21,'settings_rooms_edit','Ubah',19,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(22,'settings_rooms_delete','Hapus',19,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(23,'settings_office_tools','Peralatan Kantor',18,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(24,'settings_office_tools_create','Tambah',23,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(25,'settings_office_tools_edit','Ubah',23,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(26,'settings_office_tools_delete','Hapus',23,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(27,'settings_roles','Peran',18,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(28,'settings_roles_create','Tambah',27,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(29,'settings_roles_edit','Ubah',27,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(30,'settings_roles_delete','Hapus',27,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(31,'settings_users','Pengguna',18,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(32,'settings_users_create','Tambah',31,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(33,'settings_users_edit','Ubah',31,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(34,'settings_users_delete','Hapus',31,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(35,'reports','Laporan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(36,'approvals','Persetujuan',NULL,'api','2025-07-19 20:25:47','2025-07-19 20:25:47');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'1','3fc5c35cde3d7f7e3aaea76bc4c242bd45ba8b702a6df5754002467124cef68a','[\"*\"]','2025-07-19 21:05:07',NULL,'2025-07-19 20:26:44','2025-07-19 21:05:07'),(2,'App\\Models\\User',24,'1','5cd0a2b5c341d343b49ccc3ce836c9686a4032ddd9047ba94ee467e4bece1781','[\"*\"]','2025-07-19 21:05:50',NULL,'2025-07-19 21:05:31','2025-07-19 21:05:50'),(3,'App\\Models\\User',242,'1','b27dfbdd3b985a71721619540e127bb94c03072d70148d2ada3f3b1c10b03488','[\"*\"]','2025-07-19 21:06:23',NULL,'2025-07-19 21:06:22','2025-07-19 21:06:23'),(4,'App\\Models\\User',1,'1','b1eb52322c51bacdd97442c07a6c03f7484ad5fbf52a969ad9a8f8becd5945d6','[\"*\"]','2025-07-19 21:09:56',NULL,'2025-07-19 21:08:02','2025-07-19 21:09:56');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint NOT NULL,
  `post_type_id` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rent_rooms`
--

DROP TABLE IF EXISTS `rent_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rent_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `ref_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_status_id` tinyint NOT NULL,
  `user_id` bigint NOT NULL,
  `office_tools` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rent_rooms_ref_number_unique` (`ref_number`),
  KEY `rent_rooms_room_id_index` (`room_id`),
  KEY `rent_rooms_start_date_index` (`start_date`),
  KEY `rent_rooms_end_date_index` (`end_date`),
  KEY `rent_rooms_approval_status_id_index` (`approval_status_id`),
  KEY `rent_rooms_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rent_rooms`
--

LOCK TABLES `rent_rooms` WRITE;
/*!40000 ALTER TABLE `rent_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `rent_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,2),(17,2),(19,2),(20,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,2),(35,2),(1,4),(36,4);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator','api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(2,'Employee','api','2025-07-19 20:25:47','2025-07-19 20:25:47'),(4,'Warehouse','api','2025-07-19 21:00:16','2025-07-19 21:00:16');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'Semeru',NULL,NULL),(2,'Kelud',NULL,NULL),(3,'Bromo',NULL,NULL),(4,'Arjuno',NULL,NULL),(5,'Raung',NULL,NULL);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','KFJFZbhYOg','2025-07-19 20:25:49','2025-07-19 20:25:49'),(2,'Rizaldy Farhananda','TK000001','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','zdJt8DmXUi','2025-07-19 20:25:49','2025-07-19 20:25:49'),(3,'Hadi Winarso','TK2960121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Kr7BdJsAOS','2025-07-19 20:25:49','2025-07-19 20:25:49'),(4,'Nur Su Udi','TK2980121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ANuSl1mr9Y','2025-07-19 20:25:49','2025-07-19 20:25:49'),(5,'Dias Argha P.','TK000002','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hwp7Av8Gay','2025-07-19 20:25:49','2025-07-19 20:25:49'),(6,'Teguh Hariyo U.','TK000003','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','5RbtAl9yP7','2025-07-19 20:25:49','2025-07-19 20:25:49'),(7,'Rochmanu Surya','K1141016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','35EBFci7ui','2025-07-19 20:25:49','2025-07-19 20:25:49'),(8,'M. Salim','9','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lj5fR4e7hB','2025-07-19 20:25:49','2025-07-19 20:25:49'),(9,'M. Fujianto','K1890121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','HCDyAPvxBm','2025-07-19 20:25:50','2025-07-19 20:25:50'),(10,'M Mufidin','TK2560419','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','coIdKM7i0F','2025-07-19 20:25:50','2025-07-19 20:25:50'),(11,'Lutfi Zainudin','K470507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','iq3RncaO5o','2025-07-19 20:25:50','2025-07-19 20:25:50'),(12,'Muhamad Wafi Musabbih','TP341021','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','LVygz8pzCo','2025-07-19 20:25:50','2025-07-19 20:25:50'),(13,'Nadya Maharani','TP300721','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','b4nziecD7l','2025-07-19 20:25:50','2025-07-19 20:25:50'),(14,'Sufaroham','TP310721','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','MfnT052QWk','2025-07-19 20:25:50','2025-07-19 20:25:50'),(15,'M. Isnaini','K1001013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ckc1IK3ao0','2025-07-19 20:25:50','2025-07-19 20:25:50'),(16,'Suyono SBY','TP320821','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','F7a7h0lOYb','2025-07-19 20:25:50','2025-07-19 20:25:50'),(17,'Rudolphus AQ Radya P','T000000','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','F8R3uW28ob','2025-07-19 20:25:50','2025-07-19 20:25:50'),(18,'Finny Ryanita','K1820121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','5gX9ZOaVI5','2025-07-19 20:25:50','2025-07-19 20:25:50'),(19,'Sugianto','TP250320','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','b9OwRQvWvR','2025-07-19 20:25:50','2025-07-19 20:25:50'),(20,'Indra Mahabat','TK2970121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cbBaIKJozW','2025-07-19 20:25:50','2025-07-19 20:25:50'),(21,'Takrip','TP070218','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','4vzzSR933t','2025-07-19 20:25:50','2025-07-19 20:25:50'),(22,'Wikan Sutirto Aribowo','T314620','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','us5UteXlHB','2025-07-19 20:25:50','2025-07-19 20:25:50'),(23,'Witan Hardianto','T324675','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2YZo6PT7Jr','2025-07-19 20:25:50','2025-07-19 20:25:50'),(24,'Yoga K','123456','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','MczlYxnVhE','2025-07-19 20:25:50','2025-07-19 20:25:50'),(25,'Yudo','12345','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Y29nD3LcVk','2025-07-19 20:25:50','2025-07-19 20:25:50'),(26,'Zainul Arifin','TP080218','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','LAsCE8uUIE','2025-07-19 20:25:50','2025-07-19 20:25:50'),(27,'Syaifullah','K1111116','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CzjHPkBF1b','2025-07-19 20:25:50','2025-07-19 20:25:50'),(28,'Kusnan','K360496','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','FEsc7XqbuJ','2025-07-19 20:25:50','2025-07-19 20:25:50'),(29,'Yudha Ardhi Saputro','K630409','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','wDHrJ4eXVP','2025-07-19 20:25:50','2025-07-19 20:25:50'),(30,'Khuirul Huda','K530507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','FvPr6R17BG','2025-07-19 20:25:50','2025-07-19 20:25:50'),(31,'Sigit Setiawan','K2070121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cxhJrlnqbd','2025-07-19 20:25:50','2025-07-19 20:25:50'),(32,'Setiawan','K1570120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','JRb1WHwclf','2025-07-19 20:25:50','2025-07-19 20:25:50'),(33,'Suwandi','K951013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CKhO4H3xe8','2025-07-19 20:25:50','2025-07-19 20:25:50'),(34,'Wahyu Nanang Setiawan','K2130121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ws6AWejfwJ','2025-07-19 20:25:50','2025-07-19 20:25:50'),(35,'Winardi','K1241016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tBBk53fbMW','2025-07-19 20:25:50','2025-07-19 20:25:50'),(36,'Wandira Gustiani','K1600120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','GdnPdx02uG','2025-07-19 20:25:50','2025-07-19 20:25:50'),(37,'Sulistiowati','K510507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','4rw1vr1pzG','2025-07-19 20:25:50','2025-07-19 20:25:50'),(38,'Yulianto','K650310','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','utztmrbq0W','2025-07-19 20:25:50','2025-07-19 20:25:50'),(39,'Fendi Tristanto','K911013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sLTDFjGTsF','2025-07-19 20:25:50','2025-07-19 20:25:50'),(40,'Fatkhul Muin','K480507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hZP6HSYwhu','2025-07-19 20:25:50','2025-07-19 20:25:50'),(41,'Johan Ari Nofiyanto','K1501017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','b3DucYnqpE','2025-07-19 20:25:50','2025-07-19 20:25:50'),(42,'Tony Murdianto','K1271216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tzcwSwwZ1x','2025-07-19 20:25:50','2025-07-19 20:25:50'),(43,'Karenda Achmad Fatoni','K1640120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','B2yuzPlNV8','2025-07-19 20:25:50','2025-07-19 20:25:50'),(44,'Sunu Arief Budianto','K2220221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','SL5zFKusZJ','2025-07-19 20:25:50','2025-07-19 20:25:50'),(45,'Ida Rifdah','K1850121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jvt2GcdOoM','2025-07-19 20:25:50','2025-07-19 20:25:50'),(46,'Suhendra Trifandani','K2090121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','satl2Ro3UP','2025-07-19 20:25:50','2025-07-19 20:25:50'),(47,'Wahyu Hidayat','K1620120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OBILfdhwnv','2025-07-19 20:25:50','2025-07-19 20:25:50'),(48,'Zulfah Cahya Dresta Ramad','K2140121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Qq3mqXgv36','2025-07-19 20:25:50','2025-07-19 20:25:50'),(49,'Widiarti Kuswardani','K750412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9xh32iWV4n','2025-07-19 20:25:50','2025-07-19 20:25:50'),(50,'Yan Wenesa','K941013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','3nrWA9auiN','2025-07-19 20:25:50','2025-07-19 20:25:50'),(51,'Samsul Ichwan','K1321216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','PSkJ5OvE0N','2025-07-19 20:25:50','2025-07-19 20:25:50'),(52,'Gresma Afif Awanis','K2180221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','D2YJ9t1Fe3','2025-07-19 20:25:50','2025-07-19 20:25:50'),(53,'Ferdiyanto Indrayana','K780412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hv1aGFHDp7','2025-07-19 20:25:50','2025-07-19 20:25:50'),(54,'Kasbolah','K820412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1di7hzu5cK','2025-07-19 20:25:50','2025-07-19 20:25:50'),(55,'Sumardiono','K2100121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','7oJJVphMrJ','2025-07-19 20:25:50','2025-07-19 20:25:50'),(56,'Venti Istriani','K850712','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','FPO21jMUxT','2025-07-19 20:25:51','2025-07-19 20:25:51'),(57,'Jojok Nursijo','K191191','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9MLUT8mnFL','2025-07-19 20:25:51','2025-07-19 20:25:51'),(58,'Syamsul Ma Arif','K2230221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TPe2H0zBjW','2025-07-19 20:25:51','2025-07-19 20:25:51'),(59,'Sugiono','K871013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rdMxmfjjbB','2025-07-19 20:25:51','2025-07-19 20:25:51'),(60,'Hadi Kuswoyo','K560507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','8r2Hn2tBZB','2025-07-19 20:25:51','2025-07-19 20:25:51'),(61,'Trilaksono Widiyanto','K1360117','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VoWp1sm91q','2025-07-19 20:25:51','2025-07-19 20:25:51'),(62,'Junaidi Salat','K1880121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','T0jDCpnggf','2025-07-19 20:25:51','2025-07-19 20:25:51'),(63,'Iswandi','K931013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lPsRxdK8jn','2025-07-19 20:25:51','2025-07-19 20:25:51'),(64,'Iqbal Verdianata Rifer','TP230220','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','7ncbL32Ogl','2025-07-19 20:25:51','2025-07-19 20:25:51'),(65,'Joko Setiono','K600409','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','dMcP8oZGP7','2025-07-19 20:25:51','2025-07-19 20:25:51'),(66,'Isa Ardiansyah Afandi','K1870121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','M4C9yRmkma','2025-07-19 20:25:51','2025-07-19 20:25:51'),(67,'Gunawan','K520507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','AmFMDInLLh','2025-07-19 20:25:51','2025-07-19 20:25:51'),(68,'Ikha Nur Diana','K1491017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Wml7a4CDHd','2025-07-19 20:25:51','2025-07-19 20:25:51'),(69,'Swacita Ayu Fazhari','K2120121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','bOolEGDeN7','2025-07-19 20:25:51','2025-07-19 20:25:51'),(70,'Siti Marufah','K1580120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VlLSa20Wra','2025-07-19 20:25:51','2025-07-19 20:25:51'),(71,'Kanika Nanda Agung Budiawan','K1121116','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hHeQoXjYgb','2025-07-19 20:25:51','2025-07-19 20:25:51'),(72,'Rr Chintiadewi P.','K2040121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ev5Lzj1iiO','2025-07-19 20:25:51','2025-07-19 20:25:51'),(73,'Roy Bagus Baskoro','K1410617','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','elQJIBh4PU','2025-07-19 20:25:51','2025-07-19 20:25:51'),(74,'Sugeng Hariadi','TK3030221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doqCfEx1yy','2025-07-19 20:25:51','2025-07-19 20:25:51'),(75,'Fahrizal Rizky Putra P.','K1810121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','8D4ZG7L8yQ','2025-07-19 20:25:51','2025-07-19 20:25:51'),(76,'Seftianita N','K2060121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cCZUOT16mx','2025-07-19 20:25:51','2025-07-19 20:25:51'),(77,'Sugeng Triantoro','K1311216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Uy1ysPEb6J','2025-07-19 20:25:51','2025-07-19 20:25:51'),(78,'Sugi Yanto','K2080121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','3UFWLnvfUp','2025-07-19 20:25:51','2025-07-19 20:25:51'),(79,'Hendra Kurniawan','K1840121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','YwPblCJt2X','2025-07-19 20:25:51','2025-07-19 20:25:51'),(80,'Yolanda Noviyanti Arifin','K2240221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','YHDvpygHGp','2025-07-19 20:25:51','2025-07-19 20:25:51'),(81,'Kusenen','K1380117','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TQeScECse1','2025-07-19 20:25:51','2025-07-19 20:25:51'),(82,'Supriyana','K450600','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TRORou7P5A','2025-07-19 20:25:51','2025-07-19 20:25:51'),(83,'Izza Millati','K730412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','iayjOCs166','2025-07-19 20:25:51','2025-07-19 20:25:51'),(84,'Iwan Kumoro','K790412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cLwXPyd5AQ','2025-07-19 20:25:51','2025-07-19 20:25:51'),(85,'Heri Purnomo','K1301216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VhmZA7JYsQ','2025-07-19 20:25:51','2025-07-19 20:25:51'),(86,'Suripto','K1370117','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','7naJeAdpzX','2025-07-19 20:25:51','2025-07-19 20:25:51'),(87,'Hendra Nur Suherman','TK2520419','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tkHPFTjdsL','2025-07-19 20:25:51','2025-07-19 20:25:51'),(88,'Iwan Hermansah','K1011013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','V6qrBRzIyb','2025-07-19 20:25:51','2025-07-19 20:25:51'),(89,'Sunoto','K1061013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','nPW72KJoPQ','2025-07-19 20:25:51','2025-07-19 20:25:51'),(90,'Suroso','TP160119','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ll2oLEgYvE','2025-07-19 20:25:51','2025-07-19 20:25:51'),(91,'Sukarso','K1291216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','bXXkO92nxe','2025-07-19 20:25:51','2025-07-19 20:25:51'),(92,'Suyono LMG','K2110121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','W3gcvH67zK','2025-07-19 20:25:51','2025-07-19 20:25:51'),(93,'Siti Rahayu','K891013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','fCApuCCZKb','2025-07-19 20:25:51','2025-07-19 20:25:51'),(94,'Siti Rofikoh','K810412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','UJDlR2tvqB','2025-07-19 20:25:51','2025-07-19 20:25:51'),(95,'Sara Nita Anggraini','K2050121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rCpYiLF6gX','2025-07-19 20:25:51','2025-07-19 20:25:51'),(96,'Fabian Lazarus Ramadhan','K1800121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','iDE0OocIso','2025-07-19 20:25:51','2025-07-19 20:25:51'),(97,'Sindu Anggoro','K1481017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','GAaJa7IbKK','2025-07-19 20:25:51','2025-07-19 20:25:51'),(98,'Saifudin Arif','K1511119','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CLWrBbo3B8','2025-07-19 20:25:51','2025-07-19 20:25:51'),(99,'Indra Kusuma Kurniawan','K1860121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','vixc4NJUN1','2025-07-19 20:25:51','2025-07-19 20:25:51'),(100,'Fath Iqbal Alfarisi','K1610120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','QsxxupV2K3','2025-07-19 20:25:51','2025-07-19 20:25:51'),(101,'Lelana Arie Soekamto','K700310','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sc1KYmMNMa','2025-07-19 20:25:51','2025-07-19 20:25:51'),(102,'Fairuzi Zakiyah','K1630120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','qYsd9D8cZ4','2025-07-19 20:25:51','2025-07-19 20:25:51'),(103,'Fajar Lazuardi','K1130616','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','C6OZPuG2GB','2025-07-19 20:25:52','2025-07-19 20:25:52'),(104,'Siti Nur Hasanah','K2210221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','EnbnlCXMxa','2025-07-19 20:25:52','2025-07-19 20:25:52'),(105,'Frida Gasiani','K1830121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','FzhWUSLBeh','2025-07-19 20:25:52','2025-07-19 20:25:52'),(106,'Ferry Kustianto','K981013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','z41hhYyFYL','2025-07-19 20:25:52','2025-07-19 20:25:52'),(107,'Siswanto','TK3020221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rox8ElCRqi','2025-07-19 20:25:52','2025-07-19 20:25:52'),(108,'Early Hidayat','TK3000221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TmjFCGLnWu','2025-07-19 20:25:52','2025-07-19 20:25:52'),(109,'Nuzul Farida Arini','K1520120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','FUiQX0uZGk','2025-07-19 20:25:52','2025-07-19 20:25:52'),(110,'Rico Gally Pradana','K1281216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','W22tieGOxJ','2025-07-19 20:25:52','2025-07-19 20:25:52'),(111,'Chusnatun Kamilah','K2150221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','oWKsCyHIGG','2025-07-19 20:25:52','2025-07-19 20:25:52'),(112,'Choirunita','K1181016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tHIl6m1ddu','2025-07-19 20:25:52','2025-07-19 20:25:52'),(113,'Arif Gunawan','K971013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rXoy73eat1','2025-07-19 20:25:52','2025-07-19 20:25:52'),(114,'Dodi Ismono','K2170221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','oxJjFfN6fl','2025-07-19 20:25:52','2025-07-19 20:25:52'),(115,'Angga Bagus Prasetya W.','K760412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','HZ3slums0J','2025-07-19 20:25:52','2025-07-19 20:25:52'),(116,'Dhika Pratama Putra','K1560120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','yLMzEDofvA','2025-07-19 20:25:52','2025-07-19 20:25:52'),(117,'Parjianto','K570507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','I1DiCAa9ad','2025-07-19 20:25:52','2025-07-19 20:25:52'),(118,'Adityo Wibowo','pcs2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','mvuEr302EC','2025-07-19 20:25:52','2025-07-19 20:25:52'),(119,'Dhanniar Oktiara Caesarre','K1780121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','JjSi8QjMPo','2025-07-19 20:25:52','2025-07-19 20:25:52'),(120,'Muhammad Ainur Rizki','TK2530419','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','LEUWqNbKYv','2025-07-19 20:25:52','2025-07-19 20:25:52'),(121,'Arif Budiarto','TP180719','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','PS16euMnvU','2025-07-19 20:25:52','2025-07-19 20:25:52'),(122,'Ade Rossa Jibriliastiti','K1660121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gsf7BEgiEz','2025-07-19 20:25:52','2025-07-19 20:25:52'),(123,'Mucholis Choir','K350496','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','X633z9ViIi','2025-07-19 20:25:52','2025-07-19 20:25:52'),(124,'Misnan','1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tysEexsShY','2025-07-19 20:25:52','2025-07-19 20:25:52'),(125,'Dwi Purwanto','2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','z39woOtohR','2025-07-19 20:25:52','2025-07-19 20:25:52'),(126,'Sutiyono','3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OMdP8lQyV4','2025-07-19 20:25:52','2025-07-19 20:25:52'),(127,'Nurlaila','4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','5mKS31O8ct','2025-07-19 20:25:52','2025-07-19 20:25:52'),(128,'Sochibul Humam','6','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','H87Ks7KZL0','2025-07-19 20:25:52','2025-07-19 20:25:52'),(129,'Danis Woro','K610409','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','MmYZDc7qAc','2025-07-19 20:25:52','2025-07-19 20:25:52'),(130,'Muhammad Syaiful Fathoni','K1980121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','B0hljvZGr2','2025-07-19 20:25:52','2025-07-19 20:25:52'),(131,'Choirul Chusnah','K410600','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','taXAhtf7Lo','2025-07-19 20:25:52','2025-07-19 20:25:52'),(132,'Danang Jaka H P C P Ar','K1051013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','J3oabzjN6C','2025-07-19 20:25:52','2025-07-19 20:25:52'),(133,'Muhammad Dhofir','K1331216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tF0HVTSrhk','2025-07-19 20:25:52','2025-07-19 20:25:52'),(134,'Rizqi Camalia','K851013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2cA79ITIdl','2025-07-19 20:25:52','2025-07-19 20:25:52'),(135,'Muhammad Aries Supriyono','K1341216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','qqkUMSTOTh','2025-07-19 20:25:52','2025-07-19 20:25:52'),(136,'Darmaji','K1091013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','GZF884HQbP','2025-07-19 20:25:52','2025-07-19 20:25:52'),(137,'M. Shofil','7','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','UoiV3ndi9i','2025-07-19 20:25:52','2025-07-19 20:25:52'),(138,'Suprapto','8','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VPiCHC7fzd','2025-07-19 20:25:52','2025-07-19 20:25:52'),(139,'Muji Widodo','TP260520','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CDCKkAxbc8','2025-07-19 20:25:52','2025-07-19 20:25:52'),(140,'Muhammad Toha Mahsun','K2000121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sKsTDUBkO8','2025-07-19 20:25:52','2025-07-19 20:25:52'),(141,'Nanang Triantoro','K1041013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Y5JCikrjeP','2025-07-19 20:25:52','2025-07-19 20:25:52'),(142,'Drs. Ec. Arka Widya Udaka','TP100218','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','LyB8lp2Xur','2025-07-19 20:25:52','2025-07-19 20:25:52'),(143,'Eko Anjasmoro','5','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','m18EFCF1MO','2025-07-19 20:25:52','2025-07-19 20:25:52'),(144,'Ongky Martha Dwiyananda','K1420617','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ccfq8qS6f6','2025-07-19 20:25:52','2025-07-19 20:25:52'),(145,'Dody Kurniawan','K1790121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','R8IEZ6kALK','2025-07-19 20:25:52','2025-07-19 20:25:52'),(146,'Pujianto Nur Romadhon','TP220120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','xfBK6M64Km','2025-07-19 20:25:52','2025-07-19 20:25:52'),(147,'Agung Priyanto','K1680121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','dkKGePQMoE','2025-07-19 20:25:52','2025-07-19 20:25:52'),(148,'Arif Suriawan','K500507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cgfEXfQauo','2025-07-19 20:25:52','2025-07-19 20:25:52'),(149,'Dwi Jatmiko Utomo','K1441017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ZbbXkTP5No','2025-07-19 20:25:52','2025-07-19 20:25:52'),(150,'Edy Supatmoko','K550507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','w9E8WBVLyH','2025-07-19 20:25:52','2025-07-19 20:25:52'),(151,'Nur Rohman','K1261216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','61x0mrhwAX','2025-07-19 20:25:53','2025-07-19 20:25:53'),(152,'Putri Ayu Dwi Lestari','K2030121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','F1MxAOcOGf','2025-07-19 20:25:53','2025-07-19 20:25:53'),(153,'Aurelia Agatha','TK2670919','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','5iIdYiEMyi','2025-07-19 20:25:53','2025-07-19 20:25:53'),(154,'Rizky Kokoh Pranadita','K1451017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','JbOrkOiauf','2025-07-19 20:25:53','2025-07-19 20:25:53'),(155,'Evandi Prima Putra','K840412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','u4p501ly2C','2025-07-19 20:25:53','2025-07-19 20:25:53'),(156,'Muhammad Noor Ady Pratama','K1171016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','LDDs2YowdS','2025-07-19 20:25:53','2025-07-19 20:25:53'),(157,'Aginta Erbinda S.S','K1670121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','h5bNpvMBlt','2025-07-19 20:25:53','2025-07-19 20:25:53'),(158,'Moh.Mahfudz','K540507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Isa208biP7','2025-07-19 20:25:53','2025-07-19 20:25:53'),(159,'Eko Agus Purwanto','TP021019','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','GV1IPkbzB6','2025-07-19 20:25:53','2025-07-19 20:25:53'),(160,'Ayu Rahajeng Lalityasari','K1760121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','oHt4TpE3pU','2025-07-19 20:25:53','2025-07-19 20:25:53'),(161,'Rischa Sandia Anggriani','K720412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','PMm82b7xKm','2025-07-19 20:25:53','2025-07-19 20:25:53'),(162,'Muhammad Iwan Hariadi St','K770412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','PuJIzObJgy','2025-07-19 20:25:53','2025-07-19 20:25:53'),(163,'Aristo Ilham Wiratama','K1750121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0B8AIWKMvF','2025-07-19 20:25:53','2025-07-19 20:25:53'),(164,'Elly Sugianita','K800412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OakZsN8U9G','2025-07-19 20:25:53','2025-07-19 20:25:53'),(165,'Rizka Novianadiar','K2200221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','vzJN65stZQ','2025-07-19 20:25:53','2025-07-19 20:25:53'),(166,'Aris Yanuar Setianto','K1740121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ksAticPRBl','2025-07-19 20:25:53','2025-07-19 20:25:53'),(167,'Mohammad Reza Hafidz','TK2570919','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','MoHotBTXHR','2025-07-19 20:25:53','2025-07-19 20:25:53'),(168,'Muhammad Junaidi Abdillah','K881013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','mzEQWe4csK','2025-07-19 20:25:53','2025-07-19 20:25:53'),(169,'Carolina Indriani','K370496','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','juqKzZAYrS','2025-07-19 20:25:53','2025-07-19 20:25:53'),(170,'Mohamad Zainul Arifin','K1211016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CXW3Vtu4td','2025-07-19 20:25:53','2025-07-19 20:25:53'),(171,'Rachmad Surya Pratama','TK2540419','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VeJ9IZVMPW','2025-07-19 20:25:53','2025-07-19 20:25:53'),(172,'Anton Auliya M','K1191016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','QYZh2feYjb','2025-07-19 20:25:53','2025-07-19 20:25:53'),(173,'Muhammad Rizqi Radja Mahe','K2190221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OPhiusQGtV','2025-07-19 20:25:53','2025-07-19 20:25:53'),(174,'Nur Fuadati','K2020121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','vkgQoECaYJ','2025-07-19 20:25:53','2025-07-19 20:25:53'),(175,'Doni Tri Wardono','TP281220','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','nwByZxFaqV','2025-07-19 20:25:53','2025-07-19 20:25:53'),(176,'Ragil Catur Perwoko','K1461017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aeOenRvHo8','2025-07-19 20:25:53','2025-07-19 20:25:53'),(177,'Deviana','K1550120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','3DdyLodj3v','2025-07-19 20:25:53','2025-07-19 20:25:53'),(178,'Muzammil Walid','K2010121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','nlMJ9TMZtb','2025-07-19 20:25:53','2025-07-19 20:25:53'),(179,'Nur Indah Sari','K1650120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','x5NkYzEq9U','2025-07-19 20:25:53','2025-07-19 20:25:53'),(180,'Endah Suhesti Wardani','K1161016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lwzNkM8DdR','2025-07-19 20:25:53','2025-07-19 20:25:53'),(181,'Didik Prasetyo','K1031013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2wJdgJDZLX','2025-07-19 20:25:53','2025-07-19 20:25:53'),(182,'Muhammad Indra Arifianto','K1960121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1DTWcX0IS0','2025-07-19 20:25:53','2025-07-19 20:25:53'),(183,'Muhammad Amin','K1221016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sj3GoapjFr','2025-07-19 20:25:53','2025-07-19 20:25:53'),(184,'Agus Santoso','K1690121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','S57GDQvF7I','2025-07-19 20:25:53','2025-07-19 20:25:53'),(185,'Adilia Prisma Wulantika','K861013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','snZTcigFAO','2025-07-19 20:25:53','2025-07-19 20:25:53'),(186,'Budi Mulyanto','K320594','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','RfzrqXBBHx','2025-07-19 20:25:53','2025-07-19 20:25:53'),(187,'Nursoleh','K460507','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','fX1CtdcOAW','2025-07-19 20:25:53','2025-07-19 20:25:53'),(188,'Dhea Sundawa Istiazis','K2160221','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','rkMktCuXqI','2025-07-19 20:25:53','2025-07-19 20:25:53'),(189,'Dewi Masito','K1770121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','E5tDBFghwb','2025-07-19 20:25:53','2025-07-19 20:25:53'),(190,'Munandifah','K1101013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','YUFnWuoAFQ','2025-07-19 20:25:53','2025-07-19 20:25:53'),(191,'Aris Budiman S.P','K1530120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','f4KChunsev','2025-07-19 20:25:53','2025-07-19 20:25:53'),(192,'Arif Rachman','K1730121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','T2eCa7uI0R','2025-07-19 20:25:53','2025-07-19 20:25:53'),(193,'Muhammad Irsyad Arken','K1970121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','7SOMCKWMPy','2025-07-19 20:25:53','2025-07-19 20:25:53'),(194,'Muhammad Subekhi','K1390117','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','eXk8w9aXH7','2025-07-19 20:25:53','2025-07-19 20:25:53'),(195,'Arif Untoro','K141191','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OEBgIubSS4','2025-07-19 20:25:53','2025-07-19 20:25:53'),(196,'Anik Susilowati','K740412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Syz7OSUdQy','2025-07-19 20:25:53','2025-07-19 20:25:53'),(197,'Nur Ahlinah','K710412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','EQcwZADZAn','2025-07-19 20:25:54','2025-07-19 20:25:54'),(198,'Ricci Septian Putra','TK2791019','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','3eCZSW1ztO','2025-07-19 20:25:54','2025-07-19 20:25:54'),(199,'Muhammad Syaiful R. A.','K1990121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ad4CGP1lLf','2025-07-19 20:25:54','2025-07-19 20:25:54'),(200,'Andi Prasetiyo','K1351216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','OdkGB8dIrO','2025-07-19 20:25:54','2025-07-19 20:25:54'),(201,'Marbella Rindang Hapsari','K1920121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','bf2SsFPv99','2025-07-19 20:25:54','2025-07-19 20:25:54'),(202,'Abdul Haris Nasution','K1430617','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','fKWkPoMqqr','2025-07-19 20:25:54','2025-07-19 20:25:54'),(203,'Maulida Rahma','K1940121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','uNHacPbOoV','2025-07-19 20:25:54','2025-07-19 20:25:54'),(204,'Jatmiko Agung Nugroho','TP331021','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ZGULgncb5G','2025-07-19 20:25:54','2025-07-19 20:25:54'),(205,'Miftakhul Robbach','K921013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','DF64dEfoOl','2025-07-19 20:25:54','2025-07-19 20:25:54'),(206,'Safarianto Prio Santoso','T5052015','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ap7G1TUwfH','2025-07-19 20:25:54','2025-07-19 20:25:54'),(207,'Miseri','K230592','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aGYKqzyF4Q','2025-07-19 20:25:54','2025-07-19 20:25:54'),(208,'Machfud','K1910121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','4d4DNABE6m','2025-07-19 20:25:54','2025-07-19 20:25:54'),(209,'Venny Indah Saputri','T6052015','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hdqU6RGTNq','2025-07-19 20:25:54','2025-07-19 20:25:54'),(210,'Mochamad Abdul Aziz','K1471017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','YkHEfhjKHn','2025-07-19 20:25:54','2025-07-19 20:25:54'),(211,'Akhmad Zaelani','K1700121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hW8bmSI8fM','2025-07-19 20:25:54','2025-07-19 20:25:54'),(212,'Andria Pratama','K1720121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','xtcdzIoxiH','2025-07-19 20:25:54','2025-07-19 20:25:54'),(213,'Mardada','T344747','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','GJHhrTsKvR','2025-07-19 20:25:54','2025-07-19 20:25:54'),(214,'Anang Dwi Santoso','K1081013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Qlhp8OWsqP','2025-07-19 20:25:54','2025-07-19 20:25:54'),(215,'Moh Mustaqim','K1950121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Pmpx9MZmuX','2025-07-19 20:25:54','2025-07-19 20:25:54'),(216,'Zulkarnain Salasa','K01122020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','6XkgR4lpiM','2025-07-19 20:25:54','2025-07-19 20:25:54'),(217,'Delvi Kusuma Fibia R','T12052021','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','ja4s0xM8lL','2025-07-19 20:25:54','2025-07-19 20:25:54'),(218,'Ahmad Rosyid','K961013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ss8Ppizfh0','2025-07-19 20:25:54','2025-07-19 20:25:54'),(219,'Andi Suryaningrat Muhammad S.E','K1710121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','eGJCmr9cJH','2025-07-19 20:25:54','2025-07-19 20:25:54'),(220,'Mas Afif Wahyudi','K1930121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','7UDztFvFg0','2025-07-19 20:25:54','2025-07-19 20:25:54'),(221,'Diar Hilmi Damara','K04042021','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','CpbrXNIbT1','2025-07-19 20:25:54','2025-07-19 20:25:54'),(222,'Poernomo','pcs1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','etc84jLLXQ','2025-07-19 20:25:54','2025-07-19 20:25:54'),(223,'Agus Suwandi','K300594','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gI1AwNx19z','2025-07-19 20:25:54','2025-07-19 20:25:54'),(224,'Marsim','K121191','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','09CV2etEQc','2025-07-19 20:25:54','2025-07-19 20:25:54'),(225,'Ainurrochim','K1540120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Fzh18rpU7s','2025-07-19 20:25:54','2025-07-19 20:25:54'),(226,'M. Rozikin','K1900121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','mzggchl3Qo','2025-07-19 20:25:54','2025-07-19 20:25:54'),(227,'Andika Perdana Putra','K1590120','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','j1Hsj5PEMo','2025-07-19 20:25:54','2025-07-19 20:25:54'),(228,'Nur Yahya','K18012022','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','I0VHrFpg4p','2025-07-19 20:25:54','2025-07-19 20:25:54'),(229,'Suhendik','K19092018','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TZqqYzVSa3','2025-07-19 20:25:54','2025-07-19 20:25:54'),(230,'Siti Roudhoh','T10052020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','klDouY1w7T','2025-07-19 20:25:54','2025-07-19 20:25:54'),(231,'Nur Maulidia','T11052020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Dq3vUJ52SF','2025-07-19 20:25:54','2025-07-19 20:25:54'),(232,'Agus Purwito','T9022020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','6mm2Lt5Im8','2025-07-19 20:25:54','2025-07-19 20:25:54'),(233,'Ray Rayandika','T8092018','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','smthzHqMrS','2025-07-19 20:25:54','2025-07-19 20:25:54'),(234,'Samsul Arifin','T7022017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','3OZtlxOuen','2025-07-19 20:25:54','2025-07-19 20:25:54'),(235,'Mas Prabawa','K1021013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','zFHC28qTyq','2025-07-19 20:25:54','2025-07-19 20:25:54'),(236,'Moh.Fauzi','K830412','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0m4rk0XVyM','2025-07-19 20:25:54','2025-07-19 20:25:54'),(237,'Achmad Nairi','K1251216','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','q69K7ZmOtO','2025-07-19 20:25:54','2025-07-19 20:25:54'),(238,'Ali Chandra Lestiawan','K1400117','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','oeMEs8dnM9','2025-07-19 20:25:54','2025-07-19 20:25:54'),(239,'Abdul Wachid','K670310','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2J3cEnLA8a','2025-07-19 20:25:54','2025-07-19 20:25:54'),(240,'Ahmad Syafii','TP351121','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VjdQ6b7BBy','2025-07-19 20:25:54','2025-07-19 20:25:54'),(241,'Arie Indriyani','K20072020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','xZBHhOTG2o','2025-07-19 20:25:54','2025-07-19 20:25:54'),(242,'Andi','12345678','$2y$10$eZlTAn9uEAjIFEnCKbbQrOrO/ncJziSEMUBie7rhKE7SXWMj42uAK',NULL,'2025-07-19 21:04:53','2025-07-19 21:04:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-19 18:00:06
