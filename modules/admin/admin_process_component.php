<?php
session_start();
require_once '../../api/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.html?error=Unauthorized! Admin access required.");
    exit();
}

$allowed_tables = ['processors', 'motherboards', 'gpus', 'rams', 'storages', 'coolers', 'psus', 'cases'];

// --- HANDLE DELETE ACTION ---
if (isset($_GET['action']) &&$_GET['action'] === 'delete') {
    $category = trim($_GET['category'] ?? '');
    $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

    if (in_array($category, $allowed_tables) &&$id > 0) {
        try {
            $stmt =$pdo->prepare("DELETE FROM `$category` WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: admin_dashboard.php?category=$category&success=Component deleted successfully");
            exit();
        } catch (PDOException $e) {
            header("Location: admin_dashboard.php?category=$category&error=Database error: " . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: admin_dashboard.php?error=Invalid delete parameters.");
        exit();
    }
}

// --- HANDLE INSERT / ADD COMPONENT ACTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {$category = trim($_POST['category'] ?? '');$name = trim($_POST['name'] ?? '');$price = trim($_POST['price'] ?? '');$stock = trim($_POST['stock'] ?? '');$wattage = !empty($_POST['wattage']) ? trim($_POST['wattage']) : null;
    $socket = !empty($_POST['socket']) ? trim($_POST['socket']) : null;
    $cores = !empty($_POST['cores']) ? trim($_POST['cores']) : null;
    $threads = !empty($_POST['threads']) ? trim($_POST['threads']) : null;

    if (in_array($category, $allowed_tables) && !empty($name) && is_numeric($price) && is_numeric($stock)) {
        try {
            $columns = "name, price, stock";
            $values = "?, ?, ?";
            $params = [$name, $price,$stock];

            $optional_fields = [
                'wattage' => $wattage,
                'socket' => $socket,
                'cores' => $cores,
                'threads' => $threads
            ];

            foreach ($optional_fields as $field =>$val) {
                if ($val !== null) {
                    $check =$pdo->query("SHOW COLUMNS FROM `$category` LIKE '$field'");
                    if ($check->rowCount() > 0) {$columns .= ", `$field`";
                        $values .= ", ?";
                        $params[] =$val;
                    }
                }
            }

            $stmt =$pdo->prepare("INSERT INTO `$category` ($columns) VALUES ($values)");
            $stmt->execute($params);

            header("Location: admin_dashboard.php?category=$category&success=Component added successfully");
            exit();
        } catch (PDOException $e) {
            header("Location: admin_dashboard.php?error=Database error: " . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: admin_dashboard.php?error=Invalid input data.");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>