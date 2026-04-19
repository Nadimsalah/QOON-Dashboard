<?php
require "conn.php";

$cityID = isset($_GET['cityID']) ? $_GET['cityID'] : '';

// 1. Compute Driver Metrics
$lastweek = date('Y-m-d', strtotime("-7 days"));
$DriverNumber = 0;
$ActiveDriver = 0;
$DriverLastWeeks = 0;
$ActiveDriverweek = 0;

$res = mysqli_query($con, "SELECT * FROM Drivers ORDER BY DriverID desc");
while ($row = mysqli_fetch_assoc($res)) {
    $DriverNumber++;
    $DriverOrdersNum = $row["DriverOrdersNum"];
    if ($lastweek < $row["CreatedAtDrivers"]) {
        $DriverLastWeeks++;
        if ($DriverOrdersNum > 1) {
            $ActiveDriverweek++;
        }
    }
    if ($DriverOrdersNum > 1) {
        $ActiveDriver++;
    }
}

// 2. Compute Configuration Limits
$res_stop = mysqli_query($con, "SELECT * FROM MoneyStop");
$MoneyStopNumber = 0;
$subscription = 0;
$DriverCommesion = 0;
if ($row = mysqli_fetch_assoc($res_stop)) {
    $MoneyStopNumber = $row["MoneyStopNumber"];
    $subscription = $row["subscription"];
    $DriverCommesion = $row["DriverCommesion"];
}

// 3. Compute Financials
$cash = 0; // Unpaid
$cashw = 0; // Total
$res_fin1 = mysqli_query($con, "SELECT sum(OrderPriceFromShop) FROM Orders WHERE PaidForDriver='NotPaid'");
if ($r = mysqli_fetch_array($res_fin1))
    $cash = $r[0] ?? 0;

$res_fin2 = mysqli_query($con, "SELECT sum(OrderPriceFromShop) FROM Orders");
if ($r = mysqli_fetch_array($res_fin2))
    $cashw = $r[0] ?? 0;

// 4. Compute Limit Overflows
$Stoped = 0;
$NoyStoped = 0;
$res33 = mysqli_query($con, "SELECT DriverID FROM Drivers");
while ($row33 = mysqli_fetch_assoc($res33)) {
    $MustPaid = 0;
    $DriverID = $row33["DriverID"];

    $res = mysqli_query($con, "SELECT OrderState, OrderPrice, PaidForDriver, OrderPriceFromShop, Method FROM Orders WHERE DelvryId='$DriverID'");
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['OrderState'] == 'Rated' || $row['OrderState'] == 'Done') {
            if ($row['PaidForDriver'] == 'NotPaid') {
                if ($row['Method'] == "CASH") {
                    $MustPaid += $row['OrderPriceFromShop'];
                } else {
                    $MustPaid = $MustPaid - $row['OrderPriceFromShop'] - $row['OrderPrice'];
                }
            }
        }
    }

    $res_sub = mysqli_query($con, "SELECT count(*) FROM SubscriptionDriver WHERE DriverID='$DriverID' AND Paid = 'NO'");
    $SubscriptionNotPaidtCount = mysqli_fetch_array($res_sub)[0] ?? 0;

    if ($SubscriptionNotPaidtCount > 1) {
        $MustPaid += (($SubscriptionNotPaidtCount - 1) * $subscription);
    }

    if ($MoneyStopNumber < $MustPaid) {
        $Stoped++;
    } else {
        $NoyStoped++;
    }
}

// 5. Driver Table Logic
$DriverName = $_GET["DriverName"] ?? "";
$Page = (int) ($_GET["Page"] ?? 0);
$rr = 10 * $Page;

$cityFilter = $cityID ? " AND CityID = '$cityID'" : "";

