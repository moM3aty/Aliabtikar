-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 06, 2026 at 07:03 AM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u834188565_ibtikar`
--

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT '',
  `type` varchar(20) DEFAULT 'image',
  `src` text NOT NULL,
  `size` varchar(20) DEFAULT 'normal',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `label`, `type`, `src`, `size`, `sort_order`) VALUES
(1, 'دقة التنفيذ في ورشتنا', 'فيديو العمل', 'video', 'images/work-process.mp4', 'wide', 0),
(2, 'تفصيل دواليب مبتكرة', '', 'image', 'images/img1.jpeg', 'tall', 1),
(3, 'تركيب غرف نوم ايكيا', '', 'image', 'images/img10.jpeg', 'normal', 2),
(4, 'لمساتنا الأخيرة', 'لمساتنا الأخيرة', 'video', 'images/finishing.mp4', 'normal', 3),
(5, 'صيانة وترميم الأثاث', '', 'image', 'images/img3.jpeg', 'normal', 4),
(6, 'تركيب باب سحاب خشب فاخر', 'فيديو التنفيذ', 'video', 'images/sliding-door-work.mp4', 'tall', 5),
(7, 'مطابخ عصرية بنظام استغلال المساحات', 'تصميم مطابخ', 'image', 'images/img11.jpeg', 'wide', 6),
(8, 'أبواب خشب سويدي وزان فاخر', 'أبواب داخلية', 'image', 'images/img12.jpeg', 'tall', 7),
(9, 'تسريحات مودرن بإضاءة LED', 'ركن الأناقة', 'image', 'images/img13.jpeg', 'normal', 8),
(10, 'غرف أطفال مبهجة وآمنة', 'عالم الأطفال', 'image', 'images/img14.jpeg', 'normal', 9),
(11, 'خزائن حائط بتصميمات إيطالية', 'دواليب ملابس', 'image', 'images/img15.jpeg', 'tall', 10),
(12, 'دهانات حرارية ومقاومة للرطوبة', 'جودة التشطيب', 'image', 'images/img17.jpeg', 'wide', 11),
(13, 'غرف نوم رئيسية ملكية', '', 'image', 'images/img16.jpeg', 'normal', 12);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `date` date NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `phone`, `text`, `date`, `is_read`) VALUES
(1, 'أحمد الشمري', '0551234567', 'أرغب في تفصيل دولاب ملابس بغرفة رئيسية، كم التكلفة التقريبية؟', '2024-06-12', 0),
(2, 'سارة العتيبي', '0509876543', 'هل توفرون خدمة تركيب غرف نوم ايكيا يوم الجمعة؟', '2024-06-13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT 'fas fa-tools',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `icon`, `title`, `description`, `sort_order`) VALUES
(1, 'fas fa-bed', 'صيانة غرف نوم', 'إصلاح شامل لغرف النوم، تبديل المفصلات التالفة، ومعالجة ترهل الدواليب.', 0),
(2, 'fas fa-columns', 'تفصيل دواليب وخزائن', 'تصميم وتفصيل دواليب الملابس وخزائن الحائط باستغلال ذكي للمساحات.', 1),
(3, 'fas fa-door-closed', 'تفصيل أبواب سحاب', 'صناعة أبواب خشبية سحاب (Sliding) مودرن، مثالية لتوفير المساحة والأناقة.', 2),
(4, 'fas fa-door-open', 'تفصيل أبواب خشب', 'تصنيع أبواب المداخل والغرف من أجود أخشاب الزان والسويدي الطبيعي.', 3),
(5, 'fas fa-tools', 'صيانة أبواب خشب', 'معالجة هبوط الأبواب، صنفرة وتجديد الدهان، وتركيب المقابض الحديثة.', 4),
(6, 'fas fa-utensils', 'صيانة مطابخ', 'تجديد دواليب المطبخ، تغيير الرخام، وإصلاح الأدراج والمفصلات الصدئة.', 5),
(7, 'fas fa-couch', 'تركيب ايكيا', 'فنيون محترفون في تركيب كافة قطع أثاث ايكيا وجميع الماركات العالمية.', 6),
(8, 'fas fa-window-maximize', 'تركيب ستائر وبراويز', 'تركيب جميع أنواع الستائر واللوحات الجدارية بدقة متناهية وتوازن تام.', 7),
(9, 'fas fa-key', 'تركيب أقفال وكوالين', 'تغيير وتركيب الأقفال (الكوالين) للأبواب الخشبية لضمان أعلى مستويات الأمان.', 8);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'الابتكار',
  `tagline` varchar(255) DEFAULT 'لأعمال النجارة والديكور الحديث',
  `phone` varchar(50) DEFAULT '0540794678',
  `whatsapp` varchar(50) DEFAULT '966540794678',
  `email` varchar(255) DEFAULT 'info@alibtikar.com',
  `address` text DEFAULT NULL,
  `hours` varchar(255) DEFAULT 'متاحون لخدمتكم 24 ساعة'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `tagline`, `phone`, `whatsapp`, `email`, `address`, `hours`) VALUES
(1, 'الابتكار', 'لأعمال النجارة والديكور الحديث', '0540794678', '966540794678', 'info@alibtikar.com', 'الرياض - المملكة العربية السعودية', 'متاحون لخدمتكم 24 ساعة');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `value` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `label`, `value`, `sort_order`) VALUES
(1, 'سنة خبرة', 15, 0),
(2, 'مشروع منجز', 1200, 1),
(3, 'عميل سعيد', 950, 2);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `stars` int(11) DEFAULT 5,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `text`, `stars`, `sort_order`) VALUES
(1, 'أبو فهد (الرياض)', 'بصراحة نجارين محترفين جداً، ركبوا لي غرفة النوم في وقت قياسي وبدقة متناهية. أنصح بهم بشدة.', 5, 0),
(2, 'م. عبدالله', 'فصلت عندهم دولاب ملابس، التصميم كان عبقري واستغلوا المساحة بشكل ممتاز والشغل نظيف.', 5, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
