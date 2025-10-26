<?php
session_start();
require_once "conn.php";

// Ensure cart is always an array
if (!isset($_SESSION['cart_items']) || !is_array($_SESSION['cart_items'])) {
    $_SESSION['cart_items'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    $stmt = $mysqli->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!isset($_SESSION['cart_items'][$product_id])) {
            $_SESSION['cart_items'][$product_id] = [
                'id'       => $row['id'],
                'name'     => $row['name'],
                'price'    => $row['price'],
                'image'    => $row['image'],
                'quantity' => 1
            ];
        } else {
            $_SESSION['cart_items'][$product_id]['quantity']++;
        }
    }

    $stmt->close();
}

header("Location: homepage.php");
exit;
?>
