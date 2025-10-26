<?php
session_start();
require_once 'conn.php';

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$errors = [];
$name = $category = $price = $stock = $description = "";
$image_path = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate fields
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $description = trim($_POST['description']);

    if ($name == "") $errors[] = "Product name is required.";
    if ($category == "") $errors[] = "Category is required.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Valid price is required.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Valid stock is required.";

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) { // 2MB
            $errors[] = "Image file is too large (max 2MB).";
        }

        if (empty($errors)) {
            $image_path = $target_dir . uniqid() . "." . $imageFileType;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Insert into database if no errors
    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO products (name, category, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $stock, $description, $image_path]);
        header("Location: products.php?msg=Product+added+successfully");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="products.php">Shop Admin</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4">Add New Product</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($category) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= htmlspecialchars($price) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock</label>
                <input type="number" min="0" name="stock" class="form-control" value="<?= htmlspecialchars($stock) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Image (optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Add Product</button>
                <a href="products.php" class="btn btn-secondary ms-2">Back to Products</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
