-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema yahtzee
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema yahtzee
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `yahtzee` DEFAULT CHARACTER SET utf8 ;
USE `yahtzee` ;

-- -----------------------------------------------------
-- Table `yahtzee`.`juego`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `yahtzee`.`juego` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jugador` VARCHAR(45) NULL DEFAULT NULL,
  `lanzamientos` INT(11) NULL DEFAULT NULL,
  `dado1` INT(11) NULL DEFAULT NULL,
  `dado2` INT(11) NULL DEFAULT NULL,
  `dado3` INT(11) NULL DEFAULT NULL,
  `dado4` INT(11) NULL DEFAULT NULL,
  `dado5` INT(11) NULL DEFAULT NULL,
  `jugada1` INT(11) NULL DEFAULT NULL,
  `jugada2` INT(11) NULL DEFAULT NULL,
  `jugada3` INT(11) NULL DEFAULT NULL,
  `jugada4` INT(11) NULL DEFAULT NULL,
  `jugada5` INT(11) NULL DEFAULT NULL,
  `jugada6` INT(11) NULL DEFAULT NULL,
  `jugada7` INT(11) NULL DEFAULT NULL,
  `jugada8` INT(11) NULL DEFAULT NULL,
  `jugada9` INT(11) NULL DEFAULT NULL,
  `jugada10` INT(11) NULL DEFAULT NULL,
  `jugada11` INT(11) NULL DEFAULT NULL,
  `jugada12` INT(11) NULL DEFAULT NULL,
  `jugada13` INT(11) NULL DEFAULT NULL,
  `total` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
