SET
foreign_key_checks = 0;

ALTER TABLE `kanban_probation`.`user_board` DROP FOREIGN KEY `user_board_ibfk_1`;
ALTER TABLE `kanban_probation`.`user_board` DROP FOREIGN KEY `user_board_ibfk_2`;

CREATE TABLE `kanban_probation`.`upgrade_user_board`
(
    `user_id`  int NOT NULL,
    `board_id` int NOT NULL,
    CONSTRAINT `user_board_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_board_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `kanban_probation`.`upgrade_user_board` (user_id, board_id)
SELECT user_id, board_id
FROM `kanban_probation`.`user_board`;

DROP TABLE `kanban_probation`.`user_board`;

RENAME
TABLE `kanban_probation`.`upgrade_user_board` TO `kanban_probation`.`user_board`;

SET
foreign_key_checks = 1;
