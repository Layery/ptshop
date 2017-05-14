-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 06 月 30 日 21:31
-- 服务器版本: 10.1.8-MariaDB
-- PHP 版本: 5.3.29-upupw

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `shuju`
--

-- --------------------------------------------------------

--
-- 表的结构 `go_zhuanpan`
--

CREATE TABLE IF NOT EXISTS `go_zhuanpan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) CHARACTER SET utf8 NOT NULL,
  `money` int(10) unsigned DEFAULT NULL,
  `jilv` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `go_zhuanpan`
--

INSERT INTO `go_zhuanpan` (`id`, `name`, `money`, `jilv`) VALUES
(1, '安慰奖', 1, 10),
(2, '六等奖', 2, 10),
(3, '五等奖', 3, 5),
(4, '四等奖', 4, 3),
(5, '三等奖', 6, 6),
(6, '二等奖', 8, 3),
(7, '一等奖', 10, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
