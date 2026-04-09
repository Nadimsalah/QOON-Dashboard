<?php
require 'connlog.php';

$res = mysqli_query($con, "SELECT SUM(charge) as s FROM Drivers");
$m = mysqli_fetch_assoc($res);
echo "Drivers Total Charge: " . $m['s'] . "\n";
?>
