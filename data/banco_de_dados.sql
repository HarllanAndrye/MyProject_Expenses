-- MySQL Script generated by MySQL Workbench
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema expensesMonthly_2
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `expensesMonthly_2` DEFAULT CHARACTER SET utf8 ;
USE `expensesMonthly_2` ;

-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` CHAR(128) NOT NULL,
  `salt` CHAR(128) NOT NULL,
  `role_id` INT NOT NULL,
  `status` INT(1) NOT NULL DEFAULT 1,
  `image` VARCHAR(40) NULL,
  PRIMARY KEY (`id`),
  INDEX `users_role_id_idx` (`role_id` ASC),
  CONSTRAINT `users_role_id`
    FOREIGN KEY (`role_id`)
    REFERENCES `expensesMonthly_2`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`login_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`login_attempts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `user_id`),
  INDEX `login_attempts_user_id_idx` (`user_id` ASC),
  CONSTRAINT `login_attempts_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expensesMonthly_2`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`recoveries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`recoveries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `status` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `user_id`),
  INDEX `passwords_user_id_idx` (`user_id` ASC),
  CONSTRAINT `recoveries_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expensesMonthly_2`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`group_expenses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`group_expenses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`monthly_expenses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`monthly_expenses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(10,0) NOT NULL,
  `date` DATE NOT NULL DEFAULT CURRENT_DATE,
  `description` VARCHAR(100) NOT NULL,
  `group` VARCHAR(45) NOT NULL,
  `group_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `monthly_expenses_user_id_idx` (`user_id` ASC),
  INDEX `monthly_expenses_group_expenses_idx` (`group_id` ASC),
  CONSTRAINT `monthly_expenses_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expensesMonthly_2`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `monthly_expenses_group_expenses`
    FOREIGN KEY (`group_id`)
    REFERENCES `expensesMonthly_2`.`group_expenses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `expensesMonthly_2`.`income_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expensesMonthly_2`.`income_users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(10,0) NOT NULL,
  `additional` DECIMAL(10,0) NULL,
  `month` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `income_users_users_idx` (`user_id` ASC),
  CONSTRAINT `income_users_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expensesMonthly_2`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

