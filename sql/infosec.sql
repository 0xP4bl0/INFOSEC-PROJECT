SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infosec`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `teacher_id`, `student_id`) VALUES
(3, '', 6, 202409005),
(4, '', 6, 202301);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `user_id`, `department`, `created_at`) VALUES
(3, '202304007', 'DIT', '2026-01-03 16:53:26'),
(4, '0923', 'CAS', '2026-01-04 05:47:59');

-- --------------------------------------------------------

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `subject_id`, `enrolled_at`) VALUES
(12, '202409005', 5, '2026-01-04 00:14:59'),
(13, '202301', 5, '2026-01-04 00:17:52'),
(15, '2002', 5, '2026-01-05 02:34:12'),
(22, '2003', 5, '2026-01-05 03:27:05'),
(23, '2002', 6, '2026-01-05 04:09:52'),
(24, '202301', 6, '2026-01-05 04:09:54'),
(25, '9000', 6, '2026-01-05 04:55:59'),
(26, '2003', 6, '2026-01-05 05:08:32');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `status` enum('Released','Rejected','Pending') NOT NULL,
  `grade_status` enum('Passed','Failed') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `teacher_id`, `grade`, `status`, `grade_status`, `created_at`) VALUES
(1, '2002', 5, 6, 3.00, 'Released', 'Passed', '2026-01-05 02:56:59'),
(2, '2002', 5, 6, 2.25, 'Released', 'Passed', '2026-01-05 04:41:29'),
(3, '2003', 5, 6, 4.00, 'Released', 'Failed', '2026-01-05 04:47:13'),
(4, '2002', 6, 9, 2.50, 'Released', 'Passed', '2026-01-05 04:48:30'),
(5, '2003', 6, 9, 1.00, 'Released', 'Passed', '2026-01-05 05:10:14'),
(6, '9000', 6, 9, 4.00, 'Pending', 'Failed', '2026-01-05 05:18:12'),
(7, '202301', 6, 9, 1.25, 'Pending', 'Passed', '2026-01-05 05:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `pending_enrollments`
--

CREATE TABLE `pending_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_enrollments`
--

INSERT INTO `pending_enrollments` (`id`, `student_id`, `subject_id`, `teacher_id`, `request_date`, `status`) VALUES
(1, 2002, 5, 6, '2026-01-05 10:28:46', 'Approved'),
(3, 2002, 6, 6, '2026-01-05 11:57:01', 'Approved'),
(4, 202301, 6, 6, '2026-01-05 11:57:05', 'Approved'),
(5, 9000, 6, 9, '2026-01-05 12:55:39', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `student_status`
--

CREATE TABLE `student_status` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `status` enum('regular','irregular','transferee') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_status`
--

INSERT INTO `student_status` (`id`, `user_id`, `status`, `created_at`) VALUES
(1, '202409005', 'regular', '2026-01-03 14:10:05'),
(2, '202301', 'irregular', '2026-01-03 17:03:10'),
(3, '2001', 'regular', '2026-01-04 05:40:10'),
(4, '2002', 'irregular', '2026-01-05 02:27:26'),
(5, '2003', 'regular', '2026-01-05 02:27:58'),
(6, '9000', 'irregular', '2026-01-05 04:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `teacher_id`, `created_at`) VALUES
(5, 'ITEC 50', 'Test', 6, '2026-01-03 16:57:47'),
(6, 'ICSC90', 'Test2', 9, '2026-01-05 03:33:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `gender` varchar(255) NOT NULL,
  `role` enum('student','teacher','osas') DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `fullname`, `gender`, `role`, `password`, `status`) VALUES
(1, '200001', 'osas@testschool.com', 'ADMIN', '0', 'osas', '$2y$10$0OSphoca6ROFtdhdDhZEPuRzJqB2cdhc7GfAsQwnI8swiyVjXAld6', 'Active'),
(5, '202409005', 'test@gmail.com', 'Pablo Test', 'Male', 'student', '$2y$10$YhCvNx0Yi2SFyt5/rMwUZubl.GfUqEdv38nT59d.pECzOi./L9nWy', 'Active'),
(6, '202304007', 'Pabl@gmail.com', 'Pablo Test(Professor)', 'Male', 'teacher', '$2y$10$Q91Pz74GJeAXDL2.1MQBnexm8s3K.4hGSQrIiVojDWN6DlGPJfsgS', 'Active'),
(7, '202301', 'testawa@gmail.com', 'Test', 'Male', 'student', '$2y$10$Fem9NGZQDUqeQoiZ3Mzyp..kAoyU.hhqmIRi.qXlp1Se3k4KcS22i', 'Active'),
(8, '2001', 'tetstets@gmail.com', 'Pablo Test', 'Male', 'student', '$2y$10$cMLrEKqPUB7v3yjthIhASOzFntwwFBX.UW96a8wjxqH3K6Gkohj0C', 'Active'),
(9, '0923', 'teacher1@gmail.com', 'Teacher 1', 'Male', 'teacher', '$2y$10$MHF2QM7HeGXhnQJ11yzZ0OOfDHsUG6aTdn9glp1sAN5OD0KlycW3.', 'Active'),
(10, '2002', 'alex1291@gmail.com', 'kali', 'Female', 'student', '$2y$10$/ffFFqq9KPLch.D8flKqeO8Li6ZfMkVjd0MwL4kL9s.i6H7PfvfU6', 'Active'),
(11, '2003', 'alex1211@gmail.com', 'kali', 'Male', 'student', '$2y$10$VxLr1USG2dt7GetHFXwId.xF24JQcsxkFWh9w1ySYAW9rGwLv641K', 'Active'),
(12, '9000', 'test@pablo.com', 'kali', 'Male', 'student', '$2y$10$MJESTRTFlTooCK6.Z5cdDuJ0872oNnsCLPjNRzja9UQJ2NKNq2Vzu', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_department_user` (`user_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`user_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_enrollments`
--
ALTER TABLE `pending_enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_status`
--
ALTER TABLE `student_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pending_enrollments`
--
ALTER TABLE `pending_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_status`
--
ALTER TABLE `student_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `fk_department_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
