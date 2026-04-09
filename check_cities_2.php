<?php
require 'connlog.php';
$res = mysqli_query($con, "SELECT CityID, CityName FROM Cities LIMIT 20");
echo "Cities:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['CityID'] . " - " . $row['CityName'] . "\n";
}
?>
