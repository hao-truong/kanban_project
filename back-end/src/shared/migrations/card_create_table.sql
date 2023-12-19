CREATE TABLE `cards`
(
    `id`            int          NOT NULL AUTO_INCREMENT,
    `column_id`     int          DEFAULT NULL,
    `title`         varchar(255) NOT NULL,
    `description`   varchar(255) DEFAULT '',
    `created_at`    datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `updated_at`    datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `assigned_user` int          DEFAULT NULL,
    `position`      int          NOT NULL,
    PRIMARY KEY (`id`),
    KEY             `cards_ibfk_1_idx` (`column_id`),
    KEY             `cards_ibfk_2` (`assigned_user`),
    CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`column_id`) REFERENCES `columns` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cards_ibfk_2` FOREIGN KEY (`assigned_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
