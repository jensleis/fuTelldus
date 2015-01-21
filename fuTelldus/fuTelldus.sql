-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Vert: localhost
-- Generert den: 13. Apr, 2013 00:46 AM
-- Tjenerversjon: 5.5.30-cll
-- PHP-Versjon: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `myhopo`
--

--
-- Tabellstruktur for tabell `myhopo_session`
--
CREATE TABLE `myhopo_sessions` (
  `id` CHAR(32) NOT NULL,
  `data` longtext NOT NULL,
  `last_accessed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Tabellstruktur for tabell `myhopo_config`
--

CREATE TABLE IF NOT EXISTS `myhopo_config` (
  `config_name` varchar(256) NOT NULL,
  `config_value` varchar(256) NOT NULL,
  `comment` varchar(256) NOT NULL,
  PRIMARY KEY (`config_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dataark for tabell `myhopo_config`
--

INSERT INTO `myhopo_config` (`config_name`, `config_value`, `comment`) VALUES
('pagetitle', 'MyHoPo', ''),
('default_language', 'en', ''),
('mail_from', 'mail@mydomain.com', ''),
('public_page_language', 'en', ''),
('pushover_api_token', '', '');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_devices`
--

CREATE TABLE IF NOT EXISTS `myhopo_devices` (
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `statevalue` tinyint(4) NOT NULL,
  `methods` tinyint(4) NOT NULL,
  `type` varchar(64) NOT NULL,
  `client` mediumint(9) NOT NULL,
  `clientname` varchar(128) NOT NULL,
  `online` tinyint(4) NOT NULL,
  `editable` tinyint(4) NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_schedule`
--

CREATE TABLE IF NOT EXISTS `myhopo_schedule` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `direction` varchar(16) NOT NULL,
  `warning_value` double NOT NULL,
  `type` varchar(32) NOT NULL,
  `repeat_alert` smallint(6) NOT NULL,
  `device` int(11) NOT NULL,
  `device_set_state` tinyint(4) NOT NULL,
  `send_to_mail` tinyint(4) NOT NULL,
  `send_to_pushover` tinyint(4) NOT NULL,
  `last_warning` int(11) NOT NULL,
  `notification_mail_primary` varchar(256) NOT NULL,
  `notification_mail_secondary` varchar(256) NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- ALTER TABLE myhopo_schedule ADD send_to_pushover tinyint(4) NOT NULL;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_sensors`
--

CREATE TABLE IF NOT EXISTS `myhopo_sensors` (
  `user_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `last_update` int(11) NOT NULL,
  `ignored` tinyint(4) NOT NULL,
  `client` int(11) NOT NULL,
  `clientname` varchar(256) NOT NULL,
  `online` tinyint(4) NOT NULL,
  `editable` tinyint(4) NOT NULL,
  `monitoring` tinyint(4) NOT NULL,
  `public` tinyint(4) NOT NULL,
  `show_in_main` tinyint(4) NOT NULL,
  PRIMARY KEY (`sensor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- ALTER TABLE myhopo_sensors ADD `show_in_main` tinyint(4) NOT NULL;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_sensors_log`
--

CREATE TABLE IF NOT EXISTS `myhopo_sensors_log` (
  `sensor_id` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `temp_value` double NOT NULL,
  `humidity_value` double NOT NULL,
  PRIMARY KEY (`sensor_id`,`time_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_sensors_shared`
--

-- CREATE TABLE IF NOT EXISTS `myhopo_sensors_shared` (
--   `share_id` int(11) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `description` varchar(256) NOT NULL,
--   `url` varchar(256) NOT NULL,
--   `show_in_main` tinyint(4) NOT NULL,
--   `disable` tinyint(4) NOT NULL COMMENT '0=view, 1=disabled',
--   PRIMARY KEY (`share_id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
--drop table myhopo_sensors_shared;


--
-- Dataark for tabell `myhopo_sensors_shared`
--

--INSERT INTO `myhopo_sensors_shared` (`share_id`, `user_id`, `description`, `url`, `show_in_main`, `disable`) VALUES
--(1, 1, 'Developers sensor', 'http://robertan.com/app/telldus/fuTelldus/public/xml_sensor.php?sensorID=871223', 1, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_users`
--

CREATE TABLE IF NOT EXISTS `myhopo_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_type` tinyint(4) NOT NULL, -- 0=local, 1=google
  `provider_id` varchar(128),
  `provider_token` varchar(256),
  `mail` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `language` varchar(64) NOT NULL,
  `admin` tinyint(4) NOT NULL,
  `pushover_key` varchar(256),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
-- ALTER TABLE myhopo_users ADD pushover_key varchar(256);
-- alter table myhopo_users drop column chart_type;
-- alter table myhopo_users ADD account_type tinyint(4) NOT NULL;
-- alter table myhopo_users ADD provider_id varchar(128);
-- alter table futelldus_users ADD provider_token varchar(256);

--
-- Dataark for tabell `myhopo_users`
--

INSERT INTO `myhopo_users` (`user_id`, `mail`, `password`, `language`, `admin`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `myhopo_virtueal_sensors`
--
CREATE TABLE IF NOT EXISTS `myhopo_virtual_sensors` (
  `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `sensor_type` tinyint(4) NOT NULL,
  `last_update` int(11),
  `last_check` int(11),
  `online` tinyint(4) NOT NULL,
  `monitoring` tinyint(4) NOT NULL,
  `show_in_main` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_plugins_instance_config`
--
CREATE TABLE IF NOT EXISTS `myhopo_plugins_instance_config` (
  `sensor_id` int(11) NOT NULL,
  `config_id` int(11) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`sensor_id`, `config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabellstruktur for table `myhopo_plugins_user_config`
--
CREATE TABLE IF NOT EXISTS `myhopo_plugins_user_config` (
  `user_id` int(11) NOT NULL,
  `config_id` int(11) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`user_id`, `config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Tabellstruktur for tabell `myhopo_virtual_devices`
--
CREATE TABLE IF NOT EXISTS `myhopo_virtual_devices` (
  `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `plugin_id` tinyint(4) NOT NULL,
  `last_status` int(11),
  `last_switch` int(11),
  `online` tinyint(4) NOT NULL,
  `editable` tinyint(4) NOT NULL,
  `show_in_main` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_virtual_devices_log`
--
CREATE TABLE `myhopo_virtual_devices_log` (
  `device_id` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`device_id`,`time_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

--
-- Tabellstruktur for tabell `myhopo_devices_log`
--
CREATE TABLE `myhopo_devices_log` (
  `device_id` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`device_id`,`time_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

--
-- Tabellstruktur for tabell `myhopo_plugins_tmpvals`
--
CREATE TABLE IF NOT EXISTS `myhopo_plugins_tmpvals` (
  `sensor_id` int(11) NOT NULL,
  `value_key` varchar(256) NOT NULL,
  `value` varchar(256),
  PRIMARY KEY (`sensor_id`, `value_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_plugins`
--
CREATE TABLE IF NOT EXISTS `myhopo_plugins` (
  `type_int` MEDIUMINT NOT NULL AUTO_INCREMENT,
  `type_description` varchar(256),
  `plugin_path` varchar(256),
  `user_settings_path` varchar(256),
  `plugin_type` varchar(256),
  `activated_version` int(11) NOT NULL,
  `hidden` tinyint(4) NOT NULL COMMENT '0=off, 1=on',
  PRIMARY KEY (`type_int`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--ALTER TABLE myhopo_plugins ADD user_settings_path varchar(256);


--
-- Tabellstruktur for tabell `myhopo_plugins_config`
--
CREATE TABLE `myhopo_plugins_config` (
  `id` MEDIUMINT auto_increment not null,
  `type_int` int(11),
  `config_type` varchar(256),
  `value_key` varchar(256),
  `value_type` varchar(256) NOT NULL,
  `description` varchar(256),
  key(id),
  PRIMARY KEY (`type_int`, `value_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- ALTER TABLE myhopo_plugins_config ADD config_type varchar(256);
-- UPDATE myhopo_plugins_config set config_type='instance';

--
-- Tabellstruktur for tabell `myhopo_virtual_sensors_log`
--
CREATE TABLE IF NOT EXISTS `myhopo_virtual_sensors_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_id` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_virtual_sensors_log_values`
--
CREATE TABLE IF NOT EXISTS `myhopo_virtual_sensors_log_values` (
  `log_id` int(11) NOT NULL,
  `value_key` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`log_id`, `value_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Tabellstruktur for tabell `myhopo_flows`
--
CREATE TABLE IF NOT EXISTS `myhopo_flows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `chart` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_scenes`
--
CREATE TABLE IF NOT EXISTS `myhopo_scenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Tabellstruktur for tabell `myhopo_scenes_data`
--
CREATE TABLE IF NOT EXISTS `myhopo_scenes_data` (
  `scene_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(256) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`scene_id`, `type`, `type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Tabellstruktur for tabell `myhopo_displays`
--
CREATE TABLE IF NOT EXISTS `myhopo_displays` (
  `display_id` varchar(256) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(256),
  PRIMARY KEY (`display_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellstruktur for tabell `myhopo_displays_pages`
--
CREATE TABLE IF NOT EXISTS `myhopo_displays_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `display_id` varchar(256) NOT NULL,
  `description` varchar(256),
  `showFor` int(11),
  `refreshAfter` int(11),
  `type` varchar(256),
  `type_id` int(11),
  `html` text,
  `reqCurrent` tinyint(4) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Dataark for tabell `myhopo_users_telldus_config`
--

--INSERT INTO `myhopo_users_telldus_config` (`user_id`, `sync_from_telldus`, `public_key`, `private_key`, `token`, `token_secret`) VALUES
--(1, 1, 'FEHUVEW84RAFR5SP22RABURUPHAFRUNU', 'ZUXEVEGA9USTAZEWRETHAQUBUR69U6EF', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
