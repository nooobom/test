<?php
session_start();
require_once "conn.php";

// Make sure cart exists
if (!isset($_SESSION['cart_items'])) {
    $_SESSION['cart_items'] = [];
}

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    if (isset($_POST['increase'])) {
        $_SESSION['cart_items'][$product_id]['quantity']++;
    } elseif (isset($_POST['decrease'])) {
        if ($_SESSION['cart_items'][$product_id]['quantity'] > 1) {
            $_SESSION['cart_items'][$product_id]['quantity']--;
        } else {
            unset($_SESSION['cart_items'][$product_id]);
        }
    } elseif (isset($_POST['remove'])) {
        unset($_SESSION['cart_items'][$product_id]);
    }
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin:0; font-family:sans-serif; background:#f4f4f4; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#333; color:white; }
        .navbar a { color:white; text-decoration:none; margin-left:20px; }
        .container { max-width:1000px; margin:30px auto; padding:20px; background:white; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; }
        table th, table td { padding:15px; text-align:center; border-bottom:1px solid #ddd; }
        table img { width:70px; height:70px; object-fit:cover; border-radius:5px; }
        .actions button { padding:5px 10px; margin:2px; border:none; border-radius:5px; cursor:pointer; }
        .increase { background:#28a745; color:white; }
        .decrease { background:#ffc107; color:white; }
        .remove { background:#dc3545; color:white; }
        .checkout { margin-top:20px; padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; font-size:1rem; }
        .checkout:hover { background:#0056b3; }
        .empty { text-align:center; font-size:1.2rem; padding:30px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>Doodle Desk</h2>
        <div>
            <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
            <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="userprofile.php"><i class="fas fa-user"></i></a>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="container">
        <h2>Your Cart</h2>
        <?php if (empty($_SESSION['cart_items'])): ?>
            <p class="empty">Your cart is empty 😢</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                <?php 
                $grand_total = 0;
                foreach ($_SESSION['cart_items'] as $item): 
                    $total = $item['price'] * $item['quantity'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($total, 2) ?></td>
                    <td class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="increase" class="increase">+</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="decrease" class="decrease">-</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="remove" class="remove">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <h3 style="text-align:right; margin-top:20px;">Grand Total: ₹<?= number_format($grand_total, 2) ?></h3>
            
            <!-- Checkout Button -->
            <form action="checkout.php" method="GET" style="text-align:right;">
                <button type="submit" class="checkout">Proceed to Checkout</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
