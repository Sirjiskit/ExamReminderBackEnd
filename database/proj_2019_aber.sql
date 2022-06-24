-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2022 at 05:09 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proj_2019_aber`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_courses`
--

CREATE TABLE `tb_courses` (
  `id` int(11) NOT NULL,
  `code` varchar(9) NOT NULL,
  `title` tinytext NOT NULL,
  `level` varchar(3) NOT NULL,
  `semester` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_courses`
--

INSERT INTO `tb_courses` (`id`, `code`, `title`, `level`, `semester`) VALUES
(1, 'CSC001', 'Introduction to Computational Thinking', '100', 1),
(2, 'CSC002', 'Introduction to Programming', '100', 1),
(3, 'CSC003', 'Practice and Experience Development Basics', '100', 1),
(4, 'CSC105', 'programming language valuation ', '200', 1),
(5, 'CSC105', 'programming language valuation ', '200', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_cou_registered`
--

CREATE TABLE `tb_cou_registered` (
  `id` int(11) NOT NULL,
  `cId` int(11) NOT NULL,
  `sId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_cou_registered`
--

INSERT INTO `tb_cou_registered` (`id`, `cId`, `sId`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_shedules`
--

CREATE TABLE `tb_shedules` (
  `id` int(11) NOT NULL,
  `cId` int(11) NOT NULL,
  `vId` int(11) NOT NULL,
  `date` date NOT NULL,
  `timeStart` time NOT NULL,
  `timeEnd` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_shedules`
--

INSERT INTO `tb_shedules` (`id`, `cId`, `vId`, `date`, `timeStart`, `timeEnd`) VALUES
(1, 1, 1, '2021-10-15', '23:00:00', '23:50:00'),
(2, 2, 4, '2021-10-14', '16:00:37', '18:00:46'),
(3, 3, 1, '2021-10-16', '13:00:00', '18:30:00'),
(4, 4, 5, '2021-10-17', '10:00:13', '12:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `tb_shedules_supervisors`
--

CREATE TABLE `tb_shedules_supervisors` (
  `sId` int(11) NOT NULL,
  `supId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_shedules_supervisors`
--

INSERT INTO `tb_shedules_supervisors` (`sId`, `supId`) VALUES
(2, 5),
(2, 4),
(2, 2),
(1, 8),
(1, 1),
(1, 10),
(3, 8),
(4, 1),
(4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tb_students`
--

CREATE TABLE `tb_students` (
  `id` int(11) NOT NULL,
  `regNo` varchar(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(50) NOT NULL DEFAULT 'password'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_students`
--

INSERT INTO `tb_students` (`id`, `regNo`, `fullname`, `email`, `phone`, `image`, `dateAdded`, `password`) VALUES
(1, 'PAS/CSC/18/001', 'Igbashio Kalifort Kashimana', 'kalifort@gmail.com', '08070172002', '20a1e8931c3c26f2815e8dfc37e597b3.jpeg', '2021-10-11 18:25:10', 'password'),
(2, 'PAS/CSC/18/040', 'Tanimu Blessing Ajo', 'blessingajo20@gmail.com', '07082254162', '', '2021-10-12 07:19:45', 'password'),
(3, 'PAS/CSC/18/002', 'Divine Mark', 'divinemark@gmail.com', '09067546453', '', '2021-10-12 07:58:50', 'password'),
(4, 'PAS/CSC/18/003', 'John Heleseen', 'johnhelenseen@gmail.com', '09067546452', '', '2021-10-12 08:06:10', 'password'),
(5, 'PAS/CSC/18/023', 'Daivid Godiya', 'godiyadavivid@gmail.com', '09067546432', '', '2021-10-12 08:06:10', 'password'),
(6, 'PAS/CSC/18/014', 'Halimat Usman', 'halimatusman@gmail.com', '08027546432', '', '2021-10-12 08:06:10', 'password'),
(7, 'PAS/CSC/18/011', 'Mohammad Isa Alaji', 'isamohammad@gmail.com', '08082546432', '', '2021-10-12 08:06:10', 'password'),
(8, 'PAS/CSC/18/008', 'Abdulrasheed Wazari Bawa', 'abwaziri@gmail.com', '08078546432', '', '2021-10-12 08:06:10', 'password'),
(9, 'PAS/CSC/18/005', 'Suleiman Ibrahim', 'suleiman@gmail.com', '08037546432', '', '2021-10-12 08:06:10', 'password'),
(10, 'PAS/CSC/18/004', 'Rimamtse Ade', 'rimamade@gmail.com', '08020546432', '', '2021-10-12 08:06:10', 'password'),
(11, 'PAS/CSC/18/014', 'Ngode Haruna', 'ngodeharuna@gmail.com', '08087546442', '', '2021-10-12 08:06:10', 'password'),
(12, 'PAS/CSC/18/014', 'Terfa Oryima', 'terfaoryima@gmail.com', '08066546432', '', '2021-10-12 08:06:11', 'password'),
(13, 'PAS/CSC/19/042', 'Igbashio Julius Igbashio', 'jigbashio@gmail.com', '07018277223', '1634297904.png', '2021-10-15 11:38:26', '92CKV7LW');

-- --------------------------------------------------------

--
-- Table structure for table `tb_supervisors`
--

CREATE TABLE `tb_supervisors` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(50) NOT NULL DEFAULT 'password'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_supervisors`
--

INSERT INTO `tb_supervisors` (`id`, `fullname`, `email`, `phone`, `image`, `dateAdded`, `password`) VALUES
(1, 'Igbashio Kalifort Kashimana', 'kalifort@gmail.com', '08070172002', '20a1e8931c3c26f2815e8dfc37e597b3.jpeg', '2021-10-11 18:25:10', 'password'),
(2, 'Tanimu Blessing Ajo', 'blessingajo20@gmail.com', '07082254162', '', '2021-10-12 07:19:45', 'password'),
(3, 'Divine Mark', 'divinemark@gmail.com', '09067546453', '', '2021-10-12 07:58:50', 'password'),
(4, 'John Heleseen', 'johnhelenseen@gmail.com', '09067546452', '', '2021-10-12 08:06:10', 'password'),
(5, 'Daivid Godiya', 'godiyadavivid@gmail.com', '09067546432', '', '2021-10-12 08:06:10', 'password'),
(7, 'Mohammad Isa Alaji', 'isamohammad@gmail.com', '08082546432', '', '2021-10-12 08:06:10', 'password'),
(8, 'Abdulrasheed Wazari Bawa', 'abwaziri@gmail.com', '08078546432', '', '2021-10-12 08:06:10', 'password'),
(9, 'Suleiman Ibrahim', 'suleiman@gmail.com', '08037546432', '', '2021-10-12 08:06:10', 'password'),
(10, 'Rimamtse Ade', 'rimamade@gmail.com', '08020546432', '', '2021-10-12 08:06:10', 'password'),
(11, 'Ngode Haruna', 'ngodeharuna@gmail.com', '08087546442', '', '2021-10-12 08:06:10', 'password'),
(12, 'Terfa Oryima', 'terfaoryima@gmail.com', '08066546432', '', '2021-10-12 08:06:11', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `password` varchar(64) NOT NULL,
  `role` enum('1','2') NOT NULL,
  `image` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `email`, `name`, `phone`, `password`, `role`, `image`) VALUES
(1, 'admin@admin.com', 'Tanimu Blessing Ajo', '07082254162', 'admin', '1', ''),
(2, 'info@dootech.com.ng', 'Igbashio Julius', '07018277223', 'admin', '1', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_venues`
--

CREATE TABLE `tb_venues` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_venues`
--

INSERT INTO `tb_venues` (`id`, `name`) VALUES
(1, 'LECTURE THEATRE 2'),
(2, 'CRT 3'),
(3, 'CRT 2'),
(4, 'TWIN THEATRE HALL'),
(5, 'AMINO KANO HALL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_courses`
--
ALTER TABLE `tb_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_cou_registered`
--
ALTER TABLE `tb_cou_registered`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_shedules`
--
ALTER TABLE `tb_shedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_students`
--
ALTER TABLE `tb_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_supervisors`
--
ALTER TABLE `tb_supervisors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_venues`
--
ALTER TABLE `tb_venues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_courses`
--
ALTER TABLE `tb_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_cou_registered`
--
ALTER TABLE `tb_cou_registered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_shedules`
--
ALTER TABLE `tb_shedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_students`
--
ALTER TABLE `tb_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_supervisors`
--
ALTER TABLE `tb_supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_venues`
--
ALTER TABLE `tb_venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
