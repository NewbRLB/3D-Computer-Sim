<?php
session_start();

// 1. SECURITY GUARD: Enforce Least Privilege (Facilitator Only)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'facilitator') {
    header("Location: ../auth/login.html?error=Unauthorized! Facilitator access required.");
    exit();
}

// 2. Include Database Connection
require_once '../../api/db.php';

// 3. Fetch Registered Learners and their stats
try {
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users WHERE role = 'player' ORDER BY id DESC");
    $learners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $learners = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILASAE-G - Facilitator Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../auth/merged.css">
    <style>
        body { background: #121824; color: #fff; display: block; padding: 30px; overflow-y: auto; min-height: 100vh; }
        .facilitator-wrapper { max-width: 1200px; margin: 0 auto; }
        .glass-panel { background: #1f293a; border-radius: 16px; padding: 30px; box-shadow: -10px -10px 20px rgba(255, 255, 255, 0.03), 15px 15px 30px rgba(0, 0, 0, 0.5), inset 1px 1px 2px rgba(0, 238, 255, 0.2); margin-bottom: 30px; position: relative; }
        .glass-panel::before { content: ""; position: absolute; inset: 0; border-radius: 16px; border: 1px solid rgba(0, 238, 255, 0.1); pointer-events: none; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(0, 238, 255, 0.15); padding-bottom: 20px; margin-bottom: 25px; }
        .header-flex h2 { color: #0ef; font-size: 1.8em; display: flex; align-items: center; gap: 12px; }
        .logout-btn { background: rgba(255, 77, 77, 0.15); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .logout-btn:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 15px rgba(255, 77, 77, 0.5); }
        h3 { color: #00ff88; margin-bottom: 15px; font-size: 1.2em; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #121824; border: 1px solid rgba(0, 238, 255, 0.2); border-radius: 12px; padding: 20px; text-align: center; box-shadow: inset 3px 3px 6px rgba(0,0,0,0.5); }
        .stat-card h4 { color: #8f9ba8; font-size: 0.9em; margin-bottom: 8px; text-transform: uppercase; }
        .stat-card .stat-value { color: #0ef; font-size: 2.2em; font-weight: bold; }
        .table-responsive { overflow-x: auto; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; background: #121824; text-align: left; font-size: 0.9em; }
        th, td { padding: 12px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #2c4766; color: #0ef; font-weight: 600; }
        tr:hover td { background: rgba(0, 238, 255, 0.03); }
        .badge { background: rgba(0, 255, 136, 0.15); color: #00ff88; padding: 4px 10px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
        .btn-action { background: #0ef; color: #121824; border: none; padding: 8px 16px; border-radius: 15px; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 0.85em; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-action:hover { background: #00ffff; box-shadow: 0 0 12px #0ef; }
    </style>
</head>
<body>

<div class="facilitator-wrapper">
    <!-- Top Header Banner -->
    <div class="glass-panel">
        <div class="header-flex">
            <h2><i class="fa-solid fa-chalkboard-user"></i> Faculty & Supervisor Portal</h2>
            <div style="display: flex; align-items: center; gap: 20px;">
                <span style="color: #8f9ba8; font-size: 0.95em;">Role: <strong style="color: #0ef;">Facilitator</strong></span>
                <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-power-off"></i> Logout</a>
            </div>
        </div>

        <!-- System Analytics Cards (Matching DFD Outputs) -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Active Learners</h4>
                <div class="stat-value"><?php echo count($learners); ?></div>
            </div>
            <div class="stat-card">
                <h4>Simulation Status</h4>
                <div class="stat-value" style="color: #00ff88;">Operational</div>
            </div>
            <div class="stat-card">
                <h4>Evaluation Module</h4>
                <div class="stat-value" style="color: #ffaa00;">Active</div>
            </div>
        </div>
    </div>

    <!-- Feature 1: Generate Reports Section (Matches DFD / ERD Report entity) -->
    <div class="glass-panel">
        <h3><i class="fa-solid fa-file-invoice"></i> Academic & Performance Reports</h3>
        <p style="color: #8f9ba8; font-size: 0.9em; margin-bottom: 20px;">
            Generate system reports summarizing learner engagement, virtual 3D build validity, and gamified achievement scores across alternative education modules.
        </p>
        <a href="generate_report.php" class="btn-action" style="text-decoration: none;">
            <i class="fa-solid fa-file-csv"></i> Download CSV Spreadsheet
        </a>
        <a href="print_report.php" target="_blank" class="btn-action" style="background: #00ff88; color: #121824; text-decoration: none;">
            <i class="fa-solid fa-file-pdf"></i> View Printable PDF Report
        </a>
    </div>

    <!-- Feature 2: Monitor Learner Progress (Matches Use Case Diagram) -->
    <div class="glass-panel">
        <h3><i class="fa-solid fa-users-gear"></i> Learner Progress & Account Monitoring</h3>
        <p style="color: #8f9ba8; font-size: 0.9em; margin-bottom: 20px;">
            Monitor student accounts, review virtual workspace activities, and track technical competency progress.
        </p>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Username</th>
                        <th>Email Address</th>
                        <th>Date Registered</th>
                        <th>Status</th>
                        <th>Action / Log</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($learners) > 0): ?>
                        <?php foreach ($learners as $learner): ?>
                            <tr>
                                <td>#<?php echo $learner['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($learner['username']); ?></strong></td>
                                <td style="color: #8f9ba8;"><?php echo htmlspecialchars($learner['email']); ?></td>
                                <td><?php echo $learner['created_at']; ?></td>
                                <td><span class="badge">Active Learner</span></td>
                                <td>
                                    <a href="view_student.php?id=<?php echo $learner['id']; ?>" class="btn-action" style="padding: 5px 12px; font-size: 0.8em; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i> View Activity
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #8f9ba8; padding: 25px;">No registered learners found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>