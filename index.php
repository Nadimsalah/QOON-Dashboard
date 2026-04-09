<?php
require_once "conn.php";
include "index_logic.php";

// Initialize Filters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '2015-01-01';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$cityID = isset($_GET['city_id']) ? $_GET['city_id'] : '';
$displayPeriod = ($startDate === '2015-01-01') ? 'All Time' : date('d.m.Y', strtotime($startDate)) . ' - ' . date('d.m.Y', strtotime($endDate));

$dateFilter = " AND CreatedAtOrders BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$userDateFilter = " AND CreatedAtUser BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$cityFilter = $cityID ? " AND CityID = '$cityID'" : "";
$userCityFilter = $cityID ? " AND CityID = '$cityID'" : ""; // Assuming Users table has CityID

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $userCityFilter");
$UserNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// 1. New Users (Registered in this period)
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $userDateFilter $userCityFilter");
$NewUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

// 2. Active Users (Ordered in this period)
$res = mysqli_query($con, "SELECT COUNT(DISTINCT UserID) as total FROM Orders WHERE 1=1 $dateFilter $cityFilter");
$ActiveUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Drivers WHERE 1=1 $cityFilter AND CreatedAtDrivers BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'");
$DriverNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Shops WHERE 1=1 $cityFilter AND CreatedAtShops BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'");
$ShopsNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// 3. Inactive Users (Everyone else)
$InactiveUsers = max(0, $UserNumber - $ActiveUsers - $NewUsers);
// Ensure we don't double count Active if they are also New
$res = mysqli_query($con, "SELECT COUNT(DISTINCT Users.UserID) as total FROM Users INNER JOIN Orders ON Users.UserID = Orders.UserID WHERE 1=1 $userDateFilter $dateFilter $cityFilter");
$NewAndActive = mysqli_fetch_assoc($res)['total'] ?? 0;
// We'll just simplify for the UI:
$ActiveDisplay = $ActiveUsers;
$NewDisplay = $NewUsers;
$InactiveDisplay = max(0, $UserNumber - $ActiveDisplay);

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Orders WHERE 1=1 $dateFilter $cityFilter");
$OrdersNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// Financial Summary (Using OrderPrice as fallback for zero OrderPriceForOur)
$res = mysqli_query($con, "SELECT SUM(OrderPrice) as SalesR FROM Orders WHERE 1=1 $dateFilter $cityFilter");
$SalesR = mysqli_fetch_assoc($res)['SalesR'] ?? 0;

$feesQuery = "SELECT SUM(F.Money) as total FROM FeesTransaction F JOIN Users U ON F.UserID = U.UserID WHERE F.CreatedAtFeesTransaction BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
if ($cityID) {
    $feesQuery .= " AND U.CityID = '$cityID'";
}
$resServ = mysqli_query($con, $feesQuery);
$ServComm = mysqli_fetch_assoc($resServ)['total'] ?? 0;

$TotalIncome = $SalesR + $ServComm;

// Driver Unreturned Cash (Global Debt)
$res = mysqli_query($con, "SELECT SUM(OrderPriceFromShop) as s FROM Orders WHERE PaidForDriver='NotPaid' AND Method='Cash' AND (OrderState='Rated' OR OrderState='Done') $cityFilter");
$DriverDebt = mysqli_fetch_assoc($res)['s'] ?? 0;

// Shop Debt (Global Needs Money)
$res = mysqli_query($con, "SELECT SUM(Balance) as s FROM Shops WHERE 1=1 $cityFilter");
$ShopOwed = mysqli_fetch_assoc($res)['s'] ?? 0;

// Fetch latest orders for Tasks section (filtered)
$latest_orders_query = mysqli_query($con, "SELECT * FROM Orders WHERE 1=1 $dateFilter $cityFilter ORDER BY OrderID DESC LIMIT 3");
$latest_orders = [];
if ($latest_orders_query) {
    while ($row = mysqli_fetch_assoc($latest_orders_query)) {
        $latest_orders[] = $row;
    }
}

// Fetch Cities for dropdown
$cities_res = mysqli_query($con, "SELECT CityID, CityName FROM Cities WHERE Status = 1");

