<?php
require "conn.php";

$cityID = isset($_GET['cityID']) ? (int) $_GET['cityID'] : '';
$CityName = isset($_GET['CityName']) ? $_GET['CityName'] : '';
$ShopNameFilter = isset($_GET['ShopName']) ? $_GET['ShopName'] : '';
$Page = isset($_GET['Page']) ? (int) $_GET['Page'] : 0;
$limit = 10;
$offset = $Page * $limit;

// 1. Fetch Filter Variables
$CityLat = 0;
$CityLongt = 0;
$Deliveryzone = 0;
if ($cityID) {
    if ($resCity = mysqli_query($con, "SELECT * FROM DeliveryZone WHERE DeliveryZoneID = $cityID")) {
        if ($rowCity = mysqli_fetch_assoc($resCity)) {
            $CityLat = $rowCity["CityLat"];
            $CityLongt = $rowCity["CityLongt"];
            $Deliveryzone = $rowCity["Deliveryzone"];
        }
    }
}

// 2. Compute Master Metrics
$ShopsNumber = 0;
$ShopsLastWeeks = 0;
$Our = 0;
$BakatID2 = 0;
$BakatID3 = 0;
$lastweek = date('Y-m-d', strtotime("-7 days"));

$baseQuery = "SELECT * FROM Shops";
if ($cityID) {
    $baseQuery = "SELECT *, (6372.797 * acos(cos(radians($CityLat)) * cos(radians(ShopLat)) * cos(radians(ShopLongt) - radians($CityLongt)) + sin(radians($CityLat)) * sin(radians(ShopLat)))) AS distance FROM Shops WHERE Shops.Status = 'ACTIVE' HAVING distance <= $Deliveryzone ORDER BY priority DESC, distance ASC";
}
$resMetrics = mysqli_query($con, $baseQuery);
while ($row = mysqli_fetch_assoc($resMetrics)) {
    $ShopsNumber++;
    if ($lastweek < $row["CreatedAtShops"])
        $ShopsLastWeeks++;
    if ($row["Type"] == 'Our')
        $Our++;
    if ($row["BakatID"] == 2)
        $BakatID2++;
    elseif ($row["BakatID"] == 3)
        $BakatID3++;
}
$Freemium = max(0, $ShopsNumber - $Our + 900000); // Maintained legacy artificial offset logic

// 3. Build Table Query
$clause = [];
if (!empty($ShopNameFilter))
    $clause[] = "ShopName LIKE '%" . mysqli_real_escape_string($con, $ShopNameFilter) . "%'";
$whereStr = count($clause) > 0 ? "WHERE " . implode(' AND ', $clause) : "";

if ($cityID) {
    $tableQuery = "SELECT *, (6372.797 * acos(cos(radians($CityLat)) * cos(radians(ShopLat)) * cos(radians(ShopLongt) - radians($CityLongt)) + sin(radians($CityLat)) * sin(radians(ShopLat)))) AS distance FROM Shops WHERE Shops.Status = 'ACTIVE' AND ShopName LIKE '%" . mysqli_real_escape_string($con, $ShopNameFilter) . "%' HAVING distance <= $Deliveryzone ORDER BY priority DESC, distance ASC LIMIT $offset, $limit";
} else {
    $tableQuery = "SELECT * FROM Shops $whereStr ORDER BY ShopID DESC LIMIT $offset, $limit";
}

