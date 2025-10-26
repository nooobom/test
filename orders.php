<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: orders.php");
    exit;
}

// Fetch Orders (joining with users for customer info)
$stmt = $mysqli->query("
    SELECT o.*, u.username, u.email 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");
$orders = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Management</title>
<style>
    body { font-family: Arial, sans-serif; margin:0; background:#f4f4f4; color:#333; }
    header { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#007bff; color:white; }
    header h2 { margin:0; font-size:20px; }
    header .user { font-size:14px; }
    header a { color:white; text-decoration:none; margin-left:15px; background:#0056b3; padding:5px 10px; border-radius:5px; font-size:14px; }
    .container { max-width:1200px; margin:20px auto; background:white; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    h1 { font-size:24px; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; text-align:left; border-bottom:1px solid #ddd; }
    th { background:#f0f0f0; }
    a.action { margin-right:10px; text-decoration:none; }
    a.view { color:blue; }
    a.edit { color:green; }
    a.delete { color:red; }
    footer { text-align:center; padding:15px; margin-top:20px; font-size:14px; color:#555; }
</style>
</head>
<body>

<header>
    <h2>Admin Panel</h2>
    <div class="user">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <h1>Order Management</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
                <td><?= htmlspecialchars($order['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td>₹<?= htmlspecialchars($order['total_amount']) ?></td>
                <td><?= htmlspecialchars($order['payment_status']) ?></td>
                <td>
                    <a class="action edit" href="edit_order.php?id=<?= $order['id'] ?>">Edit</a>
                    <a class="action delete" href="orders.php?delete_id=<?= $order['id'] ?>" onclick="return confirm('Delete this order?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<footer>
    &copy; <?= date('Y') ?> Admin Panel. All rights reserved.
</footer>

</body>
</html>
