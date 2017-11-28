

USE `drug_order`;

/*Table structure for table `access_logs` */

DROP TABLE IF EXISTS `access_logs`;

CREATE TABLE `access_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `IP` varchar(64) collate utf8_unicode_ci default NULL,
  `sourcetype` varchar(20) collate utf8_unicode_ci default NULL,
  `target` varchar(20) collate utf8_unicode_ci default NULL,
  `acount_id` int(10) unsigned default NULL,
  `access_data_type` varchar(20) collate utf8_unicode_ci default NULL,
  `access_data_id` int(10) unsigned default NULL,
  `is_auto_login` tinyint(3) unsigned default NULL,
  `access_function` varchar(255) collate utf8_unicode_ci default NULL,
  `access_action` int(10) unsigned default NULL,
  `access_result` tinyint(3) unsigned default NULL,
  `access_data_debug` varchar(1024) collate utf8_unicode_ci default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `certificates_employees` */

DROP TABLE IF EXISTS `certificates_employees`;

CREATE TABLE `certificates_employees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `certificate_id` int(11) unsigned NOT NULL,
  `employee_id` int(11) unsigned NOT NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `certificate_id` (`certificate_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `certificates_employees_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `cetificates` (`id`),
  CONSTRAINT `certificates_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cetificates` */

DROP TABLE IF EXISTS `cetificates`;

CREATE TABLE `cetificates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `certificates_path` varchar(255) default NULL,
  `client_private_key` text,
  `status` tinyint(3) unsigned NOT NULL,
  `init_staff_id` int(10) unsigned default NULL,
  `init_at` datetime default NULL,
  `create_staff_id` int(10) unsigned default NULL,
  `client_certificates` text,
  `group_id` int(10) unsigned NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `cetificates_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cities` */

DROP TABLE IF EXISTS `cities`;

CREATE TABLE `cities` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `province_id` int(10) unsigned NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `cities_master` */

DROP TABLE IF EXISTS `cities_master`;

CREATE TABLE `cities_master` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `province_id` int(10) unsigned NOT NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `cities_master_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `companies` */

DROP TABLE IF EXISTS `companies`;

CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(64) default NULL,
  `name` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `group_admin_id` int(10) unsigned default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `devices` */

DROP TABLE IF EXISTS `devices`;

CREATE TABLE `devices` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `device_code` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) default NULL,
  `platform` varchar(64) default NULL,
  `version` varchar(64) default NULL,
  `notification_token` varchar(255) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `districts` */

DROP TABLE IF EXISTS `districts`;

