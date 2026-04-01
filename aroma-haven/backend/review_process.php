<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/util.php';

function review_process(array $post): string
{
    global $auth;

    if (!$auth->isLogged()) {
        return 'Please log in to submit a review.';
    }

    $productId = (int) ($post['product_id'] ?? 0);
    $rating    = (int) ($post['rating'] ?? 0);
    $body      = trim($post['body'] ?? '');

    if ($productId <= 0) {
        return 'Invalid product.';
    }
    if ($rating < 1 || $rating > 5) {
        return 'Please select a rating between 1 and 5 stars.';
    }
    if (strlen($body) < 5) {
        return 'Review must be at least 5 characters.';
    }
    if (strlen($body) > 1000) {
        return 'Review must be under 1000 characters.';
    }

    $userId = (int) $auth->getCurrentUID();
    $conn   = connect_db();

    $stmt = $conn->prepare(
        "SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ? LIMIT 1"
    );
    $stmt->execute([$productId, $userId]);
    if ($stmt->fetch()) {
        return 'You have already reviewed this product.';
    }

    $stmt = $conn->prepare(
        "INSERT INTO product_reviews (product_id, user_id, rating, body) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$productId, $userId, $rating, $body]);

    return '';
}

function review_update(array $post): string
{
    global $auth;

    if (!$auth->isLogged()) {
        return 'Please log in to update a review.';
    }

    $productId = (int) ($post['product_id'] ?? 0);
    $rating    = (int) ($post['rating'] ?? 0);
    $body      = trim($post['body'] ?? '');

    if ($productId <= 0) {
        return 'Invalid product.';
    }
    if ($rating < 1 || $rating > 5) {
        return 'Please select a rating between 1 and 5 stars.';
    }
    if (strlen($body) < 5) {
        return 'Review must be at least 5 characters.';
    }
    if (strlen($body) > 1000) {
        return 'Review must be under 1000 characters.';
    }

    $userId = (int) $auth->getCurrentUID();
    $conn   = connect_db();

    $check = $conn->prepare(
        "SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ? LIMIT 1"
    );
    $check->execute([$productId, $userId]);
    if (!$check->fetch()) {
        return 'No existing review found to update.';
    }

    $stmt = $conn->prepare(
        "UPDATE product_reviews SET rating = ?, body = ? WHERE product_id = ? AND user_id = ?"
    );
    $stmt->execute([$rating, $body, $productId, $userId]);

    return '';
}