// Sparkline Logic (Last 10 Days)
$sparkDaysConfig = [];
$sparkShopsData = [];
$sparkDriversData = [];
for ($i = 9; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $sparkDaysConfig[] = "'$d'";
    
    $resS = mysqli_query($con, "SELECT COUNT(*) as c FROM Shops WHERE CreatedAtShops LIKE '$d%'");
    $sparkShopsData[] = mysqli_fetch_assoc($resS)['c'] ?? 0;
    
    $resD = mysqli_query($con, "SELECT COUNT(*) as c FROM Drivers WHERE CreatedAtDrivers LIKE '$d%'");
    $sparkDriversData[] = mysqli_fetch_assoc($resD)['c'] ?? 0;
}
$shopsSparklineStr = implode(',', $sparkShopsData);
$driversSparklineStr = implode(',', $sparkDriversData);
$sparkLabelsStr = implode(',', $sparkDaysConfig);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QOON Dashboard</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Flatpickr for Modern Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root {
            /* Palette derived from the provided UI */
            --bg-body: #EFEAF8;
            /* Abstract outer background */
            --bg-app: #F5F6FA;
            /* Inside app background */
            --bg-white: #FFFFFF;

            --text-dark: #2A3042;
            --text-gray: #A6A9B6;
            --text-light: #B4B6C3;

            --accent-purple: #623CEA;
            --accent-purple-light: #F0EDFD;
            --accent-orange: #FF8A4C;
            --accent-yellow: #FFC000;
            --accent-green: #10B981;

            --border-radius-lg: 24px;
            --border-radius-md: 16px;
            --border-radius-sm: 8px;

            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --shadow-app: 0 20px 60px rgba(98, 60, 234, 0.15);
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
            position: relative;
        }

        /* Main Application Container */
        .app-envelope {
            width: 100%;
            height: 100%;
            background: var(--bg-app);
            display: flex;
            overflow: hidden;
        }

        /* ----- SIDEBAR ----- */
        .sidebar {
            width: 260px;
            background: var(--bg-white);
            display: flex;
            flex-direction: column;
            padding: 40px 0;
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
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-yellow));
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
            letter-spacing: 0.5px;
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

        /* Active indicator line */
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

        /* ----- MAIN CONTENT AREA ----- */
        .main-panel {
            flex: 1;
            padding: 35px 40px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Top Header */
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

        .search input::placeholder {
            color: var(--text-gray);
        }

        .search i {
            color: var(--text-gray);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .action-combo {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .action-combo .label {
            color: var(--text-gray);
        }

        .notification {
            position: relative;
            cursor: pointer;
            color: var(--text-dark);
            margin: 0 10px;
        }

        .notification .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--accent-orange);
            border-radius: 50%;
            border: 2px solid var(--bg-app);
        }

        /* Period Dropdown Style */
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
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
            border: none;
            background: none;
        }

        .preset-btn:hover {
            background: var(--bg-app);
            color: var(--accent-purple);
        }

        .preset-btn.active {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
        }

        .custom-divider {
            height: 1px;
            background: #F0F2F6;
            margin: 5px 0;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 25px;
            width: 100%;
        }

        /* Shimmer Loading Skeletons */
        .shimmer-card { position: relative; overflow: hidden; background: #FFF !important; border-radius: var(--border-radius-lg); padding: 24px; box-shadow: var(--shadow-card); display:flex; flex-direction:column; border: 1px solid rgba(255,255,255,0.6); }
        .shimmer-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(239,234,248,0.5), transparent); animation: shimmer 1.2s infinite; z-index:10; }
        @keyframes shimmer { 100% { left: 200%; } }
        .s-box { background: #f0f2f6; border-radius: 8px; margin-bottom: 15px; }
        .s-box.title { width: 50%; height: 15px; }
        .s-box.chart { width: 100%; height: 120px; margin-top: auto; }
        .s-box.num { width: 30%; height: 35px; }
        .s-circle { background: #f0f2f6; }


        /* Search Dropdown & Shimmer */
        .search-wrapper { position: relative; }
        .search-dropdown-box { position: absolute; top: 55px; left: 0; width: 450px; background: #FFF; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); border: 1px solid var(--border-color); z-index: 1000; overflow: hidden; display: none; }
        .search-header { padding: 12px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; border-bottom: 1px solid var(--border-color); background: #F8F9FA;}
        .search-results { max-height: 400px; overflow-y: auto; }
        
        .search-item { display: flex; align-items: center; gap: 15px; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid var(--border-color); transition: 0.2s; }
        .search-item:hover { background: var(--accent-purple-light); }
        .search-item-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(98, 60, 234, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-purple); font-size: 14px; }
        .search-item-info { flex: 1; display:flex; flex-direction:column; }
        .search-item-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 2px; }
        .search-item-sub { font-size: 12px; font-weight: 500; color: var(--text-gray); }
        
        /* Search Shimmer */
        .s-search-item { padding: 15px 20px; display: flex; align-items: center; gap: 15px; border-bottom: 1px solid var(--border-color); }
        .s-box.icon { width: 36px; height: 36px; margin: 0; }
        .s-box.line { height: 12px; margin-bottom: 8px; border-radius: 4px; }
        .s-box.line.short { width: 60%; margin: 0; height: 10px; }

        .col-left {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .col-right {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .top-cards-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            flex: 0 0 auto;
        }

        /* Generic Card */
        .card {
            background: var(--bg-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-card);
            padding: 24px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 15px;
            color: var(--text-dark);
            font-weight: 600;
        }

        .card-options {
            color: var(--text-light);
            cursor: pointer;
            padding: 5px;
        }

        /* Pie Card Specific */
        .pie-card {
            align-items: center;
        }

        .pie-card .card-header {
            width: 100%;
        }

        .pie-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 10px 0 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pie-center-val {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .pie-center-val .total-num {
            display: block;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }

        .pie-center-val .total-label {
            font-size: 10px;
            color: var(--text-gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pie-legends {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 10px;
        }

        .legend-block {
            text-align: center;
        }

        .l-val {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.2;
        }

        .l-num-1 {
            color: var(--accent-yellow);
        }

        .l-num-2 {
            color: var(--accent-purple);
        }

        .l-num-3 {
            color: var(--accent-orange);
        }

        .l-text {
            font-size: 11px;
            color: var(--text-gray);
            margin-top: 4px;
            font-weight: 500;
        }

        /* Stacked Mini Cards */
        .mini-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .mini-card {
            flex: 1;
            padding: 20px 24px;
        }

        .mini-card .card-title {
            font-size: 13px;
        }

        .mini-val {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
            display: flex;
            align-items: baseline;
            gap: 5px;
        }

        .mini-val span {
            font-size: 14px;
        }

        .trend {
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .trend-icon {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
        }

        .trend.up {
            color: var(--accent-orange);
        }

        .trend.up .trend-icon {
            background: #FFF0E8;
        }

        .trend.down {
            color: var(--accent-yellow);
        }

        .trend.down .trend-icon {
            background: #FFF8E1;
        }

        .mini-chart {
            height: 45px;
            width: 110px;
            align-self: flex-end;
            margin-top: -35px;
        }

        /* Financial 3D Cards */
        .cards-3d-wrap {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
        }

        .card-3d {
            background: linear-gradient(135deg, #ffffff 0%, #f4f6fb 100%);
            border-radius: var(--border-radius-lg);
            padding: 30px 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05), inset 0 2px 0 rgba(255, 255, 255, 1), inset 0 -2px 0 rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.6);
            transform-style: preserve-3d;
            perspective: 1000px;
            min-height: 150px;
            justify-content: center;
        }

        .card-3d:hover {
            transform: translateY(-5px) rotateX(2deg) rotateY(-2deg);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.08), inset 0 2px 0 rgba(255, 255, 255, 1);
        }

        .card-3d-title {
            color: var(--text-gray);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 2;
        }

        .card-3d-val {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            position: relative;
            z-index: 2;
        }

        .card-3d-val span {
            font-size: 16px;
            color: var(--text-gray);
            font-weight: 500;
        }

        .card-3d-icon {
            position: absolute;
            right: 15px;
            bottom: -10px;
            font-size: 80px;
            opacity: 0.04;
            color: var(--accent-purple);
            transform: rotate(-10deg);
            z-index: 1;
            transition: all 0.3s ease;
        }

        .card-3d:hover .card-3d-icon {
            transform: scale(1.1) rotate(0deg);
            opacity: 0.07;
            color: var(--accent-orange);
        }

        /* Large Line Chart */
        .line-card {
            flex: 1;
            min-height: 250px;
        }

        .line-labels {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 12px;
            color: var(--text-gray);
            font-weight: 500;
        }

        .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 4px;
        }

        .dot.red {
            background: var(--accent-orange);
        }

        .dot.purple {
            background: var(--accent-purple);
        }

        .dot.yellow {
            background: var(--accent-yellow);
        }

        .line-chart-wrap {
            flex: 1;
            width: 100%;
            position: relative;
            margin-top: 10px;
        }

        /* Right Column - Bar Chart */
        .bar-card {
            flex: 1.2;
        }

        .bar-metrics {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .b-metric {
            flex: 1;
            border: 1px solid #EBECEF;
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.2s;
        }

        .b-metric.active {
            border-color: var(--accent-purple);
        }

        .b-label {
            font-size: 11px;
            color: var(--text-gray);
            margin-bottom: 4px;
        }

        .b-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .bar-chart-wrap {
            flex: 1;
            width: 100%;
        }

        /* Right Column - Tasks */
        .task-card {
            flex: 1.3;
        }

        .ts-title {
            margin-bottom: 5px;
        }

        .ts-link {
            font-size: 12px;
            font-weight: 500;
            color: var(--accent-purple);
            text-decoration: none;
        }

        .ts-sub {
            font-size: 12px;
            color: var(--text-gray);
            margin-bottom: 20px;
        }

        .add-task-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 15px;
            border-bottom: 1px solid #F0F2F6;
            margin-bottom: 15px;
        }

        .add-task-row input {
            border: none;
            outline: none;
            font-size: 13px;
            color: var(--text-dark);
        }

        .add-task-row button {
            background: #F4F6FB;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            color: var(--text-gray);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .task-lists {
            display: flex;
            flex-direction: column;
        }

        .task-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #F0F2F6;
        }

        .task-row:last-child {
            border-bottom: none;
        }

        .t-radio {
            width: 18px;
            height: 18px;
            border: 2px solid #DCE0E5;
            border-radius: 50%;
            cursor: pointer;
        }

        .t-text {
            flex: 1;
            font-size: 13px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .t-badge {
            background: var(--accent-yellow);
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="app-envelope">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Workspace -->
        <main class="main-panel">

            <!-- Header -->
            <header class="header">
                <div class="search-wrapper" id="globalSearchWrapper">
                    <div class="search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="globalSearchTarget" placeholder="Search orders, users, shops, drivers..." autocomplete="off">
                    </div>
                    
                    <div class="search-dropdown-box" id="searchDropdown">
                        <div class="search-header">Universal Dynamic Search</div>
                        <div class="search-results" id="searchOutput"></div>
                    </div>
                </div>

                <div class="header-actions">
                    <div class="period-container">
                        <div class="action-combo" id="periodTrigger" style="cursor:pointer;">
                            <span class="label">Period:</span>
                            <span id="displayPeriod"><?= $displayPeriod ?></span>
                            <i class="fas fa-chevron-down" style="font-size:10px; margin-left:8px; color:#A6A9B6;"></i>
                        </div>
                        <div class="period-dropdown" id="periodMenu">
                            <button class="preset-btn" onclick="applyPreset('all-time')">All Time</button>
                            <button class="preset-btn" onclick="applyPreset('today')">Today</button>
                            <button class="preset-btn" onclick="applyPreset('yesterday')">Yesterday</button>
                            <button class="preset-btn" onclick="applyPreset('this-week')">This Week</button>
                            <button class="preset-btn" onclick="applyPreset('this-month')">This Month</button>
                            <button class="preset-btn" onclick="applyPreset('this-year')">This Year</button>
                            <div class="custom-divider"></div>
                            <button class="preset-btn" id="customTrigger">Custom Range...</button>
                        </div>
                    </div>
                    <div class="action-combo">
                        <span class="label">City:</span>
                        <select id="citySelect"
                            style="border:none; background:none; color:var(--text-dark); font-weight:600; outline:none; cursor:pointer;">
                            <option value="">All Cities</option>
                            <?php while ($city = mysqli_fetch_assoc($cities_res)): ?>
                                <option value="<?= $city['CityID'] ?>" <?= $cityID == $city['CityID'] ? 'selected' : '' ?>>
                                    <?= $city['CityName'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="notification">
                        <i class="far fa-bell" style="font-size:18px;"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="profile" onclick="window.location='settings-profile.php'">
                        <img src="images/avatar-1.png" alt="Admin"
                            onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=EFEAF8&color=623CEA'">
                        <span>Administrator</span>
                        <i class="fas fa-chevron-down" style="font-size:10px; color:#A6A9B6;"></i>
                    </div>
                </div>
            </header>

            <!-- Grid Layout Wrapper -->
            <div style="position: relative; width: 100%;">
                
                <!-- Shimmer Overlay Grid -->
                <div id="shimmerGrid" class="dashboard-grid" style="position:absolute; top:0; left:0; width:100%; z-index:5;">
                    <div class="col-left">
                        <div class="top-cards-row">
                            <div class="card shimmer-card" style="align-items:center;">
                                <div class="s-box title"></div>
                                <div class="s-circle" style="width:120px; height:120px; border-radius:50%; margin:15px 0;"></div>
                            </div>
                            <div class="mini-cards-stack">
                                <div class="card shimmer-card"><div class="s-box title"></div><div class="s-box num"></div></div>
                                <div class="card shimmer-card"><div class="s-box title"></div><div class="s-box num"></div></div>
                            </div>
                        </div>
                        <div class="card shimmer-card" style="min-height:250px;">
                            <div class="s-box title"></div><div class="s-box chart"></div>
                        </div>
                    </div>
                    <div class="col-right">
                        <div class="card shimmer-card" style="min-height:230px;">
                            <div class="s-box title"></div><div class="s-box chart"></div>
                        </div>
                        <div class="cards-3d-wrap">
                            <div class="card-3d shimmer-card" style="min-height:150px;"></div>
                            <div class="card-3d shimmer-card" style="min-height:150px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Real Dashboard Grid -->
                <div id="realDashboard" class="dashboard-grid" style="opacity:0; pointer-events:none; transition: opacity 0.4s ease;">

                <!-- Center/Left Column -->
                <div class="col-left">
                    <div class="top-cards-row">

                        <!-- Pie Card -->
                        <div class="card pie-card">
                            <div class="card-header">
                                <span class="card-title">Users Breakdown</span>
                                <i class="fas fa-ellipsis-v card-options"></i>
                            </div>
                            <div class="pie-wrapper">
                                <canvas id="pieChart"></canvas>
                                <div class="pie-center-val">
                                    <span class="total-num"><?= number_format($UserNumber) ?></span>
                                    <span class="total-label">Total</span>
                                </div>
                            </div>
                            <div class="pie-legends">
                                <div class="legend-block">
                                    <div class="l-val" style="color:#623CEA;"><?= number_format($ActiveDisplay) ?></div>
                                    <div class="l-text">Active<br>Users</div>
                                </div>
                                <div class="legend-block">
                                    <div class="l-val" style="color:#FF8A4C;"><?= number_format($InactiveDisplay) ?>
                                    </div>
                                    <div class="l-text">Inactive<br>Users</div>
                                </div>
                                <div class="legend-block">
                                    <div class="l-val" style="color:#FFC000;"><?= number_format($NewDisplay) ?></div>
                                    <div class="l-text">New<br>Joined</div>
                                </div>
                            </div>
                        </div>

                        <!-- Spark Cards -->
                        <div class="mini-cards-stack">

                            <!-- Card 1 -->
                            <div class="card mini-card">
                                <div class="card-header" style="margin-bottom: 5px;">
                                    <span class="card-title">Total Shops</span>
                                    <i class="fas fa-ellipsis-v card-options"></i>
                                </div>
                                <div class="mini-val">
                                    <?= number_format($ShopsNumber) ?>
                                </div>
                                <div class="trend up">
                                    <div class="trend-icon"><i class="fas fa-arrow-up"></i></div> +3%
                                </div>
                                <div class="mini-chart">
                                    <canvas id="barSparks1"></canvas>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="card mini-card">
                                <div class="card-header" style="margin-bottom: 5px;">
                                    <span class="card-title">Total Delivery Men</span>
                                    <i class="fas fa-ellipsis-v card-options"></i>
                                </div>
                                <div class="mini-val">
                                    <?= number_format($DriverNumber) ?>
                                </div>
                                <div class="trend up">
                                    <div class="trend-icon"><i class="fas fa-arrow-up"></i></div> +1.2%
                                </div>
                                <div class="mini-chart">
                                    <canvas id="barSparks2"></canvas>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div class="card line-card">
                        <div class="card-header">
                            <span class="card-title">All Orders Status</span>
                            <div class="line-labels">
                                <div><span class="dot red"></span> Delivered</div>
                                <div><span class="dot yellow"></span> Doing</div>
                                <div><span class="dot purple"></span> Cancelled</div>
                            </div>
                            <i class="fas fa-ellipsis-v card-options"></i>
                        </div>
                        <div class="line-chart-wrap">
                            <canvas id="lineActivity"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-right">

                    <!-- Main Bar Chart -->
                    <div class="card bar-card">
                        <div class="card-header" style="margin-bottom: 15px;">
                            <span class="card-title">Profits</span>
                            <i class="fas fa-ellipsis-v card-options"></i>
                        </div>

                        <div id="profitMetrics" class="bar-metrics" style="margin-bottom: 30px;">
                            <div class="b-metric active" onclick="switchProfitChart('revenue', this)"
                                style="cursor:pointer;">
                                <div class="b-label">Sales Revenue</div>
                                <div class="b-val"><?= number_format($SalesR) ?> <span>MAD</span></div>
                            </div>
                            <div class="b-metric" onclick="switchProfitChart('service', this)" style="cursor:pointer;">
                                <div class="b-label">Service Comm.</div>
                                <div class="b-val"><?= number_format($ServComm) ?> <span>MAD</span></div>
                            </div>
                        </div>

                        <div class="bar-chart-wrap" style="height: 180px;">
                            <canvas id="profitChart"></canvas>
                        </div>
                    </div>

                    <!-- Financial 3D Cards -->
                    <div class="cards-3d-wrap">
                        <div class="card-3d">
                            <div class="card-3d-title">Drivers Unreturned Cash</div>
                            <div class="card-3d-val"><?= number_format($DriverDebt) ?> <span>MAD</span></div>
                            <i class="fas fa-motorcycle card-3d-icon"></i>
                        </div>
                        <div class="card-3d">
                            <div class="card-3d-title">Debt To Shops (Net)</div>
                            <div class="card-3d-val"><?= number_format($ShopOwed) ?> <span>MAD</span></div>
                            <i class="fas fa-store card-3d-icon"></i>
                        </div>
                    </div>

                </div>

            </div>
            </div>
        </main>
    </div>

    <!-- Chart Configs mapping precisely to the requested design -->
    <script>
        window.onload = function () {
            console.log("QOON Dashboard: Initializing Charts...");
            if (typeof Chart === 'undefined') {
                console.error("Chart.js not loaded! Re-trying CDN...");
                return;
            }

            // Global Chart Defaults
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#5A607F';
            Chart.defaults.plugins.tooltip.backgroundColor = '#2A3042';

            const getCtx = (id) => {
                const el = document.getElementById(id);
                if (!el) console.warn("Canvas not found: " + id);
                return el ? el.getContext('2d') : null;
            };

            const initCharts = function() {
                // 1. Users Breakdown (Doughnut)
            const ctxP = getCtx('pieChart');
            if (ctxP) {
                new Chart(ctxP, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Inactive', 'New'],
                        datasets: [{
                            data: [<?= (int) $ActiveDisplay ?>, <?= (int) $InactiveDisplay ?>, <?= (int) $NewDisplay ?>],
                            backgroundColor: ['#623CEA', '#FF8A4C', '#FFC000'],
                            borderWidth: 0,
                            cutout: '72%',
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // 2 & 3 Sparklines
            const sparkOpt = {
                responsive: true, maintainAspectRatio: false,
                scales: { x: { display: false }, y: { display: false } },
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            };
            const ctxS1 = getCtx('barSparks1');
            if (ctxS1) {
                new Chart(ctxS1, {
                    type: 'bar',
                    data: { labels: [<?= $sparkLabelsStr ?>], datasets: [{ data: [<?= $shopsSparklineStr ?>], backgroundColor: '#623CEA', borderRadius: 2 }] },
                    options: sparkOpt
                });
            }
            const ctxS2 = getCtx('barSparks2');
            if (ctxS2) {
                new Chart(ctxS2, {
                    type: 'bar',
                    data: { labels: [<?= $sparkLabelsStr ?>], datasets: [{ data: [<?= $driversSparklineStr ?>], backgroundColor: '#FF8A4C', borderRadius: 2 }] },
                    options: sparkOpt
                });
            }

            // 4. All Orders Status (Area Line)
            const ctxL = getCtx('lineActivity');
            if (ctxL) {
                const gradO = ctxL.createLinearGradient(0, 0, 0, 300);
                gradO.addColorStop(0, 'rgba(255, 138, 76, 0.4)');
                gradO.addColorStop(1, 'rgba(255, 138, 76, 0)');
                const gradP = ctxL.createLinearGradient(0, 0, 0, 300);
                gradP.addColorStop(0, 'rgba(98, 60, 234, 0.2)');
                gradP.addColorStop(1, 'rgba(98, 60, 234, 0)');

                new Chart(ctxL, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [
                            {
                                label: 'Orders',
                                data: [<?= (float) $MonOrder ?>, <?= (float) $TuesOrder ?>, <?= (float) $WednesOrder ?>, <?= (float) $ThursOrder ?>, <?= (float) $FriOrder ?>, <?= (float) $SaturOrder ?>, <?= (float) $SunOrder ?>],
                                borderColor: '#FF8A4C', backgroundColor: gradO, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 0
                            },
                            {
                                label: 'Usage',
                                data: [<?= (float) $Mon ?>, <?= (float) $Tues ?>, <?= (float) $Wednes ?>, <?= (float) $Thurs ?>, <?= (float) $Fri ?>, <?= (float) $Satur ?>, <?= (float) $Sun ?>],
                                borderColor: '#623CEA', backgroundColor: gradP, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: { grid: { color: '#EDF0F5', drawBorder: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }

            // 5. Profits (Blue Area Chart)
            const ctxB = getCtx('profitChart');
            let profitChart;

            const revenueData = [<?= (float) $MonRev ?>, <?= (float) $TuesRev ?>, <?= (float) $WednesRev ?>, <?= (float) $ThursRev ?>, <?= (float) $FriRev ?>, <?= (float) $SaturRev ?>, <?= (float) $SunRev ?>];
            const serviceData = [<?= (float) $MonFees ?>, <?= (float) $TuesFees ?>, <?= (float) $WednesFees ?>, <?= (float) $ThursFees ?>, <?= (float) $FriFees ?>, <?= (float) $SaturFees ?>, <?= (float) $SunFees ?>];

            if (ctxB) {
                const gradB = ctxB.createLinearGradient(0, 0, 0, 300);
                gradB.addColorStop(0, 'rgba(0, 122, 255, 0.3)');
                gradB.addColorStop(1, 'rgba(0, 122, 255, 0)');

                profitChart = new Chart(ctxB, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Revenue',
                            data: revenueData,
                            borderColor: '#007AFF',
                            backgroundColor: gradB,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#FFF',
                            pointBorderColor: '#007AFF',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: { grid: { color: '#EDF0F5', drawBorder: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }

            window.switchProfitChart = function (type, el) {
                // UI Toggle
                document.querySelectorAll('#profitMetrics .b-metric').forEach(m => {
                    m.classList.remove('active');
                    m.style.borderColor = '#EBECEF';
                    m.querySelector('.b-val').style.color = '#2A3042';
                });
                el.classList.add('active');
                el.style.borderColor = '#007AFF';
                el.querySelector('.b-val').style.color = '#007AFF';

                // Data Toggle
                if (profitChart) {
                    profitChart.data.datasets[0].data = (type === 'revenue') ? revenueData : serviceData;
                    profitChart.data.datasets[0].label = (type === 'revenue') ? 'Revenue' : 'Fees';
                    profitChart.update();
                }
            };

            }; // end initCharts

            // Shimmer Crossfade Logic
            setTimeout(() => {
                document.getElementById('shimmerGrid').style.display = 'none';
                const realDash = document.getElementById('realDashboard');
                realDash.style.opacity = '1';
                realDash.style.pointerEvents = 'auto';
                initCharts();
            }, 800);

            // 6. Filter & Period Logic
            const periodTrigger = document.getElementById('periodTrigger');
            const periodMenu = document.getElementById('periodMenu');

            periodTrigger.addEventListener('click', () => periodMenu.classList.toggle('active'));
            document.addEventListener('click', (e) => {
                if (!periodTrigger.contains(e.target) && !periodMenu.contains(e.target)) periodMenu.classList.remove('active');
            });

            // Flatpickr for Custom Range
            const fp = flatpickr("#customTrigger", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: ["<?= $startDate ?>", "<?= $endDate ?>"],
                positionElement: periodTrigger,
                onClose: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], "Y-m-d");
                        const end = instance.formatDate(selectedDates[1], "Y-m-d");
                        window.location.href = `index.php?start_date=${start}&end_date=${end}&city_id=<?= $cityID ?>`;
                    }
                }
            });

            window.applyPreset = function (type) {
                let start, end;
                const today = new Date();
                const formatDate = (d) => d.toISOString().split('T')[0];

                switch (type) {
                    case 'all-time':
                        start = '2015-01-01';
                        end = formatDate(today);
                        break;
                    case 'today':
                        start = end = formatDate(today);
                        break;
                    case 'yesterday':
                        const yesterday = new Date(today);
                        yesterday.setDate(today.getDate() - 1);
                        start = end = formatDate(yesterday);
                        break;
                    case 'this-week':
                        const first = today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1);
                        start = formatDate(new Date(today.setDate(first)));
                        end = formatDate(new Date());
                        break;
                    case 'this-month':
                        start = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                        end = formatDate(new Date());
                        break;
                    case 'this-year':
                        start = formatDate(new Date(today.getFullYear(), 0, 1));
                        end = formatDate(new Date());
                        break;
                }
                if (start && end) {
                    window.location.href = `index.php?start_date=${start}&end_date=${end}&city_id=<?= $cityID ?>`;
                }
            };
            
            // 7. Dynamic Search Logic
            const searchInput = document.getElementById('globalSearchTarget');
            const searchDropdown = document.getElementById('searchDropdown');
            const searchOutput = document.getElementById('searchOutput');
            let searchTimeout = null;

            const renderSearchShimmer = () => {
                let html = '';
                for(let i=0; i<3; i++) {
                    html += `
                    <div class="s-search-item shimmer-card" style="padding:15px 20px; box-shadow:none; flex-direction:row; background:none; border:none; border-radius:0; border-bottom:1px solid #F0F2F6;">
                        <div class="s-box icon"></div>
                        <div style="flex:1;">
                            <div class="s-box line" style="width:80%;"></div>
                            <div class="s-box line short"></div>
                        </div>
                    </div>`;
                }
                searchOutput.innerHTML = html;
            };

            searchInput.addEventListener('input', (e) => {
                const val = e.target.value.trim();
                
                if (val.length < 2) {
                    searchDropdown.style.display = 'none';
                    return;
                }

                searchDropdown.style.display = 'block';
                renderSearchShimmer();

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetch('search_api.php?q=' + encodeURIComponent(val))
                        .then(res => res.json())
                        .then(data => {
                            if (!data.results || data.results.length === 0) {
                                searchOutput.innerHTML = `<div style="padding:30px; text-align:center; color:#A6A9B6; font-size:14px; font-weight:600;"><i class="fas fa-search mb-2" style="font-size:24px; opacity:0.3; margin-bottom:10px;"></i><br>No matching records found for "${val}"</div>`;
                                return;
                            }
                            
                            let html = '';
                            data.results.forEach(item => {
                                html += `<a href="${item.url}" class="search-item">
                                    <div class="search-item-icon"><i class="fas ${item.icon}"></i></div>
                                    <div class="search-item-info">
                                        <div class="search-item-title">${item.title} <span style="font-size:10px; padding:3px 8px; background:#F0F2F6; color:#623CEA; border-radius:6px; margin-left:8px;">${item.type}</span></div>
                                        <div class="search-item-sub">${item.subtitle}</div>
                                    </div>
                                </a>`;
                            });
                            searchOutput.innerHTML = html;
                        })
                        .catch(() => {
                            searchOutput.innerHTML = `<div style="padding:20px; text-align:center; color:#EF4444; font-size:13px;">Error fetching results.</div>`;
                        });
                }, 500);
            });

            document.addEventListener('click', (e) => {
                const wrapper = document.getElementById('globalSearchWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });

        };
    </script>
</body>

</html>