CREATE TABLE `user_board`
(
    `user_id`  int NOT NULL,
    `board_id` int NOT NULL,
    PRIMARY KEY (`user_id`, `board_id`),
    KEY        `user_board_ibfk_2_idx` (`board_id`),
    CONSTRAINT `user_board_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_user_idfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
