<?php
session_start();
require_once '../../api/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    try {
        // Look up user by email only
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // Retrieved from DB ('player', 'facilitator', or 'admin')

            // ==========================================
            // CORRECTED LEARNER REDIRECT:
            // Points to the learner dashboard hub instead of the simulator directly
            // ==========================================
            $redirectUrl = '../menu/dashboard.html'; 
            
            if ($user['role'] === 'facilitator') {
                $redirectUrl = '../facilitator/facilitator_dashboard.php';
            } elseif ($user['role'] === 'admin') {
                $redirectUrl = '../admin/admin_dashboard.php';
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful!',
                'redirect' => $redirectUrl,
                'role' => $user['role']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>