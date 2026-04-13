<?php
require_once "conn.php";

// Initialize Filters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '2015-01-01';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$cityID = isset($_GET['city_id']) ? $_GET['city_id'] : '';

$dateFilter = " AND CreatedAtOrders BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$userDateFilter = " AND CreatedAtUser BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$cityFilter = $cityID ? " AND CityID = '$cityID'" : "";
$userCityFilter = $cityID ? " AND CityID = '$cityID'" : "";

// --- METRICS ---

// Total Users
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $userCityFilter");
$UserNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// New Users
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE 1=1 $userDateFilter $userCityFilter");
$NewUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

// Active Users
$res = mysqli_query($con, "SELECT COUNT(DISTINCT Orders.UserID) as total FROM Orders INNER JOIN Users ON Orders.UserID = Users.UserID WHERE 1=1 $dateFilter $cityFilter");
$ActiveUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

// Drivers
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Drivers WHERE 1=1 $cityFilter AND CreatedAtDrivers BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'");
$DriverNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// Shops
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Shops WHERE 1=1 $cityFilter AND CreatedAtShops BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'");
$ShopsNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// Pie Chart Logic
$ActiveDisplay = $ActiveUsers;
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users WHERE UserID NOT IN (SELECT UserID FROM Orders WHERE 1=1 $dateFilter) $userDateFilter $userCityFilter");
$NewDisplay = mysqli_fetch_assoc($res)['total'] ?? 0;
$InactiveDisplay = max(0, $UserNumber - $ActiveDisplay - $NewDisplay);

// Orders
$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Orders WHERE 1=1 $dateFilter $cityFilter");
$OrdersNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

// Sales
$res = mysqli_query($con, "SELECT SUM(OrderPrice) as SalesR FROM Orders WHERE 1=1 $dateFilter $cityFilter");
$SalesR = mysqli_fetch_assoc($res)['SalesR'] ?? 0;

// Fees
$feesQuery = "SELECT SUM(F.Money) as total FROM FeesTransaction F JOIN Users U ON F.UserID = U.UserID WHERE F.CreatedAtFeesTransaction BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
if ($cityID) $feesQuery .= " AND U.CityID = '$cityID'";
$resServ = mysqli_query($con, $feesQuery);
$ServComm = mysqli_fetch_assoc($resServ)['total'] ?? 0;
$TotalIncome = $SalesR + $ServComm;

// Debts
$res = mysqli_query($con, "SELECT SUM(OrderPriceFromShop) as s FROM Orders WHERE PaidForDriver='NotPaid' AND Method='Cash' AND (OrderState='Rated' OR OrderState='Done') $cityFilter");
$DriverDebt = mysqli_fetch_assoc($res)['s'] ?? 0;

$res = mysqli_query($con, "SELECT SUM(Balance) as s FROM Shops WHERE 1=1 $cityFilter");
$ShopOwed = mysqli_fetch_assoc($res)['s'] ?? 0;

// Latest Orders
$latest_orders_query = mysqli_query($con, "SELECT * FROM Orders WHERE 1=1 $dateFilter $cityFilter ORDER BY OrderID DESC LIMIT 3");
$latest_orders = [];
while ($row = mysqli_fetch_assoc($latest_orders_query)) {
    $row['OrderPrice'] = number_format($row['OrderPrice'], 2);
    $latest_orders[] = $row;
}

// --- GROWTH VELOCITY: last 7 days of orders ---
$chartLabels = [];
$chartOrders = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $cityClause = $cityID ? " AND CityID = '$cityID'" : "";
    $r2 = mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE DATE(CreatedAtOrders)='$day' $cityClause");
    $chartLabels[] = date('D', strtotime($day));
    $chartOrders[] = (int)(mysqli_fetch_assoc($r2)['c'] ?? 0);
}

// --- DAILY REVENUE: last 7 days ---
$chartRevenue = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $cityClause = $cityID ? " AND CityID = '$cityID'" : "";
    $r3 = mysqli_query($con, "SELECT COALESCE(SUM(OrderPrice),0) as s FROM Orders WHERE DATE(CreatedAtOrders)='$day' $cityClause");
    $chartRevenue[] = (float)(mysqli_fetch_assoc($r3)['s'] ?? 0);
}

// --- DAILY USER SIGNUPS: last 7 days ---
$chartUsers = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $cityClause = $cityID ? " AND CityID = '$cityID'" : "";
    $r4 = mysqli_query($con, "SELECT COUNT(*) as c FROM Users WHERE DATE(CreatedAtUser)='$day' $cityClause");
    $chartUsers[] = (int)(mysqli_fetch_assoc($r4)['c'] ?? 0);
}

// --- ORDER STATUS BREAKDOWN ---
$statusLabels = [];
$statusCounts = [];
$r5 = mysqli_query($con, "SELECT OrderState, COUNT(*) as c FROM Orders WHERE 1=1 $dateFilter $cityFilter GROUP BY OrderState ORDER BY c DESC LIMIT 6");
if ($r5) {
    while ($row = mysqli_fetch_assoc($r5)) {
        $statusLabels[] = $row['OrderState'];
        $statusCounts[] = (int)$row['c'];
    }
}

// --- TOP 5 CITIES BY ORDERS (via Shops.CityID join) ---
$cityLabels = [];
$cityOrders = [];
$r6 = mysqli_query($con, "
    SELECT D.CityName, COUNT(O.OrderID) as c
    FROM Orders O
    JOIN Shops S ON O.ShopID = S.ShopID
    JOIN DeliveryZone D ON S.CityID = D.DeliveryZoneID
    WHERE 1=1 $dateFilter
    GROUP BY S.CityID
    ORDER BY c DESC
    LIMIT 5
");
if ($r6) {
    while ($row = mysqli_fetch_assoc($r6)) {
        $cityLabels[] = $row['CityName'];
        $cityOrders[] = (int)$row['c'];
    }
}

// --- SHOP TIER DISTRIBUTION ---
$r7 = mysqli_query($con, "SELECT BakatID, COUNT(*) as c FROM Shops GROUP BY BakatID");
$tierMap = [1 => 0, 2 => 0, 3 => 0];
if ($r7) {
    while ($row = mysqli_fetch_assoc($r7)) {
        $id = (int)$row['BakatID'];
        if (isset($tierMap[$id])) $tierMap[$id] = (int)$row['c'];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'UserNumber'    => number_format($UserNumber),
    'NewUsers'      => number_format($NewUsers),
    'ActiveUsers'   => number_format($ActiveUsers),
    'DriverNumber'  => number_format($DriverNumber),
    'ShopsNumber'   => number_format($ShopsNumber),
    'OrdersNumber'  => number_format($OrdersNumber),
    'SalesR'        => number_format($SalesR, 2),
    'TotalIncome'   => number_format($TotalIncome, 2),
    'DriverDebt'    => number_format($DriverDebt, 2),
    'ShopOwed'      => number_format($ShopOwed, 2),
    'PieData'       => [$ActiveDisplay, $NewDisplay, $InactiveDisplay],
    'latest_orders' => $latest_orders,
    'chartLabels'   => $chartLabels,
    'chartOrders'   => $chartOrders,
    'chartRevenue'  => $chartRevenue,
    'chartUsers'    => $chartUsers,
    'statusLabels'  => $statusLabels,
    'statusCounts'  => $statusCounts,
    'cityLabels'    => $cityLabels,
    'cityOrders'    => $cityOrders,
    'tierCounts'    => array_values($tierMap),
]);
