ALTER TABLE `certificates`
DROP FOREIGN KEY certificates_ibfk_3

ALTER TABLE `certificates`
MODIFY COLUMN  company_id INT(10) UNSIGNED DEFAULT NULL