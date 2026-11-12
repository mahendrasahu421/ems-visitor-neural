-- NeuralInvoice tables prepared for import into existing EMS DB
-- Source: neurab2l_neuralinvoice.sql
-- Note: NeuralInvoice table clients was renamed to invoice_clients to avoid conflict with EMS clients.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 05, 2026 at 11:59 AM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `neurab2l_neuralinvoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `citymaster`
--

CREATE TABLE `citymaster` (
  `CityId` int(10) UNSIGNED NOT NULL,
  `CityName` varchar(45) NOT NULL,
  `StateId` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `citymaster`
--

INSERT INTO `citymaster` (`CityId`, `CityName`, `StateId`) VALUES
(1, 'Haldwani', 4),
(6, 'Bangalore', 6),
(8, 'Bhopal', 4),
(9, 'Pune', 8),
(10, 'Jeddah', 9),
(11, 'Kanpur', 0),
(12, 'New Delhi', 10),
(13, 'Jhansi', 11),
(14, 'Satna', 8),
(15, 'Mumbai', 12),
(16, 'Chennai', 13),
(17, ' Moradabad ', 11),
(18, 'Allahabad', 11),
(19, 'Kochi', 14),
(20, 'Lucknow', 11),
(21, 'Banda', 11),
(22, 'Farrukhabad', 11),
(23, 'Jaipur', 15),
(24, 'New Jersey', 16),
(25, 'Pilani', 15),
(26, 'Atlanta', 17),
(27, 'Rae Bareli', 11),
(28, 'Alexandria', 18),
(29, 'Unnao', 11),
(30, 'Anpara', 11),
(31, ' Kingston Terrace', 19),
(32, 'Bangalore', 20),
(33, ' Noida ', 11),
(34, 'Dubai', 21),
(35, 'West Bengal', 22),
(36, 'New South Wales', 23),
(37, 'London', 24);

-- --------------------------------------------------------

--
-- Table structure for table `clientmaster`
--

CREATE TABLE `clientmaster` (
  `ClientID` int(11) NOT NULL,
  `CompanyID` int(11) NOT NULL,
  `ProductCategoryType` int(11) DEFAULT NULL,
  `CategoryType` int(11) DEFAULT NULL,
  `ClientCode` text NOT NULL,
  `Priority` varchar(5) NOT NULL,
  `Source` int(11) DEFAULT NULL,
  `ClientName` varchar(50) NOT NULL,
  `ConcernOwner` varchar(40) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `MobileNo` varchar(15) NOT NULL,
  `Fax` varchar(15) NOT NULL COMMENT 'fax = cin',
  `website` text NOT NULL,
  `client_logo` varchar(255) DEFAULT NULL,
  `gst_number` varchar(255) NOT NULL,
  `pinNo` varchar(15) DEFAULT NULL,
  `EmailAddress` varchar(30) NOT NULL,
  `Country` varchar(40) NOT NULL,
  `StateID` varchar(11) DEFAULT NULL,
  `PhoneNo` varchar(30) DEFAULT NULL,
  `DelStatus` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clientmaster`
--

INSERT INTO `clientmaster` (`ClientID`, `CompanyID`, `ProductCategoryType`, `CategoryType`, `ClientCode`, `Priority`, `Source`, `ClientName`, `ConcernOwner`, `Address`, `MobileNo`, `Fax`, `website`, `client_logo`, `gst_number`, `pinNo`, `EmailAddress`, `Country`, `StateID`, `PhoneNo`, `DelStatus`, `created_at`) VALUES
(1, 11, NULL, NULL, '', '', NULL, 'Event stan', 'suraj mahajan', 'Dubai', '917800789705', '', 'https://eventstancom.vercel.app/', '', '', NULL, 'info@eventstan.com', '11', '1', NULL, 1, '2026-06-10 03:57:06');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_clients`
--

CREATE TABLE `invoice_clients` (
  `id` int(11) NOT NULL,
  `invoiceDate` date NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `concern_person` varchar(255) NOT NULL,
  `cin` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `gst_number` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoice_clients`
