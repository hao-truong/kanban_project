CREATE TABLE IF NOT EXISTS `kanban_probation`.`cards`
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    column_id      INT,
    title          VARCHAR(255),
    description    VARCHAR(255),
    started_at     DATETIME(3) NOT NULL      DEFAULT CURRENT_TIMESTAMP(3),
    ended_at       DATETIME(3) NOT NULL      DEFAULT CURRENT_TIMESTAMP(3),
    status         ENUM ('0', '1', '2', '3') DEFAULT '0',
    participant_id INT,
    FOREIGN KEY (column_id) REFERENCES `kanban_probation`.`columns` (id),
    FOREIGN KEY (participant_id) REFERENCES `kanban_probation`.`users` (id)
);
