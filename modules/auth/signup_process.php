<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    $db_path = '../../api/db.php';
    if (!file_exists($db_path)) {
        throw new Exception("Database configuration file not found at: " . realpath(__DIR__ . '/../../api/db.php'));
    }
    
    require_once $db_path;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Username or Email already exists.']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Public signups default to 'player' role
        $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'player')");
        $insertStmt->execute([$username, $email, $hashedPassword]);

        echo json_encode(['success' => true, 'message' => 'Account created successfully! Please log in.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}
?>