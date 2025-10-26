<?php
session_start();
require_once "conn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch existing data
$stmt = $mysqli->prepare("SELECT username, first_name, last_name, profile_photo, address, phone, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);

    // Handle profile photo upload
    $profile_photo = $user['profile_photo']; // default to old one
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = basename($_FILES["profile_photo"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            $profile_photo = $target_file;
        } else {
            $errors[] = "Failed to upload profile photo.";
        }
    }

    if (empty($errors)) {
        $update = $mysqli->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=?, profile_photo=? WHERE id=?");
        $update->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $address, $profile_photo, $user_id);
        if ($update->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $stmt = $mysqli->prepare("SELECT username, first_name, last_name, profile_photo, address, phone, email FROM users WHERE id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = "Error updating profile.";
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family:sans-serif; background:#f4f4f4; margin:0; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .edit-container { background:white; padding:40px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); width:500px; }
        h2 { text-align:center; margin-bottom:20px; }
        .form-group { margin-bottom:15px; }
        label { font-weight:600; display:block; margin-bottom:5px; }
        input[type="text"], input[type="email"], input[type="file"] { width:100%; padding:12px; border:1px solid #ccc; border-radius:5px; }
        .profile-photo { width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:15px; display:block; }
        .btn { background:#333; color:white; padding:12px 20px; border:none; border-radius:5px; cursor:pointer; transition:0.3s; }
        .btn:hover { background:#555; }
        .message { text-align:center; margin-bottom:15px; color:#d2691e; }
        .actions { text-align:center; margin-top:20px; }
        .actions a { text-decoration:none; margin:0 10px; color:#333; font-weight:bold; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Profile</h2>

        <?php if (!empty($errors)): ?>
            <div class="message"><?php foreach ($errors as $err) echo "<p>$err</p>"; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <img src="<?= htmlspecialchars($user['profile_photo'] ?: 'default-profile.png') ?>" class="profile-photo">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">
            </div>
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="profile_photo">
            </div>

            <div class="actions">
                <button type="submit" class="btn">Save Changes</button>
                <a href="userprofile.php">Cancel</a>
                <a href="homepage.php">Back to Homepage</a>
            </div>
        </form>
    </div>
</body>
</html>
