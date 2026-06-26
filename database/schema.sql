-- ============================================================
--  Ibadan Summer Innovation Camp 2026 — Database Schema
--  Run this once in phpMyAdmin → SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS `ibadan_camp`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `ibadan_camp`;

-- ============================================================
--  Table: registrations
-- ============================================================
CREATE TABLE IF NOT EXISTS `registrations` (
    `id`                 INT UNSIGNED   NOT NULL AUTO_INCREMENT,

    -- Student
    `first_name`         VARCHAR(100)   NOT NULL,
    `last_name`          VARCHAR(100)   NOT NULL,
    `other_name`         VARCHAR(100)   DEFAULT NULL,
    `gender`             ENUM('Male','Female') NOT NULL,
    `date_of_birth`      DATE           NOT NULL,
    `age`                TINYINT UNSIGNED NOT NULL,
    `school`             VARCHAR(255)   NOT NULL,
    `class_grade`        VARCHAR(100)   NOT NULL,
    `address`            TEXT           NOT NULL,

    -- Parent / Guardian
    `parent_name`        VARCHAR(255)   NOT NULL,
    `relationship`       VARCHAR(50)    NOT NULL,
    `phone`              VARCHAR(50)    NOT NULL,
    `alt_phone`          VARCHAR(50)    DEFAULT NULL,
    `email`              VARCHAR(255)   NOT NULL,
    `parent_address`     TEXT           NOT NULL,

    -- Camp Participation
    `learning_track`     VARCHAR(100)   NOT NULL,
    `courses`            TEXT           NOT NULL,

    -- Medical
    `medical_condition`      TEXT           DEFAULT NULL,
    `allergies`              TEXT           DEFAULT NULL,
    `emergency_contact`      VARCHAR(255)   NOT NULL,
    `emergency_phone`        VARCHAR(50)    NOT NULL,
    `emergency_relationship` VARCHAR(100)   DEFAULT NULL,

    -- Package & Payment
    `package`            VARCHAR(50)    NOT NULL,
    `number_of_children` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `amount_to_pay`      INT UNSIGNED   DEFAULT NULL,

    -- Admin
    `status`             ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `admin_notes`        TEXT           DEFAULT NULL,
    `created_at`         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_email`   (`email`),
    INDEX `idx_package` (`package`),
    INDEX `idx_status`  (`status`),
    INDEX `idx_track`   (`learning_track`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Migration: run these ALTER statements if you already
--  created the table without the two new columns
-- ============================================================
-- ALTER TABLE `registrations`
--     ADD COLUMN `emergency_relationship` VARCHAR(100) DEFAULT NULL AFTER `emergency_phone`,
--     ADD COLUMN `amount_to_pay` INT UNSIGNED DEFAULT NULL AFTER `number_of_children`;

-- ============================================================
--  Table: contact_messages
-- ============================================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255) NOT NULL,
    `email`      VARCHAR(255) NOT NULL,
    `phone`      VARCHAR(50)  NOT NULL,
    `subject`    VARCHAR(255) NOT NULL DEFAULT 'General Enquiry',
    `message`    TEXT         NOT NULL,
    `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_is_read`  (`is_read`),
    INDEX `idx_created`  (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
