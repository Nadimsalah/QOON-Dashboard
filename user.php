<?php
require_once "conn.php";

// 1. Initialize Filters (Match Home Dash)
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$cityID = isset($_GET['city_id']) ? $_GET['city_id'] : '';

$userDateFilter = " AND CreatedAtUser BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$cityFilter = $cityID ? " AND CityID = '$cityID'" : "";

// Data Aggregation
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $cityFilter");
$UserNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $userDateFilter $cityFilter");
$NewUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE UserType='ANDROID' $cityFilter");
$AndroidCount = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE UserType!='ANDROID' $cityFilter");
$IphoneCount = mysqli_fetch_assoc($res)['total'] ?? 0;

// Gender Breakdown
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE Gender='Male' $cityFilter $userDateFilter");
$MaleCount = mysqli_fetch_assoc($res)['total'] ?? 0;
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE Gender!='Male' $cityFilter $userDateFilter");
$FemaleCount = mysqli_fetch_assoc($res)['total'] ?? 0;

// Daily Registration Chart (Trend)
$days = [];
$regData = [];
$start = new DateTime($startDate);
$end = new DateTime($endDate);
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));
foreach ($period as $dt) {
    if (count($days) >= 7)
        break;
    $d = $dt->format("Y-m-d");
    $days[] = $dt->format("D");
    $res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE CreatedAtUser LIKE '$d%' $cityFilter");
    $regData[] = (int) mysqli_fetch_assoc($res)['total'];
}

// Recent Signups
$recentSignups = [];
$res = mysqli_query($con, "SELECT * FROM Users WHERE name != '' $cityFilter $userDateFilter ORDER BY UserID DESC LIMIT 3");
while ($u = mysqli_fetch_assoc($res)) {
    $recentSignups[] = $u;
}

// Top Users
$topUsers = [];
$res = mysqli_query($con, "SELECT name, Balance, UserPhoto FROM Users WHERE name != '' $cityFilter ORDER BY Balance DESC LIMIT 5");
while ($u = mysqli_fetch_assoc($res)) {
    $topUsers[] = $u;
}

