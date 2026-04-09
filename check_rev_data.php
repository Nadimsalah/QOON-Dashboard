<?php
require_once "conn.php";
echo "--- SAMPLE ORDERS ---\n";
$res = mysqli_query($con, "SELECT OrderPrice, OrderPriceFromShop, OrderPriceForOur, CreatedAtOrders FROM Orders ORDER BY OrderID DESC LIMIT 5");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n--- SAMPLE FEES ---\n";
$res = mysqli_query($con, "SELECT Money, CreatedAtFeesTransaction FROM FeesTransaction ORDER BY FeesTransactionID DESC LIMIT 5");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
