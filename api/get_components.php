<?php
require_once 'db.php';
header('Content-Type: application/json');

$category = $_GET['category'] ?? '';

// Map frontend category keys to database tables and default icons
$tableMap = [
    'processors' => ['table' => 'processors', 'icon' => 'fa-microchip'], // Updated table name here
    'motherboard' => ['table' => 'motherboards', 'icon' => 'fa-server'],
    'coolers' => ['table' => 'coolers', 'icon' => 'fa-snowflake'],
    'ram' => ['table' => 'rams', 'icon' => 'fa-memory'],
    'gpus' => ['table' => 'gpus', 'icon' => 'fa-display'],
    'case' => ['table' => 'cases', 'icon' => 'fa-box'],
    'psus' => ['table' => 'psus', 'icon' => 'fa-plug'],
    'storages' => ['table' => 'storages', 'icon' => 'fa-hard-drive']
];

if (!array_key_exists($category, $tableMap)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category']);
    exit;
}

$info = $tableMap[$category];
$tableName = $info['table'];
$defaultIcon = $info['icon'];

try {
    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM $tableName WHERE is_active = 1 ORDER BY price ASC");
    $stmt->execute();
    $items = $stmt->fetchAll();

    // Attach the icon to each item for the frontend UI
    foreach ($items as &$item) {
        $item['icon'] = $defaultIcon;
    }

    echo json_encode(['success' => true, 'data' => $items]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>