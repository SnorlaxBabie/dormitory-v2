/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 10.4.22-MariaDB : Database - dormitory
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dormitory` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `dormitory`;

/*Table structure for table `announcements` */

DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `announcements` */

insert  into `announcements`(`recid`,`title`,`content`,`usr_cde`,`start_date`,`end_date`,`created_at`,`updated_at`) values 
(1,'Holiday Schedule','The dormitory will be closed on national holidays.','landlord','2024-11-15','2024-12-01','2024-11-05 18:52:19','2024-11-05 18:52:47'),
(2,'New Laundry Rules','Starting next month, bawal na mag laba','landlord','2024-12-01','2025-01-01','2024-11-05 18:52:19','2024-11-05 18:52:48'),
(3,'Emergency Procedures','All residents must review updated emergency procedures','landlord','2024-11-05','2025-01-05','2024-11-05 18:52:19','2024-11-05 18:52:48'),
(4,'Resident Meeting','Hakdog','landlord','2024-11-10','2024-11-20','2024-11-05 18:52:19','2024-11-05 18:52:49'),
(5,'Brownout','Mag-brownout 1 month','landlord','2024-10-30','2024-11-30','2024-11-05 18:52:19','2024-11-05 18:52:49'),
(6,'Cleaning Schedule Update','Common areas will now be cleaned on Tuesdays and Thursdays.','landlord','2024-11-01','2024-12-01','2024-11-05 18:52:19','2024-11-05 18:52:50');

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBJECT` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `other` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `STATUS` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `messages` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `prev_balance` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mode of Payment',
  `proofpayment` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payments` */

/*Table structure for table `roomfile` */

DROP TABLE IF EXISTS `roomfile`;

CREATE TABLE `roomfile` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomcapacity` int(20) DEFAULT NULL,
  `current_tenants` int(11) DEFAULT 0,
  `roomstat` enum('available','occupied','reserved') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roomfile` */

insert  into `roomfile`(`recid`,`roomid`,`roomnum`,`roomcapacity`,`current_tenants`,`roomstat`,`created_at`,`updated_at`) values 
(1,'ROOM6729f93e45810','Room-01',4,0,'available','2024-11-05 18:53:50','2024-11-05 18:53:50'),
(2,'ROOM6729f945d6765','Room-02',4,1,'available','2024-11-05 18:53:57','2024-11-05 19:20:55');

/*Table structure for table `roomfile0` */

DROP TABLE IF EXISTS `roomfile0`;

CREATE TABLE `roomfile0` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bedspacenum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bedspace Number',
  `current_tenants` int(11) DEFAULT 0,
  `amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Per month Amount',
  `roomstat` enum('available','occupied','reserved') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roomfile0` */

insert  into `roomfile0`(`recid`,`roomid`,`usr_cde`,`roomnum`,`bedspacenum`,`current_tenants`,`amount`,`roomstat`,`created_at`,`updated_at`) values 
(1,'ROOM6729f93e45810',NULL,'Room-01','Bed-01',0,5000.00,'available','2024-11-05 18:54:13','2024-11-05 18:56:07'),
(2,'ROOM6729f93e45810',NULL,'Room-01','Bed-02',0,5000.00,'available','2024-11-05 18:54:25','2024-11-05 18:56:07'),
(3,'ROOM6729f93e45810',NULL,'Room-01','Bed-03',0,5000.00,'available','2024-11-05 18:55:57','2024-11-05 18:55:57'),
(4,'ROOM6729f93e45810',NULL,'Room-01','Bed-04',0,5000.00,'available','2024-11-05 18:56:25','2024-11-05 18:56:25'),
(5,'ROOM6729f945d6765','Juan6729fadd26361','Room-02','Bed-05',1,5000.00,'occupied','2024-11-05 18:57:53','2024-11-05 19:19:55');

/*Table structure for table `roomfile1` */

DROP TABLE IF EXISTS `roomfile1`;

CREATE TABLE `roomfile1` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomcapacity` int(20) DEFAULT NULL,
  `bedspacenum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bedspace Number',
  `current_tenants` int(11) DEFAULT 0,
  `roomstat` enum('available','occupied','reserved') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `startlease` date DEFAULT NULL COMMENT 'Lease Start Date',
  `endlease` date DEFAULT NULL COMMENT 'Lease End Date',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roomfile1` */