if ($DriverName == '') {
    $table_res = mysqli_query($con, "SELECT * FROM Drivers WHERE 1=1 $cityFilter ORDER BY DriverID desc LIMIT $rr, 10");
} else {
    $table_res = mysqli_query($con, "SELECT * FROM Drivers WHERE (FName LIKE '%$DriverName%' OR LName LIKE '%$DriverName%' OR DriverID LIKE '%$DriverName%' OR DriverPhone LIKE '%$DriverName%') $cityFilter ORDER BY DriverOrdersNum desc LIMIT $rr, 10");
}
$cities_res = mysqli_query($con, "SELECT DeliveryZoneID, CityName FROM DeliveryZone");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers Dashboard | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <style>
        :root {
            --bg-app: #F5F6FA;
            --bg-white: #FFFFFF;
            --text-dark: #2A3042;
            --text-gray: #A6A9B6;
            --accent-purple: #623CEA;
            --accent-purple-light: #F0EDFD;
            --accent-blue: #007AFF;
            --accent-orange: #FF8A4C;
            --accent-green: #10B981;
            --accent-red: #E11D48;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
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

        .app-envelope {
            width: 100%;
            height: 100%;
            display: flex;
            overflow: hidden;
        }

        /* Unified Sidebar CSS */
        .sidebar {
            width: 260px;
            background: var(--bg-white);
            display: flex;
            flex-direction: column;
            padding: 40px 0;
            border-right: 1px solid var(--border-color);
        }

        .logo-box {
            display: flex;
            align-items: center;
            padding: 0 30px;
            gap: 12px;
            margin-bottom: 50px;
            text-decoration: none;
        }

        .logo-box img {
            max-height: 50px;
            width: auto;
            object-fit: contain;
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
            transition: all 0.2s ease;
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
            position: relative;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background: var(--accent-purple);
            border-radius: 0 4px 4px 0;
        }

        .main-panel {
            flex: 1;
            padding: 35px 40px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #EBEDF3;
            border-radius: 20px;
            padding: 12px 20px;
            width: 340px;
            gap: 12px;
            transition: 0.3s;
        }

        .search-box:focus-within {
            background: #FFF;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .search-box input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
        }

        .search-box i {
            color: var(--text-gray);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding-left: 10px;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFF;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Buttons & Actions */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 13px;
            padding: 12px 18px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            background: var(--bg-white);
            text-decoration: none;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-purple), #4F28D1);
            color: #FFF;
            border: none;
            box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2);
        }

        .btn-primary:hover {
            color: #FFF;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3);
        }

        .btn-danger {
            background: rgba(225, 29, 72, 0.08);
            color: var(--accent-red);
            border: 1px solid rgba(225, 29, 72, 0.1);
        }

        .btn-success {
            background: rgba(16, 185, 129, 0.08);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .action-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        /* Metric Cards */
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

        .metric-info .sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--accent-green);
        }

        /* Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 25px;
        }

        .card {
            background: var(--bg-white);
            border-radius: 24px;
            padding: 25px;
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
        }

        .card-header {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            padding: 15px 5px;
            text-align: left;
            background: #FFF;
            color: var(--text-gray);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 15px 5px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            transition: 0.2s;
        }

        tr:hover td {
            background: #FDFDFE;
        }

        .u-img {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .user-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name {
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            transition: 0.2s;
        }

        .user-name:hover {
            color: var(--accent-purple);
        }

        /* Rating Bar */
        .rating-bg {
            background: #F0F2F6;
            height: 8px;
            border-radius: 4px;
            width: 100%;
            overflow: hidden;
        }

        .rating-fill {
            background: linear-gradient(90deg, #FFC000, #F59E0B);
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 20px;
            justify-content: space-between;
        }

        .page-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s;
            font-size: 13px;
        }

        .page-btn:hover {
            background: #F8F9FB;
            border-color: #D1D5DF;
        }

        .page-info {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-gray);
        }

        /* Toast Notification */
        .toast-notif {
            position: fixed;
            top: 30px;
            right: 30px;
            background: var(--bg-white);
            border-left: 4px solid var(--accent-green);
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 9999;
            transform: translateX(150%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast-notif.show {
            transform: translateX(0);
        }

        .toast-icon {
            font-size: 20px;
            color: var(--accent-green);
        }

        .toast-text {
            display: flex;
            flex-direction: column;
        }

        .toast-text b {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .toast-text span {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-gray);
        }

        .limit-chart-container {
            position: relative;
            height: 280px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide 260px desktop sidebar rail — bottom tab bar from sidebar.php takes over */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px;
                overflow-y: visible;
                overflow-x: hidden;
            }

            /* Header: stack search + actions */
            .header {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
                margin-bottom: 20px;
            }

            .search-box { width: 100%; }

            .header-actions {
                flex-wrap: wrap;
                gap: 10px;
            }

            /* Action row buttons wrap */
            .action-row {
                gap: 10px;
                margin-bottom: 16px;
            }

            .btn-action {
                font-size: 12px;
                padding: 10px 14px;
            }

            /* KPI: 2×2 grid */
            .top-cards-row {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            .metric-card {
                padding: 16px 12px;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }

            .metric-icon { width: 44px; height: 44px; font-size: 18px; }
            .metric-info .val { font-size: 20px; }
            .metric-info .label { font-size: 10px; }
            .metric-info .sub { font-size: 10px; }

            /* Stack table + chart vertically */
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .card { padding: 18px; }
            .card-header { font-size: 15px; margin-bottom: 14px; }

            /* Table: readable on tablet */
            th, td { padding: 12px 4px; font-size: 13px; }

            /* Chart shorter */
            .limit-chart-container { height: 220px; }

            /* Pagination compact */
            .pagination { flex-wrap: wrap; gap: 8px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .top-cards-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .metric-card { padding: 12px 8px; }
            .metric-info .val { font-size: 18px; }

            /* Hide System ID column — keep name + rating only */
            table th:nth-child(2),
            table td:nth-child(2) { display: none; }

            /* Shorter rating bar on phone */
            .rating-bg { width: 60px; }

            .action-row { gap: 8px; }
            .btn-action { font-size: 11px; padding: 8px 12px; }

            .limit-chart-container { height: 180px; }
        }

    </style>
</head>

<body>
    <?php
    $notif = $_GET['notif'] ?? '';
    if ($notif == 'updated' || $notif == 'driver_added'):
        $notifTitle = $notif == 'driver_added' ? "Driver Registered" : "Successfully Updated";
        $notifDesc = $notif == 'driver_added' ? "The new driver has been added to the system." : "The limits have been saved to the database.";
        ?>
        <div class="toast-notif show" id="successToast">
            <i class="fas fa-check-circle toast-icon"></i>
            <div class="toast-text">
                <b><?= $notifTitle ?></b>
                <span><?= $notifDesc ?></span>
            </div>
        </div>
        <script>
            setTimeout(() => { document.getElementById('successToast').classList.remove('show'); }, 3500);
        </script>
    <?php endif; ?>

    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <form action="driver.php" method="GET" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="DriverName" placeholder="Search drivers by name or ID..."
                        value="<?= htmlspecialchars($DriverName) ?>">
                </form>
                <div class="header-actions">
                    <select
                        onchange="let url = new URL(window.location.href); url.searchParams.set('cityID', this.value); window.location.href = url.href;"
                        class="btn-action" style="outline:none; cursor:pointer; padding: 12px 20px;">
                        <option value="">Global (All Regions)</option>
                        <?php while ($c = mysqli_fetch_assoc($cities_res)) { ?>
                            <option value="<?= $c['DeliveryZoneID'] ?>" <?= $cityID == $c['DeliveryZoneID'] ? 'selected' : '' ?>><?= $c['CityName'] ?></option>
                        <?php } ?>
                    </select>

                    <div style="width: 1px; height: 30px; background: var(--border-color); margin: 0 5px;"></div>


                </div>
            </header>

            <div class="action-row">
                <a href="add-driver.php" class="btn-action btn-primary"><i class="fas fa-plus"></i> Add New Driver</a>
                <a href="UpdateLimit.php?Type=limit" class="btn-action btn-danger"><i class="fas fa-hand-paper"></i>
                    Driver Debt Limit: <?= number_format($MoneyStopNumber) ?> MAD</a>
                <a href="UpdateLimit.php?Type=Subscription" class="btn-action btn-success"><i class="fas fa-sync"></i>
                    Sub Fee: <?= number_format($subscription) ?> MAD</a>
                <a href="UpdateLimit.php?Type=DriverCommesion" class="btn-action"><i class="fas fa-percentage"></i>
                    Commission: <?= number_format($DriverCommesion) ?> MAD</a>
            </div>

            <div class="top-cards-row">
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, #F0EDFD, #FFFFFF); color: var(--accent-purple);"><i
                            class="fas fa-motorcycle"></i></div>
                    <div class="metric-info">
                        <span class="label">Total Drivers</span>
                        <span class="val"><?= number_format($DriverNumber) ?></span>
                        <span class="sub">+<?= $DriverLastWeeks ?> this week</span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.15), #FFFFFF); color: #10B981;"><i
                            class="fas fa-user-check"></i></div>
                    <div class="metric-info">
                        <span class="label">Active Drivers</span>
                        <span class="val" style="color:#10B981;"><?= number_format($ActiveDriver) ?></span>
                        <span class="sub">+<?= $ActiveDriverweek ?> this week</span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(0,122,255,0.15), #FFFFFF); color: var(--accent-blue);">
                        <i class="fas fa-money-bill-wave"></i></div>
                    <div class="metric-info">
                        <span class="label">Total System Cash Out</span>
                        <span class="val" style="color:var(--accent-blue);"><?= number_format($cashw) ?></span>
                        <span class="sub" style="color:var(--text-gray);">Lifetime Value (MAD)</span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.15), #FFFFFF); color: var(--accent-red);">
                        <i class="fas fa-exclamation-circle"></i></div>
                    <div class="metric-info">
                        <span class="label">Unpaid Outstanding</span>
                        <span class="val" style="color:var(--accent-red);"><?= number_format($cash) ?></span>
                        <span class="sub" style="color:var(--text-gray);">Pending driver clearance (MAD)</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Data Table Column -->
                <div class="card" style="flex:1;">
                    <div class="card-header">
                        <span>Driver Network</span>
                        <span style="font-size:12px; font-weight:600; color:var(--text-gray);"><i
                                class="fas fa-tachometer-alt"></i> Speed: 0 MIN</span>
                    </div>

                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50%">Driver Profile</th>
                                <th width="20%">System ID</th>
                                <th width="30%">Quality Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($table_res)):
                                $photo = $row['PersonalPhoto'];
                                if (strpos($photo, 'https://jibler.app/db/db/photo/') !== false) {
                                    // The DB falsely hardcodes a production link even for local uploads. Route it back to local.
                                    $photo = str_replace('https://jibler.app/db/db/', '', $photo);
                                }
                                if (empty($photo))
                                    $photo = 'images/jiblers.jpg';
                                ?>
                                <tr>
                                    <td>
                                        <div class="user-block">
                                            <img src="<?= $photo ?>" class="u-img"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['FName'] . ' ' . $row['LName']) ?>&background=EFEAF8&color=623CEA&bold=true'">
                                            <a href="driver-profile.php?id=<?= $row['DriverID'] ?>"
                                                class="user-name"><?= $row['FName'] . ' ' . $row['LName'] ?></a>
                                        </div>
                                    </td>
                                    <td><span
                                            style="color:var(--text-gray); font-size:13px;">#<?= $row['DriverID'] ?></span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="rating-bg">
                                                <div class="rating-fill"
                                                    style="width:<?= ($row['DriverRate'] / 5) * 100 ?>%;"></div>
                                            </div>
                                            <span
                                                style="font-size:12px; font-weight:800; color:var(--text-dark);"><?= number_format($row['DriverRate'], 1) ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <span class="page-info">Showing <?= $rr + 1 ?> to <?= $rr + 10 ?> drivers</span>
                        <div style="display:flex; gap:5px;">
                            <a href="driver.php?DriverName=<?= urlencode($DriverName) ?>&cityID=<?= urlencode($cityID) ?>&Page=<?= max(0, $Page - 1) ?>"
                                class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            <span class="page-btn"
                                style="background:var(--accent-purple); color:#FFF; border-color:var(--accent-purple);"><?= $Page + 1 ?></span>
                            <a href="driver.php?DriverName=<?= urlencode($DriverName) ?>&cityID=<?= urlencode($cityID) ?>&Page=<?= $Page + 1 ?>"
                                class="page-btn"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Live Metrics Column -->
                <div class="card" style="text-align:center;">
                    <div class="card-header" style="justify-content:center;">Driver Limit Status</div>

                    <div class="limit-chart-container">
                        <canvas id="limitChart"></canvas>
                    </div>

                    <div
                        style="display:flex; justify-content:center; gap:20px; margin-top:20px; font-size:13px; font-weight:700;">
                        <div style="color:var(--accent-green);"><i class="fas fa-circle" style="font-size:8px;"></i>
                            Cleared (<?= $NoyStoped ?>)</div>
                        <div style="color:var(--accent-red);"><i class="fas fa-circle" style="font-size:8px;"></i>
                            Suspended Debt (<?= $Stoped ?>)</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Init Doughnut Chart for Limit Status
        const ctx = document.getElementById('limitChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cleared/Active', 'Suspended for Debt'],
                datasets: [{
                    data: [<?= $NoyStoped ?>, <?= $Stoped ?>],
                    backgroundColor: ['#10B981', '#E11D48'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutoutPercentage: 70,
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                animation: { animateScale: true }
            }
        });
    </script>

    <!-- ═══════════════════════════════════
         ADAM AI ASSISTANT (Driver Page)
    ═══════════════════════════════════ -->
    <style>
        /* FAB */
        .ai-fab {
            position: fixed; bottom: 25px; right: 25px;
            width: 62px; height: 62px; border-radius: 50%;
            background: #fff; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(98,60,234,0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer; z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0; border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(98,60,234,0.45); }
        .ai-fab img { width:100%; height:100%; border-radius:50%; object-fit:cover; }
        .ai-fab-dot {
            position:absolute; bottom:2px; right:2px;
            width:14px; height:14px; background:#22c55e;
            border:2.5px solid #fff; border-radius:50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
        }

        /* Popup */
        .ai-popup {
            position: fixed; bottom: 100px; right: 25px;
            width: 390px; height: 580px;
            background: #fff; border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex; flex-direction: column; overflow: hidden;
            z-index: 9998;
            transform: translateY(20px) scale(0.97); opacity: 0; pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16,1,0.3,1);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ai-popup.open { transform: translateY(0) scale(1); opacity:1; pointer-events:all; }

        .ai-head {
            background: linear-gradient(135deg, #623CEA, #8B5CF6);
            color: #fff; padding: 16px 18px; flex-shrink:0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .ai-head-titles { display:flex; flex-direction:column; line-height:1.3; }
        .ai-head-titles span { font-weight:700; font-size:15px; }
        .ai-head-titles small { font-size:11px; opacity:0.85; margin-top:2px; }
        .ai-close {
            cursor:pointer; font-size:18px; opacity:0.8; transition:0.2s;
            width:32px; height:32px; display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:rgba(255,255,255,0.15);
        }
        .ai-close:hover { opacity:1; background:rgba(255,255,255,0.25); }

        .ai-body {
            flex:1; padding:16px; overflow-y:auto;
            display:flex; flex-direction:column; gap:12px;
            background:#F5F6FA; scroll-behavior:smooth;
        }
        .ai-body::-webkit-scrollbar { width:4px; }
        .ai-body::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:4px; }

        .ai-msg { display:flex; max-width:82%; line-height:1.55; font-size:13.5px; }
        .ai-msg.bot  { align-self:flex-start; }
        .ai-msg.user { align-self:flex-end; }
        .ai-bubble { padding:11px 15px; border-radius:18px; box-shadow:0 1px 3px rgba(0,0,0,0.06); word-break:break-word; }
        .ai-msg.bot  .ai-bubble { background:#fff; color:#111827; border-bottom-left-radius:4px; border:1px solid #E5E7EB; }
        .ai-msg.user .ai-bubble { background:#623CEA; color:#fff; border-bottom-right-radius:4px; }

        .ai-typing { font-size:12px; color:#9CA3AF; display:none; padding:0 16px 10px; background:#F5F6FA; flex-shrink:0; }
        .ai-typing span { display:inline-block; animation:typBounce 1.2s infinite; }
        .ai-typing span:nth-child(2) { animation-delay:.2s; }
        .ai-typing span:nth-child(3) { animation-delay:.4s; }
        @keyframes typBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

        .ai-foot { padding:12px 14px; background:#fff; border-top:1px solid #F0F0F0; display:flex; gap:10px; align-items:center; flex-shrink:0; }
        .ai-input {
            flex:1; border:1.5px solid #E5E7EB; border-radius:22px;
            padding:10px 16px; font-size:13.5px; outline:none;
            background:#F9FAFB; transition:0.2s; font-family:inherit;
        }
        .ai-input:focus { border-color:#623CEA; background:#fff; box-shadow:0 0 0 3px rgba(98,60,234,0.08); }
        .ai-send {
            width:40px; height:40px; border-radius:50%;
            background:#623CEA; color:white; border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#7C3AED; transform:scale(1.05); }

        /* Mobile */
        @media (max-width:600px) {
            .ai-fab { right:16px; bottom:80px; }
            .ai-popup {
                right:0; left:0; bottom:0;
                width:100%; height:90dvh;
                border-radius:24px 24px 0 0;
                transform:translateY(100%);
            }
            .ai-popup.open { transform:translateY(0); }
            .ai-foot { padding-bottom:max(12px, env(safe-area-inset-bottom)); }
        }
    </style>

    <!-- FAB Button -->
    <div class="ai-fab" id="aiDriverFab" onclick="toggleDriverAI()">
        <img src="tamo.jpg" alt="Tamo"
             onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>

    <!-- Chat Popup -->
    <div class="ai-popup" id="aiDriverPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="tamo.jpg" alt="Tamo" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);"
                         onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
                    <div style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#10B981; border:2px solid #fff; border-radius:50%;"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Tamo AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#10B981;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleDriverAI()"></i>
        </div>

        <div class="ai-body" id="aiDriverBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">
                    👋 Hello! I am <b>Tamo</b>, your QOON Express AI assistant.<br><br>
                    I can help you analyze driver performance, check debt status, review delivery stats, or find any driver by name or ID.<br><br>
                    How can I help you?
                </div>
            </div>
        </div>

        <div class="ai-typing" id="aiDriverTyping">
            Tamo is typing <span>•</span><span>•</span><span>•</span>
        </div>

        <div class="ai-foot">
            <input type="text" class="ai-input" id="aiDriverInput"
                   placeholder="Ask Tamo about drivers, debt, performance..."
                   onkeydown="if(event.key==='Enter') sendDriverMsg()">
            <button class="ai-send" onclick="sendDriverMsg()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
        function toggleDriverAI() {
            document.getElementById('aiDriverPopup').classList.toggle('open');
            if (document.getElementById('aiDriverPopup').classList.contains('open')) {
                document.getElementById('aiDriverInput').focus();
            }
        }

        async function sendDriverMsg() {
            const input = document.getElementById('aiDriverInput');
            const msg   = input.value.trim();
            if (!msg) return;
            input.value = '';

            const body  = document.getElementById('aiDriverBody');
            const typing = document.getElementById('aiDriverTyping');

            // Add user bubble
            const userDiv = document.createElement('div');
            userDiv.className = 'ai-msg user';
            userDiv.innerHTML = `<div class="ai-bubble">${msg.replace(/</g,'&lt;')}</div>`;
            body.appendChild(userDiv);
            body.scrollTop = body.scrollHeight;

            // Show typing
            typing.style.display = 'block';
            body.scrollTop = body.scrollHeight;

            try {
                const res  = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        message: msg,
                        context: 'driver',
                        page_data: {
                            total_drivers: <?= $DriverNumber ?>,
                            active_drivers: <?= $ActiveDriver ?>,
                            new_this_week: <?= $DriverLastWeeks ?>,
                            unpaid_cash_mad: <?= $cash ?>,
                            total_cash_mad: <?= $cashw ?>,
                            suspended_drivers: <?= $Stoped ?>,
                            cleared_drivers: <?= $NoyStoped ?>,
                            debt_limit_mad: <?= $MoneyStopNumber ?>,
                            subscription_fee: <?= $subscription ?>,
                            commission_mad: <?= $DriverCommesion ?>
                        }
                    })
                });
                const data = await res.json();
                typing.style.display = 'none';

                const botDiv = document.createElement('div');
                botDiv.className = 'ai-msg bot';
                const reply = (data.reply || data.error || 'No response.').replace(/\n/g,'<br>');
                botDiv.innerHTML = `<div class="ai-bubble">${reply}</div>`;
                body.appendChild(botDiv);
                body.scrollTop = body.scrollHeight;

            } catch(e) {
                typing.style.display = 'none';
                const errDiv = document.createElement('div');
                errDiv.className = 'ai-msg bot';
                errDiv.innerHTML = '<div class="ai-bubble" style="color:#DC2626;">Connection error. Please try again.</div>';
                body.appendChild(errDiv);
                body.scrollTop = body.scrollHeight;
            }
        }
    </script>
</body>
</html>