<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: products.php");
    exit;
}

// Fetch Products
$stmt = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900"
    />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        .product-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
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
                    <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight min-w-72">Product Management</p>
                    <a href="add_product.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-8 px-4 bg-[#0c7ff2] text-slate-50 text-sm font-medium leading-normal">
                        <span class="truncate">Add Product</span>
                    </a>
                </div>
                
                <!-- Responsive Table Wrapper -->
                <div class="px-4 py-3">
                    <div class="overflow-x-auto rounded-xl border border-[#cedce8] bg-slate-50">
                        <table class="w-full min-w-[1200px]">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Image</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Product Name</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Category</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Price</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Stock</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Description</th>
                                    <th class="px-4 py-3 text-left text-[#0d151c] text-sm font-medium leading-normal">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr class="border-t border-t-[#cedce8]">
                                    <td class="px-4 py-2">
                                        <?php if ($product['image'] && file_exists($product['image'])): ?>
                                            <img src="<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="Product Image">
                                        <?php else: ?>
                                            <div class="bg-gray-200 border-2 border-dashed rounded w-10 h-10 flex items-center justify-center text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7M16 3a4 4 0 00-8 0M12 11v6m0 0l-2-2m2 2l2-2" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($product['category']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($product['price']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($product['stock']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($product['description']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="edit_products.php?id=<?= $product['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                                        <a href="products.php?delete_id=<?= $product['id'] ?>" onclick="return confirm('Delete this product?')" class="text-red-600 hover:text-red-800">Delete</a>
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
