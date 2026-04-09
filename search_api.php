<?php
require "conn.php";
header('Content-Type: application/json');

$q = isset($_GET['q']) ? mysqli_real_escape_string($con, trim($_GET['q'])) : '';

if (strlen($q) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$results = [];

// 1. Search Users
$resU = mysqli_query($con, "SELECT UserID, name, PhoneNumber FROM Users WHERE name LIKE '%$q%' OR PhoneNumber LIKE '%$q%' OR UserID = '$q' LIMIT 5");
if ($resU) {
    while ($row = mysqli_fetch_assoc($resU)) {
        $results[] = [
            'type' => 'User',
            'title' => trim($row['name']),
            'subtitle' => $row['PhoneNumber'],
            'url' => "user-profile.php?id=" . $row['UserID'],
            'icon' => 'fa-user'
        ];
    }
}

// 2. Search Shops
$resS = mysqli_query($con, "SELECT ShopID, ShopName, ShopPhone FROM Shops WHERE ShopName LIKE '%$q%' OR ShopPhone LIKE '%$q%' OR ShopID = '$q' LIMIT 5");
if ($resS) {
    while ($row = mysqli_fetch_assoc($resS)) {
        $results[] = [
            'type' => 'Shop',
            'title' => $row['ShopName'],
            'subtitle' => $row['ShopPhone'],
            'url' => "shop-profile.php?id=" . $row['ShopID'],
            'icon' => 'fa-store'
        ];
    }
}

// 3. Search Drivers
$resD = mysqli_query($con, "SELECT DriverID, FName, LName, DriverPhone FROM Drivers WHERE FName LIKE '%$q%' OR LName LIKE '%$q%' OR DriverPhone LIKE '%$q%' OR DriverID = '$q' LIMIT 5");
if ($resD) {
    while ($row = mysqli_fetch_assoc($resD)) {
        $results[] = [
            'type' => 'Driver',
            'title' => trim($row['FName'] . ' ' . $row['LName']),
            'subtitle' => $row['DriverPhone'],
            'url' => "driver-profile.php?id=" . $row['DriverID'],
            'icon' => 'fa-motorcycle'
        ];
    }
}

// 4. Search Orders
$resO = mysqli_query($con, "SELECT OrderID, UserPhone, OrderState FROM Orders WHERE OrderID = '$q' OR UserPhone LIKE '%$q%' LIMIT 5");
if ($resO) {
    while ($row = mysqli_fetch_assoc($resO)) {
        $results[] = [
            'type' => 'Order',
            'title' => 'Order #' . $row['OrderID'],
            'subtitle' => $row['UserPhone'] . ' - ' . $row['OrderState'],
            'url' => "order-detail.php?id=" . $row['OrderID'],
            'icon' => 'fa-receipt'
        ];
    }
}

echo json_encode(['results' => $results]);
?>
