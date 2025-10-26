<?php
session_start();
require_once 'conn.php';

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $name = $_POST['name'] ?? '';
    $profile_photo = $_POST['profile_photo'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate required fields
    if (!$username || !$name || !$email || !$password) {
        die("Please fill all required fields");
    }

    // Hash password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $stmt = $mysqli->prepare("
        INSERT INTO users (username, name, profile_photo, first_name, last_name, address, phone, email, password, is_admin)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssssi",
        $username, $name, $profile_photo, $first_name, $last_name, $address, $phone, $email, $password_hashed, $is_admin
    );

    if (!$stmt->execute()) {
        die("DB Error: " . $stmt->error);
    }

    header("Location: admindashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
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
        h2 { text-align: center; color: #333; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-top: 10px; margin-bottom: 5px; }
        input, button { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { background-color: #007BFF; color: white; font-weight: bold; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        button:hover { background-color: #0056b3; }
        .checkbox-container { display: flex; align-items: center; }
        .checkbox-container input { width: auto; margin-right: 10px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Add User</h2>
    <form method="post">
        <label>Username *</label>
        <input type="text" name="username" required>

        <label>Name *</label>
        <input type="text" name="name" required>

        <label>Profile Photo URL</label>
        <input type="url" name="profile_photo" placeholder="Link to profile photo">

        <label>First Name</label>
        <input type="text" name="first_name">

        <label>Last Name</label>
        <input type="text" name="last_name">

        <label>Address</label>
        <input type="text" name="address">

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Email *</label>
        <input type="email" name="email" required>

        <label>Password *</label>
        <input type="password" name="password" required>

        <div class="checkbox-container">
            <input type="checkbox" name="is_admin" value="1">
            <label>Admin User</label>
        </div>

        <button type="submit">Add User</button>
    </form>
</div>
</body>
</html>
