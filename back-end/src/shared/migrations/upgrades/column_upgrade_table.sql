SET foreign_key_checks = 0;

ALTER TABLE `kanban_probation`.`columns` DROP FOREIGN KEY `new_columns_ibfk_1`;
ALTER TABLE `kanban_probation`.`columns` DROP FOREIGN KEY `new_columns_ibfk_2`;

CREATE TABLE `kanban_probation`.`upgrade_columns` (
    `id` int NOT NULL AUTO_INCREMENT,
    `board_id` int DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `creator_id` int DEFAULT NULL,
    `created_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `position` int NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `new_columns_ibfk_1` (`board_id`),
    KEY `new_columns_ibfk_2` (`creator_id`),
    CONSTRAINT `new_columns_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `new_columns_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `kanban_probation`.`upgrade_columns` (id, board_id, title, creator_id, created_at, updated_at, position)
SELECT id, board_id, title, creator_id, created_at, updated_at, position
FROM `kanban_probation`.`columns`;

DROP TABLE `kanban_probation`.`columns`;

RENAME TABLE `kanban_probation`.`upgrade_columns` TO `kanban_probation`.`columns`;

SET foreign_key_checks = 1;
