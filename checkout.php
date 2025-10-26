<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect if cart empty
if (!isset($_SESSION['cart_items']) || count($_SESSION['cart_items']) === 0) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_products = [];
$grand_total = 0.00;

// Fetch product details based on session cart
$ids = array_keys($_SESSION['cart_items']);
if (!empty($ids)) {
    $id_list = implode(",", array_map("intval", $ids));
    $result = $mysqli->query("SELECT * FROM products WHERE id IN ($id_list)");
    while ($row = $result->fetch_assoc()) {
        $pid = $row['id'];
        $row['quantity'] = $_SESSION['cart_items'][$pid]['quantity'] ?? 1;
        $row['total'] = $row['price'] * $row['quantity'];
        $grand_total += $row['total'];
        $cart_products[] = $row;
    }
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_name = $_POST['shipping_name'] ?? '';
    $shipping_address = $_POST['shipping_address'] ?? '';
    $shipping_phone = $_POST['shipping_phone'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'razorpay';

    if (!$shipping_name || !$shipping_address || !$shipping_phone) {
        die("All shipping fields are required.");
    }

    // Insert order
    $stmt_order = $mysqli->prepare("
        INSERT INTO orders 
        (user_id, order_date, status, total_amount, shipping_name, shipping_address, shipping_phone, payment_method, payment_status, created_at) 
        VALUES (?, NOW(), 'pending', ?, ?, ?, ?, ?, 'unpaid', NOW())
    ");
    $stmt_order->bind_param("idssss", $user_id, $grand_total, $shipping_name, $shipping_address, $shipping_phone, $payment_method);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // Insert order items
    foreach ($cart_products as $product) {
        $quantity = $product['quantity'];
        $item_total = $product['total'];
        $stmt_item = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, total) VALUES (?, ?, ?, ?, ?)");
        $stmt_item->bind_param("iisid", $order_id, $product['id'], $product['name'], $quantity, $item_total);
        $stmt_item->execute();
        $stmt_item->close();
    }

    if ($payment_method === "cod") {
        // For COD just confirm order and skip Razorpay
        unset($_SESSION['cart_items']);
        header("Location: order_success.php?id=" . $order_id);
        exit;
    } else {
        // Online payment via Razorpay
        $keyId = "rzp_test_RMEhB3kdVNisCY";  
        $keySecret = "xlxEtkLqHBkPWB8E0dcPVjhC"; 

        // Create Razorpay order
        $url = "https://api.razorpay.com/v1/orders";
        $orderData = [
            "amount" => $grand_total * 100,
            "currency" => "INR",
            "receipt" => "order_rcptid_" . $order_id,
            "payment_capture" => 1
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);

        $response = curl_exec($ch);
        if (curl_errno($ch)) die("Curl error: " . curl_error($ch));
        curl_close($ch);

        $razorpayOrder = json_decode($response, true);
        if (!isset($razorpayOrder['id'])) die("Razorpay order creation failed");

        $razorpayOrderId = $razorpayOrder['id'];

        // Save razorpay_order_id
        $stmt_update = $mysqli->prepare("UPDATE orders SET razorpay_order_id=? WHERE id=?");
        $stmt_update->bind_param("si", $razorpayOrderId, $order_id);
        $stmt_update->execute();
        $stmt_update->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { margin:0; font-family:sans-serif; background:#f4f4f4; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#333; color:white; }
.navbar a { color:white; text-decoration:none; margin-left:20px; }
.checkout-container { max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); }
h2 { margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
.total { font-size:1.2rem; font-weight:600; text-align:right; margin-top:15px; }
form input, form textarea, form select { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; }
button { width:100%; padding:12px; background:#28a745; color:white; border:none; cursor:pointer; font-weight:600; border-radius:5px; }
button:hover { background:#218838; }
</style>
</head>
<body>
<div class="navbar">
    <h2>Doodle Desk</h2>
    <div class="icons">
        <a href="cart.php"><i class="fas fa-shopping-cart"></i> (<?= count($_SESSION['cart_items']) ?>)</a>
        <a href="wishlist.php"><i class="fas fa-heart"></i> (<?= count($_SESSION['wishlist_items'] ?? []) ?>)</a>
        <a href="userprofile.php"><i class="fas fa-user"></i></a>
    </div>
</div>

<div class="checkout-container">
<h2>Checkout</h2>
<table>
<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
<?php foreach($cart_products as $item): ?>
<tr>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= $item['quantity'] ?></td>
<td>₹<?= number_format($item['price'],2) ?></td>
<td>₹<?= number_format($item['total'],2) ?></td>
</tr>
<?php endforeach; ?>
</table>
<div class="total">Grand Total: ₹<?= number_format($grand_total,2) ?></div>

<?php if (!isset($razorpayOrderId)) : ?>
<!-- Collect shipping info first -->
<form method="POST">
    <label>Shipping Name</label>
    <input type="text" name="shipping_name" required>

    <label>Shipping Address</label>
    <textarea name="shipping_address" required></textarea>

    <label>Shipping Phone</label>
    <input type="text" name="shipping_phone" required>

    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="razorpay">Online Payment</option>
    </select>

    <button type="submit">Proceed to Pay</button>
</form>
<?php else: ?>
<!-- Razorpay checkout -->
<form action="verify_payment.php" method="POST">
<script src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?= $keyId ?>"
    data-amount="<?= $grand_total * 100 ?>"
    data-currency="INR"
    data-order_id="<?= $razorpayOrderId ?>"
    data-buttontext="Pay with Razorpay"
    data-name="Doodle Desk"
    data-description="Order Payment"
    data-prefill.name="<?= htmlspecialchars($shipping_name) ?>"
    data-prefill.email="you@example.com"
    data-theme.color="#F37254">
</script>
<input type="hidden" name="order_id" value="<?= $razorpayOrderId ?>">
</form>
<?php endif; ?>
</div>
</body>
</html>
