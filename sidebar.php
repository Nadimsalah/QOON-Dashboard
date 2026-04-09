<?php
$currentPage = $_GET['Page'] ?? basename($_SERVER['PHP_SELF']);
$Userspage = $_COOKIE["Userspage"] ?? 1; // Default to 1 if not set to ensure menus appear in dev mode
$DriversPage = $_COOKIE["DriversPage"] ?? 1;
$ShopsPage = $_COOKIE["ShopsPage"] ?? 1;
$OrdersPage = $_COOKIE["OrdersPage"] ?? 1;
$WalletPage = $_COOKIE["WalletPage"] ?? 1;
$Notification = $_COOKIE["Notification"] ?? 1;
$Profile = $_COOKIE["Profile"] ?? 1;
?>
<aside class="sidebar">
    <a href="index.php" class="logo-box">
        <img src="images/logo.png" alt="QOON Logo" style="max-height: 50px; width: auto; object-fit: contain;">
    </a>

    <div class="nav-list">
        <a href="index.php" class="nav-item <?= ($currentPage == 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        <?php if ($Userspage == 1) { ?>
            <a href="user.php" class="nav-item <?= ($currentPage == 'user.php') ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                Users
            </a>
        <?php } ?>
        <?php if ($DriversPage == 1) { ?>
            <a href="driver.php" class="nav-item <?= ($currentPage == 'driver.php') ? 'active' : '' ?>">
                <i class="fas fa-motorcycle"></i>
                Drivers
            </a>
        <?php } ?>
        <?php if ($ShopsPage == 1) { ?>
            <a href="shop.php" class="nav-item <?= ($currentPage == 'shop.php' || $currentPage == 'shop-profile.php') ? 'active' : '' ?>">
                <i class="fas fa-store"></i>
                Shop
            </a>
        <?php } ?>
        <?php if ($OrdersPage == 1) { ?>
            <a href="orders.php" class="nav-item <?= ($currentPage == 'orders.php') ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                Orders
            </a>
        <?php } ?>
        <?php if ($WalletPage == 1) { ?>
            <a href="wallet.php" class="nav-item <?= ($currentPage == 'wallet.php') ? 'active' : '' ?>">
                <i class="fas fa-wallet"></i>
                Wallet
            </a>
        <?php } ?>
        <a href="apps.php" class="nav-item <?= ($currentPage == 'apps.php') ? 'active' : '' ?>">
            <i class="fas fa-cube"></i>
            Apps
        </a>
        <?php if ($Notification == 1) { ?>
            <a href="notifications.php" class="nav-item <?= ($currentPage == 'notifications.php') ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                Notifications
            </a>
        <?php } ?>
    </div>

    <div class="nav-list" style="flex: 0; margin-top: auto;">
        <?php if ($Profile == 1) { ?>
            <a href="settings-profile.php" class="nav-item <?= (strpos($currentPage, 'settings-') !== false || strpos($currentPage, 'bakat.php') !== false) ? 'active' : '' ?>">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        <?php } ?>
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</aside>