CREATE TABLE `districts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `province_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `name` varchar(100) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `city_id` (`city_id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `districts_ibfk_2` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `districts_master` */

DROP TABLE IF EXISTS `districts_master`;

CREATE TABLE `districts_master` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `province_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `name` varchar(100) default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `city_id` (`city_id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `districts_master_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities_master` (`id`),
  CONSTRAINT `districts_master_ibfk_2` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `employees` */

DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `password` varchar(100) default NULL,
  `company_id` int(10) unsigned NOT NULL,
  `role` tinyint(10) unsigned NOT NULL COMMENT '1=store_staff,2=company_staff,3=system_manager',
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `email` varchar(255) default NULL,
  `phone_number` varchar(100) default NULL,
  `birthday` date default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `drug_company_id` (`company_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `login_logs` */

DROP TABLE IF EXISTS `login_logs`;

CREATE TABLE `login_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `IP` varchar(64) collate utf8_unicode_ci default NULL,
  `sourcetype` varchar(20) collate utf8_unicode_ci default NULL,
  `target` varchar(20) collate utf8_unicode_ci default NULL,
  `input_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `login_result` tinyint(3) unsigned default NULL,
  `acount_type` int(10) unsigned NOT NULL,
  `acount_id` int(10) unsigned NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `logs` */

DROP TABLE IF EXISTS `logs`;

CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `user_id` int(11) default NULL,
  `request_uri` varchar(255) default NULL,
  `http_method` varchar(10) default NULL,
  `controller` varchar(100) default NULL,
  `action` varchar(100) default NULL,
  `parameters` text,
  `user_agent` varchar(255) default NULL,
  `client_ip_address` varchar(64) default NULL,
  `client_platform` varchar(64) default NULL,
  `exception` varchar(255) default NULL,
  `message` text,
  `event_type` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mediaid_staffs` */

DROP TABLE IF EXISTS `mediaid_staffs`;

CREATE TABLE `mediaid_staffs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `first_name_kana` varchar(255) default NULL,
  `last_name_kana` varchar(255) default NULL,
  `gender` tinyint(3) unsigned default NULL,
  `birthday` date default NULL,
  `postal_code` varchar(100) default NULL,
  `province` varchar(255) default NULL,
  `city1` varchar(255) default NULL,
  `city2` varchar(255) default NULL,
  `province_id` int(10) unsigned default NULL,
  `city_id` int(10) unsigned default NULL,
  `district_id` int(10) unsigned default NULL,
  `address` varchar(255) default NULL,
  `phone_number` varchar(64) default NULL,
  `is_cetified` tinyint(3) unsigned default NULL,
  `certificate_id` int(10) unsigned default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `district_id` (`district_id`),
  KEY `certificate_id` (`certificate_id`),
  CONSTRAINT `mediaid_staffs_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`),
  CONSTRAINT `mediaid_staffs_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities_master` (`id`),
  CONSTRAINT `mediaid_staffs_ibfk_3` FOREIGN KEY (`district_id`) REFERENCES `districts_master` (`id`),
  CONSTRAINT `mediaid_staffs_ibfk_4` FOREIGN KEY (`certificate_id`) REFERENCES `cetificates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mediaid_users` */

DROP TABLE IF EXISTS `mediaid_users`;

CREATE TABLE `mediaid_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `phone_number` varchar(64) default NULL,
  `birthday` date default NULL,
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `address` varchar(255) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `message_templates` */

DROP TABLE IF EXISTS `message_templates`;

CREATE TABLE `message_templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `company_id` int(11) unsigned default NULL,
  `message_type` int(10) unsigned NOT NULL,
  `type` tinyint(4) unsigned default NULL,
  `status` tinyint(3) unsigned NOT NULL COMMENT '"０：適用中\n１：未使用\n２：仮登録\n３：削除"',
  `title` varchar(255) default NULL,
  `content` varchar(1024) default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `drug_store_id` (`store_id`),
  CONSTRAINT `message_templates_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `message_type` */

DROP TABLE IF EXISTS `message_type`;

CREATE TABLE `message_type` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned NOT NULL,
  `user_id` int(11) default NULL,
  `store_id` int(10) unsigned default NULL,
  `target` tinyint(3) unsigned default NULL,
  `seen_at` datetime default NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `type` tinyint(3) unsigned default NULL COMMENT '1=received,2=dispensed,3=others',
  `template_id` int(10) unsigned default NULL,
  `sent_at` datetime default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `drug_order_id` (`order_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `message` varchar(255) default NULL,
  `status` tinyint(4) default NULL COMMENT '2=cancel,1=pushed,0=waiting',
  `pushed_at` datetime default NULL,
  `target` tinyint(3) unsigned NOT NULL,
  `store_id` int(10) unsigned default NULL,
  `company_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_access_tokens` */

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) default NULL,
  `expires` datetime NOT NULL,
  `scope` varchar(2000) default NULL,
  `app_keycode` varchar(256) default NULL,
  PRIMARY KEY  (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_authorization_codes` */

DROP TABLE IF EXISTS `oauth_authorization_codes`;

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) default NULL,
  `redirect_uri` varchar(2000) default NULL,
  `expires` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `scope` varchar(2000) default NULL,
  PRIMARY KEY  (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_clients` */

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) default NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `grant_types` varchar(80) default NULL,
  `scope` varchar(100) default NULL,
  `user_id` varchar(80) default NULL,
  PRIMARY KEY  (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_jwt` */

DROP TABLE IF EXISTS `oauth_jwt`;

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) default NULL,
  `public_key` varchar(2000) default NULL,
  PRIMARY KEY  (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_refresh_tokens` */

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) default NULL,
  `expires` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `scope` varchar(2000) default NULL,
  PRIMARY KEY  (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_scopes` */

DROP TABLE IF EXISTS `oauth_scopes`;

CREATE TABLE `oauth_scopes` (
  `scope` text,
  `is_default` tinyint(1) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_users` */

DROP TABLE IF EXISTS `oauth_users`;

CREATE TABLE `oauth_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) default NULL,
  `first_name` varchar(255) default NULL,
  `last_name` varchar(255) default NULL,
  PRIMARY KEY  (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `order_transaction` */

DROP TABLE IF EXISTS `order_transaction`;

CREATE TABLE `order_transaction` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `order_code` varchar(50) default NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `popup_at` datetime default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `order_transaction_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `order_transaction_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `order_code` varchar(50) default NULL,
  `user_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `visit_at` datetime default NULL,
  `comment` varchar(255) default NULL,
  `sent_received_msg_at` datetime default NULL,
  `sent_prepared_msg_at` datetime default NULL,
  `sent_other_msg_at` datetime default NULL,
  `status` tinyint(3) unsigned default NULL,
  `completed_flag` tinyint(3) unsigned default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  `delete_reason` varchar(255) default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `sent_dispensed_msg_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `drug_code` (`order_code`),
  KEY `user_id` (`user_id`),
  KEY `drug_store_id` (`store_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;

/*Table structure for table `photos` */

DROP TABLE IF EXISTS `photos`;

CREATE TABLE `photos` (
  `int` int(10) unsigned NOT NULL auto_increment,
  `photo_url` varchar(255) default NULL,
  `order_id` int(10) unsigned NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `file_type` varchar(64) default NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`int`),
  KEY `drug_order_id` (`order_id`),
  CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

/*Table structure for table `province_master` */

DROP TABLE IF EXISTS `province_master`;

CREATE TABLE `province_master` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `provinces` */

DROP TABLE IF EXISTS `provinces`;

CREATE TABLE `provinces` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(1024) default NULL,
  `key` varchar(255) default NULL,
  `value` varchar(1024) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  `create_staff_id` int(10) unsigned default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `staff_assign` */

DROP TABLE IF EXISTS `staff_assign`;

CREATE TABLE `staff_assign` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `staff_id` int(10) unsigned default NULL,
  `store_id` int(10) unsigned default NULL,
  `company_id` int(10) unsigned default NULL,
  `role` tinyint(3) unsigned NOT NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  `init_staff_id` int(10) unsigned default NULL,
  `init_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `store_id` (`store_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `staff_assign_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`id`),
  CONSTRAINT `staff_assign_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `staff_assign_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `staffs` */

DROP TABLE IF EXISTS `staffs`;

CREATE TABLE `staffs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `username` varchar(255) default NULL,
  `password` varchar(64) default NULL,
  `email` varchar(255) default NULL,
  `first_name` varchar(255) default NULL,
  `last_name` varchar(255) default NULL,
  `first_name_kana` varchar(255) default NULL,
  `last_name_kana` varchar(255) default NULL,
  `gender` tinyint(3) unsigned default NULL,
  `birthday` date default NULL,
  `postal_code` varchar(100) default NULL,
  `province` varchar(100) default NULL,
  `city1` varchar(100) default NULL,
  `city2` varchar(100) default NULL,
  `province_id` int(10) unsigned default NULL,
  `city_id` int(10) unsigned default NULL,
  `district_id` int(10) unsigned default NULL,
  `address` varchar(255) default NULL,
  `phone_number` varchar(100) default NULL,
  `is_cetified` tinyint(3) unsigned default NULL,
  `certificate_id` int(10) unsigned default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  `remember_token` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `staffs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `alias` varchar(64) default NULL COMMENT 'hash id',
  `internal_code` varchar(64) default NULL,
  `name` varchar(255) default NULL,
  `photo_url` varchar(255) default NULL,
  `postal_code` varchar(100) default NULL,
  `address` varchar(255) default NULL,
  `province_id` int(10) unsigned default NULL,
  `city_id` int(10) unsigned default NULL,
  `district_id` int(10) unsigned NOT NULL,
  `place` varchar(255) default NULL,
  `phone_number` varchar(100) default NULL,
  `fax_number` varchar(100) default NULL,
  `accept_credit_card` tinyint(3) unsigned default NULL COMMENT 'yes/no',
  `work_overtime` tinyint(4) default NULL,
  `overtime_alert` tinyint(3) unsigned default NULL,
  `allow_reply` tinyint(3) unsigned default NULL,
  `park_info` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `working_time` varchar(100) default NULL COMMENT 'json',
  `map_coordinates_lat` int(30) unsigned default NULL COMMENT '$latitude,$longitude',
  `map_coordinates_long` int(30) unsigned default NULL,
  `editable` tinyint(3) unsigned default NULL,
  `status` tinyint(3) unsigned default NULL,
  `created_employee_id` int(10) unsigned default NULL,
  `created_staff_id` int(10) unsigned default NULL,
  `created_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  `delete_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `drug_company_id` (`company_id`),
  KEY `district_id` (`district_id`),
  KEY `stores_ibfk_3` (`province_id`),
  KEY `stores_ibfk_4` (`city_id`),
  CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `stores_ibfk_2` FOREIGN KEY (`district_id`) REFERENCES `districts_master` (`id`),
  CONSTRAINT `stores_ibfk_3` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`),
  CONSTRAINT `stores_ibfk_4` FOREIGN KEY (`city_id`) REFERENCES `cities_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `stores_employees` */

DROP TABLE IF EXISTS `stores_employees`;

CREATE TABLE `stores_employees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `store_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `store_id` (`store_id`),
  KEY `employee_d` (`employee_id`),
  CONSTRAINT `stores_employees_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `stores_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `system_messages` */

DROP TABLE IF EXISTS `system_messages`;

CREATE TABLE `system_messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned default NULL,
  `alias` varchar(64) default NULL,
  `username` varchar(255) default NULL,
  `password` varchar(64) default NULL,
  `email` varchar(255) default NULL,
  `first_name` varchar(255) default NULL COMMENT 'Full width',
  `last_name` varchar(255) default NULL COMMENT 'Full width',
  `first_name_kana` varchar(255) default NULL,
  `last_name_kana` varchar(255) default NULL,
  `gender` tinyint(3) unsigned default NULL COMMENT '0=female,1= male, 2=others',
  `birthday` date default NULL,
  `postal_code` varchar(100) default NULL,
  `province` varchar(100) default NULL,
  `city1` varchar(100) default NULL,
  `city2` varchar(100) default NULL,
  `province_id` int(10) unsigned default NULL,
  `city_id` int(10) unsigned default NULL,
  `district_id` int(10) unsigned default NULL,
  `address` varchar(255) default NULL,
  `phone_number` varchar(100) default NULL,
  `register_token` varchar(64) default NULL,
  `register_token_expire` datetime default NULL,
  `keycode` varchar(256) default NULL,
  `status` tinyint(3) unsigned default NULL,
  `notification_enable` tinyint(3) unsigned default NULL,
  `drugbook_use` tinyint(3) unsigned default NULL,
  `drugbrand_change` tinyint(3) unsigned default NULL,
  `last_order_at` datetime default NULL,
  `last_order_id` int(10) unsigned NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `update_staff_id` int(10) unsigned default NULL,
  `deleted_at` datetime default NULL,
  `activated` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `city_id` (`city_id`),
  KEY `company_id` (`company_id`),
  KEY `district_id` (`district_id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`city_id`) REFERENCES `cities_master` (`id`),
  CONSTRAINT `users_ibfk_4` FOREIGN KEY (`district_id`) REFERENCES `districts_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

