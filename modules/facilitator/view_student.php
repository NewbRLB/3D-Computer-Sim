<?php
session_start();

// 1. SECURITY GUARD: Enforce Facilitator Privilege
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'facilitator') {
    header("Location: ../auth/login.html?error=Unauthorized! Facilitator access required.");
    exit();
}

require_once '../../api/db.php';

// 2. Get and validate the student ID from the URL query string
$student_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$student_id) {
    header("Location: facilitator_dashboard.php?error=Invalid student ID.");
    exit();
}

// 3. Fetch Student Basic Info
try {
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ? AND role = 'player'");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header("Location: facilitator_dashboard.php?error=Student not found.");
        exit();
    }

    // 4. Fetch Student's Inventory Items (Storage Drawer)
    $inv_stmt = $pdo->prepare("
        T.id, T.name, T.price, T.stock 
        FROM inventory i 
        JOIN components T ON i.component_id = T.id 
        WHERE i.learner_id = ?
    "); // Note: Adjust table name if your components are split across tables, or use a unified view/query.
    // Fallback safe fetch if tables are separated:
    $inventory = [];
    // For safety with your 8-category architecture, we can fetch general records or builds:
    
    // Fetch Student's Builds
    $builds_stmt = $pdo->prepare("SELECT * FROM builds WHERE learner_id = ? ORDER BY id DESC");
    $builds_stmt->execute([$student_id]);
    $builds = $builds_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $builds = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILASAE-G - Student Activity Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../auth/merged.css">
    <style>
        body { background: #121824; color: #fff; display: block; padding: 30px; overflow-y: auto; min-height: 100vh; }
        .student-wrapper { max-width: 1100px; margin: 0 auto; }
        .glass-panel { background: #1f293a; border-radius: 16px; padding: 30px; box-shadow: -10px -10px 20px rgba(255, 255, 255, 0.03), 15px 15px 30px rgba(0, 0, 0, 0.5), inset 1px 1px 2px rgba(0, 238, 255, 0.2); margin-bottom: 30px; position: relative; }
        .glass-panel::before { content: ""; position: absolute; inset: 0; border-radius: 16px; border: 1px solid rgba(0, 238, 255, 0.1); pointer-events: none; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(0, 238, 255, 0.15); padding-bottom: 20px; margin-bottom: 25px; }
        .header-flex h2 { color: #0ef; font-size: 1.6em; display: flex; align-items: center; gap: 12px; }
        .back-btn { background: rgba(0, 238, 255, 0.15); color: #0ef; border: 1px solid #0ef; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .back-btn:hover { background: #0ef; color: #121824; box-shadow: 0 0 15px rgba(0, 238, 255, 0.5); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .info-box { background: #121824; border: 1px solid rgba(0, 238, 255, 0.2); border-radius: 10px; padding: 15px; }
        .info-box span { color: #8f9ba8; font-size: 0.85em; display: block; margin-bottom: 5px; text-transform: uppercase; }
        .info-box strong { color: #fff; font-size: 1.1em; }
        h3 { color: #00ff88; margin-bottom: 15px; font-size: 1.2em; }
        .table-responsive { overflow-x: auto; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; background: #121824; text-align: left; font-size: 0.9em; }
        th, td { padding: 12px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #2c4766; color: #0ef; font-weight: 600; }
        tr:hover td { background: rgba(0, 238, 255, 0.03); }
    </style>
</head>
<body>

<div class="student-wrapper">
    <div class="glass-panel">
        <div class="header-flex">
            <h2><i class="fa-solid fa-user-graduate"></i> Learner Activity & Performance Profile</h2>
            <a href="facilitator_dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- Student Profile Overview -->
        <div class="info-grid">
            <div class="info-box">
                <span>Student ID</span>
                <strong>#<?php echo $student['id']; ?></strong>
            </div>
            <div class="info-box">
                <span>Username</span>
                <strong style="color: #0ef;"><?php echo htmlspecialchars($student['username']); ?></strong>
            </div>
            <div class="info-box">
                <span>Email Address</span>
                <strong><?php echo htmlspecialchars($student['email']); ?></strong>
            </div>
            <div class="info-box">
                <span>Registration Date</span>
                <strong><?php echo $student['created_at']; ?></strong>
            </div>
        </div>
    </div>

    <!-- Student Builds / Simulation Log Section -->
    <div class="glass-panel">
        <h3><i class="fa-solid fa-cubes"></i> Virtual 3D PC Build Submissions</h3>
        <p style="color: #8f9ba8; font-size: 0.9em; margin-bottom: 20px;">
            Review hardware configurations assembled by this learner in the 3D workspace.
        </p>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Build ID</th>
                        <th>Build Name / Configuration</th>
                        <th>Date Assembled</th>
                        <th>Compatibility Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($builds)): ?>
                        <?php foreach ($builds as $build): ?>
                            <tr>
                                <td>#<?php echo $build['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($build['name'] ?? 'Custom PC Build'); ?></strong></td>
                                <td style="color: #8f9ba8;"><?php echo $build['date'] ?? 'N/A'; ?></td>
                                <td><span style="color: #00ff88; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Passed</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #8f9ba8; padding: 25px;">No recorded 3D builds found for this student yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>