<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
    <style>
        body {
            margin: 0;
            font-family: Inter, "Noto Sans", sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e7edf4;
            padding: 12px 40px;
            background: #fff;
        }
        header h2 {
            font-size: 1.125rem;
            font-weight: bold;
            margin-left: 8px;
            color: #0d141c;
        }
        header .left {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #0d141c;
        }
        header .right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        header .right span {
            font-size: 1rem;
            color: #0d141c;
        }
        header .logout-btn {
            padding: 8px 16px;
            border-radius: 6px;
            background: #0c7ff2;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.2s;
        }
        header .logout-btn:hover {
            background: #066cd1;
        }

        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 20px 40px;
        }
        .dashboard {
            width: 100%;
            max-width: 960px;
        }
        .dashboard-title {
            font-size: 32px;
            font-weight: bold;
            color: #0d141c;
            margin: 20px 0;
        }
        .menu-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: #f8fafc;
            border: none;
            margin-top: 10px;
            text-decoration: none;
            color: #0d141c;
            transition: background 0.2s;
        }
        .menu-link:hover {
            background: #e2e8f0;
        }
        .menu-link p {
            margin: 0;
            font-size: 1rem;
        }
        .menu-link svg {
            fill: currentColor;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #49739c;
            font-size: 0.95rem;
            border-top: 1px solid #e7edf4;
            background: #fff;
        }
    </style>
</head>
<body>
    <header>
        <div class="left">
            <div class="icon">
                <svg viewBox="0 0 48 48" width="24" height="24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z">
                    </path>
                </svg>
            </div>
            <h2>Admin Panel</h2>
        </div>
        <div class="right">
            <span>Welcome, <?= $username ?> (Admin)</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="content">
        <div class="dashboard">
            <p class="dashboard-title">Admin Dashboard</p>

            <a href="orders.php" class="menu-link">
                <p>Manage Orders</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 256 256">
                    <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path>
                </svg>
            </a>
            <a href="products.php" class="menu-link">
                <p>Manage Products</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 256 256">
                    <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path>
                </svg>
            </a>
            <a href="users.php" class="menu-link">
                <p>Manage Users</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 256 256">
                    <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path>
                </svg>
            </a>
            <a href="manage_inventory.php" class="menu-link">
                <p>Manage Inventory</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 256 256">
                    <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path>
                </svg>
            </a>
        </div>
    </div>

    <footer>
        <p>@2024 Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>
