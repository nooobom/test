<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: users.php");
    exit;
}

// Fetch Users
$stmt = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900"
    />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body class="relative flex size-full min-h-screen flex-col bg-slate-50 overflow-x-hidden" style='font-family: Inter, "Noto Sans", sans-serif;'>
    <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf4] px-10 py-3">
            <div class="flex items-center gap-4 text-[#0d151c]">
                <div class="size-4">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" fill="currentColor"></path>
                    </svg>
                </div>
                <h2 class="text-[#0d151c] text-lg font-bold leading-tight tracking-[-0.015em]">Admin Panel</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[#0d151c] text-base font-medium">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)
                </span>
                <a href="logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#0c7ff2] text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]">
                    <span class="truncate">Logout</span>
                </a>
            </div>
        </header>
        
        <div class="px-40 flex flex-1 justify-center py-5">
            <div class="layout-content-container flex flex-col max-w-[1280px] w-full flex-1">
                <div class="flex flex-wrap justify-between gap-3 p-4">
                    <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight min-w-72">User Management</p>
                    <a href="add_user.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-8 px-4 bg-[#0c7ff2] text-slate-50 text-sm font-medium leading-normal">
                        <span class="truncate">Add User</span>
                    </a>
                </div>
                
                <!-- Responsive Table Wrapper -->
                <div class="px-4 py-3">
                    <div class="overflow-x-auto rounded-xl border border-[#cedce8] bg-slate-50">
                        <table class="w-full min-w-[1200px]">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Profile</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Username</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Name</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">First Name</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Last Name</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Address</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Phone</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Email</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr class="border-t border-t-[#cedce8]">
                                    <td class="px-4 py-2">
                                        <?php if ($user['profile_photo'] && file_exists($user['profile_photo'])): ?>
                                            <img src="<?= htmlspecialchars($user['profile_photo']) ?>" class="profile-img" alt="Profile">
                                        <?php else: ?>
                                            <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10 flex items-center justify-center text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['first_name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['last_name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['address']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['phone']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                                        <a href="users.php?delete_id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')" class="text-red-600 hover:text-red-800">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End Responsive Table Wrapper -->
            </div>
        </div>
        
        <footer class="flex justify-center">
            <div class="flex max-w-[1280px] flex-1 flex-col">
                <footer class="flex flex-col gap-6 px-5 py-10 text-center">
                    <p class="text-[#49739c] text-base font-normal leading-normal">@2024 Admin Panel. All rights reserved.</p>
                </footer>
            </div>
        </footer>
    </div>
</body>
</html>
