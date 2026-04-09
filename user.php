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
            border: 1px solid rgba(255,255,255,0.8);
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
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.5);
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

        /* Sidebar Toggle Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 10000;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .top-cards-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .sidebar {
                position: fixed;
                left: -260px;
                height: 100%;
                z-index: 10001;
                transition: 0.3s;
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-overlay.active {
                display: block;
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
                                    <?= $c['CityName'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="profile" onclick="window.location='settings-profile.php'">
                        <img src="images/avatar-1.png">
                        <span>Administrator</span>
                        <i class="fas fa-chevron-down" style="font-size:10px; color:#A6A9B6;"></i>
                    </div>
                </div>
            </header>

            <div class="top-cards-row">
                <div class="card metric-card" onclick="location.href='user_list.php?type=all&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #F0EDFD, #FFFFFF); color: var(--accent-purple);"><i class="fas fa-users"></i></div>
                    <div class="metric-info">
                        <span class="label">Total Users</span>
                        <span class="val"><?= number_format($UserNumber) ?></span>
                    </div>
                </div>
                <div class="card metric-card" onclick="location.href='user_list.php?type=new&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), #FFFFFF); color: #10B981;"><i class="fas fa-user-plus"></i></div>
                    <div class="metric-info">
                        <span class="label">New in Range</span>
                        <span class="val" style="color:#10B981;"><?= number_format($NewUsers) ?></span>
                    </div>
                </div>
                <div class="card metric-card" onclick="location.href='user_list.php?type=android&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon" style="background: linear-gradient(135deg, rgba(0,122,255,0.15), #FFFFFF); color: var(--accent-blue);"><i class="fab fa-android"></i></div>
                    <div class="metric-info">
                        <span class="label">Android</span>
                        <span class="val" style="color:var(--accent-blue);"><?= number_format($AndroidCount) ?></span>
                    </div>
                </div>
                <div class="card metric-card" onclick="location.href='user_list.php?type=ios&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>'">
                    <div class="metric-icon" style="background: linear-gradient(135deg, rgba(255,138,76,0.15), #FFFFFF); color: var(--accent-orange);"><i class="fab fa-apple"></i></div>
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
                                    <td width="55"><img src="<?= $u['UserPhoto'] ?: 'images/ensan.jpg' ?>" class="u-img">
                                    </td>
                                    <td>
                                        <div style="font-weight:700;"><?= $u['name'] ?></div>
                                        <div style="font-size:11px; color:var(--text-gray);"><?= $u['Email'] ?></div>
                                    </td>
                                    <td align="right" style="color:var(--text-gray);">
                                        <?= date('d M', strtotime($u['CreatedAtUser'])) ?></td>
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
                                <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:8px; border-bottom:1px solid #F9FAFB;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <?php if (!empty($u['UserPhoto'])) { ?>
                                            <img src="<?= $u['UserPhoto'] ?>" style="width:38px; height:38px; border-radius:12px; object-fit:cover; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                        <?php } else { ?>
                                            <div style="width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, var(--accent-purple), #FFC000); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; box-shadow: 0 4px 10px rgba(98, 60, 234, 0.2);"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
                                        <?php } ?>
                                        <span style="font-weight:700; font-size:14px; color:var(--text-dark);"><?= $u['name'] ?></span>
                                    </div>
                                    <span style="font-weight:800; color:var(--accent-blue); background:rgba(0,122,255,0.08); padding:6px 12px; border-radius:20px; font-size:12px; border:1px solid rgba(0,122,255,0.1);">
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
</body>

</html>