<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$AdminName = $_COOKIE['AdminName'] ?? 'Admin';
$adminInitial = strtoupper(substr($AdminName, 0, 1));
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* ════════════════════════════════════════════════
   DESKTOP SIDEBAR
════════════════════════════════════════════════ */
    :root {
        --sb-width: 260px;
        --sb-bg: #FFFFFF;
        --sb-border: #E5E7EB;
        --sb-text: #6B7280;
        --sb-hi: #F3F4F6;
        --sb-strong: #111827;
    }

    .sb-container {
        width: var(--sb-width);
        background: var(--sb-bg);
        border-right: 1px solid var(--sb-border);
        display: flex;
        flex-direction: column;
        height: 100vh;
        flex-shrink: 0;
        font-family: 'Inter', sans-serif;
        position: relative;

        z-index: 10;
        overflow: hidden;
    }

    /* Logo */
    .sb-logo-area {
        padding: 28px 24px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        flex-shrink: 0;
    }

    .sb-logo-img {
        max-height: 44px;
        width: auto;
        object-fit: contain;
    }

    /* Section label */
    .sb-section-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--sb-text);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 24px 6px;
    }

    /* Nav */
    .sb-nav {
        flex: 1;
        padding: 0 12px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow-y: auto;
    }

    .sb-nav::-webkit-scrollbar {
        display: none;
    }

    .sb-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: 9px;
        color: var(--sb-text);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }

    .sb-item i {
        width: 18px;
        text-align: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .sb-item:hover {
        background: var(--sb-hi);
        color: var(--sb-strong);
    }

    .sb-item.active {
        background: var(--sb-strong);
        color: #fff;
    }

    .sb-item.active:hover {
        background: #1F2937;
    }

    .sb-item.danger {
        color: #EF4444;
    }

    .sb-item.danger:hover {
        background: #FEF2F2;
        color: #DC2626;
    }

    /* Footer */
    .sb-footer {
        padding: 12px;
        border-top: 1px solid var(--sb-border);
        flex-shrink: 0;
    }

    .sb-user-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 9px;
        background: var(--sb-hi);
        margin-bottom: 4px;
    }

    .sb-user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        background: var(--sb-strong);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .sb-user-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--sb-strong);
    }

    /* ════════════════════════════════════════════════
   MOBILE — hide sidebar, show bottom bar + drawer
════════════════════════════════════════════════ */
    @media (max-width: 991px) {

        /* Fully hide desktop sidebar */
        .sb-container {
            display: none !important;
        }

        /* ── Bottom Tab Bar ── */
        #mob-tabbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 68px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--sb-border);
            display: flex;
            align-items: center;
            padding: 0 4px;
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.06);
        }

        .mob-tab {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 8px 4px;
            border-radius: 12px;
            color: var(--sb-text);
            text-decoration: none;
            font-size: 10px;
            font-weight: 600;
            transition: 0.15s;
            cursor: pointer;
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .mob-tab i {
            font-size: 19px;
            transition: 0.15s;
        }

        .mob-tab.active {
            color: var(--sb-strong);
        }

        .mob-tab.active i {
            background: var(--sb-strong);
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 15px;
        }

        .mob-tab:hover {
            color: var(--sb-strong);
        }

        /* ── Slide-up Drawer ── */
        #mob-drawer-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1100;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
        }

        #mob-drawer-overlay.open {
            display: block;
        }

        #mob-drawer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1200;
            background: #fff;
            border-radius: 24px 24px 0 0;
            padding: 12px 0 calc(env(safe-area-inset-bottom, 0px) + 32px);
            box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(100%);
            transition: transform 0.36s cubic-bezier(0.32, 0.72, 0, 1);
        }

        #mob-drawer.open {
            transform: translateY(0);
        }

        /* Drag handle */
        .drawer-handle {
            width: 36px;
            height: 4px;
            background: #E5E7EB;
            border-radius: 4px;
            margin: 0 auto 20px;
        }

        /* Drawer header */
        .drawer-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px 16px;
            border-bottom: 1px solid var(--sb-border);
            margin-bottom: 8px;
        }

        .drawer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--sb-strong);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }

        .drawer-uname {
            font-size: 15px;
            font-weight: 700;
            color: var(--sb-strong);
        }

        .drawer-role {
            font-size: 12px;
            font-weight: 500;
            color: var(--sb-text);
        }

        /* Drawer grid */
        .drawer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 8px 16px;
        }

        .drawer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: 16px 12px;
            border-radius: 14px;
            background: var(--sb-hi);
            color: var(--sb-text);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            transition: 0.15s;
            border: 1px solid transparent;
        }

        .drawer-item i {
            font-size: 20px;
        }

        .drawer-item:hover {
            background: #F0F0F0;
            color: var(--sb-strong);
        }

        .drawer-item.active {
            background: var(--sb-strong);
            color: #fff;
            border-color: var(--sb-strong);
        }

        /* Drawer footer actions */
        .drawer-footer-actions {
            display: flex;
            gap: 8px;
            padding: 12px 16px 0;
            border-top: 1px solid var(--sb-border);
            margin-top: 8px;
        }

        .drawer-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--sb-border);
            background: var(--sb-hi);
            color: var(--sb-text);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: 0.15s;
        }

        .drawer-action-btn.danger {
            color: #EF4444;
        }

        .drawer-action-btn:hover {
            background: #F0F0F0;
        }

        .drawer-action-btn.danger:hover {
            background: #FEF2F2;
            border-color: #FECACA;
        }

        /* Push page content up on mobile */
        body {
            padding-bottom: 68px !important;
        }
    }

    /* Desktop only: hide mobile elements */
    @media (min-width: 992px) {

        #mob-tabbar,
        #mob-drawer-overlay,
        #mob-drawer {
            display: none !important;
        }
    }

    /* Mobile: hide desktop sidebar rail */
    @media (max-width: 991px) {
        .sb-container {
            display: none !important;
        }
    }
