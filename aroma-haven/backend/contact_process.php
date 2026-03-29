<?php
require_once __DIR__ . '/util.php';

function contact_ensure_table(PDO $conn): void
{
    $conn->exec("
        CREATE TABLE IF NOT EXISTS `contact_submissions` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `topic` VARCHAR(50) NOT NULL,
            `message` TEXT NOT NULL,
            `status` ENUM('new', 'in_progress', 'resolved') NOT NULL DEFAULT 'new',
            `internal_note` TEXT DEFAULT NULL,
            `replied_at` DATETIME DEFAULT NULL,
            `replied_by` INT DEFAULT NULL,
            `submitted_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_contact_submissions_status` (`status`),
            KEY `idx_contact_submissions_replied_by` (`replied_by`),
            CONSTRAINT `fk_contact_submissions_replied_by`
              FOREIGN KEY (`replied_by`) REFERENCES `phpauth_users` (`id`)
              ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
    ");
}

function contact_process(array $data): string
{
    $allowedTopics = ['beans', 'brew', 'order', 'other'];

    $name    = sanitize_input($data['name']    ?? '');
    $email   = sanitize_input($data['email']   ?? '');
    $topic   = sanitize_input($data['topic']   ?? '');
    $message = sanitize_input($data['message'] ?? '');
    $consent = ($data['consent'] ?? '') === 'yes';

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Full name is required.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Full name must be 100 characters or fewer.';
    }

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email address must be 100 characters or fewer.';
    }

    if (!in_array($topic, $allowedTopics, true)) {
        $errors[] = 'Please select a valid topic.';
    }

    if (empty($message)) {
        $errors[] = 'Message is required.';
    } elseif (strlen($message) > 5000) {
        $errors[] = 'Message must be 5,000 characters or fewer.';
    }

    if (!$consent) {
        $errors[] = 'You must agree to be contacted before submitting.';
    }

    if (!empty($errors)) {
        return implode('<br>', $errors);
    }

    try {
        $conn = connect_db();
        contact_ensure_table($conn);

        $stmt = $conn->prepare(
            'INSERT INTO contact_submissions (name, email, topic, message) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $topic, $message]);

        return '';

    } catch (PDOException $e) {
        return 'Your message could not be saved. Please try again later.';
    }
}
