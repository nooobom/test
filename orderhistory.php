<?php
session_start();
require_once "conn.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];

while($order = $orders_result->fetch_assoc()){
    $stmt_items = $mysqli->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt_items->bind_param("i", $order['id']);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    $items = [];
    while($item = $items_result->fetch_assoc()){
        $items[] = $item;
    }
    $stmt_items->close();

    $order['items'] = $items;
    $orders[] = $order;
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order History</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { margin:0; font-family:sans-serif; background:#f4f4f4; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#333; color:white; }
.navbar a { color:white; text-decoration:none; margin-left:20px; }
.container { max-width:900px; margin:30px auto; padding:0 15px; }
h2 { margin-bottom:20px; }
.orders-grid { display:grid; grid-template-columns:1fr; gap:20px; }
.order-card { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); }
.order-card h3 { margin:0 0 10px; font-size:18px; }
.order-meta { display:flex; justify-content:space-between; font-size:14px; color:#555; margin-bottom:10px; }
.items { margin-top:10px; }
.item { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #eee; font-size:14px; }
.item:last-child { border-bottom:none; }
.total { font-weight:600; text-align:right; margin-top:10px; font-size:15px; color:#28a745; }
.empty { color:#666; text-align:center; margin-top:30px; font-size:15px; }
</style>
</head>
<body>
<div class="navbar">
    <h2>My Shop</h2>
    <div class="icons">
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="userprofile.php"><i class="fas fa-user"></i></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>Your Orders</h2>

    <?php if(empty($orders)): ?>
        <p class="empty">You have no orders yet.</p>
    <?php else: ?>
        <div class="orders-grid">
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <h3>Order #<?= $order['id'] ?> - <?= date("d M Y, H:i", strtotime($order['order_date'])) ?></h3>
                    <div class="order-meta">
                        <span>Status: <?= ucfirst($order['status']) ?></span>
                        <span>Payment: <?= ucfirst($order['payment_status']) ?></span>
                    </div>
                    <div class="items">
                        <?php foreach($order['items'] as $item): ?>
                            <div class="item">
                                <span><?= htmlspecialchars($item['product_name']) ?> x<?= $item['quantity'] ?></span>
                                <span>₹<?= number_format($item['total'],2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="total">Total: ₹<?= number_format($order['total_amount'],2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
