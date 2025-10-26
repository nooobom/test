<?php
session_start();
require_once "conn.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username_email = trim($_POST['username_email']);
    $password       = trim($_POST['password']);

    if (empty($username_email) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    } else {
        // ✅ Added is_admin column
        $stmt = $mysqli->prepare("SELECT id, username, name, password, is_admin FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Login success
            session_regenerate_id(true); // prevent session fixation

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['name']      = $user['name'];
            $_SESSION['is_admin']  = $user['is_admin'];

            // ✅ Direct redirect (no duplicate headers)
            if ($_SESSION['is_admin']) {
                header("Location: admindashboard.php");
                exit;
            } else {
                header("Location: homepage.php");
                exit;
            }
        } else {
            $errors[] = "Invalid username/email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            margin:0;
            font-family:sans-serif;
            background:#f0f0f0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .container {
            background:white;
            border-radius:10px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
            width:450px;
            padding:40px;
        }
        h2 { text-align:center; margin-bottom:30px; }
        .form-group { margin-bottom:20px; }
        label { display:block; margin-bottom:8px; color:#555; }
        input {
            width:100%;
            padding:14px;
            border:1px solid #ccc;
            border-radius:5px;
        }
        input:focus { border-color:#90ee90; }
        .btn {
            background:#333;
            color:white;
            padding:14px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            width:100%;
        }
        .btn:hover { background:#555; }
        .message { text-align:center; margin-bottom:20px; color:#d2691e; }
        .login-link { text-align:center; margin-top:20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if(!empty($errors)) { foreach($errors as $err) echo "<div class='message'>$err</div>"; } ?>
    <?php if($success) echo "<div class='message'>$success</div>"; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username or Email</label>
            <input type="text" name="username_email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <div class="login-link">
        <p>Don't have an account? <a href="index.php">Register here</a></p>
    </div>
</div>
</body>
</html>