</style>

<!-- ═══════════════════════════════════
     DESKTOP SIDEBAR
═══════════════════════════════════ -->
<aside class="sb-container">
    <a href="qoon-ai.php" class="sb-logo-area">
        <img src="images/logo.png" alt="QOON" class="sb-logo-img">
    </a>

    <nav class="sb-nav">

        <a href="qoon-ai.php" class="sb-item <?= $currentPage == 'qoon-ai.php' ? 'active' : '' ?>">
            <i class="fas fa-wand-magic-sparkles"></i><span>QOON AI</span>
        </a>
        <a href="index.php" class="sb-item <?= $currentPage == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i><span>Analytics</span>
        </a>

        <div class="sb-section-label">Commerce</div>
        <a href="user.php" class="sb-item <?= $currentPage == 'user.php' ? 'active' : '' ?>">
            <i class="fas fa-user-group"></i><span>QOON Users</span>
        </a>
        <a href="driver.php" class="sb-item <?= $currentPage == 'driver.php' ? 'active' : '' ?>">
            <i class="fas fa-motorcycle"></i><span>QOON Express</span>
        </a>
        <a href="shop.php"
            class="sb-item <?= ($currentPage == 'shop.php' || $currentPage == 'shop-profile.php') ? 'active' : '' ?>">
            <i class="fas fa-store"></i><span>QOON Seller</span>
        </a>
        <a href="shopB2B.php" class="sb-item <?= ($currentPage == 'shopB2B.php') ? 'active' : '' ?>">
            <i class="fas fa-industry"></i><span>QOON Pro</span>
        </a>
        <a href="orders.php" class="sb-item <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
            <i class="fas fa-bag-shopping"></i><span>Orders</span>
        </a>
        <a href="notifications.php" class="sb-item <?= $currentPage == 'notifications.php' ? 'active' : '' ?>"
            style="position:relative;">
            <i class="fas fa-bell"></i><span>Notifications</span>
            <span class="notif-badge" id="notifBadgeSidebar" style="position:absolute;top:8px;left:26px;"></span>
        </a>

        <div class="sb-section-label">Finance</div>
        <a href="wallet.php" class="sb-item <?= $currentPage == 'wallet.php' ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i><span>Financial Core</span>
        </a>
        <a href="apps.php" class="sb-item <?= $currentPage == 'apps.php' ? 'active' : '' ?>">
            <i class="fas fa-cloud-bolt"></i><span>Integrations</span>
        </a>
        <a href="content.php" class="sb-item <?= $currentPage == 'content.php' ? 'active' : '' ?>">
            <i class="fas fa-photo-video"></i><span>Content</span>
        </a>

    </nav>

    <div class="sb-footer">
        <div class="sb-user-row">
            <div class="sb-user-avatar"><?= $adminInitial ?></div>
            <span class="sb-user-name"><?= htmlspecialchars($AdminName) ?></span>
        </div>
        <a href="settings-profile.php"
            class="sb-item <?= strpos($currentPage, 'settings-') !== false || $currentPage === 'bakat.php' ? 'active' : '' ?>">
            <i class="fas fa-gear"></i><span>Settings</span>
        </a>
        <a href="logout.php" class="sb-item danger">
            <i class="fas fa-power-off"></i><span>Logout</span>
        </a>
    </div>
</aside>

<!-- ═══════════════════════════════════
     MOBILE BOTTOM TAB BAR
