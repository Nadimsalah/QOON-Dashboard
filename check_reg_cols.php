<?php
require_once "conn.php";
$res = mysqli_query($con, "DESCRIBE Shops");
$cols = []; while($row = mysqli_fetch_assoc($res)) { $cols[] = $row['Field']; }
echo "Shops: " . implode(', ', $cols) . "\n";

$res = mysqli_query($con, "DESCRIBE Drivers");
$cols = []; while($row = mysqli_fetch_assoc($res)) { $cols[] = $row['Field']; }
echo "Drivers: " . implode(', ', $cols) . "\n";
?>
