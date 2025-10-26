<?php
session_start();
require_once 'conn.php';

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Error: user id not provided");
}

$id = intval($_GET['id']);

// Fetch user
$result = $mysqli->query("SELECT * FROM users WHERE id = $id");
if (!$result) {
    die("DB Error: " . $mysqli->error);
}
$user = $result->fetch_assoc();
if (!$user) {
    die("Error: user not found");
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profile_photo = trim($_POST['profile_photo'] ?? '');
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($username === '' || $email === '') {
        die("Username and Email are required.");
    }

    // Handle optional password
    $password = trim($_POST['password'] ?? '');
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("
            UPDATE users 
            SET username=?, name=?, first_name=?, last_name=?, address=?, phone=?, email=?, profile_photo=?, password=?, is_admin=? 
            WHERE id=?
        ");
        $stmt->bind_param("ssssssssssi", $username, $name, $first_name, $last_name, $address, $phone, $email, $profile_photo, $hashed_password, $is_admin, $id);
    } else {
        $stmt = $mysqli->prepare("
            UPDATE users 
            SET username=?, name=?, first_name=?, last_name=?, address=?, phone=?, email=?, profile_photo=?, is_admin=? 
            WHERE id=?
        ");
        $stmt->bind_param("ssssssssii", $username, $name, $first_name, $last_name, $address, $phone, $email, $profile_photo, $is_admin, $id);
    }

    if (!$stmt->execute()) {
        die("DB execute error: " . $stmt->error);
    }

    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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
        .checkbox-container { display: flex; align-items: center; margin-bottom: 12px; }
        .checkbox-container input[type="checkbox"] { margin-right: 8px; }
        .image-preview { text-align: center; margin-bottom: 15px; }
        .image-preview img { width: 100px; border: 1px dashed #ccc; border-radius: 6px; padding: 4px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit User</h2>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">

        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">

        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Profile Photo URL</label>
        <input type="url" name="profile_photo" value="<?= htmlspecialchars($user['profile_photo']) ?>">
        <div class="image-preview">
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo">
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>
        </div>

        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password">

        <div class="checkbox-container">
            <input type="checkbox" name="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>>
            <label>Admin</label>
        </div>

        <button type="submit">Update User</button>
    </form>
</div>
</body>
</html>