$cities_res = mysqli_query($con, "SELECT CityID, CityName FROM Cities WHERE Status = 1");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | QOON Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        :root {
            --bg-body: #EFEAF8;
            --bg-app: #F5F6FA;
            --bg-white: #FFFFFF;
            --text-dark: #2A3042;
            --text-gray: #A6A9B6;
            --accent-purple: #623CEA;
            --accent-purple-light: #F0EDFD;
            --accent-orange: #FF8A4C;
            --accent-blue: #007AFF;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-app);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* HD Shimmer */
        #shimmerOverlay {
            position: fixed;
            inset: 0;
            background: #FFF;
            z-index: 10002;
            display: flex;
            padding: 20px;
            gap: 20px;
        }

        .skeleton-box {
            background: #F8F9FA;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .skeleton-box::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: shimmer 1.2s infinite;
        }

        .app-envelope {
            width: 100%;
            height: 100%;
            display: flex;
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background: var(--bg-white);
            display: flex;
            flex-direction: column;
            padding: 40px 0;
            border-right: 1px solid #EBECEF;
        }

        .logo-box {
            display: flex;
            align-items: center;
            padding: 0 30px;
            gap: 12px;
            margin-bottom: 50px;
            text-decoration: none;
        }

        .logo-box .icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-purple), #FFC000);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 18px;
        }

        .logo-box .text {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 20px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 20px;
            border-radius: 12px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .nav-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .nav-item:hover:not(.active) {
            color: var(--text-dark);
            background: #F8F9FB;
        }

        .nav-item.active {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
        }

        .main-panel {
            flex: 1;
            padding: 35px 40px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .search {
            display: flex;
            align-items: center;
            background: #EBEDF3;
            border-radius: 20px;
            padding: 12px 20px;
            width: 320px;
            gap: 12px;
        }

        .search input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            color: var(--text-dark);
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
            font-size: 14px;
            font-weight: 500;
        }

        .action-combo {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .period-container {
            position: relative;
        }

        .period-dropdown {
            position: absolute;
            top: 45px;
            right: 0;
            width: 200px;
            background: #FFF;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: none;
            flex-direction: column;
            gap: 5px;
            z-index: 1000;
            border: 1px solid #F0F2F6;
        }

        .period-dropdown.active {
            display: flex;
        }

        .preset-btn {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
            border: none;
            background: none;
            transition: 0.2s;
        }

        .preset-btn:hover {
            background: var(--bg-app);
            color: var(--accent-purple);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 25px;
        }

        .card {
            background: var(--bg-white);
            border-radius: var(--border-radius-lg, 24px);
            padding: 25px;
            box-shadow: var(--shadow-card);
            position: relative;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-dark);
        }

        .top-cards-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .metric-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 25px 20px;
            border-radius: 24px;
            background: var(--bg-white);
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.8);
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(98, 60, 234, 0.1);
        }

        .metric-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            z-index: 1;
        }

        .metric-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            z-index: 1;
        }

        .metric-info .label {
            color: var(--text-gray);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-info .val {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table td {
            padding: 12px 0;
            border-bottom: 1px solid #F9FAFB;
            font-size: 13px;
            font-weight: 600;
        }

        .u-img {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            object-fit: cover;
        }

        /* ----- MOBILE RESPONSIVENESS ----- */
        @media (max-width: 991px) {
            /* Allow page to scroll vertically on mobile */
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide the local inline sidebar (260px rail) — sidebar.php handles mobile nav */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 15px;
                overflow-y: visible;
            }

            .header {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
                margin-bottom: 20px;
            }

            .search { width: 100%; }

            .header-actions {
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
            }

            .action-combo { font-size: 13px; }

            /* 2×2 metric cards */
            .top-cards-row {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            .metric-card {
                padding: 14px;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }

            .metric-icon { width: 44px; height: 44px; font-size: 18px; }
            .metric-info .val { font-size: 20px; }

            /* Stack dashboard grid */
            .dashboard-grid {
                grid-template-columns: 1fr;
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .col-left,
            .col-right {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            /* Shorter chart on tablet */
            #regChart { max-height: 240px; }

            /* Hide shimmer sidebar skeleton */
            #shimmerOverlay>.skeleton-box:first-child { display: none !important; }
        }

        /* ----- PHONE (≤ 600px) ----- */
        @media (max-width: 600px) {
            .top-cards-row {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .metric-card { padding: 12px; }
            .metric-info .val { font-size: 18px; }
            .metric-info .label { font-size: 10px; }

            .card { padding: 16px; }
            .card-title { font-size: 14px; }

            /* Chart height on phone */
            #regChart  { max-height: 200px !important; }

            /* Mini-table: hide date column on very small screens */
            .mini-table td:last-child { display: none; }

            /* Period dropdown: full width */
            .period-dropdown { width: 100%; right: auto; left: 0; }
        }

        /* ----- USER AI ASSISTANT ----- */
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(98, 60, 234, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(98,60,234,0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #22c55e;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
        }

        /* ── AI CHAT POPUP ── */
        .ai-popup {
            position: fixed;
            bottom: 100px; right: 25px;
            width: 390px; height: 580px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex; flex-direction: column;
            overflow: hidden;
            z-index: 9998;
            transform: translateY(20px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ai-popup.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }

        /* Header */
        .ai-head {
            background: linear-gradient(135deg, #623CEA, #8B5CF6);
            color: #fff;
            padding: 16px 18px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .ai-head-titles { display:flex; flex-direction:column; line-height:1.3; }
        .ai-head-titles span { font-weight:700; font-size:15px; }
        .ai-head-titles small { font-size:11px; opacity:0.85; margin-top:2px; }
        .ai-close {
            cursor:pointer; font-size:18px; opacity:0.8;
            transition:0.2s; width:32px; height:32px;
            display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:rgba(255,255,255,0.15);
        }
        .ai-close:hover { opacity:1; background:rgba(255,255,255,0.25); }

        /* Messages */
        .ai-body {
            flex: 1; padding: 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
            background: #F5F6FA;
            scroll-behavior: smooth;
        }
        .ai-body::-webkit-scrollbar { width: 4px; }
        .ai-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }

        .ai-msg { display:flex; max-width:82%; line-height:1.55; font-size:13.5px; }
        .ai-msg.bot  { align-self: flex-start; }
        .ai-msg.user { align-self: flex-end; }
        .ai-bubble {
            padding: 11px 15px; border-radius: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            word-break: break-word;
        }
        .ai-msg.bot  .ai-bubble { background:#fff; color:#111827; border-bottom-left-radius:4px; border:1px solid #E5E7EB; }
        .ai-msg.user .ai-bubble { background:#623CEA; color:#fff; border-bottom-right-radius:4px; }

        /* Typing */
        .ai-typing {
            font-size:12px; color:#9CA3AF;
            display:none; padding:0 16px 10px;
            background:#F5F6FA; flex-shrink:0;
        }
        .ai-typing span { display:inline-block; animation: typBounce 1.2s infinite; }
        .ai-typing span:nth-child(2) { animation-delay:.2s; }
        .ai-typing span:nth-child(3) { animation-delay:.4s; }
        @keyframes typBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

        /* Input foot */
        .ai-foot {
            padding: 12px 14px;
            background: #fff; border-top: 1px solid #F0F0F0;
            display:flex; gap:10px; align-items:center;
            flex-shrink: 0;
        }
        .ai-input {
            flex: 1; border: 1.5px solid #E5E7EB; border-radius: 22px;
            padding: 10px 16px; font-size:13.5px;
            outline:none; background:#F9FAFB;
            transition:0.2s; font-family:inherit;
            resize: none; line-height: 1.4;
        }
        .ai-input:focus { border-color:#623CEA; background:#fff; box-shadow:0 0 0 3px rgba(98,60,234,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#623CEA; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#7C3AED; transform:scale(1.05); }

        /* ── MOBILE: full-screen chat ── */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }

            .ai-popup {
                /* slide up from bottom, full-screen */
                right: 0; left: 0; bottom: 0;
                width: 100%; height: 90dvh;
                border-radius: 24px 24px 0 0;
                transform: translateY(100%);
            }
            .ai-popup.open { transform: translateY(0); }

            .ai-head { padding: 14px 16px; }
            .ai-body { padding: 12px; }
            .ai-foot {
                padding: 10px 12px;
                padding-bottom: max(12px, env(safe-area-inset-bottom));
            }
        }
    </style>
</head>

<body>
    <div id="shimmerOverlay">
        <div class="skeleton-box" style="width:260px; height:100%;"></div>
        <div style="flex:1; display:flex; flex-direction:column; gap:20px;">
            <div class="skeleton-box" style="height:60px;"></div>
            <div style="display:flex; gap:20px;">
                <div class="skeleton-box" style="flex:1; height:120px;"></div>
                <div class="skeleton-box" style="flex:1; height:120px;"></div>
                <div class="skeleton-box" style="flex:1; height:120px;"></div>
                <div class="skeleton-box" style="flex:1; height:120px;"></div>
            </div>
            <div class="skeleton-box" style="height:400px;"></div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search Users...">
                </div>
                <div class="header-actions">
                    <div class="period-container">
                        <div class="action-combo" id="periodTrigger">
                            <span class="label">Period:</span>
                            <span><?= date('d.m.Y', strtotime($startDate)) ?> -
                                <?= date('d.m.Y', strtotime($endDate)) ?></span>
                            <i class="fas fa-chevron-down" style="font-size:10px; color:#A6A9B6;"></i>
                        </div>
                        <div class="period-dropdown" id="periodMenu">
                            <button class="preset-btn" onclick="applyPreset('today')">Today</button>
                            <button class="preset-btn" onclick="applyPreset('yesterday')">Yesterday</button>
                            <button class="preset-btn" onclick="applyPreset('this-week')">This Week</button>
                            <button class="preset-btn" onclick="applyPreset('this-month')">This Month</button>
                            <button class="preset-btn" onclick="applyPreset('this-year')">This Year</button>
                            <button class="preset-btn" onclick="applyPreset('max')">Max</button>
                            <div class="custom-divider"></div>
                            <button class="preset-btn" id="customTrigger">Custom Range...</button>
                        </div>
                    </div>
                    <div class="action-combo">
                        <span class="label">City:</span>
                        <select onchange="location.href='user.php?city_id='+this.value"
                            style="border:none; outline:none; font-weight:700; background:none; cursor:pointer;">
                            <option value="">All Cities</option>
                            <?php while ($c = mysqli_fetch_assoc($cities_res)) { ?>
                                <option value="<?= $c['CityID'] ?>" <?= $cityID == $c['CityID'] ? 'selected' : '' ?>>
                                    <?= $c['CityName'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
            </header>

            <div class="top-cards-row">
                <div class="card metric-card"
                    onclick="location.href='user_list.php?type=all&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, #F0EDFD, #FFFFFF); color: var(--accent-purple);"><i
                            class="fas fa-users"></i></div>
                    <div class="metric-info">
                        <span class="label">Total Users</span>
                        <span class="val"><?= number_format($UserNumber) ?></span>
                    </div>
                </div>
                <div class="card metric-card"
                    onclick="location.href='user_list.php?type=new&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.15), #FFFFFF); color: #10B981;"><i
                            class="fas fa-user-plus"></i></div>
                    <div class="metric-info">
                        <span class="label">New in Range</span>
                        <span class="val" style="color:#10B981;"><?= number_format($NewUsers) ?></span>
                    </div>
                </div>
                <div class="card metric-card"
                    onclick="location.href='user_list.php?type=android&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(0,122,255,0.15), #FFFFFF); color: var(--accent-blue);">
                        <i class="fab fa-android"></i></div>
                    <div class="metric-info">
                        <span class="label">Android</span>
                        <span class="val" style="color:var(--accent-blue);"><?= number_format($AndroidCount) ?></span>
                    </div>
                </div>
                <div class="card metric-card"
                    onclick="location.href='user_list.php?type=ios&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(255,138,76,0.15), #FFFFFF); color: var(--accent-orange);">
                        <i class="fab fa-apple"></i></div>
                    <div class="metric-info">
                        <span class="label">iOS</span>
                        <span class="val" style="color:var(--accent-orange);"><?= number_format($IphoneCount) ?></span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="col-left">
                    <div class="card" style="flex:1;">
                        <div class="card-header"><span class="card-title">Registration Trend</span></div>
                        <div style="height:320px;"><canvas id="regChart"></canvas></div>
                    </div>
                    <div class="card" style="margin-top:25px;">
                        <div class="card-header"><span class="card-title">Recent Signups</span></div>
                        <table class="mini-table">
                            <?php foreach ($recentSignups as $u) { ?>
                                <tr>
                                    <td width="55">
                                        <?php
                                        $uPhoto = $u['UserPhoto'];
                                        if (strpos($uPhoto, 'https://jibler.app/db/db/photo/') !== false) {
                                            $uPhoto = str_replace('https://jibler.app/db/db/', '', $uPhoto);
                                        }
                                        ?>
                                        <?php if (!empty($uPhoto)) { ?>
                                            <img src="<?= $uPhoto ?>" class="u-img"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=EFEAF8&color=623CEA&bold=true'">
                                        <?php } else { ?>
                                            <div
                                                style="width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, var(--accent-purple), #FFC000); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; box-shadow: 0 4px 10px rgba(98, 60, 234, 0.2);">
                                                <?= strtoupper(substr(trim($u['name']), 0, 1) . (strpos(trim($u['name']), ' ') !== false ? substr(explode(' ', trim($u['name']))[1], 0, 1) : '')) ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;"><?= $u['name'] ?></div>
                                        <div style="font-size:11px; color:var(--text-gray);"><?= $u['Email'] ?></div>
                                    </td>
                                    <td align="right" style="color:var(--text-gray);">
                                        <?= date('d M', strtotime($u['CreatedAtUser'])) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <div class="col-right">
                    <div class="card" style="text-align:center;">
                        <div class="card-header"><span class="card-title">Gender Breakdown</span></div>
                        <div style="height:220px; position:relative;">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div
                            style="display:flex; justify-content:center; gap:20px; margin-top:20px; font-size:12px; font-weight:700;">
                            <div style="color:var(--accent-purple);"><i class="fas fa-circle"
                                    style="font-size:8px;"></i> Male (<?= $MaleCount ?>)</div>
                            <div style="color:var(--accent-orange);"><i class="fas fa-circle"
                                    style="font-size:8px;"></i> Female (<?= $FemaleCount ?>)</div>
                        </div>
                    </div>
                    <div class="card" style="margin-top:25px;">
                        <div class="card-header"><span class="card-title">Top Spenders</span></div>
                        <div style="display:flex; flex-direction:column; gap:15px;">
                            <?php foreach ($topUsers as $u) { ?>
                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; padding-bottom:8px; border-bottom:1px solid #F9FAFB;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <?php if (!empty($u['UserPhoto'])) { ?>
                                            <img src="<?= $u['UserPhoto'] ?>"
                                                style="width:38px; height:38px; border-radius:12px; object-fit:cover; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                        <?php } else { ?>
                                            <div
                                                style="width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, var(--accent-purple), #FFC000); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; box-shadow: 0 4px 10px rgba(98, 60, 234, 0.2);">
                                                <?= strtoupper(substr(trim($u['name']), 0, 1) . (strpos(trim($u['name']), ' ') !== false ? substr(explode(' ', trim($u['name']))[1], 0, 1) : '')) ?>
                                            </div>
                                        <?php } ?>
                                        <span
                                            style="font-weight:700; font-size:14px; color:var(--text-dark);"><?= $u['name'] ?></span>
                                    </div>
                                    <span
                                        style="font-weight:800; color:var(--accent-blue); background:rgba(0,122,255,0.08); padding:6px 12px; border-radius:20px; font-size:12px; border:1px solid rgba(0,122,255,0.1);">
                                        <?= number_format($u['Balance'] ?? 0) ?> MAD
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        window.onload = function () {
            const shim = document.getElementById('shimmerOverlay');
            if (shim) { shim.style.transition = "opacity 0.4s ease"; shim.style.opacity = "0"; setTimeout(() => shim.style.display = "none", 400); }

            const pt = document.getElementById('periodTrigger');
            const pm = document.getElementById('periodMenu');
            pt.onclick = () => pm.classList.toggle('active');
            document.addEventListener('click', (e) => { if (!pt.contains(e.target) && !pm.contains(e.target)) pm.classList.remove('active'); });

            window.applyPreset = (type) => {
                let s, e; const today = new Date(); const fmt = (d) => d.toISOString().split('T')[0];
                switch (type) {
                    case 'today': s = e = fmt(today); break;
                    case 'yesterday': let y = new Date(today); y.setDate(today.getDate() - 1); s = e = fmt(y); break;
                    case 'this-week': let f = today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1); s = fmt(new Date(today.setDate(f))); e = fmt(new Date()); break;
                    case 'this-month': s = fmt(new Date(today.getFullYear(), today.getMonth(), 1)); e = fmt(new Date()); break;
                    case 'this-year': s = fmt(new Date(today.getFullYear(), 0, 1)); e = fmt(new Date()); break;
                    case 'max': s = '2020-01-01'; e = fmt(new Date()); break;
                }
                if (s && e) location.href = `user.php?start_date=${s}&end_date=${e}&city_id=<?= $cityID ?>`;
            };

            flatpickr("#customTrigger", {
                mode: "range", dateFormat: "Y-m-d", positionElement: pt, onClose: (sel, str, inst) => {
                    if (sel.length === 2) location.href = `user.php?start_date=${inst.formatDate(sel[0], "Y-m-d")}&end_date=${inst.formatDate(sel[1], "Y-m-d")}&city_id=<?= $cityID ?>`;
                }
            });

            new Chart(document.getElementById('regChart').getContext('2d'), { type: 'line', data: { labels: <?= json_encode($days) ?>, datasets: [{ label: 'Signups', data: <?= json_encode($regData) ?>, borderColor: '#623CEA', backgroundColor: 'rgba(98, 60, 234, 0.08)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { color: '#F0F2F6' }, ticks: { stepSize: 1 } } } } });
            new Chart(document.getElementById('genderChart').getContext('2d'), { type: 'doughnut', data: { labels: ['Male', 'Female'], datasets: [{ data: [<?= $MaleCount ?>, <?= $FemaleCount ?>], backgroundColor: ['#623CEA', '#FF8A4C'], borderWidth: 0, cutout: '75%' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
        }
    </script>

    <!-- USER AI ASSISTANT OVERLAY -->
    <div class="ai-fab" id="aiUserFab" onclick="toggleUserAI()" style="position:fixed;">
        <img src="adam.png" alt="Adam"
             onerror="this.src='https://ui-avatars.com/api/?name=Adam&background=EFEAF8&color=623CEA&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiUserPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="adam.png" alt="Adam" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Adam&background=EFEAF8&color=623CEA&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#10B981; border:2px solid #fff; border-radius:50%; box-shadow:0 2px 4px rgba(16,185,129,0.4);"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Adam AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#10B981; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleUserAI()"></i>
        </div>
        <div class="ai-body" id="aiUserBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">Hello! I am Adam, your virtual AI assistant for QOON Users. You can ask me to find specific users via ID/Phone or summarize registration data. How can I help you?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiUserTyping">Analyzing database...</div>
        <div class="ai-foot">
            <input type="text" id="aiUserInput" class="ai-input" placeholder="E.g. Show me user ID = 5..." onkeypress="if(event.key === 'Enter') sendUserAIMessage()">
            <button class="ai-send" onclick="sendUserAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let userChatHistory = [];
        
        function toggleUserAI() {
            document.getElementById('aiUserPopup').classList.toggle('open');
            document.getElementById('aiUserInput').focus();
        }

        async function sendUserAIMessage() {
            const input = document.getElementById('aiUserInput');
            const msg = input.value.trim();
            if(!msg) return;

            addUserAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiUserTyping');
            typing.style.display = 'block';
            scrollUserAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, history: userChatHistory })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addUserAIMsg('bot', data.reply);
                        userChatHistory.push({ role: 'user', content: msg });
                        userChatHistory.push({ role: 'ai', content: data.reply });
                    } else if (data.error) {
                        addUserAIMsg('bot', 'Error: ' + data.error);
                    } else {
                        addUserAIMsg('bot', 'Network error or unable to reach AI cluster.');
                    }
                } catch (jsonErr) {
                    addUserAIMsg('bot', 'A connection error occurred. Raw response:<br> ' + textOutput.substring(0,200));
                }
            } catch(e) {
                typing.style.display = 'none';
                addUserAIMsg('bot', 'A connection error occurred. Please try again. ' + e.message);
            }
        }

        function addUserAIMsg(sender, text) {
            const body = document.getElementById('aiUserBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            
            // Format bold text using regex mapping
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            formattedText = formattedText.replace(/\n/g, '<br>');

            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollUserAIBottom();
        }

        function scrollUserAIBottom() {
            const body = document.getElementById('aiUserBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>

</html>