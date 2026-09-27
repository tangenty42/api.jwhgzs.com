-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 1Panel-mysql
-- Generation Time: Sep 27, 2026 at 03:33 PM
-- Server version: 8.4.8
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jwhgzs_com`
--

-- --------------------------------------------------------

--
-- Table structure for table `action`
--

CREATE TABLE `action` (
  `id` bigint NOT NULL,
  `parent` text NOT NULL,
  `type` int NOT NULL,
  `value` int NOT NULL DEFAULT '0',
  `pid` bigint NOT NULL,
  `uid` bigint NOT NULL,
  `actionTime` bigint NOT NULL,
  `userIP` text NOT NULL,
  `userUA` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `id` bigint NOT NULL,
  `type` int NOT NULL,
  `classify` int DEFAULT NULL,
  `pid` bigint DEFAULT NULL,
  `uid` bigint NOT NULL,
  `title` text,
  `content` text NOT NULL,
  `coverImg` text,
  `postTime` bigint NOT NULL,
  `postIP` text NOT NULL,
  `postUA` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phoneverify`
--

CREATE TABLE `phoneverify` (
  `id` bigint NOT NULL,
  `phone` bigint NOT NULL,
  `verifyCode` int NOT NULL,
  `sendTime` bigint NOT NULL,
  `userIP` text NOT NULL,
  `userUA` text NOT NULL,
  `used` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shorturl`
--

CREATE TABLE `shorturl` (
  `id` bigint NOT NULL,
  `tag` text NOT NULL,
  `url` text NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_20230813`
--

CREATE TABLE `tmp_20230813` (
  `id` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `uploadTime` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `pass` text NOT NULL,
  `phone` bigint NOT NULL,
  `signupTime` bigint NOT NULL,
  `signupIP` text NOT NULL,
  `signupUA` text NOT NULL,
  `userGroup` text,
  `userAuth` text,
  `avatarVersion` bigint NOT NULL DEFAULT '0',
  `avatarExt` varchar(10) NOT NULL DEFAULT 'jpg',
  `selfIntroduce` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usertoken`
--

CREATE TABLE `usertoken` (
  `id` bigint NOT NULL,
  `userId` int NOT NULL,
  `token` text NOT NULL,
  `loginTime` bigint NOT NULL,
  `loginIP` text NOT NULL,
  `loginUA` text NOT NULL,
  `onlineTime` bigint DEFAULT NULL,
  `onlineIP` text,
  `onlineUA` text,
  `rand` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xfcl`
--

CREATE TABLE `xfcl` (
  `id` bigint NOT NULL,
  `time` bigint NOT NULL,
  `score` text NOT NULL,
  `note` text,
  `forumTopicId` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xnzx_class_table`
--

CREATE TABLE `xnzx_class_table` (
  `id` int NOT NULL,
  `year` int NOT NULL,
  `class` int NOT NULL,
  `PA_slogan` text NOT NULL,
  `PA_photosName` text NOT NULL,
  `PA_photosVersion` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xnzx_student_pa`
--

CREATE TABLE `xnzx_student_pa` (
  `id` bigint NOT NULL,
  `pid` bigint NOT NULL,
  `type` int NOT NULL,
  `name` text NOT NULL,
  `detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xnzx_student_table`
--

CREATE TABLE `xnzx_student_table` (
  `id` int NOT NULL,
  `type` int NOT NULL,
  `year` int NOT NULL,
  `class` int NOT NULL DEFAULT '0',
  `sid` int NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `sex` int NOT NULL,
  `uid` bigint DEFAULT NULL,
  `disabled` int NOT NULL DEFAULT '0',
  `PA_photosName` text NOT NULL,
  `PA_photosVersion` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xnzx_weekly`
--

CREATE TABLE `xnzx_weekly` (
  `id` bigint NOT NULL,
  `year` int NOT NULL,
  `class` int NOT NULL,
  `term` text NOT NULL,
  `num` int NOT NULL,
  `note` text,
  `nameInvisible` int NOT NULL DEFAULT '0',
  `postTime` bigint NOT NULL,
  `forceFinished` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xnzx_weekly_article`
--

CREATE TABLE `xnzx_weekly_article` (
  `id` bigint NOT NULL,
  `parentId` bigint NOT NULL,
  `author` int NOT NULL,
  `typist` int NOT NULL,
  `title` text,
  `tiji` text,
  `content` text,
  `houji` text,
  `oriContent` text,
  `postUid` bigint DEFAULT NULL,
  `postTime` bigint DEFAULT NULL,
  `postIP` text,
  `postUA` text,
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `phoneverify`
--
ALTER TABLE `phoneverify`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `shorturl`
--
ALTER TABLE `shorturl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `tmp_20230813`
--
ALTER TABLE `tmp_20230813`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `usertoken`
--
ALTER TABLE `usertoken`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xfcl`
--
ALTER TABLE `xfcl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xnzx_class_table`
--
ALTER TABLE `xnzx_class_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xnzx_student_pa`
--
ALTER TABLE `xnzx_student_pa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xnzx_student_table`
--
ALTER TABLE `xnzx_student_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xnzx_weekly`
--
ALTER TABLE `xnzx_weekly`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `xnzx_weekly_article`
--
ALTER TABLE `xnzx_weekly_article`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tmp_20230813`
--
ALTER TABLE `tmp_20230813`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
