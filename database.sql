SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `feeds`
(
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `name`    varchar(100)  NOT NULL,
    `url`     varchar(1000) NOT NULL,
    `filters` text,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;