SET
foreign_key_checks = 0;

ALTER TABLE `kanban_probation`.`cards` DROP FOREIGN KEY `cards_ibfk_1`;
ALTER TABLE `kanban_probation`.`cards` DROP FOREIGN KEY `cards_ibfk_2`;

CREATE TABLE `kanban_probation`.`upgrade_cards`
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
    KEY             `cards_ibfk_1` (`column_id`),
    KEY             `cards_ibfk_2` (`assigned_user`),
    CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`column_id`) REFERENCES `columns` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cards_ibfk_2` FOREIGN KEY (`assigned_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `kanban_probation`.`upgrade_cards` (id, column_id, title, description, created_at, updated_at,
                                                assigned_user, position)
SELECT id,
       column_id,
       title,
       description,
       created_at,
       updated_at,
       assigned_user,
       position
FROM `kanban_probation`.`cards`;

DROP TABLE `kanban_probation`.`cards`;

RENAME
TABLE `kanban_probation`.`upgrade_cards` TO `kanban_probation`.`cards`;

SET
foreign_key_checks = 1;
