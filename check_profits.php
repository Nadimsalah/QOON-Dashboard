<?php
require_once "conn.php";
$res = mysqli_query($con, "SELECT SUM(OrderPriceForOur) as s FROM Orders");
$row = mysqli_fetch_assoc($res);
echo "Total Sales Rev: " . $row['s'] . "\n";

$res = mysqli_query($con, "SELECT SUM(Money) as s FROM FeesTransaction");
$row = mysqli_fetch_assoc($res);
echo "Total Fees: " . $row['s'] . "\n";
?>