insert  into `roomfile1`(`recid`,`roomid`,`usr_cde`,`roomnum`,`roomcapacity`,`bedspacenum`,`current_tenants`,`roomstat`,`startlease`,`endlease`,`created_at`,`updated_at`) values 
(2,'ROOM6729f945d6765','Jonathan6729fa8db43de','Room-02',4,'Bed-05',1,'occupied','2024-11-05','2024-11-30','2024-11-05 19:10:19','2024-11-05 19:10:19');

/*Table structure for table `roompendingrequest` */

DROP TABLE IF EXISTS `roompendingrequest`;

CREATE TABLE `roompendingrequest` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `usr_status` tinyint(1) DEFAULT 0,
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT 0.00 COMMENT 'Deposit Amount',
  `balance` decimal(10,2) DEFAULT 0.00,
  `request_status` tinyint(1) DEFAULT 0,
  `bedspacenum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bedspace Number',
  `startlease` date DEFAULT NULL COMMENT 'Lease Start Date',
  `endlease` date DEFAULT NULL COMMENT 'Lease End Date',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roompendingrequest` */

insert  into `roompendingrequest`(`recid`,`usr_status`,`roomid`,`usr_cde`,`roomnum`,`deposit`,`balance`,`request_status`,`bedspacenum`,`startlease`,`endlease`,`created_at`,`updated_at`) values 
(1,0,'ROOM6729f945d6765','Jonathan6729fa8db43de','Room-02',5000.00,5000.00,1,'Bed-05','2024-11-05','2024-11-30','2024-11-05 18:59:25','2024-11-05 19:10:19'),
(2,0,'ROOM6729f945d6765','Juan6729fadd26361','Room-02',5000.00,5000.00,0,'Bed-05','2024-11-05','2024-11-30','2024-11-05 19:00:45','2024-11-05 19:22:06');

/*Table structure for table `stafffile` */

DROP TABLE IF EXISTS `stafffile`;

