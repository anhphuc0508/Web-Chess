CREATE DATABASE IF NOT EXISTS `chess_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chess_db`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `nickname` VARCHAR(50) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT 'assets/img/default-avatar.png',
    `elo` INT DEFAULT 400
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `matches` (
    `id` VARCHAR(50) PRIMARY KEY,
    `white_id` INT NOT NULL,
    `black_id` INT NOT NULL,
    `current_fen` TEXT,
    `move_history` TEXT,
    `status` ENUM('playing', 'finished', 'aborted') DEFAULT 'playing',
    FOREIGN KEY (`white_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`black_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `match_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `opponent_name` VARCHAR(50) NOT NULL,
    `game_mode` VARCHAR(50) NOT NULL,
    `result` ENUM('win', 'lose', 'draw') NOT NULL,
    `total_moves` INT NOT NULL,
    `played_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `friend_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `status` ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    UNIQUE KEY `unique_request` (`sender_id`, `receiver_id`),
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