$table_res = mysqli_query($con, $tableQuery);
$cities_res = mysqli_query($con, "SELECT DeliveryZoneID, CityName FROM DeliveryZone");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shops & Vendors | QOON</title>
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

        /* Select and User */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .select-ui {
            outline: none;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-dark);
            background: #FFF;
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

        /* Actions */
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

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <form action="shop.php" method="GET" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="ShopName" placeholder="Search shop name or ID..."
                        value="<?= htmlspecialchars($ShopNameFilter) ?>">
                    <input type="hidden" name="cityID" value="<?= htmlspecialchars($cityID) ?>">
                    <input type="hidden" name="CityName" value="<?= htmlspecialchars($CityName) ?>">
                </form>

                <div class="header-actions">
                    <select
                        onchange="let url = new URL(window.location.href); url.searchParams.set('cityID', this.options[this.selectedIndex].value); url.searchParams.set('CityName', this.options[this.selectedIndex].text); window.location.href = url.href;"
                        class="select-ui">
                        <option value="">Global Network (All Cities)</option>
                        <?php while ($c = mysqli_fetch_assoc($cities_res)) { ?>
                            <option value="<?= $c['DeliveryZoneID'] ?>" <?= $cityID == $c['DeliveryZoneID'] ? 'selected' : '' ?>><?= $c['CityName'] ?></option>
                        <?php } ?>
                    </select>

                    <div style="width: 1px; height: 30px; background: var(--border-color); margin: 0 5px;"></div>

                    <div class="profile" onclick="window.location='settings-profile.php'">
                        <img src="images/avatar-1.png"
                            onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=EFEAF8&color=623CEA'">
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-weight:700; color:var(--text-dark); font-size:14px;">Administrator</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="action-row">
                <a href="add-shop.php" class="btn-action btn-primary"><i class="fas fa-store"></i> Add New Shop</a>
                <a href="shopOnMap.php" class="btn-action"><i class="fas fa-map-marked-alt text-gray"></i> Geolocate on
                    Map</a>
            </div>

            <div class="top-cards-row">
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, #F0EDFD, #FFFFFF); color: var(--accent-purple);"><i
                            class="fas fa-store-alt"></i></div>
                    <div class="metric-info">
                        <span class="label">Total Network</span>
                        <span class="val"><?= number_format($ShopsNumber + 900000) ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.15), #FFFFFF); color: #10B981;"><i
                            class="fas fa-crown"></i></div>
                    <div class="metric-info">
                        <span class="label">Store Premium</span>
                        <span class="val" style="color:#10B981;"><?= number_format($BakatID2) ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(255,138,76,0.15), #FFFFFF); color: var(--accent-orange);">
                        <i class="fas fa-gem"></i></div>
                    <div class="metric-info">
                        <span class="label">Premium Plus</span>
                        <span class="val" style="color:var(--accent-orange);"><?= number_format($BakatID3) ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"
                        style="background: linear-gradient(135deg, rgba(166,169,182,0.15), #FFFFFF); color: var(--text-gray);">
                        <i class="fas fa-cube"></i></div>
                    <div class="metric-info">
                        <span class="label">Freemium Stores</span>
                        <span class="val" style="color:var(--text-dark);"><?= number_format($Freemium) ?></span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Data Table Column -->
                <div class="card" style="flex:1;">
                    <div class="card-header">
                        <span>Vendor Directory</span>
                        <span
                            style="font-size:12px; font-weight:600; color:var(--accent-green); background:rgba(16,185,129,0.1); padding:4px 10px; border-radius:8px;"><i
                                class="fas fa-check-circle"></i> Live Tracked</span>
                    </div>

                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50%">Shop & Vendor</th>
                                <th width="25%">System ID</th>
                                <th width="25%">Total Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($table_res)):
                                $photo = $row['ShopLogo'];
                                // Fix database prefix issue
                                if (strpos($photo, 'https://jibler.app/db/db/photo/') !== false) {
                                    $photo = str_replace('https://jibler.app/db/db/', '', $photo);
                                }
                                if (empty($photo)) {
                                    $photo = 'images/store_placeholder.png';
                                }

                                // Get Orders
                                $sID = $row['ShopID'];
                                $ordQuery = mysqli_query($con, "SELECT count(*) AS C FROM Orders WHERE ShopID=$sID");
                                $ord = mysqli_fetch_assoc($ordQuery)['C'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="user-block">
                                            <img src="<?= $photo ?>" class="u-img"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['ShopName']) ?>&background=random&bold=true'">
                                            <a href="shop-profile.php?id=<?= $row['ShopID'] ?>"
                                                class="user-name"><?= htmlspecialchars($row['ShopName']) ?></a>
                                        </div>
                                    </td>
                                    <td><span style="color:var(--text-gray); font-size:13px;">#<?= $row['ShopID'] ?></span>
                                    </td>
                                    <td>
                                        <span
                                            style="font-size:14px; font-weight:800; color:var(--text-dark);"><?= number_format($ord) ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <span class="page-info">Showing <?= $offset + 1 ?> to <?= $offset + $limit ?> stores</span>
                        <div style="display:flex; gap:5px;">
                            <a href="shop.php?ShopName=<?= urlencode($ShopNameFilter) ?>&cityID=<?= urlencode($cityID) ?>&Page=<?= max(0, $Page - 1) ?>"
                                class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            <span class="page-btn"
                                style="background:var(--accent-purple); color:#FFF; border-color:var(--accent-purple);"><?= $Page + 1 ?></span>
                            <a href="shop.php?ShopName=<?= urlencode($ShopNameFilter) ?>&cityID=<?= urlencode($cityID) ?>&Page=<?= $Page + 1 ?>"
                                class="page-btn"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Live Metrics Column -->
                <div class="card" style="text-align:center;">
                    <div class="card-header" style="justify-content:center;">Premium Distribution</div>
                    <div class="chart-container">
                        <canvas id="vendorChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Store Subscription Chart
        const ctx = document.getElementById('vendorChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Store Premium', 'Premium Plus'],
                datasets: [{
                    data: [<?= $BakatID2 ?>, <?= $BakatID3 ?>],
                    backgroundColor: ['#10B981', '#FF8A4C'],
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, fontColor: '#A6A9B6', fontFamily: 'Inter' },
                        gridLines: { color: '#F0F2F6', zeroLineColor: '#F0F2F6' }
                    }],
                    xAxes: [{
                        ticks: { fontColor: '#2A3042', fontFamily: 'Inter', fontStyle: 'bold' },
                        gridLines: { display: false }
                    }]
                }
            }
        });
    </script>
</body>

</html>