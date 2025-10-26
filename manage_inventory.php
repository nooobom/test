<?php
session_start();
require_once "conn.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle stock actions
if (isset($_GET['stock_action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['stock_action'];

    if ($action === "inc") {
        $stmt = $mysqli->prepare("UPDATE products SET stock = stock + 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === "dec") {
        $stmt = $mysqli->prepare("UPDATE products SET stock = GREATEST(stock - 1, 0) WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === "delete") {
        $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to this page
    header("Location: manage_inventory.php");
    exit;
}

// Fetch products
$result = $mysqli->query("SELECT * FROM products");
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
    <style>
        body { margin:0; font-family:sans-serif; background:#f9f9f9; padding:20px; }
        h2 { text-align:center; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding:12px; border:1px solid #ddd; text-align:center; }
        th { background:#333; color:white; }
        a.btn { text-decoration:none; padding:6px 12px; border-radius:5px; font-size:14px; }
        .inc { background:#28a745; color:white; }
        .dec { background:#ffc107; color:white; }
        .del { background:#dc3545; color:white; }
        .inc:hover { background:#218838; }
        .dec:hover { background:#e0a800; }
        .del:hover { background:#c82333; }
    </style>
</head>
<body>
    <h2>Manage Inventory</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($products)) { ?>
            <?php foreach ($products as $p) { ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo $p['stock']; ?></td>
                    <td>
                        <a class="btn inc" href="manage_inventory.php?stock_action=inc&id=<?php echo $p['id']; ?>">+</a>
                        <a class="btn dec" href="manage_inventory.php?stock_action=dec&id=<?php echo $p['id']; ?>">-</a>
                        <a class="btn del" href="manage_inventory.php?stock_action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="4">No products found.</td></tr>
        <?php } ?>
    </table>
</body>
</html>
