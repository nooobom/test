<?php
session_start();
require_once 'conn.php';

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Error: order id not provided");
}

$id = intval($_GET['id']);

// Fetch order
$result = $mysqli->query("SELECT * FROM orders WHERE id = $id");
if (!$result) {
    die("DB Error: " . $mysqli->error);
}
$order = $result->fetch_assoc();
if (!$order) {
    die("Error: order not found");
}

// Fetch user for dropdown (optional)
$users_result = $mysqli->query("SELECT id, username FROM users ORDER BY username ASC");
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $order_date = $_POST['order_date'] ?? '';
    $status = $_POST['status'] ?? '';
    $total_amount = floatval($_POST['total_amount']);
    $shipping_name = $_POST['shipping_name'] ?? '';
    $shipping_address = $_POST['shipping_address'] ?? '';
    $shipping_phone = $_POST['shipping_phone'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';

    $stmt = $mysqli->prepare("
        UPDATE orders SET user_id=?, order_date=?, status=?, total_amount=?, shipping_name=?, shipping_address=?, shipping_phone=?, payment_method=?, payment_status=? WHERE id=?
    ");
    $stmt->bind_param("issdsssssi", $user_id, $order_date, $status, $total_amount, $shipping_name, $shipping_address, $shipping_phone, $payment_method, $payment_status, $id);

    if (!$stmt->execute()) {
        die("DB execute error: " . $stmt->error);
    }

    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 450px;
        }
        h2 { text-align: center; color: #333; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-top: 10px; margin-bottom: 5px; }
        input, select, button { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { background-color: #007BFF; color: white; font-weight: bold; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit Order</h2>
    <form method="post">
        <label>User</label>
        <select name="user_id" required>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $u['id'] == $order['user_id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Order Date</label>
        <input type="date" name="order_date" value="<?= htmlspecialchars(substr($order['order_date'],0,10)) ?>" required>

        <label>Status</label>
        <input type="text" name="status" value="<?= htmlspecialchars($order['status']) ?>" required>

        <label>Total Amount</label>
        <input type="number" step="0.01" name="total_amount" value="<?= htmlspecialchars($order['total_amount']) ?>" required>

        <label>Shipping Name</label>
        <input type="text" name="shipping_name" value="<?= htmlspecialchars($order['shipping_name']) ?>">

        <label>Shipping Address</label>
        <input type="text" name="shipping_address" value="<?= htmlspecialchars($order['shipping_address']) ?>">

        <label>Shipping Phone</label>
        <input type="text" name="shipping_phone" value="<?= htmlspecialchars($order['shipping_phone']) ?>">

        <label>Payment Method</label>
        <input type="text" name="payment_method" value="<?= htmlspecialchars($order['payment_method']) ?>">

        <label>Payment Status</label>
        <input type="text" name="payment_status" value="<?= htmlspecialchars($order['payment_status']) ?>">

        <button type="submit">Update Order</button>
    </form>
</div>
</body>
</html>