═══════════════════════════════════ -->
<div id="mob-tabbar">
    <a href="index.php" class="mob-tab <?= $currentPage == 'index.php' ? 'active' : '' ?>">
        <i class="fas fa-chart-pie"></i>
        <span>Analytics</span>
    </a>
    <a href="orders.php" class="mob-tab <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
        <i class="fas fa-bag-shopping"></i>
        <span>Orders</span>
    </a>
    <a href="qoon-ai.php" class="mob-tab <?= $currentPage == 'qoon-ai.php' ? 'active' : '' ?>">
        <i class="fas fa-wand-magic-sparkles"></i>
        <span>AI</span>
    </a>
    <a href="shop.php" class="mob-tab <?= $currentPage == 'shop.php' ? 'active' : '' ?>">
        <i class="fas fa-store"></i>
        <span>Shops</span>
    </a>
    <button class="mob-tab" onclick="openDrawer()" id="mob-more-btn">
        <i class="fas fa-grid-2"></i>
        <span>More</span>
    </button>
</div>

<!-- ═══════════════════════════════════
     MOBILE SLIDE-UP DRAWER
═══════════════════════════════════ -->
<div id="mob-drawer-overlay" onclick="closeDrawer()"></div>
<div id="mob-drawer">
    <div class="drawer-handle"></div>

    <!-- Identity -->
    <div class="drawer-header">
        <div class="drawer-avatar"><?= $adminInitial ?></div>
        <div>
            <div class="drawer-uname"><?= htmlspecialchars($AdminName) ?></div>
            <div class="drawer-role">Administrator</div>
        </div>
    </div>

    <!-- All nav items in a 2-column grid -->
    <div class="drawer-grid">
        <a href="qoon-ai.php" class="drawer-item <?= $currentPage == 'qoon-ai.php' ? 'active' : '' ?>">
            <i class="fas fa-wand-magic-sparkles"></i>QOON AI
        </a>
        <a href="index.php" class="drawer-item <?= $currentPage == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i>Analytics
        </a>
        <a href="user.php" class="drawer-item <?= $currentPage == 'user.php' ? 'active' : '' ?>">
            <i class="fas fa-user-group"></i>QOON Users
        </a>
        <a href="driver.php" class="drawer-item <?= $currentPage == 'driver.php' ? 'active' : '' ?>">
            <i class="fas fa-motorcycle"></i>QOON Express
        </a>
        <a href="shop.php"
            class="drawer-item <?= ($currentPage == 'shop.php' || $currentPage == 'shop-profile.php') ? 'active' : '' ?>">
            <i class="fas fa-store"></i>QOON Seller
        </a>
        <a href="shopB2B.php" class="drawer-item <?= ($currentPage == 'shopB2B.php') ? 'active' : '' ?>">
            <i class="fas fa-industry"></i>QOON Pro
        </a>
        <a href="orders.php" class="drawer-item <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
            <i class="fas fa-bag-shopping"></i>Orders
        </a>
        <a href="notifications.php" class="drawer-item <?= $currentPage == 'notifications.php' ? 'active' : '' ?>"
            style="position:relative;">
            <i class="fas fa-bell"></i>Notifications
            <span class="notif-badge" id="notifBadgeDrawer" style="position:absolute;top:8px;right:8px;"></span>
        </a>
        <a href="wallet.php" class="drawer-item <?= $currentPage == 'wallet.php' ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i>Financial Core
        </a>
        <a href="apps.php" class="drawer-item <?= $currentPage == 'apps.php' ? 'active' : '' ?>">
            <i class="fas fa-cloud-bolt"></i>Integrations
        </a>
        <a href="content.php" class="drawer-item <?= $currentPage == 'content.php' ? 'active' : '' ?>">
            <i class="fas fa-photo-video"></i>Content
        </a>
        <a href="settings-profile.php"
            class="drawer-item <?= strpos($currentPage, 'settings-') !== false || $currentPage == 'bakat.php' ? 'active' : '' ?>">
            <i class="fas fa-gear"></i>Settings
        </a>
    </div>

    <!-- Footer actions -->
    <div class="drawer-footer-actions">
        <button class="drawer-action-btn" onclick="closeDrawer()">
            <i class="fas fa-times"></i> Close
        </button>
        <a href="logout.php" class="drawer-action-btn danger">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</div>

<script>
    function updateBadges(count) {
        ['notifBadgeDesktop', 'notifBadgeMob', 'notifBadgeSidebar', 'notifBadgeDrawer'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = count > 0 ? count : '';
        });
    }
    function openDrawer() {
        document.getElementById('mob-drawer').classList.add('open');
        document.getElementById('mob-drawer-overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        document.getElementById('mob-drawer').classList.remove('open');
        document.getElementById('mob-drawer-overlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    // Swipe down to close
    let startY = 0;
    const drawer = document.getElementById('mob-drawer');
    drawer.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
    drawer.addEventListener('touchend', e => {
        if (e.changedTouches[0].clientY - startY > 60) closeDrawer();
    }, { passive: true });
</script>