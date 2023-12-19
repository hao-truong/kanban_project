CREATE TABLE `columns`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `board_id`   int          DEFAULT NULL,
    `title`      varchar(255) DEFAULT NULL,
    `creator_id` int          DEFAULT NULL,
    `created_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `updated_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `position`   int NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY          `creator_id` (`creator_id`),
    KEY          `columns_ibfk_1` (`board_id`),
    CONSTRAINT `columns_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `columns_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
