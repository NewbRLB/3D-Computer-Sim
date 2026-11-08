<?php
require_once '../../api/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Password reset instructions have been simulated and sent to your email.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No account found with that email address.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>