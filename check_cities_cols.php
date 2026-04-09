<?php
require 'connlog.php';
$res = mysqli_query($con, "SHOW COLUMNS FROM Cities");
echo "Cities table columns:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
