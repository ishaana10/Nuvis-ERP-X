CREATE DATABASE IF NOT EXISTS `nuvis`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'nuvis'@'127.0.0.1'
    IDENTIFIED WITH caching_sha2_password BY 'nuvis';

CREATE USER IF NOT EXISTS 'nuvis'@'localhost'
    IDENTIFIED WITH caching_sha2_password BY 'nuvis';

GRANT ALL PRIVILEGES ON `nuvis`.* TO 'nuvis'@'127.0.0.1';
GRANT ALL PRIVILEGES ON `nuvis`.* TO 'nuvis'@'localhost';

FLUSH PRIVILEGES;
