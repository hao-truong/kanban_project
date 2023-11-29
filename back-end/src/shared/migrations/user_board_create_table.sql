CREATE TABLE IF NOT EXISTS `kanban_probation`.`user_board`
(
    user_id  INT,
    board_id INT,
    PRIMARY KEY (user_id, board_id),
    FOREIGN KEY (user_id) REFERENCES `kanban_probation`.`users` (id),
    FOREIGN KEY (board_id) REFERENCES `kanban_probation`.`boards` (id)
);
