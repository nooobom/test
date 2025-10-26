<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "conn.php";

// Make sure wishlist session exists
if (!isset($_SESSION['wishlist_items'])) {
    $_SESSION['wishlist_items'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Check if product exists in DB (optional but safer)
    $stmt = $mysqli->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Add if not already in wishlist
        if (!in_array($product_id, $_SESSION['wishlist_items'])) {
            $_SESSION['wishlist_items'][] = $product_id;
        }
    }
    $stmt->close();
}

// Redirect back to homepage or wishlist
header("Location: wishlist.php");
exit;
