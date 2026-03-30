-- Optional table for persisting admin email reply attempts.
-- Safe to run multiple times.

CREATE TABLE IF NOT EXISTS `contact_replies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `submission_id` INT NOT NULL,
  `replied_by` INT DEFAULT NULL,
  `reply_subject` VARCHAR(255) NOT NULL,
  `reply_body` MEDIUMTEXT NOT NULL,
  `sent_success` TINYINT(1) NOT NULL DEFAULT 0,
  `error_message` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_replies_submission` (`submission_id`),
  KEY `idx_contact_replies_replied_by` (`replied_by`),
  CONSTRAINT `fk_contact_replies_submission`
    FOREIGN KEY (`submission_id`) REFERENCES `contact_submissions` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_contact_replies_replied_by`
    FOREIGN KEY (`replied_by`) REFERENCES `phpauth_users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
