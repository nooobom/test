<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php';

if (!isset($_GET['id'])) {
    die("Error: product id not provided");
}

$id = intval($_GET['id']);  // sanitize

// Fetch product
$result = $mysqli->query("SELECT * FROM products WHERE id = $id");
if (!$result) {
    die("DB Error (select products): " . $mysqli->error);
}
$products = $result->fetch_assoc();
if (!$products) {
    die("Error: product not found");
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $desc = $_POST['description'] ?? '';

    if ($name === '' || $price === '' || $category === '' || $stock === '') {
        die("Please fill all required fields");
    }

    // Handle image (if new provided, else keep old one)
    $new_image = trim($_POST['image'] ?? '');
    $final_image = $new_image !== '' ? $new_image : $products['image'];

    // Prepared statement
    $stmt = $mysqli->prepare("
        UPDATE products 
        SET name=?, price=?, category=?, stock=?, description=?, image=? 
        WHERE id=?
    ");
    if (!$stmt) {
        die("DB prepare error: " . $mysqli->error);
    }

    // Bind params correctly
    $stmt->bind_param("sdsissi", $name, $price, $category, $stock, $desc, $final_image, $id);

    if (!$stmt->execute()) {
        die("DB execute error: " . $stmt->error);
    }

    // Redirect back to dashboard
    header("Location: admindashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit product</title>
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

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .image-preview {
            text-align: center;
            margin-bottom: 15px;
        }

        .image-preview img {
            width: 120px;
            border: 1px dashed #ccc;
            border-radius: 6px;
            padding: 4px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit Product</h2>
    <form method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($products['name']) ?>">

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($products['price']) ?>">

        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($products['category']) ?>">

        <label>Stock</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($products['stock']) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($products['description']) ?></textarea>

        <h3>Current Image</h3>
        <div class="image-preview">
            <?php if (!empty($products['image'])): ?>
                <img src="<?= htmlspecialchars($products['image']) ?>" alt="Product Image">
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>
        </div>

        <label>Change Image URL</label>
        <input type="url" name="image" placeholder="Paste new image link">

        <button type="submit">Update Product</button>
    </form>
</div>
</body>
</html>
