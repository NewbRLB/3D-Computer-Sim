<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.html?error=Unauthorized! Admin access required.");
    exit();
}

require_once '../../api/db.php';

$selected_category = $_GET['category'] ?? 'processors';$allowed_tables = ['processors', 'motherboards', 'gpus', 'rams', 'storages', 'coolers', 'psus', 'cases'];

if (!in_array($selected_category, $allowed_tables)) {$selected_category = 'processors';
}

try {
    $stmt =$pdo->query("SELECT * FROM `$selected_category` ORDER BY id DESC");
    $components =$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {$components = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILASAE-G - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../auth/merged.css">
    <style>
        body { background: #121824; color: #fff; display: block; padding: 30px; overflow-y: auto; min-height: 100vh; }
        .admin-wrapper { max-width: 1200px; margin: 0 auto; }
        .glass-panel { background: #1f293a; border-radius: 16px; padding: 30px; box-shadow: -10px -10px 20px rgba(255, 255, 255, 0.03), 15px 15px 30px rgba(0, 0, 0, 0.5), inset 1px 1px 2px rgba(0, 238, 255, 0.2); margin-bottom: 30px; position: relative; }
        .glass-panel::before { content: ""; position: absolute; inset: 0; border-radius: 16px; border: 1px solid rgba(0, 238, 255, 0.1); pointer-events: none; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(0, 238, 255, 0.15); padding-bottom: 20px; margin-bottom: 25px; }
        .header-flex h2 { color: #0ef; font-size: 1.8em; display: flex; align-items: center; gap: 12px; }
        .logout-btn { background: rgba(255, 77, 77, 0.15); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .logout-btn:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 15px rgba(255, 77, 77, 0.5); }
        h3 { color: #00ff88; margin-bottom: 15px; font-size: 1.2em; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-control { width: 100%; height: 44px; background: #121824; border: 1px solid rgba(0, 238, 255, 0.2); border-radius: 22px; color: #fff; padding: 0 20px; font-size: 0.9em; outline: none; box-shadow: inset 3px 3px 6px rgba(0,0,0,0.5); }
        .form-control:focus { border-color: #0ef; box-shadow: 0 0 8px rgba(0, 238, 255, 0.4); }
        select.form-control { appearance: none; cursor: pointer; }
        .btn-action { background: #0ef; color: #121824; border: none; padding: 10px 25px; border-radius: 22px; font-weight: 700; cursor: pointer; box-shadow: 0 0 10px rgba(0, 238, 255, 0.3); transition: 0.3s; }
        .btn-action:hover { background: #00ffff; box-shadow: 0 0 20px #0ef; transform: translateY(-2px); }
        .table-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .table-responsive { overflow-x: auto; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; background: #121824; text-align: left; font-size: 0.9em; }
        th, td { padding: 12px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #2c4766; color: #0ef; font-weight: 600; }
        tr:hover td { background: rgba(0, 238, 255, 0.03); }
        .badge { background: rgba(0, 255, 136, 0.15); color: #00ff88; padding: 4px 10px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
        .delete-btn { background: rgba(255, 77, 77, 0.15); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 0.8em; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 5px; }
        .delete-btn:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 10px rgba(255, 77, 77, 0.5); }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="glass-panel">
        <div class="header-flex">
            <h2><i class="fa-solid fa-microchip"></i> System Administrator Control Center</h2>
            <div style="display: flex; align-items: center; gap: 20px;">
                <span style="color: #8f9ba8; font-size: 0.95em;">Role: <strong style="color: #0ef;">Administrator</strong></span>
                <a href="../auth/logout.php" class="logout-btn"><i class="fa-solid fa-power-off"></i> Logout</a>
            </div>
        </div>

        <h3><i class="fa-solid fa-circle-plus"></i> Add New Hardware Component</h3>
        <form action="admin_process_component.php" method="POST">
            <div class="form-grid">
                <select name="category" class="form-control" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="processors">Processor (CPU)</option>
                    <option value="motherboards">Motherboard</option>
                    <option value="gpus">Graphics Card (GPU)</option>
                    <option value="rams">Memory (RAM)</option>
                    <option value="storages">Storage (SSD/HDD)</option>
                    <option value="coolers">CPU Cooler</option>
                    <option value="psus">Power Supply (PSU)</option>
                    <option value="cases">PC Case</option>
                </select>
                <input type="text" name="name" class="form-control" placeholder="Component Name" required>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Price ($)" required>
                <input type="number" name="stock" class="form-control" placeholder="Stock Quantity" required>
                <input type="number" name="wattage" class="form-control" placeholder="Wattage (W) [Optional]">
                <input type="text" name="socket" class="form-control" placeholder="Socket [Optional]">
                <input type="number" name="cores" class="form-control" placeholder="Cores [Optional]">
                <input type="number" name="threads" class="form-control" placeholder="Threads [Optional]">
            </div>
            <button type="submit" class="btn-action"><i class="fa-solid fa-plus"></i> Add to Database</button>
        </form>
    </div>

    <!-- Inventory Table Viewer -->
    <div class="glass-panel">
        <div class="table-toolbar">
            <h3><i class="fa-solid fa-boxes-stacked"></i> Database Catalog Inventory</h3>
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <label style="color: #8f9ba8; font-size: 0.85em;">Filter Category:</label>
                <select name="category" class="form-control" style="height: 36px; padding: 0 15px;" onchange="this.form.submit()">
                    <?php foreach ($allowed_tables as$table): ?>
                        <option value="<?php echo $table; ?>" <?php echo ($selected_category ===$table) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($table); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Component Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Technical Specs</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($components) > 0): ?>
                        <?php 
                        $counter = 1; // Initialize sequence counter
                        foreach ($components as$comp): 
                        ?>
                            <tr>
                                <td><?php echo $counter++; ?>.</td> <!-- Outputs clean sequential 1, 2, 3... --> <td><strong><?php echo htmlspecialchars($comp['name']); ?></strong></td>
                                <td style="color: #00ff88;">$<?php echo number_format($comp['price'], 2); ?></td>
                                <td><?php echo $comp['stock']; ?> units</td>
                                <td>
                                    <?php 
                                        $spec_output = [];
                                        if (isset($comp['wattage']) && $comp['wattage'] > 0) $spec_output[] = $comp['wattage'] . 'W';
                                        if (isset($comp['socket']) && !empty($comp['socket'])) $spec_output[] = $comp['socket'];
                                        if (isset($comp['cores']) && $comp['cores'] > 0) $spec_output[] = $comp['cores'] . ' Cores';
                                        if (isset($comp['threads']) && $comp['threads'] > 0) $spec_output[] = $comp['threads'] . ' Threads';
                                        if (isset($comp['vram_gb']) && $comp['vram_gb'] > 0) $spec_output[] = $comp['vram_gb'] . 'GB VRAM';
                                        if (isset($comp['capacity_gb']) && $comp['capacity_gb'] > 0) $spec_output[] = $comp['capacity_gb'] . 'GB';
                                        
                                        echo !empty($spec_output) ? implode(' | ', $spec_output) : 'Standard';
                                    ?>
                                </td>
                                <td><span class="badge">Active</span></td>
                                <td>
                                    <a href="admin_process_component.php?action=delete&category=<?php echo $selected_category; ?>&id=<?php echo$comp['id']; ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($comp['name'])); ?>?');">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #8f9ba8; padding: 25px;">No components found in this category.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>