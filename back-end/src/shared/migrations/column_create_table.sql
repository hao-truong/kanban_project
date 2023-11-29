CREATE TABLE IF NOT EXISTS `kanban_probation`.`columns`
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    board_id   INT,
    title      VARCHAR(255),
    creator_id INT,
    created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    FOREIGN KEY (board_id) REFERENCES `kanban_probation`.`boards` (id),
    FOREIGN KEY (creator_id) REFERENCES `kanban_probation`.`users` (id)
);
