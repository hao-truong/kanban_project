CREATE TABLE `boards`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `title`      varchar(255) NOT NULL,
    `created_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `updated_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `creator_id` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY          `new_boards_ibfk_1_idx` (`creator_id`),
    CONSTRAINT `new_boards_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
