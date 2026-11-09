<?php
// Replace these placeholder strings with your actual Clever Cloud credentials
$host = 'boun4onq59mye73e1puj-mysql.services.clever-cloud.com';         // e.g., u78x...-mysql.services.clever-cloud.com
$db   = 'boun4onq59mye73e1puj';   // e.g., bl8x...
$user = 'u6h2zfj3z4scdyvu';            // e.g., u78x...
$pass = 'Y5iA7mxa8vn1nXF23I5eD';        // Your long secret password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>
