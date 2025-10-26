<?php
session_start();

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: cart.php");
    exit;
}

$id = intval($_GET['id']);
$action = $_GET['action'];

if (isset($_SESSION['cart'][$id])) {
    switch ($action) {
        case "add":
            $_SESSION['cart'][$id]['quantity']++;
            break;
        case "remove":
            $_SESSION['cart'][$id]['quantity']--;
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
            break;
        case "delete":
            unset($_SESSION['cart'][$id]);
            break;
    }
}

header("Location: cart.php");
exit;
?>
