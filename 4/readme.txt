$sql = "CREATE TABLE `app_rhea`.`rhea_20130614` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `x` DOUBLE NOT NULL, `y` DOUBLE NOT NULL, `type` TINYINT(4) NOT NULL, `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE = MyISAM;";


$sql = "INSERT INTO `app_rhea`.`rhea_20130614` (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, \'112.36\', \'31.95\', \'0\', \'2013-06-14 10:47:26\');";


$sql = "INSERT INTO `app_rhea`.`rhea_20130614` (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, \'121.49\', \'31.190656\', \'0\', CURRENT_TIMESTAMP);";