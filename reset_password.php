<?php
session_start();
include 'conn.php'; // Make sure $mysqli is defined here

$errors = [];
$success = "";
$showForm = false;

// Check if token is provided
if (!isset($_GET['token'])) {
    $errors[] = "Invalid or missing token.";
} else {
    $token = $_GET['token'];

    // Fetch token info
    $stmt = $mysqli->prepare("SELECT pr.user_id, pr.expires_at, u.email FROM password_resets pr JOIN users u ON pr.user_id=u.id WHERE pr.token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();
    $stmt->close();

    if (!$reset) {
        $errors[] = "Invalid token.";
    } elseif (strtotime($reset['expires_at']) < time()) {
        $errors[] = "Token has expired. Please request a new password reset.";
    } else {
        $showForm = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm) {
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm)) {
        $errors[] = "Please fill in both fields.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update user's password
        $stmt = $mysqli->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $reset['user_id']);
        $stmt->execute();
        $stmt->close();

        // Delete used token
        $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE token=?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();

        $success = "Password updated successfully! You can now <a href='login.php'>login</a>.";
        $showForm = false;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            margin:0;
            font-family: sans-serif;
            background:#f7fafc;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }
        .container {
            background:#fff;
            padding:40px;
            border-radius:12px;
            box-shadow:0 6px 18px rgba(0,0,0,0.1);
            width:400px;
        }
        h2 { text-align:center; margin-bottom:20px; color:#ff6f3c; }
        input[type=password] {
            width:100%;
            padding:12px;
            margin-bottom:20px;
            border:1px solid #ccc;
            border-radius:8px;
        }
        button {
            width:100%;
            padding:12px;
            background:#ff6f3c;
            color:white;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
        }
        button:hover { background:#e65c2f; }
        .message { margin-bottom:15px; color:green; text-align:center; }
        .error { margin-bottom:15px; color:red; text-align:center; }
        .back-link { margin-top:15px; display:block; text-align:center; text-decoration:none; color:#555; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <?php
    foreach($errors as $err) echo "<div class='error'>$err</div>";
    if($success) echo "<div class='message'>$success</div>";
    ?>

    <?php if($showForm): ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>

    <a class="back-link" href="login.php">Back to Login</a>
</div>
</body>
</html>
