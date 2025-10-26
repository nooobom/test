<?php
session_start();

// Ensure cart & wishlist exist
if (!isset($_SESSION['wishlist_items'])) $_SESSION['wishlist_items'] = [];
if (!isset($_SESSION['cart_items'])) $_SESSION['cart_items'] = [];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action'])) {
    $product_id = intval($_POST['product_id']);

    if ($_POST['action'] === "move") {
        // Move to cart
        if (isset($_SESSION['wishlist_items'][$product_id])) {
            $item = $_SESSION['wishlist_items'][$product_id];
            $item['quantity'] = 1;
            $_SESSION['cart_items'][$product_id] = $item;
            unset($_SESSION['wishlist_items'][$product_id]);
        }
    } elseif ($_POST['action'] === "remove") {
        unset($_SESSION['wishlist_items'][$product_id]);
    }

    header("Location: wishlist.php");
    exit;
}

$wishlist_products = $_SESSION['wishlist_items'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wishlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{margin:0;font-family:sans-serif;background:#f4f4f4}
        .navbar{display:flex;justify-content:space-between;align-items:center;padding:15px 30px;background:#333;color:white}
        .navbar a{color:white;text-decoration:none;margin-left:20px}
        .container{padding:30px}
        .card{background:white;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 5px 20px rgba(0,0,0,0.1);display:flex;align-items:center;gap:20px}
        .card img{width:120px;height:120px;object-fit:cover;border-radius:10px}
        .card-info{flex:1}
        .card h3{margin:0 0 10px;font-size:1.2rem;color:#333}
        .card p{margin:0 0 5px;font-size:0.95rem;color:#666}
        .card .price{font-weight:600;margin:5px 0;color:#28a745}
        .actions{display:flex;gap:10px}
        .actions button{padding:10px 15px;border:none;border-radius:5px;cursor:pointer;font-weight:500;transition:0.3s}
        .cart-btn{background:#28a745;color:white}
        .cart-btn:hover{background:#218838}
        .remove-btn{background:#dc3545;color:white}
        .remove-btn:hover{background:#c82333}
    </style>
</head>
<body>
<div class="navbar">
    <h2>My Wishlist</h2>
    <div>
        <a href="homepage.php"><i class="fas fa-home"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i> (<?= count($_SESSION['cart_items']) ?>)</a>
        <a href="wishlist.php"><i class="fas fa-heart"></i> (<?= count($_SESSION['wishlist_items']) ?>)</a>
    </div>
</div>

<div class="container">
    <?php if(empty($wishlist_products)): ?>
        <p>Your wishlist is empty. <a href="homepage.php">Continue shopping</a></p>
    <?php else: ?>
        <?php foreach($wishlist_products as $prod): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                <div class="card-info">
                    <h3><?= htmlspecialchars($prod['name']) ?></h3>
                    <p class="price">₹<?= number_format($prod['price'],2) ?></p>
                    <p><?= htmlspecialchars($prod['description']) ?></p>
                </div>
                <div class="actions">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                        <input type="hidden" name="action" value="move">
                        <button type="submit" class="cart-btn">Move to Cart</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="remove-btn">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
