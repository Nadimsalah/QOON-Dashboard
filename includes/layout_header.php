<?php
require_once "conn.php";
$currentPage = basename($_SERVER['PHP_SELF']);

// Cookie based permissions
$Userspage        = $_COOKIE["Userspage"] ?? 0;
$DriversPage      = $_COOKIE["DriversPage"] ?? 0;
$ShopsPage        = $_COOKIE["ShopsPage"] ?? 0;
$OrdersPage       = $_COOKIE["OrdersPage"] ?? 0;
$WalletPage       = $_COOKIE["WalletPage"] ?? 0;
$Notification     = $_COOKIE["Notification"] ?? 0;
$Profile          = $_COOKIE["Profile"] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QOON Dashboard</title>
    
    <!-- Modern Font and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap & Custom CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/modern-dash.css">
    
    <script src="js/jquery-3.2.1.min.js"></script>
</head>
<body>

    <aside class="modern-sidebar">
        <div class="sidebar-header">
            <img src="images/logo.png" alt="QOON" class="sidebar-logo">
            <button class="d-lg-none border-0 bg-transparent text-muted" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Overview</span>
                </a>
            </li>
            
            <?php if($Userspage == 1): ?>
            <li class="nav-item">
                <a href="user.php" class="nav-link <?= $currentPage == 'user.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if($DriversPage == 1): ?>
            <li class="nav-item">
                <a href="driver.php" class="nav-link <?= $currentPage == 'driver.php' ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i>
                    <span>Drivers</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if($ShopsPage == 1): ?>
            <li class="nav-item">
                <a href="shop.php" class="nav-link <?= $currentPage == 'shop.php' ? 'active' : '' ?>">
                    <i class="fas fa-store"></i>
                    <span>Shops</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if($OrdersPage == 1): ?>
            <li class="nav-item">
                <a href="orders.php" class="nav-link <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Orders</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if($WalletPage == 1): ?>
            <li class="nav-item">
                <a href="wallet.php" class="nav-link <?= $currentPage == 'wallet.php' ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Finances</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="settings-profile.php" class="nav-link <?= $currentPage == 'settings-profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-wrapper">
        <nav class="top-nav">
            <button class="d-lg-none border-0 bg-transparent text-muted" id="toggleSidebar">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            
            <div class="search-container d-none d-md-flex">
                <i class="fas fa-search text-muted"></i>
                <input type="text" placeholder="Search data, reports...">
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <div class="notifications position-relative">
                    <i class="far fa-bell fa-lg text-muted"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </div>
                
                <div class="user-profile d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 fw-600 extra-small">Administrator</p>
                        <p class="mb-0 text-muted extra-small">Admin Status</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Profile" class="rounded-circle" style="width: 40px;">
                </div>
            </div>
        </nav>

        <section class="page-content">
