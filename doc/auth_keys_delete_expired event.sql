SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS `rcse`.`auth_keys_delete_expired`
ON SCHEDULE EVERY 10 MINUTE
COMMENT 'Checks for and removes expired auth keys'
DO
	DELETE FROM `rcse`.`auth_keys` WHERE `key_expires`<=NOW()
