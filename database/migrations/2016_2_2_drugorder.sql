
DROP TABLE IF EXISTS `access_logs`;

CREATE TABLE `access_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `IP` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourcetype` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acount_id` int(10) unsigned DEFAULT NULL,
  `access_data_type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_data_id` int(10) unsigned DEFAULT NULL,
  `is_auto_login` tinyint(3) unsigned DEFAULT NULL,
  `access_function` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_action` int(10) unsigned DEFAULT NULL,
  `access_result` tinyint(3) unsigned DEFAULT NULL,
  `access_data_debug` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `certificates` */

DROP TABLE IF EXISTS `certificates`;

CREATE TABLE `certificates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(40) DEFAULT NULL,
  `ssl_client_s_dn_cn` varchar(255) DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `client_private_key` text,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `init_at` datetime DEFAULT NULL,
  `create_staff_id` int(10) unsigned DEFAULT NULL,
  `client_certificates` text,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `export_password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  UNIQUE KEY `ssl_client_s_dn_cn` (`ssl_client_s_dn_cn`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `certificates_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `certificates_ibfk_4` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `certificates_employees` */

DROP TABLE IF EXISTS `certificates_employees`;

CREATE TABLE `certificates_employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `certificate_id` int(11) unsigned NOT NULL,
  `employee_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `certificate_id` (`certificate_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `certificates_employees_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `cetificates` (`id`),
  CONSTRAINT `certificates_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cities_master` */

DROP TABLE IF EXISTS `cities_master`;

CREATE TABLE `cities_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `province_id` int(10) unsigned NOT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `cities_master_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2409 DEFAULT CHARSET=utf8;

/*Table structure for table `companies` */

DROP TABLE IF EXISTS `companies`;

CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `group_admin_id` int(10) unsigned DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `debug_logs` */

DROP TABLE IF EXISTS `debug_logs`;

CREATE TABLE `debug_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `browser` varchar(64) DEFAULT NULL,
  `http_method` varchar(10) DEFAULT NULL,
  `controller` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `parameters` text,
  `response` text,
  `user_agent` varchar(255) DEFAULT NULL,
  `client_ip_address` varchar(64) DEFAULT NULL,
  `client_platform` varchar(64) DEFAULT NULL,
  `exception` varchar(255) DEFAULT NULL,
  `stack_trace` text,
  `message` text,
  `event_type` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2373 DEFAULT CHARSET=utf8;

/*Table structure for table `devices` */

DROP TABLE IF EXISTS `devices`;

CREATE TABLE `devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_code` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `version` varchar(64) DEFAULT NULL,
  `notification_token` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

/*Table structure for table `employees` */

DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(100) DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `role` tinyint(10) unsigned NOT NULL COMMENT '1=store_staff,2=company_staff,3=system_manager',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drug_company_id` (`company_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `login_logs` */

DROP TABLE IF EXISTS `login_logs`;

CREATE TABLE `login_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `IP` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sourcetype` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `input_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `login_result` tinyint(3) unsigned DEFAULT NULL,
  `acount_type` int(10) unsigned NOT NULL,
  `acount_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `mediaid_staffs` */

DROP TABLE IF EXISTS `mediaid_staffs`;

CREATE TABLE `mediaid_staffs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name_kana` varchar(255) DEFAULT NULL,
  `last_name_kana` varchar(255) DEFAULT NULL,
  `gender` tinyint(3) unsigned DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `postal_code` varchar(100) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `city1` varchar(255) DEFAULT NULL,
  `city2` varchar(255) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(64) DEFAULT NULL,
  `is_cetified` tinyint(3) unsigned DEFAULT NULL,
  `certificate_id` int(10) unsigned DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(64) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `message_templates` */

DROP TABLE IF EXISTS `message_templates`;

CREATE TABLE `message_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(40) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `company_id` int(11) unsigned DEFAULT NULL,
  `message_type` int(10) unsigned NOT NULL,
  `type` tinyint(4) unsigned DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL COMMENT '"０：適用中\n１：未使用\n２：仮登録\n３：削除"',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `drug_store_id` (`store_id`),
  CONSTRAINT `message_templates_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;

/*Table structure for table `message_type` */

DROP TABLE IF EXISTS `message_type`;

CREATE TABLE `message_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `target` tinyint(3) unsigned DEFAULT NULL,
  `seen_at` datetime DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL COMMENT '1=received,2=dispensed,3=others',
  `template_id` int(10) unsigned DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drug_order_id` (`order_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1245 DEFAULT CHARSET=utf8;

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '2=cancel,1=pushed,0=waiting',
  `pushed_at` datetime DEFAULT NULL,
  `target` tinyint(3) unsigned NOT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_access_token_winapps` */

DROP TABLE IF EXISTS `oauth_access_token_winapps`;

CREATE TABLE `oauth_access_token_winapps` (
  `access_token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `oauth_access_tokens` */

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` datetime NOT NULL,
  `scope` varchar(2000) DEFAULT NULL,
  `app_keycode` varchar(256) DEFAULT NULL,
  `device_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_authorization_codes` */

DROP TABLE IF EXISTS `oauth_authorization_codes`;

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_clients` */

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_jwt` */

DROP TABLE IF EXISTS `oauth_jwt`;

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_refresh_tokens` */

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_scopes` */

DROP TABLE IF EXISTS `oauth_scopes`;

CREATE TABLE `oauth_scopes` (
  `scope` text,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `oauth_users` */

DROP TABLE IF EXISTS `oauth_users`;

CREATE TABLE `oauth_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `order_transaction` */

DROP TABLE IF EXISTS `order_transaction`;

CREATE TABLE `order_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `popup_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `print_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `order_transaction_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `order_transaction_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=556 DEFAULT CHARSET=utf8;

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `visit_at` datetime DEFAULT NULL,
  `visit_at_string` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `sent_received_msg_at` datetime DEFAULT NULL,
  `sent_prepared_msg_at` datetime DEFAULT NULL,
  `sent_other_msg_at` datetime DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT '0',
  `completed_flag` tinyint(3) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `delete_reason` varchar(255) DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `sent_dispensed_msg_at` datetime DEFAULT NULL,
  `drugbook_use` tinyint(3) unsigned DEFAULT NULL,
  `drugbrand_change` tinyint(3) unsigned DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_code` (`order_code`),
  UNIQUE KEY `alias` (`alias`),
  KEY `user_id` (`user_id`),
  KEY `drug_store_id` (`store_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=556 DEFAULT CHARSET=utf8;

/*Table structure for table `photos` */

DROP TABLE IF EXISTS `photos`;

CREATE TABLE `photos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photo_url` varchar(255) DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `file_type` varchar(64) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drug_order_id` (`order_id`),
  CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1452 DEFAULT CHARSET=utf8;

/*Table structure for table `province_master` */

DROP TABLE IF EXISTS `province_master`;

CREATE TABLE `province_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `key` varchar(64) DEFAULT NULL,
  `value` varchar(1024) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `create_staff_id` int(10) unsigned DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`key`,`company_id`,`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `staffs` */

DROP TABLE IF EXISTS `staffs`;

CREATE TABLE `staffs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(40) DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name_kana` varchar(255) DEFAULT NULL,
  `last_name_kana` varchar(255) DEFAULT NULL,
  `gender` tinyint(3) unsigned DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `postal_code` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city1` varchar(100) DEFAULT NULL,
  `city2` varchar(100) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `is_cetified` tinyint(3) unsigned DEFAULT NULL,
  `certificate_id` int(10) unsigned DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `job_category` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `staffs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `alias` varchar(64) DEFAULT NULL COMMENT 'hash id',
  `internal_code` varchar(64) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `postal_code` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city1` varchar(100) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `fax_number` varchar(100) DEFAULT NULL,
  `accept_credit_card` tinyint(3) unsigned DEFAULT NULL COMMENT 'yes/no',
  `park_info` varchar(100) DEFAULT NULL,
  `overtime_alert` tinyint(3) unsigned DEFAULT NULL,
  `allow_reply` tinyint(3) unsigned DEFAULT NULL,
  `map_coordinates_lat` float(17,10) unsigned DEFAULT NULL COMMENT '$latitude,$longitude',
  `description` varchar(255) DEFAULT NULL,
  `working_time` text COMMENT 'json',
  `work_overtime` tinyint(4) DEFAULT NULL,
  `map_coordinates_long` decimal(17,10) unsigned DEFAULT NULL,
  `editable` tinyint(3) unsigned DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `created_staff_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `delete_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `drug_company_id` (`company_id`),
  KEY `stores_ibfk_3` (`province`),
  KEY `stores_ibfk_4` (`city1`),
  CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `stores_employees` */

DROP TABLE IF EXISTS `stores_employees`;

CREATE TABLE `stores_employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `employee_d` (`employee_id`),
  CONSTRAINT `stores_employees_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `stores_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `system_messages` */

DROP TABLE IF EXISTS `system_messages`;

CREATE TABLE `system_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `alias` varchar(64) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL COMMENT 'Full width',
  `last_name` varchar(255) DEFAULT NULL COMMENT 'Full width',
  `first_name_kana` varchar(255) DEFAULT NULL,
  `last_name_kana` varchar(255) DEFAULT NULL,
  `gender` tinyint(3) DEFAULT NULL COMMENT '0=female,1= male, 2=others',
  `birthday` date DEFAULT NULL,
  `postal_code` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city1` varchar(100) DEFAULT NULL,
  `city2` varchar(100) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `register_token` varchar(64) DEFAULT NULL,
  `register_token_expire` datetime DEFAULT NULL,
  `keycode` varchar(256) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `notification_enable` tinyint(3) unsigned DEFAULT NULL,
  `drugbook_use` tinyint(3) unsigned DEFAULT NULL,
  `drugbrand_change` tinyint(3) unsigned DEFAULT NULL,
  `last_order_at` datetime DEFAULT NULL,
  `last_order_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `update_staff_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `activated` tinyint(3) unsigned DEFAULT NULL,
  `accept_saleinfo` tinyint(3) unsigned DEFAULT NULL,
  `accept_saleinfo_dm` tinyint(3) unsigned DEFAULT NULL,
  `reset_pass_token` varchar(64) DEFAULT NULL,
  `reset_pass_token_expire` datetime DEFAULT NULL,
  `exited_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`),
  KEY `company_id` (`company_id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`province_id`) REFERENCES `province_master` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`city_id`) REFERENCES `cities_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=646 DEFAULT CHARSET=utf8;

/* Trigger structure for table `certificates` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `certificates` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `certificates` BEFORE INSERT ON `certificates` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `companies` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `companies` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `companies` BEFORE INSERT ON `companies` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `message_templates` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `message_templates` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `message_templates` BEFORE INSERT ON `message_templates` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `orders` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `orders` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `orders` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `staffs` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `staffs` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `staffs` BEFORE INSERT ON `staffs` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `stores` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `stores` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `stores` BEFORE INSERT ON `stores` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;

/* Trigger structure for table `users` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `users` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `users` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
	SET NEW.`alias`=SHA1(UUID());
    END */$$


DELIMITER ;


