<?php
require 'connlog.php';

$res = mysqli_query($con, "SELECT SUM(Balance) as s FROM Shops");
$m = mysqli_fetch_assoc($res);
echo "Shops Total Balance: " . $m['s'] . "\n";

$res = mysqli_query($con, "SELECT BalanceTraComm, BalanceWithComm FROM Money LIMIT 1");
$m = mysqli_fetch_assoc($res);
echo "Money BalanceTraComm: " . $m['BalanceTraComm'] . "\n";
echo "Money BalanceWithComm: " . $m['BalanceWithComm'] . "\n";

?>
