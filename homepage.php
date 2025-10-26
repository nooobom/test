<?php
session_start();
require_once "conn.php";

// Ensure cart & wishlist are arrays
if (!isset($_SESSION['cart_items']) || !is_array($_SESSION['cart_items'])) {
    $_SESSION['cart_items'] = [];
}
if (!isset($_SESSION['wishlist_items']) || !is_array($_SESSION['wishlist_items'])) {
    $_SESSION['wishlist_items'] = [];
}

// Handle wishlist toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_id'])) {
    $wishlist_id = intval($_POST['wishlist_id']);

    if (!isset($_SESSION['wishlist_items'][$wishlist_id])) {
        $stmt = $mysqli->prepare("SELECT id, name, price, image, description FROM products WHERE id = ?");
        $stmt->bind_param("i", $wishlist_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($prod = $result->fetch_assoc()) {
            $_SESSION['wishlist_items'][$wishlist_id] = [
                'id'    => $prod['id'],
                'name'  => $prod['name'],
                'price' => $prod['price'],
                'image' => $prod['image'],
                'description' => $prod['description']
            ];
        }
        $stmt->close();
    } else {
        unset($_SESSION['wishlist_items'][$wishlist_id]);
    }

    header("Location: homepage.php");
    exit;
}

// Search products
$searchTerm = "";
$products = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
    $stmt = $mysqli->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $like = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query("SELECT * FROM products");
}

// Fetch products into array
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doodle Desk Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin:0; font-family:sans-serif; background:#fff; color:#333; }
        .page-wrapper { 
            max-width:1200px; 
            margin:0 auto; 
            min-height:100vh; 
            background:#fff; 
            box-shadow:0 0 20px rgba(0,0,0,0.08); 
        }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#007bff; color:white; }
        .navbar a { color:white; text-decoration:none; margin-left:20px; transition:0.3s; }
        .navbar a:hover { color:#ffdd57; }
        .navbar .icons { display:flex; align-items:center; gap:20px; }
        .navbar .icons i { margin-right:6px; }
        .logout-btn { padding:8px 15px; background:#dc3545; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:500; transition:0.3s; }
        .logout-btn:hover { background:#a71d2a; }
        .search-bar { text-align:center; padding:20px; background:#f9f9f9; border-bottom:1px solid #eee; }
        .search-bar input[type="text"] { padding:10px; width:60%; max-width:400px; border-radius:5px; border:1px solid #ccc; font-size:1rem; }
        .search-bar button { padding:10px 15px; border:none; border-radius:5px; background:#007bff; color:white; cursor:pointer; font-weight:500; margin-left:10px; }
        .search-bar button:hover { background:#0056b3; }
        .section-header { display:flex; justify-content:space-between; align-items:center; padding:20px 30px; border-bottom:1px solid #eee; }
        .section-header h2 { margin:0; font-size:1.3rem; }
        .filter-btn { padding:8px 15px; border:none; border-radius:5px; background:#6f42c1; color:white; cursor:pointer; font-weight:500; }
        .filter-btn:hover { background:#563098; }
        .container { display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; padding:30px; }
        .card { background:#fff; border-radius:10px; padding:15px; border:1px solid #eee; display:flex; flex-direction:column; align-items:center; transition:0.3s; }
        .card:hover { box-shadow:0 6px 18px rgba(0,0,0,0.1); }
        .card img { width:100%; height:150px; object-fit:cover; border-radius:8px; }
        .card h3 { margin:10px 0 5px; font-size:1rem; color:#007bff; text-align:center; }
        .card p { margin:0 0 8px; font-size:0.85rem; color:#555; text-align:center; }
        .card .price { font-weight:600; margin-bottom:8px; color:#28a745; }
        .card button { padding:8px 12px; border:none; border-radius:5px; cursor:pointer; font-weight:500; margin:5px; transition:0.3s; font-size:0.85rem; }
        .cart-btn { background:#007bff; color:white; }
        .cart-btn:hover { background:#0056b3; }
        .wishlist-btn { background:#ff9800; color:white; }
        .wishlist-btn:hover { background:#e68900; }
        footer { text-align:center; padding:20px; background:#f1f1f1; color:#555; margin-top:20px; border-top:1px solid #eee; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="navbar">
        <h2>Doodle Desk</h2>
        <div class="icons">
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> (<?php echo count($_SESSION['cart_items']); ?>)</a>
            <a href="wishlist.php"><i class="fas fa-heart"></i> (<?php echo count($_SESSION['wishlist_items']); ?>)</a>
            <a href="userprofile.php"><i class="fas fa-user"></i></a>
            <form method="POST" action="orderhistory.php" style="display:inline;">
                <button type="submit" class="logout-btn">Order History</button>
            </form>
            <form method="POST" action="logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Search bar -->
    <div class="search-bar">
        <form method="GET" action="homepage.php">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Section header -->

    <div class="container">
        <?php if(empty($products)): ?>
            <p style="grid-column:1/-1; text-align:center;">No products found.</p>
        <?php else: ?>
            <?php foreach($products as $prod): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                    <h3><?php echo htmlspecialchars($prod['name']); ?></h3>
                    <p class="price">₹<?php echo number_format($prod['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($prod['description']); ?></p>
                    <div>
                        <form method="POST" action="addcart.php" style="display:inline-block;">
                            <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                            <button type="submit" class="cart-btn">Add to Cart</button>
                        </form>
                        <form method="POST" action="homepage.php" style="display:inline-block;">
                            <input type="hidden" name="wishlist_id" value="<?php echo $prod['id']; ?>">
                            <button type="submit" class="wishlist-btn">
                                <?php echo isset($_SESSION['wishlist_items'][$prod['id']]) ? "Remove" : "Add to Wishlist"; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> My Shop — Crafted with ❤️
    </footer>
</div>
</body>
</html>
