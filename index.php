<?php
// register.php
session_start();
require_once "conn.php"; // <-- your DB connection file

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);

    // Validation
    if (empty($username) || strlen($username) < 4 || strlen($username) > 13 || preg_match('/\s/', $username)) {
        $errors[] = "Username must be 4–13 characters with no spaces.";
    }
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($password) || strlen($password) < 6 || strlen($password) > 18 || 
        !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must be 6–18 characters and include at least one special character.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpos($email, '@') === false || strpos($email, '..') !== false) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $check = $mysqli->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Username or Email already exists!";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO users (username, name, password, email, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $name, $hashedPassword, $email, $phone);

            if ($stmt->execute()) {
                $success = "Registration successful! 🎉 Redirecting to login...";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
          margin: 0;
          font-family: sans-serif;
          background: #f0f0f0;
          height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .container {
          background: white;
          border-radius: 10px;
          box-shadow: 0 5px 20px rgba(0,0,0,0.1);
          width: 450px;
          padding: 40px;
        }
        .signup-title {
          font-size: 1.8rem;
          font-weight: 600;
          color: #333;
          margin-bottom: 30px;
          text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        label {
          display: block;
          font-size: 0.9rem;
          color: #555;
          margin-bottom: 8px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
          width: 100%;
          padding: 14px;
          border: 1px solid #ccc;
          border-radius: 5px;
          font-size: 1rem;
          color: #333;
          box-sizing: border-box;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
          border-color: #90ee90;
        }
        .signup-button {
          background: #333;
          color: white;
          padding: 14px;
          border: none;
          border-radius: 5px;
          font-size: 1.1rem;
          font-weight: 500;
          cursor: pointer;
          width: 100%;
          transition: background-color 0.3s;
        }
        .signup-button:hover {
          background: #555;
        }
        .login-link {
          text-align: center;
          margin-top: 20px;
        }
        .login-link a {
          color: #777;
          text-decoration: none;
        }
        .login-link a:hover { text-decoration: underline; }
        .message {
          text-align: center;
          margin-bottom: 20px;
          color: #d2691e;
          font-size: 1rem;
        }
        .error-list {
          background: #ffe6e6;
          border: 1px solid #ff9999;
          color: #cc0000;
          padding: 10px;
          border-radius: 5px;
          margin-bottom: 20px;
        }
        @media (max-width: 500px) {
          .container {
            width: 90%;
            padding: 30px;
          }
          .signup-title { font-size: 1.6rem; }
          input[type="text"],
          input[type="email"],
          input[type="password"] {
            padding: 12px;
          }
          .signup-button {
            padding: 12px;
            font-size: 1rem;
          }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="signup-title">Create Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message"><?= $success ?></div>
            <script>
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 3000); // redirect after 3 sec
            </script>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <div id="clientErrors" class="error-list" style="display:none;"></div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>
            <button type="submit" class="signup-button">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
    document.querySelector("#registerForm").addEventListener("submit", function(e) {
        let errors = [];
        let username = document.querySelector('input[name="username"]').value.trim();
        let name     = document.querySelector('input[name="name"]').value.trim();
        let password = document.querySelector('input[name="password"]').value.trim();
        let email    = document.querySelector('input[name="email"]').value.trim();
        let phone    = document.querySelector('input[name="phone"]').value.trim();

        // Username: 4–13 chars, no spaces
        if (username.length < 4 || username.length > 13 || /\s/.test(username)) {
            errors.push("Username must be 4–13 characters with no spaces.");
        }

        // Name required
        if (name.length === 0) {
            errors.push("Name is required.");
        }

        // Password: 6–18 chars + at least one special char
        if (password.length < 6 || password.length > 18 || !/[^a-zA-Z0-9]/.test(password)) {
            errors.push("Password must be 6–18 characters and include at least one special character.");
        }

        // Email: must contain @, valid format, and no double dots
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email) || email.indexOf("..") !== -1) {
            errors.push("Invalid email format.");
        }

        // Phone: exactly 10 digits
        if (!/^[0-9]{10}$/.test(phone)) {
            errors.push("Phone number must be 10 digits.");
        }

        let clientErrorDiv = document.getElementById("clientErrors");
        if (errors.length > 0) {
            e.preventDefault();
            clientErrorDiv.innerHTML = errors.map(err => `<p>${err}</p>`).join("");
            clientErrorDiv.style.display = "block";
        } else {
            clientErrorDiv.style.display = "none";
        }
    });
    </script>
</body>
</html>
