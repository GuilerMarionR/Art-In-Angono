-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 05:06 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `art_in_angono_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_name`, `email`, `password`) VALUES
(1, 'Blanco Family Museum', 'artinangono.blanco@gmail.com', 'blancoangono123'),
(2, 'Nemiranda Arthouse', 'artinangono.nemiranda@gmail.com', 'nemirandaangono123'),
(3, 'Nono Museum', 'artinangono.nono@gmail.com', 'nonoangono123'),
(4, 'Balagtas Gallerie', 'artinangono.balagtas@gmail.com', 'balagtasangono123'),
(5, 'Angkla Art Gallery', 'aa.gallery@gmail.com', 'angkla123'),
(6, 'Balaw-Balaw Gallerie', 'bb.gallerie@gmail.com', 'balawbalaw123'),
(7, 'Giant Dwarf Art Space', 'gda.space@gmail.com', 'giant123'),
(8, 'House of Botong Francisco', 'hb.francisco@gmail.com', 'botong123'),
(9, 'Angono Petroglyps', 'angono.petro@gmail.com', 'Petro123'),
(10, 'Kuta Artspace', 'kuta@gmail.com', 'kuta1234'),
(11, 'Galleria Perlita', 'g.perlita@gmail.com', 'perlita123');

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `artworkID` int(11) NOT NULL,
  `title` varchar(34) NOT NULL,
  `museumName` varchar(34) NOT NULL,
  `artistName` varchar(34) NOT NULL,
  `description` varchar(255) NOT NULL,
  `medium` varchar(34) NOT NULL,
  `dimension` varchar(20) NOT NULL,
  `email` varchar(34) NOT NULL,
  `websiteLink` varchar(50) DEFAULT NULL,
  `contactNumber` varchar(15) NOT NULL,
  `imagePath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artworkID`, `title`, `museumName`, `artistName`, `description`, `medium`, `dimension`, `email`, `websiteLink`, `contactNumber`, `imagePath`) VALUES
