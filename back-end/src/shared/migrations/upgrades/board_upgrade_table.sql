SET
foreign_key_checks = 0;

ALTER TABLE `kanban_probation`.`boards` DROP FOREIGN KEY `new_boards_ibfk_1`;

CREATE TABLE `kanban_probation`.`upgrade_boards`
(
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `updated_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP (3),
    `creator_id` INT          DEFAULT NULL,
    `new_column` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY          `new_boards_ibfk_1` (`creator_id`),
    CONSTRAINT `new_boards_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `kanban_probation`.`upgrade_boards` (id, title, created_at, updated_at, creator_id, new_column)
SELECT id, title, created_at, updated_at, creator_id, NULL as new_column
FROM `kanban_probation`.`boards`;

DROP TABLE `kanban_probation`.`boards`;

RENAME
TABLE `kanban_probation`.`upgrade_boards` TO `kanban_probation`.`boards`;

SET
foreign_key_checks = 1;
