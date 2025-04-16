-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 02:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `tasker_id` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid','refunded') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`bid_id`, `task_id`, `tasker_id`, `amount`, `message`, `status`, `created_at`, `payment_status`) VALUES
(1, 1, '1274659752', 1.00, 'i am very good in my work', '', '2025-04-14 04:13:16', 'pending'),
(2, 5, '1274659752', 111.00, 'i am a carpenter', 'accepted', '2025-04-14 05:06:31', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` varchar(20) NOT NULL,
  `outgoing_msg_id` varchar(20) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`, `created_at`) VALUES
(1, '1274659752', '665058838', 'hi', '2025-04-14 04:19:27'),
(2, '1274659752', '665058838', 'hiiiiii', '2025-04-14 05:13:12'),
(3, '665058838', '1274659752', 'hi', '2025-04-14 05:24:44');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `tasker_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taskers`
--

CREATE TABLE `taskers` (
  `tasker_id` varchar(20) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `skills` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `completed_tasks` int(11) DEFAULT 0,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskers`
--

INSERT INTO `taskers` (`tasker_id`, `profession`, `bio`, `skills`, `location`, `hourly_rate`, `rating`, `completed_tasks`, `verification_status`, `created_at`) VALUES
('1274659752', 'Carpenter', '', '', '', 0.00, 0.00, 0, 'pending', '2025-04-14 05:46:09'),
('1744633590', 'Electrician', 'electrician', 'wires', 'panvel', 100.00, 0.00, 0, 'pending', '2025-04-14 12:26:30'),
('2', 'Carpenter', 'i am a carpenter', 'assembly', 'panvel', 100.00, 0.00, 0, 'pending', '2025-04-14 04:07:38'),
('2147483647', 'Plumber', 'i am a plumber', 'pipe fitting ', 'panvel', 100.00, 0.00, 0, 'pending', '2025-04-14 12:19:49');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `profession` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `status` enum('open','in_progress','completed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `selected_tasker_id` varchar(20) DEFAULT NULL,
  `chat_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `completion_date` timestamp NULL DEFAULT NULL,
  `bid_deadline` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `user_id`, `title`, `description`, `profession`, `location`, `budget`, `status`, `created_at`, `selected_tasker_id`, `chat_enabled`, `completion_date`, `bid_deadline`) VALUES
(1, '665058838', 'aaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Carpenter', 'aa', 1.00, 'completed', '2025-04-14 03:22:15', NULL, 0, '2025-04-14 04:19:33', NULL),
(5, '665058838', 'aaaaaaaaaaaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Carpenter', 'aaaaa', 1111.00, 'completed', '2025-04-14 04:58:02', NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Offline now',
  `user_type` enum('user','tasker') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profession` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `unique_id`, `fname`, `lname`, `email`, `password`, `img`, `status`, `user_type`, `created_at`, `profession`) VALUES
(1, '665058838', 'nandu', 'suraj', 'nandu@gmail.com', '87fb922bc0c963d8d35f2ada99c00b84', '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAYGBgYHBgcICAcKCwoLCg8ODAwODxYQERAREBYiFRkVFRkVIh4kHhweJB42KiYmKjY+NDI0PkxERExfWl98fKcBBgYGBgcGBwgIBwoLCgsKDw4MDA4PFhAREBEQFiIVGRUVGRUiHiQeHB4kHjYqJiYqNj40MjQ+TERETF9aX3x8p//CABEIBAAEAAMBIgACEQEDEQH/xAAyAAEAAgMBAQAAAAAAAAA', 'Active now', 'user', '2025-04-13 17:48:29', NULL),
(2, '1274659752', 'nnnnnnnnn', 'nnnnnnnnn', 'nnnnnnnnnn@gmail.com', '87fb922bc0c963d8d35f2ada99c00b84', '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAYGBgYHBgcICAcKCwoLCg8ODAwODxYQERAREBYiFRkVFRkVIh4kHhweJB42KiYmKjY+NDI0PkxERExfWl98fKcBBgYGBgcGBwgIBwoLCgsKDw4MDA4PFhAREBEQFiIVGRUVGRUiHiQeHB4kHjYqJiYqNj40MjQ+TERETF9aX3x8p//CABEIBgAEAAMBIgACEQEDEQH/xAAyAAEAAwEBAQEAAAAAAAA', 'Active now', 'tasker', '2025-04-14 04:07:38', 'carpenter'),
(3, '2147483647', 'plum', 'plum', 'plum@gmail.com', '87fb922bc0c963d8d35f2ada99c00b84', '', 'Active now', 'tasker', '2025-04-14 12:19:49', NULL),
(15, '1744633590', 'elec', 'elec', 'elec@gmail.com', '87fb922bc0c963d8d35f2ada99c00b84', '', 'Active now', 'tasker', '2025-04-14 12:26:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bid_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `tasker_id` (`tasker_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `incoming_msg_id` (`incoming_msg_id`),
  ADD KEY `outgoing_msg_id` (`outgoing_msg_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `unique_task_rating` (`task_id`,`user_id`),
  ADD KEY `ratings_ibfk_2` (`user_id`),
  ADD KEY `ratings_ibfk_3` (`tasker_id`);

--
-- Indexes for table `taskers`
--
ALTER TABLE `taskers`
  ADD PRIMARY KEY (`tasker_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profession` (`profession`),
  ADD KEY `selected_tasker_id` (`selected_tasker_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `bid_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`),
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`tasker_id`) REFERENCES `users` (`unique_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`selected_tasker_id`) REFERENCES `users` (`unique_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
