/*
SQLyog Ultimate v12.14 (64 bit)
MySQL - 5.5.57-0ubuntu0.14.04.1 : Database - autopart
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`autopart` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `autopart`;

/*Table structure for table `aauth_group_to_group` */

DROP TABLE IF EXISTS `aauth_group_to_group`;

CREATE TABLE `aauth_group_to_group` (
  `group_id` int(11) unsigned NOT NULL,
  `subgroup_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`subgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_groups` */

DROP TABLE IF EXISTS `aauth_groups`;

CREATE TABLE `aauth_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_login_attempts` */

DROP TABLE IF EXISTS `aauth_login_attempts`;

CREATE TABLE `aauth_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(39) DEFAULT '0',
  `timestamp` datetime DEFAULT NULL,
  `login_attempts` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `aauth_perm_to_group` */

DROP TABLE IF EXISTS `aauth_perm_to_group`;

CREATE TABLE `aauth_perm_to_group` (
  `perm_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`perm_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_perm_to_user` */

DROP TABLE IF EXISTS `aauth_perm_to_user`;

CREATE TABLE `aauth_perm_to_user` (
  `perm_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`perm_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_perms` */

DROP TABLE IF EXISTS `aauth_perms`;

CREATE TABLE `aauth_perms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_pm` */

DROP TABLE IF EXISTS `aauth_pm`;

CREATE TABLE `aauth_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text,
  `date` datetime DEFAULT NULL,
  `read` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_pms` */

DROP TABLE IF EXISTS `aauth_pms`;

CREATE TABLE `aauth_pms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) unsigned NOT NULL,
  `receiver_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `pm_deleted_sender` int(1) DEFAULT NULL,
  `pm_deleted_receiver` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `full_index` (`id`,`sender_id`,`receiver_id`,`date_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_user_to_group` */

DROP TABLE IF EXISTS `aauth_user_to_group`;

CREATE TABLE `aauth_user_to_group` (
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_user_variables` */

DROP TABLE IF EXISTS `aauth_user_variables`;

CREATE TABLE `aauth_user_variables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `aauth_users` */

DROP TABLE IF EXISTS `aauth_users`;

CREATE TABLE `aauth_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `forgot_exp` text,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text,
  `verification_code` text,
  `totp_secret` varchar(16) DEFAULT NULL,
  `ip_address` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


/*Table structure for table `ci_sessions` */

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `backet`;

CREATE TABLE `backet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikul` varchar(255) DEFAULT NULL COMMENT 'Артикул товара',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Бренд товара',
  `quantity` int(11) DEFAULT NULL COMMENT 'Количество товара',
  `supplier_price` decimal(10,2) DEFAULT NULL COMMENT 'Цена от поставщика',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Розничная цена',
  `description` text COMMENT 'Описание позиции',
  `delivery` varchar(13) DEFAULT NULL COMMENT 'Срок доставки',
  `uid` varchar(255) DEFAULT NULL COMMENT 'Уникальный идентификатор',
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

/*Table structure for table `new_backet_state` */

DROP TABLE IF EXISTS `backet_state`;

CREATE TABLE `backet_state` (
  `id_user` int(11) unsigned NOT NULL,
  `id_backet` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `sid` varchar(255) DEFAULT NULL COMMENT 'Идентификатор сессии если пользователь id=4',
  `provider_id` int(11) DEFAULT NULL COMMENT 'Уникальный идентификатор поставщика',
  KEY `new_backet_state_fk2` (`id_status`),
  KEY `new_backet_state_fk1` (`id_backet`),
  KEY `id_user` (`id_user`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `backet_state_fk1` FOREIGN KEY (`id_backet`) REFERENCES `backet` (`id`),
  CONSTRAINT `backet_state_fk2` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`),
  CONSTRAINT `backet_state_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `aauth_users` (`id`),
  CONSTRAINT `backet_state_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `new_order_state` */

DROP TABLE IF EXISTS `order_state`;

CREATE TABLE `order_state` (
  `id_order` int(11) NOT NULL,
  `id_backet` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `sid` varchar(32) DEFAULT NULL,
  KEY `order_state_fk0` (`id_order`),
  KEY `order_state_fk2` (`id_user`),
  KEY `order_state_fk1` (`id_backet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `new_order_status` */

DROP TABLE IF EXISTS `order_status`;

CREATE TABLE `order_status` (
  `id_order` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  KEY `order_status_fk0` (`id_order`),
  KEY `order_status_fk1` (`id_status`),
  CONSTRAINT `order_status_fk0` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_status_fk1` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `new_orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `phone` varchar(255) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `new_status` */

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `new_vins` */

DROP TABLE IF EXISTS `vins`;

CREATE TABLE `vins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vin` varchar(17) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Table structure for table `new_vins_state` */

DROP TABLE IF EXISTS `vins_state`;

CREATE TABLE `vins_state` (
  `id_vin` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(20) DEFAULT NULL,
  `content` longtext,
  `title` varchar(255) DEFAULT NULL,
  `deleted` enum('1','0') DEFAULT '0',
  `description` text,
  PRIMARY KEY (`id`),
  KEY `pagename` (`pagename`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `providers`;

CREATE TABLE `providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext,
  `uri` longtext,
  `hash` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `templates`;

CREATE TABLE `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `description` tinytext,
  `datetime` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/* Procedure structure for procedure `addBacketPosition` */

/*!50003 DROP PROCEDURE IF EXISTS  `addBacketPosition` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addBacketPosition`(
		IN vart varchar(100)  CHARACTER SET utf8, 
		IN vbrand VARCHAR(100)  CHARACTER SET utf8,
		 IN vquant INT, IN vsupplier_price FLOAT, 
		 IN vprice FLOAT,
		  in vdescr VARCHAR(255) CHARACTER SET utf8,
		   IN vdeliv VARCHAR(100) CHARACTER SET utf8,
		    IN vuid VARCHAR(255)  CHARACTER SET utf8,
		    in vtime INT)
    COMMENT 'Процедура добавляет позицию в корзину пользователя'
BEGIN
	INSERT INTO backet (artikul ,brand,   quantity , supplier_price, price,  description , delivery , uid, `time` )
	values ( vart ,  vbrand ,  vquant , vsupplier_price,  vprice ,  vdescr ,  vdeliv ,  vuid, vtime );
	SELECT last_insert_id() as `insert_id`;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `addBacketStatePosition` */

/*!50003 DROP PROCEDURE IF EXISTS  `addBacketStatePosition` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addBacketStatePosition`(IN var_id_user INT, IN var_id_backet INT, IN var_id_status INT, IN var_sid varchar(32), IN var_prov_id INT)
    COMMENT 'Метод добавляет данные  о позициии в корзине в таблицу backe'
BEGIN
	INSERT INTO backet_state (id_user, id_backet, id_status, sid, provider_id) VALUES (var_id_user, var_id_backet, var_id_status, var_sid,  var_prov_id);
    END */$$
DELIMITER ;

/* Procedure structure for procedure `addParameterVin` */

/*!50003 DROP PROCEDURE IF EXISTS  `addParameterVin` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addParameterVin`(IN var_parameter VARCHAR(255))
    COMMENT 'Добавляет параметр для вин номера Возвращает его ID'
BEGIN
		IF(SELECT id FROM vins_params WHERE `value`=var_parameter) THEN
			SELECT id FROM vins_params WHERE `value`=var_parameter;
		ELSE
			INSERT INTO vins_params (`value`) VALUES (var_parameter);
			SELECT LAST_INSERT_ID() as `id`;
		END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `addStateVinParams` */

/*!50003 DROP PROCEDURE IF EXISTS  `addStateVinParams` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addStateVinParams`(IN var_id_vin INT,  IN var_id_param INT, IN var_id_value INT)
    COMMENT 'Добавляет запись в таблицу соотношений вин - параметр - значение'
BEGIN
		INSERT INTO vins_param_state (id_param,  id_value,  id_vin) VALUES (var_id_param, var_id_value, var_id_vin);
    END */$$
DELIMITER ;

/* Procedure structure for procedure `addValueVin` */

/*!50003 DROP PROCEDURE IF EXISTS  `addValueVin` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addValueVin`(IN var_value VARCHAR(255))
    COMMENT 'Добавляет значение для вин номера Возвращает его ID'
BEGIN
		IF(SELECT id FROM vins_values WHERE `value`=var_value) THEN
			SELECT id FROM vins_values WHERE `value`=var_value;
		ELSE
			INSERT INTO vins_values (`value`) VALUES (var_value);
			SELECT LAST_INSERT_ID() AS `id`;
		END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `addVin` */

/*!50003 DROP PROCEDURE IF EXISTS  `addVin` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `addVin`(IN var_vin VARCHAR(255))
    COMMENT 'Добавляет значение для вин номера Возвращает его ID'
BEGIN
		IF(SELECT id FROM vins WHERE `vin`=var_vin) THEN
			SELECT id FROM vins WHERE `vin`=var_vin;
		ELSE
			INSERT INTO vins (`vin`) VALUES (var_vin);
			SELECT LAST_INSERT_ID() AS `id`;
		END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `checkBacketUserId` */

/*!50003 DROP PROCEDURE IF EXISTS  `checkBacketUserId` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `checkBacketUserId`(IN var_user_id INT, IN var_session_id VARCHAR(32))
    COMMENT 'Возвращает количество и сумму заказов для данного пользователя п'
BEGIN
	if(var_user_id!=4) THEN
		SELECT COUNT(nb.id) AS positions, SUM(nb.quantity) AS quant, SUM(nb.`price`) AS price FROM backet_state AS nbs 
		INNER JOIN backet AS nb ON nb.id=nbs.id_backet 
		WHERE nbs.id_user=var_user_id AND nbs.id_status=1;
	ELSE
		SELECT COUNT(nb.id) AS positions, SUM(nb.quantity) AS quant, SUM(nb.`price`) AS price FROM backet_state AS nbs 
		INNER JOIN backet AS nb ON nb.id=nbs.id_backet 
		WHERE nbs.sid= var_session_id AND nbs.id_status=1;
	END IF;
 END */$$
DELIMITER ;

/* Procedure structure for procedure `clearBacket` */

/*!50003 DROP PROCEDURE IF EXISTS  `clearBacket` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `clearBacket`(IN var_user_id INT,  IN var_sess_id VARCHAR(255))
    COMMENT 'Меняет статус у данной позиции на Удален'
BEGIN
	IF(var_user_id!=4) THEN
		UPDATE backet_state SET  id_status=5 WHERE id_user= var_user_id;
	ELSE
		UPDATE backet_state SET  id_status=5 WHERE sid= var_sess_id;
	END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `deleteBacketPosition` */

/*!50003 DROP PROCEDURE IF EXISTS  `deleteBacketPosition` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `deleteBacketPosition`(IN var_user_id INT,  IN var_sess_id VARCHAR(255), IN var_position_id INT)
    COMMENT 'Меняет статус у данной позиции на Удален'
BEGIN
	IF(var_user_id!=4) THEN
		UPDATE backet_state SET  id_status=5 WHERE id_user= var_user_id AND id_backet=var_position_id;
	ELSE
		UPDATE backet_state SET  id_status=5 WHERE sid= var_sess_id AND id_backet=var_position_id ;
	END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `get100RandomAcessoriesEmptyPrice` */

/*!50003 DROP PROCEDURE IF EXISTS  `get100RandomAcessoriesEmptyPrice` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `get100RandomAcessoriesEmptyPrice`(IN var1 INT)
BEGIN
	SELECT a.id, a.artikul, a.price, a.`lastupd`, b.name AS brand FROM `acessories` AS a
	INNER JOIN `acessory_state` AS acs ON acs.`id_acessory`=a.`id`
	INNER JOIN brands AS b ON acs.`id_brand`=b.`id` WHERE price IS NULL OR lastupd<var1  ORDER BY RAND() LIMIT 50;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getAcessoryImages` */

/*!50003 DROP PROCEDURE IF EXISTS  `getAcessoryImages` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `getAcessoryImages`(IN var1 INT)
BEGIN
	SELECT ai.`emeximgid` AS imgId, ai.`width` as w, ai.`height` as h FROM acessory_images AS ai
	INNER JOIN acessory_img_state AS ais ON ais.id_img=ai.`id`
	WHERE ais.`id_acessory`=var1;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getActiveBacketPosition` */

/*!50003 DROP PROCEDURE IF EXISTS  `getActiveBacketPosition` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getActiveBacketPosition`()
    COMMENT 'Возвращает все позиции корзины для всех пользователей, где стату'
BEGIN
	SELECT 
  backet.id,
  backet.artikul,
  backet.brand,
  backet.quantity,
  backet.supplier_price,
  backet.price,
  backet.description,
  backet.delivery,
  backet.uid,
  backet.time
FROM
  backet 
  INNER JOIN backet_state AS nbs ON nbs.`id_backet`=backet.`id`
  WHERE nbs.id_status=1;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getActiveOrders` */

/*!50003 DROP PROCEDURE IF EXISTS  `getActiveOrders` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getActiveOrders`(IN var_user_id INT, IN var_sess_id VARCHAR(32))
    COMMENT 'Возвращает активные заказы для текущего юзера'
BEGIN
	IF(var_user_id!=4) THEN
		SELECT DISTINCT
			orders.id AS order_id, 
			orders.date AS `date`,
			orders.phone,
			 orders.`comment`, 
			 ns.`name` AS `status`
		FROM orders 
		INNER JOIN order_state AS nos ON nos.id_order=orders.`id`
		INNER JOIN order_status AS nost ON orders.`id`=nost.`id_order`
		INNER JOIN status AS ns ON ns.`id`=nost.`id_status`
		WHERE nos.id_user=var_user_id AND nost.`id_status` NOT IN (4,5,7,8,9);
	ELSE
		SELECT DISTINCT
			orders.id AS order_id, 
			orders.date AS `date`,
			orders.phone,
			 orders.`comment`, 
			 ns.`name` AS `status`
		FROM orders 
		INNER JOIN order_state AS nos ON nos.id_order=orders.`id`
		INNER JOIN order_status AS nost ON orders.`id`=nost.`id_order`
		INNER JOIN status AS ns ON ns.`id`=nost.`id_status`
		WHERE nos.sid=var_sess_id AND nost.`id_status` NOT IN (4,5,7,8,9);
	END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getAllActiveOrders` */

/*!50003 DROP PROCEDURE IF EXISTS  `getAllActiveOrders` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getAllActiveOrders`()
    COMMENT 'Возвращает все активные заказы'
BEGIN
   SELECT 
  ns.`id`,
  UNIX_TIMESTAMP(ns.`date`) AS `time`,
  ns.`comment`,
  ns.`phone`,
  au.`username`,
   au.`id` AS user_id,
   nost.id_status,
  SUM((SELECT 
    SUM(nb.quantity) 
  FROM
    backet AS nb 
  WHERE nb.id=nos.`id_backet`)) AS quant,
  SUM((SELECT 
    SUM(nb.quantity *nb.supplier_price)
  FROM
    backet AS nb 
  WHERE nb.id=nos.`id_backet`)) AS whoes_cost ,
  SUM((SELECT 
    SUM(nb.quantity *nb.price)
  FROM
    backet AS nb 
  WHERE nb.id=nos.`id_backet`)) AS retail_cost ,
  ((SUM((SELECT 
    SUM(nb.quantity *nb.price)
  FROM
    backet AS nb 
  WHERE nb.id=nos.`id_backet`))) -
  (SUM((SELECT 
    SUM(nb.quantity *nb.supplier_price)
  FROM
    backet AS nb 
  WHERE nb.id=nos.`id_backet`)))) AS profit
FROM
  orders AS ns 
  INNER JOIN order_state AS nos 
    ON nos.`id_order` = ns.`id` 
  INNER JOIN aauth_users AS au 
    ON au.id = nos.`id_user` 
  INNER JOIN order_status AS nost 
    ON nost.`id_order` = ns.`id` 
WHERE nost.`id_status` NOT IN (1, 4, 5, 7, 8) 
GROUP BY ns.`id`;
  END */$$
DELIMITER ;

/* Procedure structure for procedure `getAllArchiveOrders` */

/*!50003 DROP PROCEDURE IF EXISTS  `getAllArchiveOrders` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getAllArchiveOrders`()
    COMMENT 'Возвращает все архивные заказы'
BEGIN
  SELECT 
    ns.`id`,
    UNIX_TIMESTAMP(ns.`date`) AS `time`,
    ns.`comment`,
    ns.`phone`,
    au.`username`,
    au.`id` AS user_id,
    nost.id_status,
    SUM(
      (SELECT 
        SUM(nb.quantity) 
      FROM
        backet AS nb 
      WHERE nb.id = nos.`id_backet`)
    ) AS quant,
    SUM(
      (SELECT 
        SUM(nb.quantity * nb.supplier_price) 
      FROM
        backet AS nb 
      WHERE nb.id = nos.`id_backet`)
    ) AS whoes_cost,
    SUM(
      (SELECT 
        SUM(nb.quantity * nb.price) 
      FROM
        backet AS nb 
      WHERE nb.id = nos.`id_backet`)
    ) AS retail_cost,
    (
      (
        SUM(
          (SELECT 
            SUM(nb.quantity * nb.price) 
          FROM
            backet AS nb 
          WHERE nb.id = nos.`id_backet`)
        )
      ) - (
        SUM(
          (SELECT 
            SUM(nb.quantity * nb.supplier_price) 
          FROM
            backet AS nb 
          WHERE nb.id = nos.`id_backet`)
        )
      )
    ) AS profit 
  FROM
    orders AS ns 
    INNER JOIN order_state AS nos 
      ON nos.`id_order` = ns.`id` 
    INNER JOIN aauth_users AS au 
      ON au.id = nos.`id_user` 
    INNER JOIN order_status AS nost 
      ON nost.`id_order` = ns.`id` 
  WHERE nost.`id_status` IN (1, 4, 5, 7, 8) 
  GROUP BY ns.`id` ;
END */$$
DELIMITER ;

/* Procedure structure for procedure `getAllStatus` */

/*!50003 DROP PROCEDURE IF EXISTS  `getAllStatus` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getAllStatus`()
    COMMENT 'Возвращает все статусы'
BEGIN
		SELECT  id, `name`, description FROM status;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getArchiveOrders` */

/*!50003 DROP PROCEDURE IF EXISTS  `getArchiveOrders` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getArchiveOrders`(IN var_user_id INT, IN var_sess_id VARCHAR(32))
    COMMENT 'Возвращает активные заказы для текущего юзера'
BEGIN
	IF(var_user_id!=4) THEN
		SELECT DISTINCT
			orders.id AS order_id, 
			orders.date AS `date`,
			orders.phone,
			 orders.`comment`, 
			 ns.`name` AS `status`
		FROM orders 
		INNER JOIN order_state AS nos ON nos.id_order=orders.`id`
		INNER JOIN order_status AS nost ON orders.`id`=nost.`id_order`
		INNER JOIN status AS ns ON ns.`id`=nost.`id_status`
		WHERE nos.id_user=var_user_id AND nost.`id_status` IN (4,5,7,8,9);
	ELSE
		SELECT DISTINCT
			orders.id AS order_id, 
			orders.date AS `date`,
			orders.phone,
			 orders.`comment`, 
			 ns.`name` AS `status`
		FROM orders 
		INNER JOIN order_state AS nos ON nos.id_order=orders.`id`
		INNER JOIN order_status AS nost ON orders.`id`=nost.`id_order`
		INNER JOIN status AS ns ON ns.`id`=nost.`id_status`
		WHERE nos.sid=var_sess_id AND nost.`id_status` IN (4,5,7,8,9);
	END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getBacketPosition` */

/*!50003 DROP PROCEDURE IF EXISTS  `getBacketPosition` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getBacketPosition`(IN var_user_id INT, IN var_sess_id VARCHAR(255))
    COMMENT 'Процедура возвращает все позиции для данного пользователя'
BEGIN
	IF(var_user_id!=4) THEN
		SELECT nb.id, nb.artikul, nb.brand, nb.quantity, nb.supplier_price, nb.price, nb.description, nb.delivery, nb.uid FROM backet_state AS nbs 
		INNER JOIN backet AS nb ON nb.id=nbs.id_backet 
		WHERE nbs.id_user=var_user_id AND nbs.id_status=1;
	ELSE
		SELECT nb.id, nb.artikul, nb.brand, nb.quantity, nb.supplier_price, nb.price, nb.description, nb.delivery, nb.uid FROM backet_state AS nbs 
		INNER JOIN backet AS nb ON nb.id=nbs.id_backet 
		WHERE nbs.sid=  var_sess_id  AND nbs.id_status=1;
	END IF;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getBrandsThisCategory` */

/*!50003 DROP PROCEDURE IF EXISTS  `getBrandsThisCategory` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `getBrandsThisCategory`(IN var1 INT)
BEGIN
		SELECT DISTINCT b.id, b.name FROM brands AS b 
		INNER JOIN acessory_state AS acs ON acs.`id_brand`=b.`id`
		INNER JOIN categories AS c ON c.`id`=acs.`id_category`
		WHERE acs.`id_category`=var1 ORDER BY b.`name`;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getFindedArtikuls` */

/*!50003 DROP PROCEDURE IF EXISTS  `getFindedArtikuls` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getFindedArtikuls`(IN var_art VARCHAR(255))
    COMMENT 'Возвращает артикулы, номер которых частично соотвествует указанн'
BEGIN
  SELECT 
    art.`artikul`,
    art.`description`,
    b.`name` 
  FROM
    artikuls AS art 
    INNER JOIN artikul_state AS `as` 
      ON `as`.`id_artikul` = art.`id` 
    INNER JOIN brands AS b 
      ON `as`.`id_brand` = b.`id` 
  WHERE art.`artikul` LIKE CONCAT(var_art, "%")  LIMIT 20;
  END */$$
DELIMITER ;

/* Procedure structure for procedure `getParamsThisArticle` */

/*!50003 DROP PROCEDURE IF EXISTS  `getParamsThisArticle` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `getParamsThisArticle`(IN var1 INT)
BEGIN
 SELECT pn.name, pv.value, pn.`dimension` FROM param_values AS pv
	INNER JOIN param_state AS ps ON pv.`id`=ps.`id_param_value`
	INNER JOIN parameters AS pn ON pn.`id`=ps.`id_param_name`
	WHERE ps.`id_acessory`=var1;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getParamsThisCat` */

/*!50003 DROP PROCEDURE IF EXISTS  `getParamsThisCat` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `getParamsThisCat`(IN var1 INT)
BEGIN
	SELECT DISTINCT a.id, a.name FROM `parameters` AS a 
	INNER JOIN param_state AS b ON b.id_param_name=a.id
	INNER JOIN categories AS c ON c.`id`=b.`id_category`
	WHERE c.id=var1 ORDER BY a.id ASC;
END */$$
DELIMITER ;

/* Procedure structure for procedure `getThisOrderList` */

/*!50003 DROP PROCEDURE IF EXISTS  `getThisOrderList` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getThisOrderList`(IN var_id_order INT)
    COMMENT 'Возвращает состав указанного заказа'
BEGIN
	SELECT 
  backet.id,
  backet.artikul,
  backet.brand,
  backet.quantity,
  backet.supplier_price,
  backet.price,
  backet.description,
  backet.delivery,
  backet.uid,
  backet.time,
  status.`name` AS `status`,
  status.`id` as id_status,
  providers.`name` as provider
FROM
  backet 
  INNER JOIN backet_state AS nbs ON nbs.`id_backet`=backet.`id`
  INNER JOIN status ON nbs.`id_status`=status.`id`
  INNER JOIN order_state AS nos ON nos.`id_backet`=backet.`id`
  INNER JOIN providers ON providers.`id`=nbs.provider_id
  WHERE nos.`id_order`=var_id_order  AND nbs.id_status NOT IN(1, 5,8);
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getThisOrderPositions` */

/*!50003 DROP PROCEDURE IF EXISTS  `getThisOrderPositions` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getThisOrderPositions`(in var_id_order INT)
    COMMENT 'Вовзращает позиции для текущего заказа'
BEGIN
	SELECT nb.id , 
		artikul, 
		 brand,
		 quantity,
		 supplier_price , 
		 price, 
		 nb.description, 
		 delivery,
		 ns.name AS `status`
		 FROM backet  AS nb
INNER JOIN  backet_state AS nbs ON nbs.`id_backet`=nb.`id`
INNER JOIN order_state AS nos ON nos.`id_backet`=nb.`id`
INNER JOIN order_status AS nost ON nost.`id_order`=nos.`id_order`
INNER JOIN status AS ns ON nost.`id_status`=ns.`id`
WHERE nos.`id_order`=var_id_order AND nost.`id_status`!= 5;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getValuesToCatAndParams` */

/*!50003 DROP PROCEDURE IF EXISTS  `getValuesToCatAndParams` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`postapokrf_car`@`217.25.226.22` PROCEDURE `getValuesToCatAndParams`(IN var1 INT, IN var2 INT)
BEGIN
	SELECT DISTINCT p.id, p.value, ps.`id_category` as catId FROM param_values AS p
	INNER JOIN param_state AS ps ON  ps.`id_param_value`=p.`id`
	INNER JOIN parameters AS n ON ps.`id_param_name`=n.id
	INNER JOIN `categories` AS c ON ps.`id_category`=c.`id`
	WHERE c.`id`=var1 AND n.id=var2 ORDER BY p.value ASC;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getVinInfo` */

/*!50003 DROP PROCEDURE IF EXISTS  `getVinInfo` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getVinInfo`(IN var_vin_id INT)
    COMMENT 'Возвращает параметры вин номера'
BEGIN
		SELECT vp.`value` AS param, vv.`value` AS `value` FROM vins_params AS vp
		INNER JOIN vins_param_state AS vps ON vps.id_param=vp.id
		INNER JOIN vins_values AS vv ON vv.id=vps.id_value
		WHERE vps.id_vin=var_vin_id;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `getVinsThisUser` */

/*!50003 DROP PROCEDURE IF EXISTS  `getVinsThisUser` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `getVinsThisUser`(IN var_id_user INT)
    COMMENT 'Возвращает вин номера для текущего пользователя'
BEGIN
	SELECT id, vin FROM vins AS nv
	INNER JOIN vins_state AS nvs ON nvs.id_vin=nv.id
	WHERE nvs.id_user=var_id_user AND nvs.deleted!=1;
 END */$$
DELIMITER ;

/* Procedure structure for procedure `setOrderStatus` */

/*!50003 DROP PROCEDURE IF EXISTS  `setOrderStatus` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `setOrderStatus`(IN var_id_status INT,IN var_id_order INT )
    COMMENT 'Изменяет статус указанного заказа'
BEGIN
	UPDATE order_status SET  id_status=var_id_status WHERE id_order=var_id_order ;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `setPositionStatus` */

/*!50003 DROP PROCEDURE IF EXISTS  `setPositionStatus` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `setPositionStatus`(IN var_status_id INT, IN var_position_id INT)
    COMMENT 'Меняет статутс для заданной позиции'
BEGIN
	UPDATE `backet_state` SET id_status=var_status_id WHERE id_backet=var_position_id;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `toOrder` */

/*!50003 DROP PROCEDURE IF EXISTS  `toOrder` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `toOrder`(IN var_user_id INT, IN var_sess_id VARCHAR(32), IN var_phonenumber VARCHAR(255), IN var_comment TEXT)
    COMMENT 'Меняет статус у товаров в корзине для конкретного пользователя н'
BEGIN
	INSERT INTO orders (phone, `comment`) VALUES (var_phonenumber, var_comment);
	IF(var_user_id!=4) THEN
		INSERT INTO order_state (id_order, id_backet, id_user, sid) SELECT LAST_INSERT_ID(), id_backet, var_user_id, var_sess_id FROM backet_state WHERE id_status=1 AND `id_user`= var_user_id;
	ELSE
		INSERT INTO order_state (id_order, id_backet, id_user, sid) SELECT LAST_INSERT_ID(), id_backet, var_user_id, var_sess_id  FROM backet_state WHERE id_status=1 AND `sid`= var_sess_id;
	END IF;
	IF(var_user_id!=4) THEN
		UPDATE backet_state SET  id_status=2 WHERE id_user= var_user_id AND id_status=1;
	ELSE
		UPDATE backet_state SET  id_status=2 WHERE sid= var_sess_id AND id_status=1;
	END IF;
	INSERT INTO order_status (id_order, id_status) VALUES (LAST_INSERT_ID() ,  2);
	SELECT LAST_INSErT_ID() as order_num;
    END */$$
DELIMITER ;



/*Data for the table `aauth_groups` */

insert  into `aauth_groups`(`id`,`name`,`definition`) values 

(1,'Admin','Super Admin Group'),

(2,'Public','Public Access Group'),

(3,'Default','Default Access Group');

/*Table structure for table `aauth_login_attempts` */

DROP TABLE IF EXISTS `aauth_login_attempts`;

CREATE TABLE `aauth_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(39) DEFAULT '0',
  `timestamp` datetime DEFAULT NULL,
  `login_attempts` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;



insert  into `aauth_user_to_group`(`user_id`,`group_id`) values 

(1,1),

(1,3),

(4,3);



insert  into `aauth_user_variables`(`id`,`user_id`,`data_key`,`value`) values 



(6,4,'merge','1.15'),

(7,4,'id_template','3'),

(8,4,'tmpl_name','newtemplate');



insert  into `aauth_users`(`id`,`email`,`pass`,`username`,`banned`,`last_login`,`last_activity`,`date_created`,`forgot_exp`,`remember_time`,`remember_exp`,`verification_code`,`totp_secret`,`ip_address`) values 

(1,'admin@example.com','dd5073c93fb477a167fd69072e95455834acd93df8fed41a2c468c45b394bfe3','Admin',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0'),

(4,'guest@example.ru','445d6ccd81cff89ffe1541117b370ceb8fba5f0fbdf96ba41fd4080ff7b32d1a','guest',0,NULL,NULL,'2017-04-17 09:05:45',NULL,NULL,NULL,NULL,NULL,NULL);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
