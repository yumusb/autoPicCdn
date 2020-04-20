-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2020-04-20 22:53:19
-- 服务器版本： 5.6.37-log
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pic`
--

-- --------------------------------------------------------

--
-- 表的结构 `remote_imgs`
--

CREATE TABLE IF NOT EXISTS `remote_imgs` (
  `imgmd5` varchar(32) NOT NULL COMMENT '文件md5',
  `imguploadtime` int(10) NOT NULL COMMENT '上传时间，10位时间戳',
  `imguploadip` varchar(20) NOT NULL COMMENT '上传IP',
  `imgurl` varchar(200) NOT NULL COMMENT '远程访问URL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图片统计表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `remote_imgs`
--
ALTER TABLE `remote_imgs`
  ADD PRIMARY KEY (`imgmd5`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
