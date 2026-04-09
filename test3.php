<?php
require 'connlog.php';

$res = mysqli_query($con, "SELECT COUNT(*) as total FROM Users");
$UserNumber = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(DISTINCT Users.UserID) as total FROM Users INNER JOIN Orders ON Users.UserID = Orders.UserID");
$UsersWithOrders = mysqli_fetch_assoc($res)['total'] ?? 0;

$UsersNoOrders = max(0, $UserNumber - $UsersWithOrders);

echo "Total Users: $UserNumber\n";
echo "Users With Orders: $UsersWithOrders\n";
echo "Users No Orders: $UsersNoOrders\n";
?>
