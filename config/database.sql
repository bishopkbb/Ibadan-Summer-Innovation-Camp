-- =====================================================
-- Ibadan Summer Innovation Camp 2026
-- Database: ibadan_camp
-- Run this SQL in cPanel > phpMyAdmin after creating
-- a new database named: ibadan_camp
-- =====================================================

CREATE DATABASE IF NOT EXISTS `ibadan_camp`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ibadan_camp`;

-- =====================================================
-- Table: registrations
-- =====================================================
CREATE TABLE IF NOT EXISTS `registrations` (
    `id`                INT AUTO_INCREMENT PRIMARY KEY,

    -- Student Information
    `first_name`        VARCHAR(100)  NOT NULL,
    `last_name`         VARCHAR(100)  NOT NULL,
    `other_name`        VARCHAR(100)  DEFAULT NULL,
    `gender`            VARCHAR(20)   NOT NULL,
    `date_of_birth`     DATE          NOT NULL,
    `age`               INT           NOT NULL,
    `school`            VARCHAR(255)  NOT NULL,
    `class_grade`       VARCHAR(100)  NOT NULL,
    `address`           TEXT          NOT NULL,

    -- Parent / Guardian Information
    `parent_name`       VARCHAR(255)  NOT NULL,
    `relationship`      VARCHAR(100)  NOT NULL,
    `phone`             VARCHAR(50)   NOT NULL,
    `alt_phone`         VARCHAR(50)   DEFAULT NULL,
    `email`             VARCHAR(255)  NOT NULL,
    `parent_address`    TEXT          NOT NULL,

    -- Camp Participation
    `learning_track`    VARCHAR(100)  NOT NULL,
    `courses`           TEXT          DEFAULT NULL,

    -- Medical Information
    `medical_condition` TEXT          DEFAULT NULL,
    `allergies`         TEXT          DEFAULT NULL,
    `emergency_contact` VARCHAR(255)  NOT NULL,
    `emergency_phone`   VARCHAR(50)   NOT NULL,

    -- Package & Payment
    `package`           VARCHAR(100)  NOT NULL,
    `number_of_children` INT          DEFAULT 1,
    `payment_status`    ENUM('Pending','Confirmed','Cancelled') DEFAULT 'Pending',

    -- Timestamps
    `created_at`        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX `idx_email` (`email`),
    INDEX `idx_package` (`package`),
    INDEX `idx_created` (`created_at`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: contact_messages
-- =====================================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(255) NOT NULL,
    `email`      VARCHAR(255) NOT NULL,
    `phone`      VARCHAR(50)  DEFAULT NULL,
    `subject`    VARCHAR(255) DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `is_read`    TINYINT(1)   DEFAULT 0,
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_read`  (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample check: view all registrations
-- =====================================================
-- SELECT id, first_name, last_name, email, package, payment_status, created_at
-- FROM registrations ORDER BY created_at DESC;
