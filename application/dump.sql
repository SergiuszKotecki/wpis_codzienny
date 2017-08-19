/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela mirko.threads
DROP TABLE IF EXISTS `threads`;
CREATE TABLE IF NOT EXISTS `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `hour_send` time NOT NULL,
  `flag_call_followers` tinyint(3) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_followers
DROP TABLE IF EXISTS `threads_followers`;
CREATE TABLE IF NOT EXISTS `threads_followers` (
  `thread_id` int(10) unsigned NOT NULL,
  `follower_name` varchar(50) NOT NULL,
  KEY `Indeks 1` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_followers_ban
DROP TABLE IF EXISTS `threads_followers_ban`;
CREATE TABLE IF NOT EXISTS `threads_followers_ban` (
  `thread_id` int(10) unsigned NOT NULL,
  `follower_name` varchar(50) NOT NULL,
  KEY `Indeks 1` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_followers_pluses
DROP TABLE IF EXISTS `threads_followers_pluses`;
CREATE TABLE IF NOT EXISTS `threads_followers_pluses` (
  `thread_id` int(10) unsigned NOT NULL,
  `follower_name` varchar(50) NOT NULL,
  KEY `Indeks 1` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_lists
DROP TABLE IF EXISTS `threads_lists`;
CREATE TABLE IF NOT EXISTS `threads_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `hash` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_moderators
DROP TABLE IF EXISTS `threads_moderators`;
CREATE TABLE IF NOT EXISTS `threads_moderators` (
  `thread_id` int(10) unsigned NOT NULL,
  `moderator_id` int(10) unsigned NOT NULL,
  KEY `Indeks 1` (`thread_id`,`moderator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_moderators_hash
DROP TABLE IF EXISTS `threads_moderators_hash`;
CREATE TABLE IF NOT EXISTS `threads_moderators_hash` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `moderator_name` varchar(50) NOT NULL,
  `moderator_hash` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_rows
DROP TABLE IF EXISTS `threads_rows`;
CREATE TABLE IF NOT EXISTS `threads_rows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `thread_row_id` int(10) unsigned DEFAULT NULL,
  `body_text` text NOT NULL,
  `body_embedded` varchar(255) DEFAULT NULL,
  `body_embedded_file` varchar(75) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`thread_id`,`author_id`,`thread_row_id`),
  KEY `Indeks 3` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_rows_comments
DROP TABLE IF EXISTS `threads_rows_comments`;
CREATE TABLE IF NOT EXISTS `threads_rows_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_row_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `thread_row_comment_id` int(10) unsigned DEFAULT NULL,
  `body_text` text NOT NULL,
  `body_embedded` varchar(255) DEFAULT NULL,
  `body_embedded_file` varchar(75) DEFAULT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`thread_row_id`,`author_id`,`thread_row_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_rows_comments_cnt
DROP TABLE IF EXISTS `threads_rows_comments_cnt`;
CREATE TABLE IF NOT EXISTS `threads_rows_comments_cnt` (
  `thread_row_id` int(10) unsigned NOT NULL,
  `hour` tinyint(3) unsigned NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  KEY `Indeks 1` (`thread_row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_rows_cron
DROP TABLE IF EXISTS `threads_rows_cron`;
CREATE TABLE IF NOT EXISTS `threads_rows_cron` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `thread_row_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`thread_id`,`thread_row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.threads_rows_pluses
DROP TABLE IF EXISTS `threads_rows_pluses`;
CREATE TABLE IF NOT EXISTS `threads_rows_pluses` (
  `thread_row_id` int(10) unsigned NOT NULL,
  `hour` tinyint(3) unsigned NOT NULL,
  `pluses` int(10) unsigned NOT NULL,
  KEY `Indeks 1` (`thread_row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Zrzut struktury tabela mirko.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `account_key` varchar(50) DEFAULT NULL,
  `auth_key` varchar(75) DEFAULT NULL,
  `image` varchar(225) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `Indeks 2` (`login`),
  KEY `Indeks 3` (`account_key`,`auth_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
