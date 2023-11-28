CREATE TABLE IF NOT EXISTS `kanban_probation`.`users`
(
    `id`       INT           NOT NULL AUTO_INCREMENT,
    `username` NVARCHAR(255) NOT NULL,
    `password` NVARCHAR(255) NOT NULL,
    `alias`    NVARCHAR(255) NOT NULL,
    `email`    NVARCHAR(255) NULL,
    `access_token` varchar(255) DEFAULT NULL,
    `refresh_token` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username_UNIQUE` (`username`)
);