(7, 'Bunso', 'Balagtas Gallerie', 'Lito Balagtas', 'LOREMIPSOM SIT AMER DOLOR', 'LOREM IPSUM', '50&quot; x  60&quot;', 'marionregalado20@gmail.com', 'https://www.google.com/maps', '09982901878', '../uploads/Bunso(Lito Balagtas).jpeg'),
(9, 'Artist', 'Balagtas Gallerie', 'popopo', 'Hi', 'Medium Rare', '4\' x 4.5\"', 'marionregalado20@gmail.com', 'https://www.google.com/maps', 'wjwjwjw', '../uploads/Black Horizon.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `clientbookings`
--

CREATE TABLE `clientbookings` (
  `bookingID` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `middleName` varchar(100) DEFAULT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(34) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `contactNumber` varchar(15) NOT NULL,
  `numberOfGuests` int(11) NOT NULL,
  `appointmentDate` varchar(34) NOT NULL,
  `startTime` time(6) NOT NULL,
  `endTime` time(6) NOT NULL,
  `museumName` varchar(100) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `closed_dates`
--

CREATE TABLE `closed_dates` (
  `id` int(11) NOT NULL,
  `closed_date` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `museumName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `closed_times`
--

CREATE TABLE `closed_times` (
  `id` int(11) NOT NULL,
  `startTime` time(6) NOT NULL,
  `endTime` time(6) NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `museumName` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `collectionID` int(11) NOT NULL,
  `museumName` varchar(34) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `collections`
--

INSERT INTO `collections` (`collectionID`, `museumName`, `title`, `description`, `image_url`) VALUES
(1, 'Balagtas Gallerie', 'Fruit Vendor', 'Lorem ipsum', '../uploads/collections/watermarked_Fruit Vendor(Lito Balagtas).jpeg'),
(2, 'Balagtas Gallerie', 'Black Horizon', 'Lorem ipsum', '../uploads/collections/watermarked_Black Horizon.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `museumName` varchar(34) NOT NULL,
  `date` varchar(34) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `title`, `museumName`, `date`, `description`, `image`) VALUES
(1, 'Balagtas OpenHouse', 'Balagtas Gallerie', '2024-11-05', 'Come to our Open House', '../uploads/Bunso(Lito Balagtas).jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guestID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contactNumber` varchar(15) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guestID`, `firstName`, `lastName`, `middleName`, `address`, `birthDate`, `email`, `contactNumber`, `username`, `password`, `created_at`) VALUES
(1, 'Guiler Marion', 'Regalado', 'Ruffy', 'Blk. 5 Lt. 11 Calle Suave Villa Remedios Subdivision', '2002-04-20', 'marionregalado20@gmail.com', '09982901878', 'Marion100', 'Marion100', '2024-11-12 03:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `museums`
--

CREATE TABLE `museums` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `history` text NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `views` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `museums`
--

INSERT INTO `museums` (`id`, `name`, `address`, `image_url`, `history`, `description`, `views`) VALUES
(1, 'Blanco Family Museum', '312 Ibañez St. Angono, Rizal', 'https://i.imgur.com/VnfrLGM.jpg', 'History of Blanco Family Museum', 'This is the descriptions', 12),
(2, 'Nemiranda Arthouse', 'Doña Justa Street, Village 1, Angono, Rizal', 'https://i.imgur.com/MEanjPz.jpg', 'Some History', 'Some Description', 8),
(3, 'Balagtas Gallerie', 'J. Intalan, Angono, Rizal', 'https://i.imgur.com/3mwqNVK.jpg', 'Unknown to many, Bernardo “Bernie” Balagtas the owner was the author of “Resolusyon 03-169” or “Kapasiyang Hinihiling sa Pangulo ng Pilipinas Gloria Macapagal Arroyo na Maging Art Capital of the Philippines and Bayan ng Angono, Rizal sa Pamamagitan ng Isang Kautusang Tagapagpaganap”  which the Angono Municipal council adopted in 2003 and approved by then Mayor Gerry Calderon and Vice-Mayor Aurora Villamayor.  The Sculpture in front of the Municipality of Angono represents the Culture and Art of the Municipality. However, the sculpture is still incomplete. It initially symbolizes the “Musika ng Bayan”, particularly the visualization of the classical composition of “Sa Ugoy ng Duyan” by the Angono National Artist Prof. Lucio D. San Pedro, wherein it represents a man, a mother and child in a hammock. Aside from that, Angelito Balagtas also made the “Ang Nuno” placed in the Triangle in the highway of Angono, Rizal, the “Inang Kalayaan” in front of Angono Elementary School that gives recognition to the veterans of Angono during the World War II, “Bas Relief” the history of Barangay Kalayaan located in the Barangay Hall, “Bas Relief” located in the Gallery of Museum and Arts that is now the Tourism Office under the Angono Municipal Trial Court. In October 30, 2017, the “Higanteng Itik” was placed in Angono, Rizal in recognize to “Fried Itik” the municipality’s specialty.', 'Balagtas Gallerie was established in commemoration for the late Angelito Balagtas, owned by his son Bernardo “Bernie” Balagtas. It was opened on December 14, 2021 which dates the birthday of the late artist. The opening of Balagtas Gallerie garnered the artists of Angono Ateliers Association Philippines in their tribute for the late Angelito Balagtas. Balagtas Gallerie serves as the home office for the Angono Ateliers Association Philippines which caters artworks and exhibitions from various artists, developing and professional. \n\nAs a budding Artist in the 1990s, Bernie has consistently reaped awards from local and national art competitions. Among his awards include being 1st place for five consecutive years in Angono Ateliers Association painting contest from 1985-1989; 1st place for three consecutive years in Population Commision painting contest; winner in 1993 Metrobank and shell National Art Competitions; 2nd place in PLDT Cover Painting Contest in 1994 and National Children’s Medical Painting Competition in 1992; and as commissioned artist of Philippine Institute of Certified Public Accountants.\n', 30),
(4, 'Nono Museum', 'P. Roman St, Angono, Rizal', 'https://i.imgur.com/BskX7Vo.png', 'Some History', 'Some Descrption', 2),
(5, 'Angkla Art Gallery', '3/F, CPV Business Center, Manila East Road, corner Col. Guido, Angono, Rizal', '../Assets/Angkla.jpg', 'Lorem Ipsum Sit Amet', 'Lorem Ipsum Sit Amet', 0),
(6, 'Balaw-Balaw Art Gallerie', 'Don Justo Dona Justa Subdivision, Phase I Manila E Rd, Angono, Rizal', '../Assets/Balaw Balaw.jpg', 'Lorem Ipsum Sit AMet', 'Lorem Ipsum Sit Amet', 0),
(7, 'Giant Dwarf Art Space', ' Corner of Doña Aurora Street and Manila East Road, Hi-way, Brgy, Angono, Rizal', '../Assets/Giant Dwarf.jpg', 'Lorem Ipsum Sit Amet', 'Lorem Ipsum Sit Amet', 0),
(8, 'House of Botong Francisco', '217 Doña Aurora St, Angono, 1930 Rizal\r\n', '../Assets/Botong Museum.jpg', '', '', 0),
(9, 'Angono Petroglyphs', 'Angono-Binangonan, Rizal', '../Assets/Petroglyphs.jpg', 'Lorem Ipsum Sit Amet', 'Lorem Ipsum Sit AMet', 0),
(10, 'Kuta Artspace', 'Unit 4 picones, Col. Guido, Angono, Rizal', '../Assets/Kuta Artspace.jpg', 'Lorem Ipsum Sit Amet', 'Lorem Ipsum sit amet', 0),
(11, 'Galleria Perlita', '115-112 R-5, Angono, Rizal', 'https://i.imgur.com/BskX7Vo.png', 'Lorem Ipsum', 'Sit Amer', 0);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` int(11) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `ViewID` int(11) NOT NULL,
  `museumName` varchar(100) NOT NULL,
  `360_URL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`ViewID`, `museumName`, `360_URL`) VALUES
(1, 'Blanco Family Museum', 'https://my.matterport.com/show/?m=YUemUS3vsUi'),
(2, 'Nemiranda Arthouse', 'https://my.matterport.com/show/?m=6ARsg1Lqxsw'),
(3, 'Balagtas Gallerie', 'https://my.matterport.com/show/?m=jDn55aUEquo'),
(4, 'Nono Museum', 'https://my.matterport.com/show/?m=tDbvZ76XNTG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artworkID`);

--
-- Indexes for table `clientbookings`
--
ALTER TABLE `clientbookings`
  ADD PRIMARY KEY (`bookingID`);

--
-- Indexes for table `closed_dates`
--
ALTER TABLE `closed_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `closed_times`
--
ALTER TABLE `closed_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`collectionID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guestID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `museums`
--
ALTER TABLE `museums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`ViewID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artworkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `clientbookings`
--
ALTER TABLE `clientbookings`
  MODIFY `bookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `closed_dates`
--
ALTER TABLE `closed_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `closed_times`
--
ALTER TABLE `closed_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `collectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `museums`
--
ALTER TABLE `museums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `ViewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
