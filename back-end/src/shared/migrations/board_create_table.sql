CREATE TABLE IF NOT EXISTS `kanban_probation`.`boards`
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    title      VARCHAR(255) NOT NULL,
    created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    creator_id INT,
    FOREIGN KEY (creator_id) REFERENCES `kanban_probation`.`users` (id)
);
