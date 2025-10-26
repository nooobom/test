<?php
session_start();
require_once "conn.php";

$razorpay_secret = "xlxEtkLqHBkPWB8E0dcPVjhC";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
    $razorpay_signature = $_POST['razorpay_signature'] ?? '';

    if (!$order_id || !$razorpay_payment_id || !$razorpay_signature) {
        die("Invalid request");
    }

    $generated_signature = hash_hmac('sha256', $order_id . "|" . $razorpay_payment_id, $razorpay_secret);

    if ($generated_signature === $razorpay_signature) {
        $stmt = $mysqli->prepare("UPDATE orders SET status='confirmed', payment_status='paid', razorpay_payment_id=? WHERE razorpay_order_id=?");
        $stmt->bind_param("ss", $razorpay_payment_id, $order_id);
        $stmt->execute();
        $stmt->close();

        // Clear cart
        $_SESSION['cart_items'] = [];

        header("Location: orderhistory.php?success=1");
        exit;
    } else {
        $stmt = $mysqli->prepare("UPDATE orders SET status='failed', payment_status='failed' WHERE razorpay_order_id=?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $stmt->close();

        header("Location: orderhistory.php?error=1");
        exit;
    }
} else {
    die("Invalid access");
}
