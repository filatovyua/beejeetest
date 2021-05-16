-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.3.13-MariaDB-log - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных beejeetest
CREATE DATABASE IF NOT EXISTS `beejeetest` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `beejeetest`;

-- Дамп структуры для таблица beejeetest.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_insert` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для процедура beejeetest.tasks_add
DELIMITER //
CREATE PROCEDURE `tasks_add`(
	IN `i_name` TEXT,
	IN `i_email` TEXT,
	IN `i_content` TEXT
)
BEGIN

	INSERT INTO tasks (`name`, `email`, `content`)
	VALUES (i_name, i_email, i_content);
	
	SELECT LAST_INSERT_ID() AS `id`;

END//
DELIMITER ;

-- Дамп структуры для процедура beejeetest.tasks_edit
DELIMITER //
CREATE PROCEDURE `tasks_edit`(
	IN `i_id` TEXT,
	IN `i_key` TEXT,
	IN `i_value` TEXT
)
BEGIN

	IF (i_key = 'name') THEN
	
		UPDATE tasks t
		SET t.`name` = i_value
		WHERE t.id = i_id;
	ELSEIF (i_key = 'email') THEN
	
		UPDATE tasks t
		SET t.`email` = i_value
		WHERE t.id = i_id;
			
	ELSEIF (i_key = 'content') THEN
	
		UPDATE tasks t
		SET t.`content` = i_value
		WHERE t.id = i_id;	
	
	ELSEIF (i_key = 'status') THEN
	
		UPDATE tasks t
		SET t.`status` = i_value
		WHERE t.id = i_id;
		
	END IF;
	
	SELECT 'ok' AS `status`;

END//
DELIMITER ;

-- Дамп структуры для процедура beejeetest.tasks_get
DELIMITER //
CREATE PROCEDURE `tasks_get`(
	IN `i_id` TEXT
)
BEGIN

	SELECT 
		t.id,
		t.`name`,
		t.email,
		t.content,
		t.`status`
	FROM tasks t 
	WHERE (i_id = '' OR t.id = i_id);

END//
DELIMITER ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
