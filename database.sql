CREATE TABLE `feeds`
(
    `id`                int(11) NOT NULL AUTO_INCREMENT,
    `name`              varchar(100)  NOT NULL,
    `title`             varchar(200)  NOT NULL DEFAULT '',
    `url`               varchar(1000) NOT NULL,
    `filters`           text,
    `filterIsWhitelist` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;