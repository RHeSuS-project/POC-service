-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 07, 2014 at 10:17 PM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hrmservice`
--
CREATE DATABASE IF NOT EXISTS `hrmservice` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `hrmservice`;

-- --------------------------------------------------------

--
-- Table structure for table `charasteristic`
--

CREATE TABLE IF NOT EXISTS `charasteristic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` int(11) NOT NULL,
  `charasteristicUuid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service` (`service`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `descriptor`
--

CREATE TABLE IF NOT EXISTS `descriptor` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `charasteristic` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `charasteristic` (`charasteristic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE IF NOT EXISTS `device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device` int(11) NOT NULL,
  `serviceUuid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device` (`device`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_data`
--

CREATE TABLE IF NOT EXISTS `subscription_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `charasteristic` int(11) NOT NULL,
  `value` double NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `charasteristic` (`charasteristic`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4869 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authkey` varchar(255) DEFAULT NULL,
  `accessToken` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `charasteristic`
--
ALTER TABLE `charasteristic`
  ADD CONSTRAINT `charasteristic_ibfk_1` FOREIGN KEY (`service`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `descriptor`
--
ALTER TABLE `descriptor`
  ADD CONSTRAINT `descriptor_ibfk_1` FOREIGN KEY (`charasteristic`) REFERENCES `charasteristic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`device`) REFERENCES `device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscription_data`
--
ALTER TABLE `subscription_data`
  ADD CONSTRAINT `subscription_data_ibfk_1` FOREIGN KEY (`charasteristic`) REFERENCES `charasteristic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
