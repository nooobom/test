<?php
session_start();
require_once "conn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT username, first_name, last_name, profile_photo, address, phone, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body { font-family:sans-serif; background:#f4f4f4; margin:0; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .profile-container { background:white; padding:40px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); width:450px; text-align:center; }
        h2 { margin-bottom:30px; }
        .profile-photo { width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:20px; }
        .info { text-align:left; margin-bottom:15px; }
        .info label { font-weight:600; color:#555; display:block; margin-bottom:5px; }
        .info span { color:#333; }
        .btn { display:inline-block; margin:10px 5px; padding:12px 20px; background:#333; color:white; text-decoration:none; border-radius:5px; transition:0.3s; }
        .btn:hover { background:#555; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>User Profile</h2>
        <img src="<?= htmlspecialchars($user['profile_photo'] ?: 'default-profile.png') ?>" class="profile-photo" alt="Profile Photo">
        <div class="info">
            <label>Username</label>
            <span><?= htmlspecialchars($user['username']) ?></span>
        </div>
        <div class="info">
            <label>First Name</label>
            <span><?= htmlspecialchars($user['first_name']) ?></span>
        </div>
        <div class="info">
            <label>Last Name</label>
            <span><?= htmlspecialchars($user['last_name']) ?></span>
        </div>
        <div class="info">
            <label>Email</label>
            <span><?= htmlspecialchars($user['email']) ?></span>
        </div>
        <div class="info">
            <label>Phone</label>
            <span><?= htmlspecialchars($user['phone']) ?></span>
        </div>
        <div class="info">
            <label>Address</label>
            <span><?= htmlspecialchars($user['address']) ?></span>
        </div>

        <a href="editprofile.php" class="btn">Edit Profile</a>
        <a href="homepage.php" class="btn">Back to Homepage</a>
    </div>
</body>
</html>