CREATE TABLE `stafffile` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `staffname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staffage` int(100) DEFAULT 0,
  `staffcontact` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staffemail` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staffposition` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staffimage` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`),
  UNIQUE KEY `staffemail` (`staffemail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stafffile` */

/*Table structure for table `standardparameter` */

DROP TABLE IF EXISTS `standardparameter`;

CREATE TABLE `standardparameter` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companyname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companyemail` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companyaddress` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companycontactnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `online_logo` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gcash_api` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail_username` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail_password` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days_due_date` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `standardparameter` */

insert  into `standardparameter`(`recid`,`title`,`companyname`,`companyemail`,`companyaddress`,`companycontactnum`,`online_logo`,`gcash_api`,`gmail_username`,`gmail_password`,`days_due_date`,`updated_at`) values 
(1,'Dormitory System','Dormitory System','managementsystemdormitory@gmail.com','Palanginan Iba, Zambales','09xxxxxxxxxx','https://i.ibb.co/L6Wmh44/logo.png','sk_test_wTUZeavhz87nvqCt8JFsf6kU','managementsystemdormitory@gmail.com','ctyz dubx koir ndgk',30,'2024-11-05 19:25:23');

/*Table structure for table `tenantrequest` */

DROP TABLE IF EXISTS `tenantrequest`;

CREATE TABLE `tenantrequest` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `requestid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requestprio` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Priority Level',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Description',
  `requestsched` date DEFAULT NULL COMMENT 'date request',
  `staffname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reqstatus` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tenantrequest` */

/*Table structure for table `userfile` */

DROP TABLE IF EXISTS `userfile`;

CREATE TABLE `userfile` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `usr_cde` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Code',
  `usr_fname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User First Name',
  `usr_mname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Middle Name',
  `usr_lname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Last Name',
  `usr_sex` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Sex',
  `age` decimal(10,2) DEFAULT 0.00 COMMENT 'Age',
  `usr_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Email',
  `usr_contactnum` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Contact Number',
  `usr_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Username',
  `usr_pwd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User Password (Hashed)',
  `usr_brgy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Barangay',
  `usr_municipality` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Municipality',
  `usr_province` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Province',
  `usr_havemedcondition` tinyint(1) DEFAULT 0 COMMENT 'Has Medical Condition (0 = No, 1 = Yes)',
  `trmscheck` tinyint(1) DEFAULT 0 COMMENT 'if check terms and condition (0 = No, 1 = Yes)',
  `eci_fullname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Full Name',
  `eci_relationship` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Relationship',
  `eci_contactnum` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Number',
  `eci_homenum` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Home Number',
  `eci_worknum` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Work Number',
  `eci_address` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emergency Contact Address',
  `usr_status` tinyint(1) DEFAULT 0,
  `vacated` tinyint(1) DEFAULT 0,
  `vacated_date` date DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT 0.00 COMMENT 'Deposit Amount',
  `due_date` date DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) DEFAULT 0.00,
  `roomnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Room Number',
  `bedspacenum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bedspace Number',
  `startlease` date DEFAULT NULL COMMENT 'Lease Start Date',
  `endlease` date DEFAULT NULL COMMENT 'Lease End Date',
  `usr_versionuse` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'System Version',
  `usr_lvl` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'USER' COMMENT 'User Level',
  `roomid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record Created Timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record Last Updated Timestamp',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `userfile` */

insert  into `userfile`(`recid`,`usr_cde`,`usr_fname`,`usr_mname`,`usr_lname`,`usr_sex`,`age`,`usr_email`,`usr_contactnum`,`usr_name`,`usr_pwd`,`usr_brgy`,`usr_municipality`,`usr_province`,`usr_havemedcondition`,`trmscheck`,`eci_fullname`,`eci_relationship`,`eci_contactnum`,`eci_homenum`,`eci_worknum`,`eci_address`,`usr_status`,`vacated`,`vacated_date`,`deposit`,`due_date`,`paid`,`balance`,`roomnum`,`bedspacenum`,`startlease`,`endlease`,`usr_versionuse`,`usr_lvl`,`roomid`,`created_at`,`updated_at`) values 
(1,'landlord','Juan',NULL,'Dela Cruz','Male',30.00,'managementsystemdormitory@gmail.com','09123456789','superadmin','40bd001563085fc35165329ea1ff5c5ecbdbbeef','sample brgy','sample municipality','sample province',0,1,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,0.00,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'ADMIN',NULL,'2024-11-05 18:45:41','2024-11-05 18:47:48'),
(2,'Jonathan6729fa8db43de','Jonathan','','Abiva','Male',0.00,'abivajonathan17@gmail.com','09123456789','Jonathanabiva123','40bd001563085fc35165329ea1ff5c5ecbdbbeef','Apostol','San Felipe','Zambales',0,1,'Test','Father','09123456789','123','1234','HAKDOG',1,0,NULL,5000.00,'2024-12-05',0.00,5000.00,'Room-02','Bed-05','2024-11-05','2024-11-30',NULL,'USER','ROOM6729f945d6765','2024-11-05 18:59:25','2024-11-05 19:10:19'),
(3,'Juan6729fadd26361','Juan','','Dela Cruz','Male',0.00,'Test@gmail.com','09123456789','juan123','40bd001563085fc35165329ea1ff5c5ecbdbbeef','dklasjd','lhjflsdhsd','p;sdhflsdfh',0,1,'wdl;fhjfopg','pjgdewpgheg','0192345687','wfhjwof','234234','dfhokwdeoiewfhwjg',0,0,NULL,NULL,'2024-12-05',0.00,NULL,NULL,NULL,NULL,NULL,NULL,'USER',NULL,'2024-11-05 19:00:45','2024-11-05 19:21:24');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
