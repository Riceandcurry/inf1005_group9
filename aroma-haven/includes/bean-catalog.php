<?php

require_once __DIR__ . '/../backend/util.php';

function ah_row_to_bean(array $row): array
{
    $roastMap = [
        'Light'  => 'Light roast',
        'Medium' => 'Medium roast',
        'Dark'   => 'Dark roast',
    ];

    $tags = [];
    if (!empty($row['tasting_notes'])) {
        $decoded = json_decode($row['tasting_notes'], true);
        if (is_array($decoded)) {
            $tags = $decoded;
        }
    }

    return [
        'id'          => (int) $row['id'],
        'slug'        => $row['slug'] ?? '',
        'name'        => $row['name'],
        'origin'      => $row['origin'],
        'price'       => '$' . number_format((float) $row['price'], 0),
        'price_raw'   => (float) $row['price'],
        'roast'       => $roastMap[$row['roast_level']] ?? $row['roast_level'],
        'image'       => $row['image'] ?? './images/products/product_1.jpg',
        'tags'        => $tags,
        'description' => $row['description'] ?? '',
        'process'     => $row['process'] ?? '',
        'altitude'    => $row['altitude'] ?? '',
    ];
}

function ah_get_bean_catalog(): array
{
    $conn = connect_db();
    $stmt = $conn->query(
        "SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC"
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $catalog = [];
    foreach ($rows as $row) {
        $bean = ah_row_to_bean($row);
        $catalog[$bean['id']] = $bean;
    }

    return $catalog;
}

function ah_get_bean_by_id(int $id): ?array
{
    $conn = connect_db();
    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    return ah_row_to_bean($row);
}

function ah_get_home_beans(): array
{
    $conn = connect_db();
    $stmt = $conn->query(
        "SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC LIMIT 3"
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map('ah_row_to_bean', $rows);
}
