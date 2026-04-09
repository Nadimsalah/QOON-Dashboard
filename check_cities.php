<?php
require 'connlog.php';
$res = mysqli_query($con, "SELECT CitiesID, CityName FROM Cities LIMIT 10");
echo "Cities:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['CitiesID'] . " - " . $row['CityName'] . "\n";
}
?>
