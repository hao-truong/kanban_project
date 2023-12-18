SET foreign_key_checks = 0;

CREATE TABLE `kanban_probation`.`upgrade_users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    `access_token` varchar(255) DEFAULT NULL,
    `refresh_token` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username_UNIQUE` (`username`)
    ) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `kanban_probation`.`upgrade_users` (id, username, password, alias, email, access_token, refresh_token)
SELECT id, username, password, alias, email, access_token, refresh_token
FROM `kanban_probation`.`users`;

DROP TABLE `kanban_probation`.`users`;

RENAME TABLE `kanban_probation`.`upgrade_users` TO `kanban_probation`.`users`;

SET foreign_key_checks = 1;
