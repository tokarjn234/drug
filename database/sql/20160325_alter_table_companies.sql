ALTER TABLE `companies`
ADD COLUMN `key_push_android` VARCHAR(255) AFTER `cert_add`,
ADD COLUMN `key_push_ios` VARCHAR(255) AFTER `key_push_android`;