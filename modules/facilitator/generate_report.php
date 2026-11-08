<?php
session_start();

// 1. SECURITY GUARD: Enforce Facilitator Privilege
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'facilitator') {
    header("Location: ../auth/login.html?error=Unauthorized! Facilitator access required.");
    exit();
}

require_once '../../api/db.php';

// 2. Set headers to force a CSV file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ILASAE-G_Student_Performance_Report_' . date('Y-m-d') . '.csv');

// 3. Open output stream
$output = fopen('php://output', 'w');

// 4. Write CSV Column Headers
fputcsv($output, ['Learner ID', 'Username', 'Email Address', 'Date Registered', 'System Role', 'Account Status']);

try {
    // 5. Fetch all registered players/learners from database
    $stmt = $pdo->query("SELECT id, username, email, created_at, role FROM users WHERE role = 'player' ORDER BY id DESC");
    $learners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Loop through and write each learner row into the CSV file
    foreach ($learners as $learner) {
        fputcsv($output, [
            '#' . $learner['id'],
            $learner['username'],
            $learner['email'],
            $learner['created_at'],
            ucfirst($learner['role']),
            'Active'
        ]);
    }
} catch (PDOException $e) {
    fputcsv($output, ['Database Error: Could not retrieve learner records.']);
}

fclose($output);
exit();
?>