--

INSERT INTO `invoice_clients` (`id`, `invoiceDate`, `client_name`, `concern_person`, `cin`, `website`, `email`, `mobile`, `country_id`, `state_id`, `address`, `gst_number`) VALUES
(1, '2025-01-10', 'test', 'Shashank Shrivastava', 'test', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=neurab2l_emsdb&table=statemaster', 'laxmandas@gmail.com', '8318721943', 4, 6, 'SQDWFSGDHFJ', 'DHTFYKU');

-- --------------------------------------------------------

--
-- Table structure for table `client_projects`
--

CREATE TABLE `client_projects` (
  `project_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `engagement_type` enum('project','amc_only') NOT NULL DEFAULT 'project',
  `has_amc` enum('yes','no') DEFAULT 'no',
  `payment_mode` enum('percentage','module') NOT NULL DEFAULT 'percentage',
  `total_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) DEFAULT '0.00',
  `tax_amount` decimal(12,2) DEFAULT '0.00',
  `cgst_percent` decimal(5,2) DEFAULT '0.00',
  `cgst_amount` decimal(12,2) DEFAULT '0.00',
  `sgst_percent` decimal(5,2) DEFAULT '0.00',
  `sgst_amount` decimal(12,2) DEFAULT '0.00',
  `igst_percent` decimal(5,2) DEFAULT '0.00',
  `igst_amount` decimal(12,2) DEFAULT '0.00',
  `tax_type` varchar(50) DEFAULT NULL,
  `total_value_with_tax` decimal(12,2) DEFAULT '0.00',
  `total_with_tax` decimal(12,2) DEFAULT '0.00',
  `final_amount` decimal(12,2) DEFAULT '0.00',
  `amc_start` date DEFAULT NULL,
  `amc_end` date DEFAULT NULL,
  `project_start_date` date DEFAULT NULL,
  `project_end_date` date DEFAULT NULL,
  `financial_year` varchar(12) DEFAULT NULL,
  `amc_working_days` int(11) DEFAULT '0',
  `project_duration_days` int(11) DEFAULT NULL,
  `duration_mode` varchar(20) DEFAULT 'working',
  `work_type` varchar(100) DEFAULT NULL,
  `work_type_id` int(11) DEFAULT NULL,
  `proposal_file` varchar(255) DEFAULT NULL,
  `notes` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `client_projects`
--

INSERT INTO `client_projects` (`project_id`, `client_id`, `project_name`, `engagement_type`, `has_amc`, `payment_mode`, `total_value`, `tax_percent`, `tax_amount`, `cgst_percent`, `cgst_amount`, `sgst_percent`, `sgst_amount`, `igst_percent`, `igst_amount`, `tax_type`, `total_value_with_tax`, `total_with_tax`, `final_amount`, `amc_start`, `amc_end`, `project_start_date`, `project_end_date`, `financial_year`, `amc_working_days`, `project_duration_days`, `duration_mode`, `work_type`, `work_type_id`, `proposal_file`, `notes`, `status`, `created_at`) VALUES
(1, 1, 'DULATI WEBSITE', 'project', 'no', 'percentage', 2000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'international', 2000.00, 2000.00, 2000.00, NULL, NULL, '2026-06-01', '2026-07-13', '2026-27', 0, 30, 'working', 'Mobile App', 1, NULL, '', 1, '2026-06-15 17:57:28');

-- --------------------------------------------------------

--
-- Table structure for table `companymaster`
--

CREATE TABLE `companymaster` (
  `CompanyID` int(11) NOT NULL,
  `CompanyName` varchar(150) NOT NULL,
  `CompanyCode` varchar(50) DEFAULT NULL,
  `CompanyLogo` varchar(255) DEFAULT NULL,
  `EmailAddress` varchar(150) DEFAULT NULL,
  `MobileNo` varchar(30) DEFAULT NULL,
  `PhoneNo` varchar(30) DEFAULT NULL,
  `Website` varchar(150) DEFAULT NULL,
  `Address` text,
  `RegistrationNo` varchar(45) DEFAULT NULL,
  `PanNo` varchar(25) DEFAULT NULL,
  `GSTNumber` varchar(25) DEFAULT NULL,
  `IECNo` varchar(30) DEFAULT NULL,
  `LUTNo` varchar(50) DEFAULT NULL,
  `TANNo` varchar(30) DEFAULT NULL,
  `Bank1AccountName` varchar(150) DEFAULT NULL,
  `Bank1AccountNo` varchar(50) DEFAULT NULL,
  `Bank1BankName` varchar(150) DEFAULT NULL,
  `Bank1IFSC` varchar(30) DEFAULT NULL,
  `Bank1MICR` varchar(30) DEFAULT NULL,
  `Bank1SwiftCode` varchar(30) DEFAULT NULL,
  `Bank1Branch` varchar(150) DEFAULT NULL,
  `Bank1BranchCode` varchar(30) DEFAULT NULL,
  `Bank2AccountName` varchar(150) DEFAULT NULL,
  `Bank2AccountNo` varchar(50) DEFAULT NULL,
  `Bank2BankName` varchar(150) DEFAULT NULL,
  `Bank2IFSC` varchar(30) DEFAULT NULL,
  `Bank2MICR` varchar(30) DEFAULT NULL,
  `Bank2SwiftCode` varchar(30) DEFAULT NULL,
  `Bank2Branch` varchar(150) DEFAULT NULL,
  `Bank2BranchCode` varchar(30) DEFAULT NULL,
  `DelStatus` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `countrymaster`
--

CREATE TABLE `countrymaster` (
  `CountryID` int(10) UNSIGNED NOT NULL,
  `CountryName` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countrymaster`
--

INSERT INTO `countrymaster` (`CountryID`, `CountryName`) VALUES
(3, 'United States'),
(4, 'India'),
(5, 'China'),
(6, 'Russia'),
(8, 'Japan'),
(9, 'Saudi Arabia '),
(10, 'Singapoor'),
(11, 'Dubai'),
(12, 'United Arab Emirates'),
(13, 'Australia'),
(14, 'United Kingdom');

-- --------------------------------------------------------

--
-- Table structure for table `designationmaster`
--

CREATE TABLE `designationmaster` (
  `DesignationID` int(10) UNSIGNED NOT NULL,
  `CreationDate` datetime NOT NULL,
  `Designation` varchar(45) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `designationmaster`
--

INSERT INTO `designationmaster` (`DesignationID`, `CreationDate`, `Designation`) VALUES
(1, '2012-08-04 00:00:00', 'Senior Developer'),
(2, '2012-08-04 00:00:00', 'Project Manager'),
(3, '2012-08-04 00:00:00', 'Office Admin'),
(7, '2012-12-29 00:00:00', 'Chief executive'),
(8, '2013-01-03 00:00:00', 'Asdasdas'),
(9, '2013-01-04 00:00:00', 'Advisor'),
(10, '2013-01-09 00:00:00', 'Techno'),
(11, '2013-01-09 00:00:00', 'Customer Care'),
(12, '2013-01-31 00:00:00', 'Developer'),
(13, '2023-09-15 00:00:00', 'Tech Lead'),
(14, '2023-09-15 00:00:00', 'Senior graphic designer');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `expense_name` varchar(255) NOT NULL,
  `expense_cost` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `expense_mode` enum('Cash','Online') NOT NULL,
  `expense_invoice` varchar(255) DEFAULT NULL,
  `expense_status` int(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `expense_categories_id` int(11) DEFAULT NULL,
  `categories` varchar(50) NOT NULL,
  `status` int(5) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_type` int(2) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `CompanyID` int(11) DEFAULT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `invoiceDate` date NOT NULL,
  `concern` varchar(50) NOT NULL,
  `mobile` int(13) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `deliverydate` date NOT NULL,
  `duedate` date NOT NULL,
  `duration_days` varchar(100) DEFAULT NULL,
  `sacCode` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `finalAmount` int(11) NOT NULL,
  `sgstAmount` int(11) DEFAULT NULL,
  `cgstAmount` int(11) DEFAULT NULL,
  `igstAmount` int(11) DEFAULT NULL,
  `bankChargePercent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `bankChargeAmount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `totalAmount` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `total_amount_foreign` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` int(2) NOT NULL DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `creation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `invoice_type`, `ClientID`, `CompanyID`, `milestone_id`, `invoiceDate`, `concern`, `mobile`, `terms`, `deliverydate`, `duedate`, `duration_days`, `sacCode`, `subtotal`, `discount`, `finalAmount`, `sgstAmount`, `cgstAmount`, `igstAmount`, `bankChargePercent`, `bankChargeAmount`, `totalAmount`, `currency`, `exchange_rate`, `total_amount_foreign`, `status`, `sent_at`, `cancelled_at`, `cancel_reason`, `creation_date`) VALUES
(1, 1, 1, NULL, NULL, '2026-06-15', 'Shashank Shrivastava', 2147483647, 'E.& O.E.\r\n                                1. Payment to be made by \r\n                                Cheque/NEFT/RTGS\r\n                                2. Interest @18% p.a. will be \r\n                                charged if the payment is not \r\n        ', '2026-06-15', '2026-06-15', '30-05-2026 to 31-06-20226', 998313, 500000, 0, 500000, 0, 0, 0, 0.00, 0.00, 500000, 'INR', 1.0000, 0.00, 1, '2026-06-15 17:37:04', NULL, NULL, '2026-06-15'),
(2, 1, 1, NULL, 1, '2026-06-15', 'Shashank Shrivastava', 2147483647, 'E.& O.E.\r\n                                1. Payment to be made by \r\n                                Cheque/NEFT/RTGS\r\n                                2. Interest @18% p.a. will be \r\n                                charged if the payment is not \r\n        ', '2026-06-15', '2026-06-15', '', 998313, 2000, 0, 2000, 0, 0, 0, 0.00, 0.00, 2000, 'INR', 1.0000, 0.00, 1, '2026-06-15 17:57:40', NULL, NULL, '2026-06-15');

-- --------------------------------------------------------

--
-- Table structure for table `invoiceemployees`
--

CREATE TABLE `invoiceemployees` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoiceemployees`
--

INSERT INTO `invoiceemployees` (`id`, `username`, `password`) VALUES
(1, 'mahendra.s@neuralinfo.org', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `paymentinvoice`
--

CREATE TABLE `paymentinvoice` (
  `Payment_InvoiceID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Invoice_Date` datetime NOT NULL,
  `Invoice_No` varchar(20) NOT NULL,
  `Party_ID` int(13) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Discount` decimal(10,2) NOT NULL,
  `FAmount` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_payment_milestones`
--

CREATE TABLE `project_payment_milestones` (
  `milestone_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `milestone_label` varchar(255) NOT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `percentage` decimal(6,2) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) DEFAULT '0.00',
  `amount_with_tax` decimal(12,2) DEFAULT '0.00',
  `invoiced_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cgst_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sgst_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `igst_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_with_tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_date` date DEFAULT NULL,
  `is_paid` tinyint(4) NOT NULL DEFAULT '0',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `partial_paid` decimal(12,2) DEFAULT '0.00',
  `paid_date` date DEFAULT NULL,
  `last_paid_date` date DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_payment_milestones`
--

INSERT INTO `project_payment_milestones` (`milestone_id`, `project_id`, `milestone_label`, `module_name`, `percentage`, `amount`, `tax_amount`, `amount_with_tax`, `invoiced_amount`, `cgst_amount`, `sgst_amount`, `igst_amount`, `total_with_tax`, `due_date`, `is_paid`, `paid_amount`, `partial_paid`, `paid_date`, `last_paid_date`, `sort_order`) VALUES
(1, 1, 'face 1', NULL, 100.00, 2000.00, 0.00, 2000.00, 2000.00, 0.00, 0.00, 0.00, 0.00, NULL, 1, 2000.00, 0.00, NULL, '2026-06-15', 0),
(1, 1, 'face 1', NULL, 100.00, 2000.00, 0.00, 2000.00, 2000.00, 0.00, 0.00, 0.00, 0.00, NULL, 1, 2000.00, 0.00, NULL, '2026-06-15', 0),
(1, 1, 'face 1', NULL, 100.00, 2000.00, 0.00, 2000.00, 2000.00, 0.00, 0.00, 0.00, 0.00, NULL, 1, 2000.00, 0.00, NULL, '2026-06-15', 0),
(1, 1, 'face 1', NULL, 100.00, 2000.00, 0.00, 2000.00, 2000.00, 0.00, 0.00, 0.00, 0.00, NULL, 1, 2000.00, 0.00, NULL, '2026-06-15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `receiptinvoice`
--

CREATE TABLE `receiptinvoice` (
  `Receipt_InvoiceID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Invoice_Date` datetime NOT NULL,
  `Invoice_No` varchar(20) NOT NULL,
  `Party_ID` int(12) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Discount` decimal(10,2) NOT NULL,
  `FAmount` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recived_payment`
--

CREATE TABLE `recived_payment` (
  `id` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `workAmount_invoice_id` int(11) NOT NULL,
  `taxinvoiceNumber` varchar(50) NOT NULL,
  `recived_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tds_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `received_net` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_note` varchar(255) DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `status` int(2) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sessionmaster`
--

CREATE TABLE `sessionmaster` (
  `SessionID` int(10) UNSIGNED NOT NULL,
  `SessionFrom` datetime NOT NULL,
  `SessionTo` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessionmaster`
--

INSERT INTO `sessionmaster` (`SessionID`, `SessionFrom`, `SessionTo`) VALUES
(1, '2012-08-10 00:00:00', '2012-12-30 00:00:00'),
(2, '2012-07-10 00:00:00', '2013-08-10 00:00:00'),
(3, '2009-01-20 00:00:00', '2012-06-30 00:00:00'),
(4, '2012-08-10 00:00:00', '2012-08-17 00:00:00'),
(1, '2012-08-10 00:00:00', '2012-12-30 00:00:00'),
(2, '2012-07-10 00:00:00', '2013-08-10 00:00:00'),
(3, '2009-01-20 00:00:00', '2012-06-30 00:00:00'),
(4, '2012-08-10 00:00:00', '2012-08-17 00:00:00'),
(1, '2012-08-10 00:00:00', '2012-12-30 00:00:00'),
(2, '2012-07-10 00:00:00', '2013-08-10 00:00:00'),
(3, '2009-01-20 00:00:00', '2012-06-30 00:00:00'),
(4, '2012-08-10 00:00:00', '2012-08-17 00:00:00'),
(1, '2012-08-10 00:00:00', '2012-12-30 00:00:00'),
(2, '2012-07-10 00:00:00', '2013-08-10 00:00:00'),
(3, '2009-01-20 00:00:00', '2012-06-30 00:00:00'),
(4, '2012-08-10 00:00:00', '2012-08-17 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `statemaster`
--

CREATE TABLE `statemaster` (
  `StateID` int(10) UNSIGNED NOT NULL,
  `StateName` varchar(45) NOT NULL,
  `CountryID` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statemaster`
--

INSERT INTO `statemaster` (`StateID`, `StateName`, `CountryID`) VALUES
(1, 'International', 11),
(4, 'Himachal Pradesh', 4),
(5, 'Amsterdam', 3),
(6, 'Kerala', 4),
(7, 'Andhra Pradesh', 4),
(8, 'Madhya Pradesh', 4),
(9, 'Jeddah', 9),
(10, 'Delhi', 4),
(11, 'Uttar Pradesh', 4),
(12, 'Maharashtra', 4),
(13, 'Tamil Nadu', 4),
(14, 'Kerala', 4),
(15, 'Rajasthan', 4),
(16, 'New Jersey', 3),
(17, 'Atlanta', 3),
(18, 'Alexandria', 3),
(19, 'None of these', 10),
(20, 'Karnataka', 4),
(21, 'Dubai', 12),
(22, 'West Bengal', 4),
(23, 'New South Wales', 13),
(24, 'London', 14),
(25, 'Arunachal Pradesh', 4),
(26, 'Assam', 4),
(27, 'Bihar', 4),
(28, 'Chhattisgarh', 4),
(29, 'Goa', 4),
(30, 'Gujarat', 4),
(31, 'Haryana', 4),
(32, 'Jharkhand', 4),
(33, 'Manipur', 4),
(34, 'Meghalaya', 4),
(35, 'Mizoram', 4),
(36, 'Nagaland', 4),
(37, 'Odisha', 4),
(38, 'Punjab', 4),
(39, 'Sikkim', 4),
(40, 'Telangana', 4),
(41, 'Tripura', 4),
(42, 'Uttarakhand', 4),
(43, 'Andaman and Nicobar Islands', 4),
(44, 'Chandigarh', 4),
(45, 'Dadra and Nagar Haveli and Daman and Diu', 4),
(46, 'Jammu and Kashmir', 4),
(47, 'Ladakh', 4),
(48, 'Lakshadweep', 4),
(49, 'Puducherry', 4),
(1, 'International', 11),
(4, 'Himachal Pradesh', 4),
(5, 'Amsterdam', 3),
(6, 'Kerala', 4),
(7, 'Andhra Pradesh', 4),
(8, 'Madhya Pradesh', 4),
(9, 'Jeddah', 9),
(10, 'Delhi', 4),
(11, 'Uttar Pradesh', 4),
(12, 'Maharashtra', 4),
(13, 'Tamil Nadu', 4),
(14, 'Kerala', 4),
(15, 'Rajasthan', 4),
(16, 'New Jersey', 3),
(17, 'Atlanta', 3),
(18, 'Alexandria', 3),
(19, 'None of these', 10),
(20, 'Karnataka', 4),
(21, 'Dubai', 12),
(22, 'West Bengal', 4),
(23, 'New South Wales', 13),
(24, 'London', 14),
(25, 'Arunachal Pradesh', 4),
(26, 'Assam', 4),
(27, 'Bihar', 4),
(28, 'Chhattisgarh', 4),
(29, 'Goa', 4),
(30, 'Gujarat', 4),
(31, 'Haryana', 4),
(32, 'Jharkhand', 4),
(33, 'Manipur', 4),
(34, 'Meghalaya', 4),
(35, 'Mizoram', 4),
(36, 'Nagaland', 4),
(37, 'Odisha', 4),
(38, 'Punjab', 4),
(39, 'Sikkim', 4),
(40, 'Telangana', 4),
(41, 'Tripura', 4),
(42, 'Uttarakhand', 4),
(43, 'Andaman and Nicobar Islands', 4),
(44, 'Chandigarh', 4),
(45, 'Dadra and Nagar Haveli and Daman and Diu', 4),
(46, 'Jammu and Kashmir', 4),
(47, 'Ladakh', 4),
(48, 'Lakshadweep', 4),
(49, 'Puducherry', 4),
(1, 'International', 11),
(4, 'Himachal Pradesh', 4),
(5, 'Amsterdam', 3),
(6, 'Kerala', 4),
(7, 'Andhra Pradesh', 4),
(8, 'Madhya Pradesh', 4),
(9, 'Jeddah', 9),
(10, 'Delhi', 4),
(11, 'Uttar Pradesh', 4),
(12, 'Maharashtra', 4),
(13, 'Tamil Nadu', 4),
(14, 'Kerala', 4),
(15, 'Rajasthan', 4),
(16, 'New Jersey', 3),
(17, 'Atlanta', 3),
(18, 'Alexandria', 3),
(19, 'None of these', 10),
(20, 'Karnataka', 4),
(21, 'Dubai', 12),
(22, 'West Bengal', 4),
(23, 'New South Wales', 13),
(24, 'London', 14),
(25, 'Arunachal Pradesh', 4),
(26, 'Assam', 4),
(27, 'Bihar', 4),
(28, 'Chhattisgarh', 4),
(29, 'Goa', 4),
(30, 'Gujarat', 4),
(31, 'Haryana', 4),
(32, 'Jharkhand', 4),
(33, 'Manipur', 4),
(34, 'Meghalaya', 4),
(35, 'Mizoram', 4),
(36, 'Nagaland', 4),
(37, 'Odisha', 4),
(38, 'Punjab', 4),
(39, 'Sikkim', 4),
(40, 'Telangana', 4),
(41, 'Tripura', 4),
(42, 'Uttarakhand', 4),
(43, 'Andaman and Nicobar Islands', 4),
(44, 'Chandigarh', 4),
(45, 'Dadra and Nagar Haveli and Daman and Diu', 4),
(46, 'Jammu and Kashmir', 4),
(47, 'Ladakh', 4),
(48, 'Lakshadweep', 4),
(49, 'Puducherry', 4),
(1, 'International', 11),
(4, 'Himachal Pradesh', 4),
(5, 'Amsterdam', 3),
(6, 'Kerala', 4),
(7, 'Andhra Pradesh', 4),
(8, 'Madhya Pradesh', 4),
(9, 'Jeddah', 9),
(10, 'Delhi', 4),
(11, 'Uttar Pradesh', 4),
(12, 'Maharashtra', 4),
(13, 'Tamil Nadu', 4),
(14, 'Kerala', 4),
(15, 'Rajasthan', 4),
(16, 'New Jersey', 3),
(17, 'Atlanta', 3),
(18, 'Alexandria', 3),
(19, 'None of these', 10),
(20, 'Karnataka', 4),
(21, 'Dubai', 12),
(22, 'West Bengal', 4),
(23, 'New South Wales', 13),
(24, 'London', 14),
(25, 'Arunachal Pradesh', 4),
(26, 'Assam', 4),
(27, 'Bihar', 4),
(28, 'Chhattisgarh', 4),
(29, 'Goa', 4),
(30, 'Gujarat', 4),
(31, 'Haryana', 4),
(32, 'Jharkhand', 4),
(33, 'Manipur', 4),
(34, 'Meghalaya', 4),
(35, 'Mizoram', 4),
(36, 'Nagaland', 4),
(37, 'Odisha', 4),
(38, 'Punjab', 4),
(39, 'Sikkim', 4),
(40, 'Telangana', 4),
(41, 'Tripura', 4),
(42, 'Uttarakhand', 4),
(43, 'Andaman and Nicobar Islands', 4),
(44, 'Chandigarh', 4),
(45, 'Dadra and Nagar Haveli and Daman and Diu', 4),
(46, 'Jammu and Kashmir', 4),
(47, 'Ladakh', 4),
(48, 'Lakshadweep', 4),
(49, 'Puducherry', 4);

-- --------------------------------------------------------

--
-- Table structure for table `workamount`
--

CREATE TABLE `workamount` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `invoiceNumber` varchar(50) NOT NULL,
  `taxinvoiceNumber` varchar(50) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `duration` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `workamount`
--

INSERT INTO `workamount` (`id`, `invoice_id`, `invoiceNumber`, `taxinvoiceNumber`, `description`, `amount`, `duration`) VALUES
(1, 1, 'NISPL/26-27/001', NULL, 'TBM website AMC', 500000, '30-05-2026 to 31-06-20226'),
(2, 2, 'NISPL/26-27/002', NULL, 'face 1', 2000, ''),
(1, 1, 'NISPL/26-27/001', NULL, 'TBM website AMC', 500000, '30-05-2026 to 31-06-20226'),
(2, 2, 'NISPL/26-27/002', NULL, 'face 1', 2000, ''),
(1, 1, 'NISPL/26-27/001', NULL, 'TBM website AMC', 500000, '30-05-2026 to 31-06-20226'),
(2, 2, 'NISPL/26-27/002', NULL, 'face 1', 2000, ''),
(1, 1, 'NISPL/26-27/001', NULL, 'TBM website AMC', 500000, '30-05-2026 to 31-06-20226'),
(2, 2, 'NISPL/26-27/002', NULL, 'face 1', 2000, '');

-- --------------------------------------------------------

--
-- Table structure for table `work_type_master`
--

CREATE TABLE `work_type_master` (
  `WorkTypeID` int(11) NOT NULL,
  `WorkTypeName` varchar(100) NOT NULL,
  `amc_type` enum('project','amc_only') NOT NULL DEFAULT 'project',
  `DelStatus` tinyint(4) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `work_type_master`
--

INSERT INTO `work_type_master` (`WorkTypeID`, `WorkTypeName`, `amc_type`, `DelStatus`, `sort_order`, `created_at`) VALUES
(1, 'Mobile App', 'project', 1, 0, '2026-05-29 12:42:27'),
(2, 'Web Application', 'project', 1, 1, '2026-05-29 12:42:27'),
(3, 'Website', 'project', 1, 2, '2026-05-29 12:42:27'),
(4, 'Digital Marketing', 'project', 1, 3, '2026-05-29 12:42:27'),
(5, 'ERP / Software', 'project', 1, 4, '2026-05-29 12:42:27'),
(6, 'AMC Support', 'amc_only', 1, 5, '2026-05-29 12:42:27'),
(1, 'Mobile App', 'project', 1, 0, '2026-05-29 12:42:27'),
(2, 'Web Application', 'project', 1, 1, '2026-05-29 12:42:27'),
(3, 'Website', 'project', 1, 2, '2026-05-29 12:42:27'),
(4, 'Digital Marketing', 'project', 1, 3, '2026-05-29 12:42:27'),
(5, 'ERP / Software', 'project', 1, 4, '2026-05-29 12:42:27'),
(6, 'AMC Support', 'amc_only', 1, 5, '2026-05-29 12:42:27'),
(1, 'Mobile App', 'project', 1, 0, '2026-05-29 12:42:27'),
(2, 'Web Application', 'project', 1, 1, '2026-05-29 12:42:27'),
(3, 'Website', 'project', 1, 2, '2026-05-29 12:42:27'),
(4, 'Digital Marketing', 'project', 1, 3, '2026-05-29 12:42:27'),
(5, 'ERP / Software', 'project', 1, 4, '2026-05-29 12:42:27'),
(6, 'AMC Support', 'amc_only', 1, 5, '2026-05-29 12:42:27'),
(1, 'Mobile App', 'project', 1, 0, '2026-05-29 12:42:27'),
(2, 'Web Application', 'project', 1, 1, '2026-05-29 12:42:27'),
(3, 'Website', 'project', 1, 2, '2026-05-29 12:42:27'),
(4, 'Digital Marketing', 'project', 1, 3, '2026-05-29 12:42:27'),
(5, 'ERP / Software', 'project', 1, 4, '2026-05-29 12:42:27'),
(6, 'AMC Support', 'amc_only', 1, 5, '2026-05-29 12:42:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `citymaster`
--
ALTER TABLE `citymaster`
  ADD PRIMARY KEY (`CityId`);

--
-- Indexes for table `clientmaster`
--
ALTER TABLE `clientmaster`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexes for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_projects`
--
ALTER TABLE `client_projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_client_projects_client` (`client_id`);

--
-- Indexes for table `companymaster`
--
ALTER TABLE `companymaster`
  ADD PRIMARY KEY (`CompanyID`),
  ADD UNIQUE KEY `uk_company_name` (`CompanyName`);

--
-- Indexes for table `countrymaster`
--
ALTER TABLE `countrymaster`
  ADD PRIMARY KEY (`CountryID`);

--
-- Indexes for table `designationmaster`
--
ALTER TABLE `designationmaster`
  ADD PRIMARY KEY (`DesignationID`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoiceemployees`
--
ALTER TABLE `invoiceemployees`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companymaster`
--
ALTER TABLE `companymaster`
  MODIFY `CompanyID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

